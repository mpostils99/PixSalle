<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\UserRepository;
use DateTime;
use Salle\PixSalle\Model\Album;
use Salle\PixSalle\Model\Picture;
use Salle\PixSalle\Model\Portfolio;

final class MySQLUserRepository implements UserRepository {
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createUser(User $user): void {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, createdAt, updatedAt)
        VALUES(:email, :password, :createdAt, :updatedAt)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getUserId(string $email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchColumn();

        return intval($result);
    }
 
    public function getPictureId(string $url) {
        $query = "SELECT pictureid FROM pictures WHERE url = :url";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('url', $url, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchColumn();

        return intval($result);
    }

    public function getUserByEmail(string $email) {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUserType(string $email) {
        $userId = $this->getUserId($email);
        $query = "SELECT type FROM userMembership WHERE user_id = :user_id";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $type = $statement->fetchColumn(0);

        return $type;
    }

    public function getUserMoney(string $email) {
        $userId = $this->getUserId($email);
        $query = "SELECT money FROM users WHERE id = :user_id";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $money = $statement->fetchColumn(0);

        return intval($money);
    }

    public function setUserMoney(string $email, int $money): void {
        $userId = $this->getUserId($email);
        $query = "UPDATE users SET money = :money WHERE id = :userId";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('money', $money, PDO::PARAM_STR);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function setUserType(string $email, string $type): void {
        $userId = $this->getUserId($email);
        $query = "UPDATE userMembership SET type = :type WHERE user_id = :userId";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('type', $type, PDO::PARAM_STR);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);
        $statement->execute();

    }

    public function createMembership(int $userId, string $type): void {
        $query = "INSERT INTO userMembership(user_id, type)
        VALUES(:userId, :type)";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->bindParam('type', $type);

        $statement->execute();
    }
    public function selectUserPicture(string $email){
        $query = "SELECT pictureName FROM users Where email = :email";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchColumn(0);
        return $result;
    }

    public function getAllPictures(){
        $query = "SELECT users.userName, pictures.url 
        FROM users, portfolio, album, pictures 
        WHERE users.id = portfolio.user_id
        AND portfolio.id = album.portfolio_id
        AND album.id = pictures.album_id";
        
        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();
        $pictures = $statement->fetchAll();

        return $pictures;
    }

    public function updatePassword(string $newPassword): void {
        $query = "UPDATE users SET password = :password";
        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('password', $newPassword, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateUser(User $user): void{
        $query = <<<'QUERY'
             UPDATE users SET updatedAt=:updatedAt ,userName = :userName , phone = :phone ,pictureName =:pictureName WHERE email = :email
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        
        $email = $user->email();
        $userName = $user->userName();
        $phone = $user->phone();
        $pictureName = $user->pictureName();
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('userName', $userName, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('pictureName', $pictureName, PDO::PARAM_STR);


        $statement->execute();
    }

    public function updateUserName(string $email, string $userName): void {
        $query = "UPDATE users SET userName = :userName WHERE email = :email";

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('userName', $userName, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
    }

    public function checkExistantPortfolio(string $email) {
        $userId = $this->getUserId($email);
        $query = "SELECT id 
        FROM portfolio
        WHERE user_id = :userId";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('userId', $userId,  PDO::PARAM_INT);
    
        $statement->execute();

        $result = $statement->fetchColumn(0);

        return intval($result);
    }

    public function createPortfolio(Portfolio $portfolio): void {
        $query = "INSERT INTO portfolio(title, user_id)
        VALUES(:title, :userId)";
        $statement = $this->databaseConnection->prepare($query);
        $title = $portfolio->title();
        $userId = $portfolio->userId();
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('userId', $userId, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getPortfolioTitle(int $portfolioId) {
        $query = "SELECT title 
        FROM portfolio
        WHERE id = :portfolio_id";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('portfolio_id', $portfolioId,  PDO::PARAM_INT);

        $statement->execute();

        $title = $statement->fetchColumn(0);

        return $title;
    }

    public function checkExistantAlbum(int $portfolioId) {
        $query = "SELECT id 
        FROM album
        WHERE portfolio_id = :portfolioId";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('portfolioId', $portfolioId,  PDO::PARAM_INT);
    
        $statement->execute();

        // Aixi obtenim tots els id dels albums (si hi han)
        $albumsIds = $statement->fetchAll();

        return $albumsIds;
    }

    public function createAlbum(Album $album): void {
        $query = "INSERT INTO album(name, portfolio_id)
        VALUES(:name, :portfolioId)";
        $statement = $this->databaseConnection->prepare($query);
        $name = $album->name();
        $portfolioId = $album->portfolioId();
        $statement->bindParam('name', $name, PDO::PARAM_STR);
        $statement->bindParam('portfolioId', $portfolioId, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getAlbums(int $portfolioId) {
        $query = "SELECT id, name
        FROM album
        WHERE portfolio_id = :portfolioId";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('portfolioId', $portfolioId, PDO::PARAM_INT);

        $statement->execute();

        $albums = $statement->fetchAll();

        return $albums;
    }

    public function getAlbumPictures(int $albumId) {
        $query = "SELECT id, url
        FROM pictures 
        WHERE album_id = :albumId";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('albumId', $albumId, PDO::PARAM_INT);

        $statement->execute();

        $pictures = $statement->fetchAll();

        return $pictures;
    }

    public function getAlbumsIds() {
        $query = "SELECT id
        FROM album";

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        $albumsIds = $statement->fetchAll();

        return $albumsIds;
    }

    public function checkExistantAlbumPictures(int $albumId) {
        $query = "SELECT id 
        FROM pictures
        WHERE album_id = :albumId";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('albumId', $albumId,  PDO::PARAM_INT);
    
        $statement->execute();

        // Aixi obtenim tots els id dels albums (si hi han)
        $picturesIds = $statement->fetchAll();

        return $picturesIds;
    }

    public function uploadPhoto(Picture $picture) {
        $query = "INSERT INTO pictures(url, album_id)
        VALUES(:url, :albumId)";
        $statement = $this->databaseConnection->prepare($query);
        
        $pictureUrl = $picture->url();
        $albumId = $picture->albumId();
        $statement->bindParam('url', $pictureUrl, PDO::PARAM_STR);
        $statement->bindParam('albumId', $albumId, PDO::PARAM_INT);

        $statement->execute();
    }

    public function deleteAlbumAndPictures(int $albumId) {
        $query = "DELETE FROM pictures
        WHERE album_id = :albumId";

        $statement = $this->databaseConnection->prepare($query);   
        
        $statement->bindParam('albumId', $albumId, PDO::PARAM_INT);

        $statement->execute();

        $query = "DELETE FROM album
        WHERE id = :albumId";

        $statement = $this->databaseConnection->prepare($query);   
        
        $statement->bindParam('albumId', $albumId, PDO::PARAM_INT);

        $statement->execute();
    }

    public function deletePicture(int $pictureId) {
        $query = "DELETE FROM pictures
        WHERE id = :pictureId";

        $statement = $this->databaseConnection->prepare($query);   
        
        $statement->bindParam('pictureId', $pictureId, PDO::PARAM_INT);

        $statement->execute();
    }
    public function deleteUserPicture(string $email) {
        $query = "UPDATE users SET pictureName = null WHERE email = :email";

        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
    }
}
