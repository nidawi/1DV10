<?php

namespace Login\model;

class TemporaryPassword {

  const TEMPORARY_PASSWORD_LIFESPAN = (60 * 60 * 24 * 3);

  private $password;
  private $createdAt;

  public function __construct(string $password, int $createdAt) {
    $this->password = $password;
    $this->createdAt = $createdAt;
  }

  public static function generateTemporaryPassword() : string {
    return md5(microtime());
  }

  public function getPassword() : string {
    return $this->password;
  }

  public function isPasswordMatch(string $passwordToCompare) : bool {
    return hash_equals($this->password, $passwordToCompare);
  }

  public function assertValidity() {
    $expirationTime = $this->createdAt + self::TEMPORARY_PASSWORD_LIFESPAN;

    if(time() - $expirationTime > 0)
      throw new TemporaryPasswordExpiredException();
  }
}