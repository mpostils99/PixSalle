<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Blog {

  private  int $id;
  private string $title;
  private string $content;
  private int $userId;
  
  public function __construct(
   int $id,
   string $title,
   string $content,
   int $userId;
  ) {
    $this->id = $id;
    $this->title = $title;
    $this->content = $content;
    $this->userId = $userId;
  }

  public function id()
  {
    return $this->id;
  }

  public function title()
  {
    return $this->title;
  }

  public function content()
  {
    return $this->content;
  }
  public function userId()
  {
    return $this->userId;
  }
}