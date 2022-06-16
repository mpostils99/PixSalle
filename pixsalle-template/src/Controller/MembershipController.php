<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class MembershipController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    /**
     * Renders the form
     */
    public function showMembershipForm(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $username = "";
        $type = "";
        $loggedin = "";

        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            if($user->userName == null){
                $username = "user".$user->id;
            }else{
                $username = $user->userName;
            }
            $type = $this->userRepository->getUserType($_SESSION['email']);
            $loggedin = $_SESSION['loggedin'];

            //DEBUG
            //$debug = $this->userRepository->insertPicture($_SESSION['email'],"http://cdn.shopify.com/s/files/1/1566/2889/articles/Clasificacion-de-tipos-de-olas-por-su-outbreak-y-origen-1.jpg?v=1616068745");

        }else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
        return $this->twig->render(
            $response,
            'membership.twig',
            [
                'username' => $username,
                'formData'  => $user,
                'type' => $type,
                'formAction' => $routeParser->urlFor('membership'),
                'loggedIn' => $loggedin,
                'userWeb' => "membership",
            ]
        );
    }

    public function changePlan(Request $request, Response $response): Response
    {
        
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $type = $this->userRepository->setUserType($_SESSION['email'],$data['membership']);

        return $response->withHeader('Location', '/user/membership')->withStatus(302);


    }
}
