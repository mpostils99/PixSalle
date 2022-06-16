<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Service\ValidatorService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Salle\PixSalle\Repository\UserRepository;

final class LandingPageController {

    private Twig $twig;
    private ValidatorService $validator;
    private UserRepository $userRepository;

    public function __construct(Twig $twig , UserRepository $userRepository) {
        $this->twig = $twig;
        $this->validator = new ValidatorService();
        $this->userRepository = $userRepository;
    }

    public function showLandingPage(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $username = "";
        $loggedIn = "";
        $user = "";
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $username = $_SESSION['email'];
            $loggedIn = $_SESSION['loggedin'];
            $username = substr($username, 0, strpos($username, "@"));  
            $user = $this->userRepository->getUserByEmail($_SESSION['email']); 
        }

        return $this->twig->render(
            $response,
            'landing-page.twig',
            [
                'loggedIn' => $loggedIn,
                'username' => $username,
                'formData'  => $user,
                'userWeb' => "landing",
            ]
        );
    }

}