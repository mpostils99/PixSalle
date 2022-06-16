<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Picture {

  private int $id;
  private string $url;
  private int $albumId;
  
  public function __construct(
    string $url,
    int $albumId
  ) {
    $this->url = $url;
    $this->albumId = $albumId;
  }

  public function id()
  {
    return $this->id;
  }

  public function url()
  {
    return $this->url;
  }

  public function albumId()
  {
    return $this->albumId;
  }

}