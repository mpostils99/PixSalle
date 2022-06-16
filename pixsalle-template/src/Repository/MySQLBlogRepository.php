<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use PDO;
use DateTime;
use Salle\PixSalle\Model\Blog;

final class MySQLBlogRepository implements BlogRepository {
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function getBlogs(){
        $query = "SELECT * FROM posts";
        $statement = $this->databaseConnection->prepare($query);
        $statement->execute();
        $blogs = $statement->fetchAll();

        return $blogs;
    }

    public function getAblog(int $id){
        $query = "SELECT * FROM posts where id=:id";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $blog = $statement->fetchAll();

        return $blog;
    }
    public function createBlog(string $title,string $content,int $userId){
        $query = "INSERT INTO posts(title, content, user_id) VALUES
        (:title, :content, :user_id)";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        
        $query = "SELECT * FROM posts WHERE title = :title 
        AND content = :content AND user_id = :user_id";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':user_id', $user_id);

        $statement->execute();
        $result = $statement->fetchAll();


        return $result;
    }

    public function deleteBlog(int $id){

        $query = "SELECT COUNT(*) WHERE id=:id";
        $statement = $dbConn->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $result = $statement->fetchAll();
        
        if ($result == 0){
            $detelted == false;
        }else{
            $detelted == true;
        }

        $query = "DELETE FROM posts where id=:id";
        $statement = $dbConn->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $deleted;
    }

    public function modifyBlog(string $title,string $content,int $id){

        $query = "SELECT COUNT(*) WHERE id=:id";
        $statement = $dbConn->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $result = $statement->fetchAll();
        
        if ($result == 0){
            return false;
        }

        $query = "UPDATE posts SET (title = :title, content = :content) WHERE id = :postId";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':content', $content);
        $statement->bindValue(':postId', $id);
        $statement->execute();

        $query = "SELECT * FROM posts WHERE id = :id";
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindValue(':user_id', $id);

        $statement->execute();
        $result = $statement->fetchAll();

        return $result;
    }

}
