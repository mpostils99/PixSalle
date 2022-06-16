<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;
use Psr\Http\Message\UploadedFileInterface ;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Ramsey\Uuid\Uuid;
use DateTime;

final class ProfileController {
    private Twig $twig;
    private UserRepository $userRepository;
    private User $user;
    private ValidatorService $validator;
    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';
    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";

    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";
  
    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = new ValidatorService();
    }

    /**
     * Renders the form
     */
    public function showProfileForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $username = "";
        $type = "";
        $loggedIn = "";
        $pathPicture = "";
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $loggedIn = $_SESSION['loggedin'];
             
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);

            
        } else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }




        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formAction' => $routeParser->urlFor('profile'),
                'formData'  => $user,
                'loggedIn' => $loggedIn,
                'userWeb' => "profile",
            ]
        );
    }

    public function modifyProfile(Request $request, Response $response): Response {
        
        $data = $request->getParsedBody();
        $errors = [];
        $loggedIn = $_SESSION['loggedin'];
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $errors['phone'] = $this->validator->validatePhone($data['phone']);
        
        if(!empty($data['userName'])){
            $errors['userName'] = $this->validator->validateUserName($data['userName']);
            if ($errors['userName'] == '') {
                unset($errors['userName']);
            }
        }
        if ($errors['phone'] == '') {
            unset($errors['phone']);
        }
        //var_dump($data['file']);
       //$errors = [];
       if (isset($_POST["btnDelete"])){ 
        $this->userRepository->deleteUserPicture($_SESSION['email']);
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            if($user->userName == null){
                $user->userName = "user".$user->id;
            }
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formErrors' => $errors,
                'formData' => $user,
                'formAction' => $routeParser->urlFor('profile'),
                'loggedIn' => $loggedIn,
                'userWeb' => "profile",
            ]
        );

        }

        $uploadedFiles = $request->getUploadedFiles();
        $newName="";
        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $uploadedFiles['files'];
            $name = $uploadedFile->getClientFilename();
            if($name != ""){
                $fileInfo = pathinfo($name);
                $uuid = Uuid::uuid4();
                //var_dump($uploadedFile);
                $format = $fileInfo['extension'];
                $newName = $uuid->toString() . '.' . $format;
                list($width, $height) = getimagesize($_FILES['files']['tmp_name']);
                $size = $uploadedFile->getSize();
    
                if (!$this->validator->isValidFormat($format)) {
                    $errors['profile_picture'] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                }elseif($width > 500 || $height > 500){
                    $errors['profile_picture'] = "The image must be less than 500x500 pixels";
                }elseif($size > 1000000){
                    $errors['profile_picture'] = "The image must be less than 1MB";
                }else{
                   // var_dump(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $newName);
                    $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $newName);
                }
            }


            // We should generate a custom name here instead of using the one coming form the form
         
           //$this->userRepository->getUserByEmail($_SESSION['email']);
        
        
        if (count($errors) == 0) {
            if($newName == "" ){
                $newName = $this->userRepository->selectUserPicture($_SESSION['email']);
                //var_dump($newName);
            }

            $user = new User(
                $data["userName"] ?? '',
                $_SESSION['email'],
                 '',
                $data["phone"] ?? '',
                $newName ?? '',
                0 ?? '',
                new DateTime(),
                new DateTime()
            );
            $this->userRepository->updateUser($user);
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            return $response->withHeader('Location', '/profile')->withStatus(302);
        }
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            if($user->userName == null){
                $user->userName = "user".$user->id;
            }
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                'formErrors' => $errors,
                'formData' => $user,
                'formAction' => $routeParser->urlFor('profile'),
                'loggedIn' => $loggedIn,
                'userWeb' => "profile",
            ]
        );
    }

    public function showChangePasswordForm(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $loggedIn = "";

        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $loggedIn = $_SESSION['loggedin'];
        } else {
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }

        return $this->twig->render(
            $response, 
            'profile-change-password.twig',
            [
                'loggedIn' => $loggedIn
            ]);
    }

    public function changePasswordAction(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $data = $request->getParsedBody();
        $errors = [];
        $validated = false;

        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $loggedIn = $_SESSION['loggedin'];
        } else {
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
        
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        if($user->password != md5($data['old-password'])) {
            //$errors['password'] = "Your old password doesn't match.";
            $errors['password'] = "Some or all of the passwords are incorrect.";
        }

        if (count($errors) == 0) {
            //Validem que les 3 contrasenyes tinguin el format correcte.
            $errors['password'] = $this->validator->validatePassword($data['old-password']);
            $errors['password'] = $this->validator->validatePassword($data['new-password']);
            $errors['password'] = $this->validator->validatePassword($data['confirm-password']);

            //echo count($errors);

            if ($errors['password'] == '') {
                unset($errors['password']);
                if(strcmp($data['new-password'], $data['confirm-password']) != 0) {
                    //$errors['password'] = "The new and confirm password doesn't match";
                    $errors['password'] = "Some or all of the passwords are incorrect.";
                } else {
                    $this->userRepository->updatePassword(md5($data['new-password']));
                    $validated = true;
                    //S'hauria de comprovar si la new i confirm son la mateixa que la old? No ho diu a l'enunciat.
                }
            } else {
                $errors['password'] = "Some or all of the passwords are incorrect.";
            }
       }
       
       return $this->twig->render(
            $response,
            'profile-change-password.twig',
            [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor('change-password'),
                'validated' => $validated,
                'loggedIn' => $loggedIn
            ]
        );     
    }
}
