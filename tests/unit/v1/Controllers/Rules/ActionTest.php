<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers\Rules;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Rules\Action')]
#[CoversClass('\App\Models\Rules\Ruleaction')]
#[CoversClass('\App\Models\Rules\Ticket')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\DataInterface\Post')]
#[UsesClass('\App\DataInterface\PostTicket')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\Rule')]
#[UsesClass('\App\Models\Definitions\Ruleaction')]
#[UsesClass('\App\Models\Definitions\Rulecriterium')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Rules\Rule')]
#[UsesClass('\App\Models\Ticket')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\v1\Controllers\Fusioninventory\Validation')]

final class ActionTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    \App\Models\Rules\Ticket::create([
      'name'      => 'test',
      'match'     => 'AND',
      'is_active' => true,
    ]);

    // delete all rule actions
    \App\Models\Rules\Ruleaction::truncate();

    // delete all rules
    \App\Models\Rules\Rule::truncate();

    \App\Models\Rules\Ticket::create([
      'name'      => 'test',
      'match'     => 'AND',
      'is_active' => true,
    ]);

    // Clean user
    $users = \App\Models\User::where('id', '>', 1)->get();
    foreach ($users as $user)
    {
      $user->forceDelete();
    }

    $user = new \App\Models\User();
    $user->id = 5;
    $user->name = 'nb 5';
    $user->save();

    $user = new \App\Models\User();
    $user->id = 6;
    $user->name = 'nb 6';
    $user->save();

    $user = new \App\Models\User();
    $user->id = 10;
    $user->name = 'nb 10';
    $user->save();    
  }

  public static function tearDownAfterClass(): void
  {
    // delete rules
    \App\Models\Rules\Rule::truncate();

    // Clean user
    $users = \App\Models\User::where('id', '>', 1)->get();
    foreach ($users as $user)
    {
      $user->forceDelete();
    }
  }

  public static function providerAction(): array
  {

    return [
      // type, field, value, preparedData, regexResults, expected
      'title assign' => [0, 'name', 'new title', ['name' => 'my problem'], [], 'new title'],
      'title assign no exists' => [0, 'name', 'new title', [], [], 'new title'],

      'title append' => [2, 'name', 'test', ['name' => 'my problem'], [], 'my problemtest'],
      'title append no exists' => [2, 'name', 'test', [], [], 'test'],

      'requester assign' => [1, 'requester', '5', ['requester' => '1'], [], ['user5']],
      'requester assign no exists' => [1, 'requester', '5', [], [], ['user5']],

      'requester append' => [3, 'requester', '5', ['requester' => '1'], [], ['user1', 'user5']],
      'requester append order different' => [
        3,
        'requester',
        '5',
        ['requester' => '6,10'], [], ['user6', 'user10', 'user5']
      ],
      'requester append no exists' => [3, 'requester', '5', [], [], ['user5']],

      'title regex result' =>
          [4, 'name', '#0', ['name' => 'my problem'], ['blem'], 'blem'],
      'title regex result with predefined value' =>
          [4, 'name', 'my #0', ['name' => 'my problem'], ['blem'], 'my blem'],
      'title regex result with predefined value and second not exists' =>
          [4, 'name', 'my #0 to #1', ['name' => 'my problem'], ['blem'], 'my blem to #1'],

      'title append regex result' =>
          [5, 'name', '#0', ['name' => 'my problem'], ['blem'], 'my problemblem'],
    ];
  }

  #[DataProvider('providerAction')]
  public function testCheckAction($type, $field, $value, $preparedData, $regexResults, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Action();
    $action = new \App\Models\Rules\Ruleaction();
    $action->rule_id = 1;
    $action->action_type = $type;
    $action->field = $field;
    $action->value = $value;
    $action->save();
    $action->refresh();

    $this->assertNotNull($action, 'create action failed');

    $data = new \App\DataInterface\PostTicket((object) $preparedData);

    $ret = $ctrl->runAction($action, $data, $regexResults);
  
    // change expected if have user* and replace by user model
    $user1 = \App\Models\User::where('id', 1)->first();
    $user5 = \App\Models\User::where('id', 5)->first();
    $user6 = \App\Models\User::where('id', 6)->first();
    $user10 = \App\Models\User::where('id', 10)->first();

    if (is_array($expected))
    {
      foreach ($expected as $idx => $value)
      {
        switch ($value) {
          case 'user1':
            $expected[$idx] = $user1;
              break;

          case 'user5':
            $expected[$idx] = $user5;
              break;

          case 'user6':
            $expected[$idx] = $user6;
              break;

          case 'user10':
            $expected[$idx] = $user10;
              break;           
          
        }
      }
    }

    $this->assertEquals($expected, $ret->{$field});
  }
}
