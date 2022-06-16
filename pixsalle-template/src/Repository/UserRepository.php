<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Picture;
use Salle\PixSalle\Model\Portfolio;
use Salle\PixSalle\Model\User;

interface UserRepository {
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);
    public function getUserId(string $email);
    public function createMembership(int $userId, string $type): void;
    public function getUserType(string $email);
    public function setUserType(string $email,string $type): void;
    public function updateUser(User $user):void;
    public function updateUserName(string $email, string $userName): void;
    public function getAllPictures();
    public function getPictureId(string $url);
    public function updatePassword(string $password): void;
    public function checkExistantPortfolio(string $email);
    public function createPortfolio(Portfolio $portfolio): void;
    public function getPortfolioTitle(int $portfolioId);
    public function checkExistantAlbum(int $portfolioId);
    public function createAlbum(Album $album): void;
    public function getAlbums(int $portfolioId);
    public function getAlbumPictures(int $albumId);
    public function checkExistantAlbumPictures(int $albumId);
    public function uploadPhoto(Picture $picture);
    public function deleteAlbumAndPictures(int $albumId);
    public function getAlbumsIds();
    public function deletePicture(int $pictureId);
    public function setUserMoney(string $email, int $money): void;
    public function getUserMoney(string $email);
    public function selectUserPicture(string $email);
    
}
