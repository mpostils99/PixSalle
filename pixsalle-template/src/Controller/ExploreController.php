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

final class ExploreController
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
    public function showPictures(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $email = $_SESSION['email'];
            $pictures = $this->userRepository->getAllPictures();
            $loggedIn = $_SESSION['loggedin'];
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
        }else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }

        return $this->twig->render(
            $response,
            'explore.twig',
            [
                'pictures' => $pictures,
                'loggedIn' => $loggedIn,
                'formData'  => $user,
                'userWeb' => "explore",
            ]
        );
    }
}
