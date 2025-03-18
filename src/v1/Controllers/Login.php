<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Illuminate\Support\Facades\Date;

final class Login extends Common
{
  /**
   * @param array<string, string> $args
   */
  public function getLogin(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $view = Twig::fromRequest($request);

    $viewData = [
      'title'     => 'Fusion Resolve IT - ' . 'Login page',
      'rootpath'  => \App\v1\Controllers\Toolbox::getRootPath($request),
      'basePath'  => $basePath,
      'sso'       => [],
    ];

    $authsso = \App\Models\Authsso::where('is_active', true)->get();
    foreach ($authsso as $sso)
    {
      $viewData['sso'][] = [
        'id'    => $sso->callbackid,
        'name'  => $sso->name,
      ];
    }

    return $view->render($response, 'login.html.twig', $viewData);
  }

  /**
   * @param array<string, string> $args
   */
  public function postLogin(Request $request, Response $response, array $args): Response
  {
    $data = (object) $request->getParsedBody();
    $token = new \App\v1\Controllers\Token();

    // Validate data
    if (!property_exists($data, 'login') || !property_exists($data, 'password'))
    {
      throw new \Exception('Login error', 401);
    }

    if (gettype($data->login) != 'string' || gettype($data->password) != 'string')
    {
      throw new \Exception('Login error', 401);
    }

    // check if account exists in local
    $user = \App\Models\User::
        where('name', $data->login)
      ->where('is_active', true)
      ->where('auth_id', 0)
      ->first();
    if (!is_null($user))
    {
      $this->checkAttempt($user);

      // check passwords
      $check = $token->checkPassword($data->password, $user->password);
      if (!$check)
      {
        \App\v1\Controllers\Audit::addEntry(
          $request,
          'CONNECTION',
          'fail, login: ' . $data->login,
          null,
          0,
          401,
          'FAIL'
        );
      }
      $this->setAttempt($user, $check);
      \App\v1\Controllers\Audit::addEntry(
        $request,
        'CONNECTION',
        'successfull, login: ' . $data->login,
        null,
        0,
        401,
        'SUCCESSFULL'
      );

      return $this->authOkAndRedirect($user, $response);
    } else {
      // Search in LDAP
      $users = \App\Models\User::
          where('name', $data->login)
      ->where('is_active', true)
      ->where('auth_id', '>', 0)
      ->get();
      foreach ($users as $user)
      {
        if (is_null($user->user_dn))
        {
          continue;
        }
        $authRet = \App\v1\Controllers\Authldap::tryAuth($user->auth_id, $user->user_dn, $data->password);
        if ($authRet)
        {
          return $this->authOkAndRedirect($user, $response);
        }
      }
    }

    // Not found in database, so now try to find into ldaps
    $ldaps = \App\Models\Authldap::where('is_active', true)->get();
    foreach ($ldaps as $ldap)
    {
      $foundDN = \App\v1\Controllers\Authldap::importUsers($ldap, $data->login);
      if ($foundDN != false)
      {
        // Create user
        $user = new \App\Models\User();
        $user->name = $data->login;
        $user->is_active = true;
        $user->auth_id = $ldap->id;
        $user->user_dn = $foundDN;
        $user->save();
        return $this->authOkAndRedirect($user, $response);
      }
    }
    throw new \Exception('Login or password error, first attempt, wait 30 seconds before try again', 401);
  }

  private function authOkAndRedirect(\App\Models\User $user, Response $response): Response
  {
    global $basePath;

    $token = new \App\v1\Controllers\Token();

    // generate token
    // put into cookie, key token

    $jwt = $token->generateJWTToken($user, $response);
    if (gettype($jwt) != 'array')
    {
      return $jwt;
    }

    // Set Cookie
    // $cookie_lifetime = empty($cookie_value) ? time() - 3600 : time() + $CFG_GLPI['login_remember_time'];
    // $cookie_path     = ini_get('session.cookie_path');
    // $cookie_domain   = ini_get('session.cookie_domain');
    // $cookie_secure   = (bool)ini_get('session.cookie_secure');

    setcookie('token', $jwt['token'], 0, $basePath . '/view');
    //, $cookie_lifetime, $cookie_path, $cookie_domain, $cookie_secure, true);

    return $response
      ->withHeader('Location', $basePath . '/view/home')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function doSSO(Request $request, Response $response, array $args): Response
  {
    $provider = $this->prepareSSOService($request, $args);

    try
    {
      return $response
        ->withHeader('Location', $provider->makeAuthUrl())
        ->withStatus(302);
    }
    catch (\Exception $e)
    {
      echo $e->getMessage();
    }
    exit;
  }

  /**
   * @param array<string, string> $args
   */
  public function callbackSSO(Request $request, Response $response, array $args): void
  {
    $provider = $this->prepareSSOService($request, $args);
    $accessToken = $provider->getAccessTokenByRequestParameters($_GET);
    $ssoUser = $provider->getIdentity($accessToken);

    $user = \App\Models\User::firstOrCreate(['name' => $ssoUser->email]);

    $this->authOkAndRedirect($user, $response);
  }

  /**
   * @param array<string, string> $args
   */
  private function prepareSSOService(Request $request, array $args): \SocialConnect\Provider\AbstractBaseProvider
  {
    global $basePath;

    $uri = $request->getUri();

    $callbackid = $args['callbackid'];

    $authsso = \App\Models\Authsso::where('callbackid', $callbackid)->where('is_active', true)->first();
    if (is_null($authsso))
    {
      echo 'error';
      exit;
    }
    $providers = \App\Models\Definitions\Authsso::getProviderArray();
    $dataProvider = [];
    if (is_null($authsso->provider) || !isset($providers[$authsso->provider]))
    {
      echo "error";
      exit;
    }
    $item = $providers[$authsso->provider];
    foreach ($item['fields'] as $field)
    {
      if ($field == 'scope')
      {
        $dataProvider['scope'] = [];
        $scopes = \App\Models\Authssoscope::where('authsso_id', $authsso->id)->get();
        foreach ($scopes as $scope)
        {
          $dataProvider['scope'][] = $scope->name;
        }
      }
      elseif ($field == 'options')
      {
        $dataProvider['options'] = [];
        $options = \App\Models\Authssooption::where('authsso_id', $authsso->id)->get();
        if (isset($item['suboption']))
        {
          $dataProvider['options'][$item['suboption']] = [];
          foreach ($options as $option)
          {
            if (is_null($option->key))
            {
              $dataProvider['options'][$item['suboption']][] = $option->value;
            }
            else
            {
              $dataProvider['options'][$item['suboption']][$option->key] = $option->value;
            }
          }
        }
        else
        {
          $dataProvider['options'] = [];
          foreach ($options as $option)
          {
            if (is_null($option->key))
            {
              $dataProvider['options'][] = $option->value;
            }
            else
            {
              $dataProvider['options'][$option->key] = $option->value;
            }
          }
        }
      }
      else
      {
        $dataProvider[$field] = $authsso->{strtolower($field)};
      }
    }

    $configureProviders = [
      'redirectUri' => $uri->getScheme() . '://' . $uri->getHost() . $basePath . '/view/login/sso/' .
        $callbackid . '/cb',
      'provider' => [
        $authsso->provider => $dataProvider,
      ],
    ];
    return \App\v1\Controllers\Authsso::getProviderInstance($authsso->provider, $configureProviders);
  }

  /**
   * @param array<string, string> $args
   */
  public function logout(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    setcookie('token', '', -1, $basePath . '/view');

    return $response
      ->withHeader('Location', $basePath . '/')
      ->withStatus(302);
  }

  /**
   * @param array<string, string> $args
   */
  public function changeProfileEntity(Request $request, Response $response, array $args): Response
  {
    global $basePath;

    $data = new \App\DataInterface\PostLoginChangeProfileEntity((object) $request->getParsedBody());
    $token = new \App\v1\Controllers\Token();

    $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
    if (is_null($user))
    {
      throw new \Exception('Id not found', 404);
    }

    // Check if the entity is associated to this profile
    // check if the recursive is associated to the entity and profile
    $validation = false;
    foreach ($user->profiles()->get() as $profile)
    {
      if ($profile->id == $data->profileId)
      {
        if ($profile->getRelationValue('pivot')->entity_id == $data->entityId)
        {
          $validation = true;
          if ($data->recursive == true && !$profile->getRelationValue('pivot')->is_recursive)
          {
            $data->recursive = false;
          }
          break;
        }
        elseif ($profile->getRelationValue('pivot')->is_recursive)
        {
          // search if $data->changeEntity is in sub
          $profileEntity = \App\Models\Entity::where('id', $profile->getRelationValue('pivot')->entity_id)->first();
          if (is_null($profileEntity))
          {
            continue;
          }
          $entity = \App\Models\Entity::
              where('id', $data->entityId)
            ->where('treepath', 'LIKE', $profileEntity->treepath . '%')
            ->first();
          if (!is_null($entity))
          {
            $validation = true;
          }
        }
      }
    }

    if ($validation)
    {
      $jwt = $token->generateJWTToken(
        $user,
        $response,
        $data->profileId,
        $data->entityId,
        $data->recursive
      );
      if (gettype($jwt) != 'array')
      {
        return $jwt;
      }

      setcookie('token', $jwt['token'], 0, $basePath . '/view');
    }

    return $response
      ->withHeader('Location', $_SERVER['HTTP_REFERER'])
      ->withStatus(302);
  }

  private function checkAttempt(\App\Models\User $user)
  {
    if ($user->security_locked)
    {
      throw new \Exception('This account is security locked, contact your app administrator', 401);
    }
    if ($user->security_attempt > 0)
    {
      $timeElapsed = (Date::now()->timestamp) - strtotime($user->security_last_attempt);
      switch ($user->security_attempt) {
        case 1:
          if ($timeElapsed < 30)
          {
            throw new \Exception('Security, wait ' . (30 - $timeElapsed) . ' seconds before try again', 401);
          }
            return;
  
        case 2:
          if ($timeElapsed < 120)
          {
            throw new \Exception('Security, wait ' . (120 - $timeElapsed) . ' seconds before try again', 401);
          }
            return;
  
        case 3:
          if ($timeElapsed < 300)
          {
            throw new \Exception('Security, wait ' . (300 - $timeElapsed) . ' seconds before try again', 401);
          }
            return;

        case 4:
          if ($timeElapsed < 600)
          {
            throw new \Exception('Security, wait ' . (600 - $timeElapsed) . ' seconds before try again', 401);
          }
            return;
      }
    }
  }

  private function setAttempt(\App\Models\User $user, bool $passwordCheck)
  {
    if ($passwordCheck)
    {
      $user->security_attempt = 0;
      $user->save();
      return;
    }

    // we are here because the auth was fail (not right password)
    $user->security_last_attempt = Date::now();
    switch ($user->security_attempt) {
      case 0:
        $user->security_attempt = 1;
        $user->save();
        throw new \Exception('Login or password error, first attempt, wait 30 seconds before try again', 401);

      case 1:
        $user->security_attempt = 2;
        $user->save();
        throw new \Exception('Login or password error, second attempt, wait 2 minutes before try again', 401);

      case 2:
        $user->security_attempt = 3;
        $user->save();
        throw new \Exception('Login or password error, third attempt, wait 5 minutes before try again', 401);

      case 3:
        $user->security_attempt = 4;
        $user->save();
        throw new \Exception('Login or password error, fourth attempt, wait 10 minutes before try again', 401);

      case 4:
        $user->security_attempt = 5;
        $user->security_locked = true;
        $user->save();
        throw new \Exception('Login or password error, the account is locked', 401);
    }
  }
}
