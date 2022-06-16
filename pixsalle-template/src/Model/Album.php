<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

class Album {

  private int $id;
  private string $name;
  private int $portfolioId;
  
  public function __construct(
    string $name,
    int $portfolioId
  ) {
    $this->name = $name;
    $this->portfolioId = $portfolioId;
  }

  public function id()
  {
    return $this->id;
  }

  public function name()
  {
    return $this->name;
  }

  public function portfolioId()
  {
    return $this->portfolioId;
  }

}