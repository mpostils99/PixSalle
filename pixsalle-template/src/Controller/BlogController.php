<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Repository\BlogRepository;
use Salle\PixSalle\Model\Blog;

use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Model\User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

use GuzzleHttp\Psr7\Req;
use GuzzleHttp\Client;

final class BlogController
{
    private Twig $twig;
    private BlogRepository $blogRepository;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        BlogRepository $blogRepository,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->blogRepository = $blogRepository;
        $this->userRepository = $userRepository;

    }

    public function showPosts(Request $request, Response $response): Response {
        
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $blogs = "";
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $loggedIn = $_SESSION['loggedin'];
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        }else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }

        
        
        return $this->twig->render(
            $response,
            'blog.twig',
            [
                'blogs' => $blogs,
                'loggedIn' => $loggedIn,
                'userWeb' => "blogs",
                'formData'  => $user,

            ]
        ); 
    }

    public function showSinglePost(Request $request, Response $response, $id): Response {

        $blogId = intval($id['id']);
        
    }
}


/*
}else{
    
    if(isset($_POST['search']) && !empty($_POST['searchqry'])){

        $con = new dbConnection;
        $search = trim($_POST['searchqry']);
    
        $con->searchInsert($search);

    
        $results = $con->getSearch($search);
        $sesion = $_SESSION['id'];
    
        $con->userSearchInsert($results, $sesion);
    
        $apiKey ="v9bXnrpFKv6uEVObzg3qUIqrLsWxTdce";
        $apiUrl = "api.giphy.com/v1/gifs/search?api_key=$apiKey&q=$search";
        $client = new GuzzleHttp\Client();
        $guzzleResponse = $client->request('GET', $apiUrl);
        $response = json_decode($guzzleResponse->getBody()->getContents(),true);
    }
}*/