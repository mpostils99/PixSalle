<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Service\ValidatorService;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class WalletController
{
    private Twig $twig;
    private UserRepository $userRepository;

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
    public function showWalletForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $username = "";
        $money = "";
        $loggedin = "";
        $errors = [];

        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            if($user->userName == null){
                $username = "user".$user->id;
            }else{
                $username = $user->userName;
            }
            $money = $this->userRepository->getUserMoney($_SESSION['email']);
            $loggedin = $_SESSION['loggedin'];
           
        }else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'formErrors' => $errors,
                'username' => $username,
                'formData'  => $user,
                'money' => $money,
                'formAction' => $routeParser->urlFor('wallet'),
                'loggedIn' => $loggedin,
                'userWeb' => "wallet",
            ]
        );
    }

    public function addMoney(Request $request, Response $response): Response
    {
        $username = "";
        $loggedin = "";
        $errors = [];
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);
        if($user->userName == null){
            $username = "user".$user->id;
        }else{
            $username = $user->userName;
        }
        $loggedin = $_SESSION['loggedin'];

        $money = $this->userRepository->getUserMoney($_SESSION['email']);
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        $errors['money'] = $this->validator->validateMoney(intval($data['money']),$money);

        if(!$errors['money']){
            $money = $this->userRepository->setUserMoney($_SESSION['email'],$money + intval($data['money']));
            return $response->withHeader('Location', '/user/wallet')->withStatus(302);
        }

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'formErrors' => $errors,
                'username' => $username,
                'money' => $money,
                'formAction' => $routeParser->urlFor('wallet'),
                'loggedIn' => $loggedin,
                'userWeb' => "wallet",
            ]
        );
    }
}
