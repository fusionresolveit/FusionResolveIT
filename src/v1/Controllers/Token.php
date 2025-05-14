<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use DateTime;
use Firebase\JWT\JWT;
use JimTools\JwtAuth\Exceptions\ExpiredException;
use Tuupola\Base62;
use Firebase\JWT\ExpiredException as JwtExpiredException;

final class Token
{
   /**
    * Check is a password match the stored hash
    *
    * @param string|null $password Password (pain-text)
    * @param string|null $hash Hash
    *
    * @return boolean
    */
  public static function checkPassword($password, $hash)
  {
    if (is_null($password) || is_null($hash))
    {
      return false;
    }
    if (!strstr($hash, '.'))
    {
      return false;
    }

    $spl = explode('.', $hash);
    if (count($spl) !== 2 || empty($spl[0]) || empty($spl[1]))
    {
      return false;
    }
    $bin = hex2bin($spl[0]);
    if ($bin === false)
    {
      return false;
    }
    $hashpassword = self::hashPasword($password, $bin);

    if ($hashpassword === $spl[1])
    {
      return true;
    }
    return false;
  }

  public static function generateSalt(): string
  {
    $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
    return $salt;
  }

  /**
   * recommandations of OWASP: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html
   *
   * @param string $password
   * @param string $salt
   */
  public static function hashPasword($password, $salt): string
  {
    // Using bin2hex to keep output readable
    return bin2hex(
      sodium_crypto_pwhash(
        32,
        $password,
        $salt,
        SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
        SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
        SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
      )
    );
  }

  /**
   * @param string $password
   */
  public static function generateDBHashPassword($password): string
  {
    $salt = self::generateSalt();
    $hash = self::hashPasword($password, $salt);
    return bin2hex($salt) . '.' . $hash;
  }

  /**
   * @param \App\Models\User  $user
   * @param int|null $profileId
   * @param int|null $entityId
   * @param boolean $entityRecursive
   *
   * @return array{token: string, refreshtoken: string, expires: int}
   */
  public function generateJWTToken(
    \App\Models\User $user,
    $profileId = null,
    $entityId = null,
    $entityRecursive = false
  ): array
  {
    global $basePath;

    $firstName = $user->firstname;
    $lastName = $user->lastname;

    // Generate a new refreshtoken and save in DB
    $refreshtoken = $user->refreshtoken;
    if (is_null($refreshtoken))
    {
      $refreshtoken = $this->generateToken();
      $user->refreshtoken = $refreshtoken;
      $user->save();
    }
    setcookie('refresh-token', $refreshtoken, 0, $basePath . '/view', '', true, true);

    $jti = $this->generateToken();

    if (!is_null($entityId))
    {
      $entity = \App\Models\Entity::where('id', $entityId)->exists();
      if ($entity === false)
      {
        $entityId = null;
      }
    }

    if (is_null($profileId))
    {
      foreach ($user->profiles()->get() as $profile)
      {
        $profileId = $profile->id;
        $entityId = $profile->getRelationValue('pivot')->entity_id;
        $entityRecursive = $profile->getRelationValue('pivot')->is_recursive;
        break;
      }
    } else {
      $profile = \App\Models\Profile::where('id', $profileId)->exists();
      if ($profile === false)
      {
        $profileId = null;
      }
    }

    if (is_null($profileId) || is_null($entityId))
    {
      throw new \Exception('Unauthorized access', 401);
    }

    $entity = \App\Models\Entity::where('id', $entityId)->first();
    if (is_null($entity))
    {
      throw new \Exception('Wrong request', 400);
    }

    $now = new DateTime();
    $future = new DateTime("+2 minutes");

    $payload = [
      'iat'              => $now->getTimeStamp(),
      'exp'              => $future->getTimeStamp(),
      'jti'              => $jti,
      'sub'              => '',
      'scope'            => $this->getScope($user->id),
      'user_id'          => $user->id,
      // 'role_id'          => $role->id,
      'firstname'        => $firstName,
      'lastname'         => $lastName,
      'apiversion'       => "v1",
      // 'entities_id'      => $user->entities_id,
      'sub_organization' => true,
      'profile_id'       => $profileId,
      'entity_id'        => $entityId,
      'entity_treepath'  => $entity->treepath,
      'entity_recursive' => $entityRecursive,
    ];
    // $configSecret = include(__DIR__ . '/../../../config/current/config.php');
    $secret = sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL);
    $token = JWT::encode($payload, $secret, "HS256");
    $responseData = [
      "token"        => $token,
      "refreshtoken" => $refreshtoken,
      "expires"      => $future->getTimeStamp()
    ];
    return $responseData;
  }

  /**
   * get rights of this user.
   *
   * @param int $userId
   *
   * @return array<string>
   */
  private function getScope(int $userId): array
  {
    $scope = [
    ];

    return $scope;
  }

  private function generateToken(): string
  {
    return (new Base62())->encode(random_bytes(16));
  }

  /**
   * @return array<mixed>
   */
  public static function manageExpiredToken(JwtExpiredException $e): array
  {
    global $basePath;
    if (isset($_COOKIE['refresh-token']))
    {
      $payload = $e->getPayload();
      if (property_exists($payload, 'user_id'))
      {
        $userId = $payload->user_id;
        $user = \App\Models\User::where('id', $userId)->first();
        if (!is_null($user))
        {
          if ($user->refreshtoken === $_COOKIE['refresh-token'])
          {
            $token = new \App\v1\Controllers\Token();
            $jwt = $token->generateJWTToken($user);
            setcookie('token', $jwt['token'], 0, $basePath . '/view');
            return (array) $payload;
          }
        }
      }
    }
    throw new ExpiredException($e->getMessage(), 0, $e);
  }
}
