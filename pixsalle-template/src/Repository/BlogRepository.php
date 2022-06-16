<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Blog;


interface BlogRepository {
    public function getBlogs();
    public function getAblog(int $id);
    public function createBlog(string $title,string $content,int $userId);
    public function deleteBlog(int $id);
    public function modifyBlog(string $title,string $content,int $postId);


}
