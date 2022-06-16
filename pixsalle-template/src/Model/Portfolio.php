<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Portfolio {

  private int $id;
  private string $title;
  private int $userId;
  
  public function __construct(
    string $title,
    int $userId
  ) {
    $this->title = $title;
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

  public function userId()
  {
    return $this->userId;
  }

}