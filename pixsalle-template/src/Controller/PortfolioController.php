<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Picture;
use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use stdClass;

final class PortfolioController {
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(
        Twig $twig,
        UserRepository $userRepository
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showPortfolio(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $existantPortfolio = false;
        $portfolioTitle = "";
        $albumsIds = [[]];
        //$numAlbums = 0;
        $existantAlbum = false;
        $albums = [];

        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $email = $_SESSION['email'];
            $loggedIn = $_SESSION['loggedin'];

            $userType = $this->userRepository->getUserType($email);
            
            $portfolioId = $this->userRepository->checkExistantPortfolio($email);
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);
            // Si l'usuari no ha creat cap portfolio, posem un condicional al twig...
            if($portfolioId == NULL) {
                $existantPortfolio = false;
            } else {
                $existantPortfolio = true;
                $portfolioTitle = $this->userRepository->getPortfolioTitle($portfolioId);
                $albumsIds = $this->userRepository->checkExistantAlbum($portfolioId);
                
                // Si no hi ha cap album creat...
                if(empty($albumsIds)) { //empty
                    $existantAlbum = false;
                } else {
                    $existantAlbum = true;
                    $albums = $this->userRepository->getAlbums($portfolioId);
                }
            }

        }else{
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }
        
        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'formAction' => $routeParser->urlFor('portfolio'),
                'userType' => $userType,
                'existantPortfolio' => $existantPortfolio,
                'existantAlbum' => $existantAlbum,
                'portfolioTitle' => $portfolioTitle,
                'albums' => $albums,
                'formData'  => $user,
                'loggedIn' => $loggedIn,
                'userWeb' => "portfolio",
            ]
        );
     
    }

    public function managePortfolioAction(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $loggedIn = $_SESSION['loggedin'];
        $email = $_SESSION['email'];

        $existantPortfolio = true;
        $portfolioId = $this->userRepository->checkExistantPortfolio($email);
        // Inicialitza un objecte buit.
        $portfolio = new stdClass();
        $portfolioTitle = "";
        $existantAlbum = true;
        $albums = [];
        $userType = $this->userRepository->getUserType($email);
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        // Entra al if si li l'usuari li ha donat al botó de crear portfolio.
        if($portfolioId == NULL) {
            $title = $data['title'];
            
            $userId = $this->userRepository->getUserId($email);
            //$portfolio = new Portfolio($title, $userId);
            $portfolio = new Portfolio($title, $userId);

            $this->userRepository->createPortfolio($portfolio);
            
            $portfolioTitle = $title;
            $existantAlbum = false;
        }
        // Entra aqui si l'usuari li ha donat al botó de crear àlbum.
        else {
            $portfolioTitle = $this->userRepository->getPortfolioTitle($portfolioId);
            $albumName = $data['albumName'];
            if ($this->userRepository->getUserMoney($email) >= 2) {
                $album = new Album($albumName, $portfolioId);
                $this->userRepository->createAlbum($album);
                $this->userRepository->setUserMoney($email, $this->userRepository->getUserMoney($email) - 2);

                $albums = $this->userRepository->getAlbums($portfolioId);
            } else {
                //TODO: Enviar flash message indicant que no té 2€ per comprar un album.
                return $response->withHeader('Location', $routeParser->urlFor("wallet"))->withStatus(200);
            }
            
            
        }

        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                'existantPortfolio' => $existantPortfolio,
                'existantAlbum' => $existantAlbum,
                'userType' => $userType,
                'portfolioTitle' => $portfolioTitle,
                'albums' => $albums,
                'formAction' => $routeParser->urlFor('portfolio'),
                'loggedIn' => $loggedIn,
                'userWeb' => "portfolio",
                'formData'  => $user
            ]
        );
    }
    
    public function showPictures(Request $request, Response $response, $id): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    
        $pictures = [];
        
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
            $email = $_SESSION['email'];
            $loggedIn = $_SESSION['loggedin'];
            $user = $this->userRepository->getUserByEmail($_SESSION['email']);

            $albumsIds = [];
            $count = 0;

            $albumId = intval(implode($id));
            $albumsIds = $this->userRepository->getAlbumsIds();

            // Si a la ruta posen un id d'un album que no existeix doncs el redirigim a /portfolio
            if(!empty($albumsIds)) {
                foreach ($albumsIds as $aidi){
                    if((intval($aidi['id'])) == $albumId) {
                         $count = $count + 1;
                    }
                 }

                 if ($count == 0) {
                    // TODO: Enviar missatge flash indicant que aquell album no existeix.
                    return $response->withHeader('Location', $routeParser->urlFor("portfolio"))->withStatus(200);
                 }
            }

            $userType = $this->userRepository->getUserType($email);
            
            // query per comprovar si hi ha fotos.
            $picturesIds = $this->userRepository->checkExistantAlbumPictures($albumId);
            if(!empty($picturesIds)) {
                $pictures = $this->userRepository->getAlbumPictures($albumId);
            } 
            
        } else {
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))->withStatus(200);
        }

        return $this->twig->render(
            $response,
            'album.twig',
            [   
                'pictures' => $pictures,
                'userType' => $userType,
                'loggedIn' => $loggedIn,
                'formData'  => $user,
                'userWeb' => "portfolio",

            ]
        );
    }

    public function albumAction(Request $request, Response $response, $id): Response {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $user = $this->userRepository->getUserByEmail($_SESSION['email']);

        $loggedIn = $_SESSION['loggedin'];
        //$email = $_SESSION['email'];
        $picture = new stdClass();
        $picturesIds = [];
        $albumId = intval(implode($id));
        $qrName = "";
        $userType = $this->userRepository->getUserType($_SESSION['email']);

        if(isset($_POST['upload'])) {
            $pictureUrl = $data['photo-url'];
            if($pictureUrl != "") {

                $picture = new Picture($pictureUrl, $albumId);
                $this->userRepository->uploadPhoto($picture);  
            }
            //$picturesSrc = $this->convertUrl($id);
        } else if (isset($_POST['delete-photo'])) {
            if(isset($data['picture'])) {
                $picturesIds = $data['picture'];
                foreach ($picturesIds as $pictureId) {
                    $this->userRepository->deletePicture(intval($pictureId));
                }
            }
            
        } else if (isset($_POST['delete-album'])) {
            $this->userRepository->deleteAlbumAndPictures($albumId);

            return $response->withHeader('Location', $routeParser->urlFor('portfolio'))->withStatus(302);

        } else if (isset($_POST['share-album'])){
            $qrName = "qr".$id['id'].'.png';
            $this->createQR("http://localhost:8030/portfolio/album/".$id['id'],$qrName);

        }

        $pictures = $this->userRepository->getAlbumPictures($albumId);

        return $this->twig->render(
            $response,
            'album.twig',
            [   
                'formAction' => $routeParser->urlFor('album-pictures', $id),
                'pictures' => $pictures,
                'userType' => $userType,
                'loggedIn' => $loggedIn,
                'qr' => $qrName,
                'formData'  => $user,
                'userWeb' => "portfolio",

            ]
        );
        
        
    }

    public function createQR($code, $name){
        
        $data = array(
            'symbology' => 'QRCode',
            'code' => $code
        );
        
        $options = array(
            'http' => array(
            'method'  => 'POST',
            'content' => json_encode( $data ),
            'header' =>  "Content-Type: application/json\r\n" .
                        "Accept: image/png\r\n"
            )
        );
        
        $context  = stream_context_create( $options );
        $url = 'http://barcode/BarcodeGenerator';
        $response = file_get_contents( $url, false, $context );
        file_put_contents($name, $response);
    }
    /*
    public function convertUrl($id) {
        $picturesSrc = [];

        $albumId = intval(implode($id));
        $pictures = $this->userRepository->getAlbumPictures($albumId);
            
        foreach ($pictures as $url) {
            $url_info['extension'] = "";
            $url_aux = implode($url);
            //echo($url_aux);
            $img = file_get_contents($url_aux);
            $url_info = pathinfo($url_aux);
            $src = 'data:image/'. $url_info['extension'] .';base64,'. base64_encode($img);
            array_push($picturesSrc, $src);
        }

        return $picturesSrc;
    }
    

    public function deleteAlbum(Request $request, Response $response, $id): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $loggedIn = $_SESSION['loggedin'];


        //if $id no existeix a bbdd, redirect de locos
        echo("TOOOOOOOOOP");
        $albumId = intval(implode($id));
        $this->userRepository->deleteAlbumAndPictures($albumId);

        return $response->withHeader('Location', $routeParser->urlFor('portfolio'))->withStatus(302);
    }
    */

}