<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Repository\BlogRepository;
use Salle\PixSalle\Model\Blog;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use DateTime;

final class ApiBlogController
{
    private Twig $twig;
    private BlogRepository $blogRepository;

    public function __construct(
        Twig $twig,
        BlogRepository $blogRepository
    ) {
        $this->twig = $twig;
        $this->blogRepository = $blogRepository;
    }

    /**
     * Renders the form
     */
    public function showPosts(Request $request, Response $response): Response {
        $blogs = $this->blogRepository->getBlogs();
        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(
            json_encode($blogs)
        );
        return $response;
    }

    public function showSinglePost(Request $request, Response $response, $id): Response {

        $blogId = intval($id['id']);
        $blog = $this->blogRepository->getAblog($blogId);

        if ($blog == null){
            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
            $response->getBody()->write(
                "{message: Blog entry with id ".$blogId." does not exist}"
            );
        }else{
            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $response->getBody()->write(
                json_encode($blog)
            );
        }
        return $response;
    }

    public function createPost(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        if(!isset($data['title']) || !isset($data['content']) || !isset($data['userId'])){
            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
            $response->getBody()->write(
                "{message: 'title' and/or 'content' and/or 'userId' key missing}"
            );       
        }else{
            $blogCreated = $this->blogRepository->createBlog($data['title'],$data['content'],$data['userId']);
            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $response->getBody()->write(
                json_encode($blogCreated)
            );
        }

        return $response; 
    }

    public function deletePost(Request $request, Response $response, $id): Response {

        $blogId = intval($id['id']);

        $deleted = $this->blogRepository->deleteBlog($blogId);
        
        if(!$deleted){

            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
            $response->getBody()->write(
                "{message: Blog entry with id ".$blogId." does not exist}"
            ); 

        }else{

            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
            $response->getBody()->write(
                "{message: Blog entry with id ".$blogId." was successfully deleted}"
            ); 
        }

        return $response;
    }


    public function modifyPost(Request $request, Response $response, $id): Response {

        $blogId = intval($id['id']);
        $data = $request->getParsedBody();

        if(!isset($data['title']) || !isset($data['content'])){
            $response->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
            $response->getBody()->write(
                "{message: The title and/or content cannot be empty}"
            ); 
        }else{

            $modifiedBlog = $this->blogRepository->modifyPost($data['title'],$data['content'],$blogId);

            
            
            if($modifiedBlog == false){
                $response->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
                $response->getBody()->write(
                    "{message: Blog entry with id".$blogId."does not exist}"
                );
            }else{
                $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                $response->getBody()->write(
                    json_encode($modifiedBlog)
                );
            }


        } 
    
        return $response;
    }

}
