<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DateTime;
use Firebase\JWT\JWT;
use Tuupola\Base62;

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
    $hashpassword = self::hashPasword($password, hex2bin($spl[0]));

    if ($hashpassword === $spl[1])
    {
      return true;
    }
    return false;
  }

  public static function generateSalt()
  {
    $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
    return $salt;
  }

  // recommandations of OWASP: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html
  public static function hashPasword($password, $salt)
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

  public static function generateDBHashPassword($password)
  {
    $salt = self::generateSalt();
    $hash = self::hashPasword($password, $salt);
    return bin2hex($salt) . '.' . $hash;
  }

  public function generateJWTToken(
    \App\Models\User $user,
    Response $response,
    $profile_id = null,
    $entity_id = null,
    $entity_recursive = false
  )
  {
    global $basePath;

    $firstName = $user->firstname;
    $lastName = $user->lastname;
    // $jwtid = $user->getPropertyAttribute('userjwtid');
    $jwtid = null;
    // $jwtidId = $user->getPropertyAttribute('userjwtid', 'id');
    // $refreshtokenPropId = $user->getPropertyAttribute('userrefreshtoken', 'id');
    // if (is_null($jwtidId) || is_null($refreshtokenPropId))
    // {
    //   throw new \Exception('The database is corrupted', 500);
    // }

    // Generate a new refreshtoken and save in DB
    $refreshtoken = $this->generateToken();
    // $user->properties()->updateExistingPivot($refreshtokenPropId, ['value_string' => $refreshtoken]);

    // the jwtid (jit), used to revoke the JWT by server (for example when change rights, disable user...)
    // if (is_null($jwtid))
    // {
      $jti = $this->generateToken();
      // $user->properties()->updateExistingPivot($jwtidId, ['value_string' => $jti]);
    // }
    // else
    // {
      // $jti = $jwtid;
    // }

    if (is_null($profile_id))
    {
      foreach ($user->profiles()->get() as $profile)
      {
        $profile_id = $profile->id;
        $entity_id = $profile->getRelationValue('pivot')->entity_id;
        $entity_recursive = $profile->getRelationValue('pivot')->is_recursive;
        break;
      }
    }
    $entity = \App\Models\Entity::find($entity_id);

    if (is_null($profile_id) || is_null($entity_id))
    {
      return $response
        ->withHeader('Location', $basePath);
    }
    $now = new DateTime();
    $future = new DateTime("+2000 minutes");
    // For test / DEBUG
    // $future = new DateTime("+30 seconds");
    // Get roles
    // $role = $user->roles()->first();

    // if (is_null($role))
    // {
    //   throw new \Exception('No role assigned to the user', 401);
    // }

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
      'profile_id'       => $profile_id,
      'entity_id'        => $entity_id,
      'entity_treepath'  => $entity->treepath,
      'entity_recursive' => $entity_recursive,
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

  // get rights of this user.
  private function getScope($userId)
  {
    $scope = [
    ];

    return $scope;
  }

  private function generateToken()
  {
     return (new Base62())->encode(random_bytes(16));
  }
}
