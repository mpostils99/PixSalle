<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private string $userName;
  private string $email;
  private string $password;
  private string $phone;
  private string $pictureName;
  private int $money;
  private DateTime $createdAt;
  private DateTime $updatedAt;

  public function __construct(
    string $userName,
    string $email,
    string $password,
    string $phone,
    string $pictureName,
    int $money,
    DateTime $createdAt,
    DateTime $updatedAt
  ) {
    $this->userName = $userName;
    $this->email = $email;
    $this->password = $password;
    $this->phone = $phone;
    $this->pictureName = $pictureName;
    $this->money = $money;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function id()
  {
    return $this->id;
  }
  public function userName()
  {
    return $this->userName;
  }
  public function email()
  {
    return $this->email;
  }

  public function password()
  {
    return $this->password;
  }

  public function phone()
  {
    return $this->phone;
  }

  public function pictureName()
  {
    return $this->pictureName;
  }
  public function money()
  {
    return $this->money;
  }
  public function setUserName($userName){
    $this->userName = $userName;
 }
 public function setPhone($phone){
  $this->phone = $phone;
}
 public function setUpdatedAt($updatedAt){
  $this->updatedAt = $updatedAt;
}
  public function createdAt(): Datetime
  {
    return $this->createdAt;
  }

  public function updatedAt(): Datetime
  {
    return $this->updatedAt;
  }
}
