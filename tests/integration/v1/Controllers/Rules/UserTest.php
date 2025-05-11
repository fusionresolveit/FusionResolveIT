<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers\Rules;

use App\DataInterface\PostUser;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\v1\Controllers\User')]
#[CoversClass('\App\v1\Controllers\Login')]
#[CoversClass('\App\v1\Controllers\Common')]

final class UserTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // create Authsso
    $authsso = \App\Models\Authsso::create([
      'name'              => 'test authsso provider',
      'is_active'         => true,
      'provider'          => 'keycloak',
      'callbackid'        => 'b4837ae642f7',
      'applicationid'     => 'FusionResolveIT',
      'applicationpublic' => 'FusionResolveIT',
      'baseurl'           => 'http://localhost:8080/',
      'realm'             => 'My company',
    ]);

    // create rules
    $rule = \App\Models\Rules\User::create([
      'name'      => 'test profile with SSO',
      'match'     => 'AND',
      'is_active' => true,
    ]);
    \App\Models\Rules\Rulecriterium::create([
      'rule_id'   => $rule->id,
      'criteria'  => 'authsso',
      'condition' => 0,
      'pattern'   => $authsso->id,
    ]);

    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 1, //assign_dropdown
      'field'       => 'profile',
      'value'       => '1',
    ]);
    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 1, //assign_dropdown
      'field'       => 'entity',
      'value'       => '1',
    ]);
  }

  public static function tearDownAfterClass(): void
  {
    \App\Models\Authsso::truncate();
    // delete rules
    \App\Models\Rules\Rule::truncate();
    \App\Models\Rules\Rulecriterium::truncate();
    \App\Models\Rules\Ruleaction::truncate();
  }

  public function testRuleMatch(): void
  {
    $authsso = \App\Models\Authsso::first();
    $prepareData = [
      'name'    => 't.stark',
      'authsso' => $authsso->id,
    ];
    $GLOBALS['profile_id'] = 0;
    $data = new PostUser((object) $prepareData);
    $data->forceAllDefinitions();

    $ctrlUser = new \App\v1\Controllers\User();
    $dataUser = $ctrlUser->runRules($data);

    $dataUserArray = $dataUser->exportToArray();

    $this->assertEquals($dataUserArray['name'], 't.stark');
    $this->assertArrayHasKey('profile_id', $dataUserArray);
    $this->assertArrayHasKey('entity_id', $dataUserArray);
    $this->assertEquals($dataUserArray['profile_id'], 1);
    $this->assertEquals($dataUserArray['entity_id'], 1);
  }

  public function testRuleNotMatch(): void
  {
    $prepareData = [
      'name'    => 't.stark',
    ];
    $GLOBALS['profile_id'] = 0;
    $data = new PostUser((object) $prepareData);
    $data->forceAllDefinitions();

    $ctrlUser = new \App\v1\Controllers\User();
    $dataUser = $ctrlUser->runRules($data);

    $dataUserArray = $dataUser->exportToArray();

    $this->assertEquals($dataUserArray['name'], 't.stark');
    $this->assertArrayNotHasKey('profile_id', $dataUserArray);
    $this->assertArrayNotHasKey('entity_id', $dataUserArray);
  }
}
