<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Salle\PixSalle\Controller\LandingPageController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\SignInController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\ApiBlogController;
use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Salle\PixSalle\Repository\MySQLBlogRepository;
use Salle\PixSalle\Repository\PDOConnectionBuilder;


use Slim\Views\Twig;

function addDependencies(ContainerInterface $container): void {
    $container->set(
        'view',
        function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        }
    );

    $container->set('db', function () {
        $connectionBuilder = new PDOConnectionBuilder();
        return $connectionBuilder->build(
            $_ENV['MYSQL_ROOT_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD'],
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE']
        );
    });

    $container->set('user_repository', function (ContainerInterface $container) {
        return new MySQLUserRepository($container->get('db'));
    });

    $container->set('blog_repository', function (ContainerInterface $container) {
        return new MySQLBlogRepository($container->get('db'));
    });

    $container->set(
        LandingPageController::class,
        function (ContainerInterface $c) {
            return new LandingPageController($c->get('view'),$c->get('user_repository'));
        }
    );

    $container->set(
        SignInController::class,
        function (ContainerInterface $c) {
            return new SignInController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        SignUpController::class,
        function (ContainerInterface $c) {
            return new SignUpController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        MembershipController::class,
        function (ContainerInterface $c) {
            return new MembershipController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        ExploreController::class,
        function (ContainerInterface $c) {
            return new ExploreController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        ProfileController::class,
        function (ContainerInterface $c) {
            return new ProfileController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        PortfolioController::class,
        function (ContainerInterface $c) {
            return new PortfolioController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        WalletController::class,
        function (ContainerInterface $c) {
            return new WalletController($c->get('view'), $c->get('user_repository'));
        }
    );

    
    $container->set(
        ApiBlogController::class,
        function (ContainerInterface $c) {
            return new ApiBlogController($c->get('view'), $c->get('blog_repository'));
        }
    );

    $container->set(
        BlogController::class,
        function (ContainerInterface $c) {
            return new BlogController($c->get('view'), $c->get('blog_repository'), $c->get('user_repository'));
        }
    );
}
