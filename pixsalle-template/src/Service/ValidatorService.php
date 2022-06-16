<?php

declare(strict_types=1);

namespace Salle\PixSalle\Service;

class ValidatorService
{
  private const ALLOWED_EXTENSIONS = ['jpg', 'png'];

  public function __construct()
  {
  }

  public function validateEmail(string $email)
  {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'The email address is not valid';
    } else if (!strpos($email, "@salle.url.edu")) {
      return 'Only emails from the domain @salle.url.edu are accepted.';
    }
    return '';
  }

  public function validateMoney(int $money, int $currentMoney)
  {
    if (empty($money) || $money <= 0) {
      return 'Enter a valid amount of money';
    } else if ($money > 1000) {
      return 'You can not add more than 1000';
    }else if (($currentMoney + $money)>10000){
      return 'You have reached the maximum amount permitted (10K)';
    }
    return '';
  }

  public function validatePassword(string $password)
  {
    if (empty($password) || strlen($password) < 6) {
      return 'The password must contain at least 6 characters.';
    } else if (!preg_match("~[0-9]+~", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password)) {
      return 'The password must contain both upper and lower case letters and numbers';
    }
    return '';
  }
  public function validatePhone(string $phone)
  {
    if ($phone != "" && (!preg_match("/(6)[ -]*([0-9][ -]*){8}$/", $phone))) {
      return 'The phone has the wrong format';
    }
    return '';
  }
  public function validateUserName(string $userName)
  {
     if (!preg_match("~[0-9]+~", $userName) || !preg_match("/[a-z]/", $userName)) {
      return 'The username is not valid and must be in aplhanumeric format.';
    }
    return '';
  }
  public function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}

