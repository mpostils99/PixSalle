<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\LandingPageController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\SignInController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Controller\ApiBlogController;
use Salle\PixSalle\Controller\BlogController;

use Slim\App;

function addRoutes(App $app): void {
    $app->get('/', LandingPageController::class . ':showLandingPage')->setName('landing-page');
    $app->get('/sign-in', SignInController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', SignInController::class . ':signInAction');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUpAction');
    $app->get('/user/membership', MembershipController::class . ':showMembershipForm')->setName('membership');
    $app->post('/user/membership', MembershipController::class . ':changePlan');
    $app->get('/explore', ExploreController::class . ':showPictures')->setName('explore');
    $app->get('/profile', ProfileController::class . ':showProfileForm')->setName('profile');
    $app->post('/profile', ProfileController::class . ':modifyProfile');
    $app->get('/profile/changePassword', ProfileController::class . ':showChangePasswordForm')->setName('change-password');
    $app->post('/profile/changePassword', ProfileController::class . ':changePasswordAction');
    $app->get('/portfolio', PortfolioController::class . ':showPortfolio')->setName('portfolio');
    $app->post('/portfolio', PortfolioController::class . ':managePortfolioAction');
    $app->get('/portfolio/album/{id}', PortfolioController::class . ':showPictures')->setName('album-pictures');
    $app->post('/portfolio/album/{id}', PortfolioController::class . ':albumAction');
    //$app->delete('/portfolio/album/{id}', PortfolioController::class . ':deleteAlbum');
    $app->delete('/portfolio/album/{id}', PortfolioController::class . ':albumAction');
    $app->get('/user/wallet', WalletController::class . ':showWalletForm')->setName('wallet');
    $app->post('/user/wallet', WalletController::class . ':addMoney');

    $app->get('/api/blog', ApiBlogController::class . ':showPosts')->setName('Post');
    $app->get('/api/blog/{id}', ApiBlogController::class . ':showSinglePost')->setName('singlePost');
    $app->post('/api/blog', ApiBlogController::class . ':createPost');
    $app->delete('/api/blog/{id}', ApiBlogController::class . ':deletePost');
    $app->put('/api/blog/{id}', ApiBlogController::class . ':modifyPost');

    $app->get('/blog', BlogController::class . ':showPosts')->setName('blog');
    $app->get('/blog/{id}', BlogController::class . ':showSinglePost')->setName('aBlog');


}
