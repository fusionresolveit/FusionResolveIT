<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Rules\Action')]
#[CoversClass('\App\v1\Controllers\Rules\Common')]
#[CoversClass('\App\Models\Rules\Ruleaction')]
#[CoversClass('\App\Models\Rules\Ticket')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Rule')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Ticket')]

final class ActionTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    \App\Models\Rules\Rule::create([
      'name'      => 'test',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
  }

  public static function tearDownAfterClass(): void
  {
    // delete rules
    \App\Models\Rules\Rule::truncate();
  }

  public static function providerAction(): array
  {
    return [
      // type, field, value, preparedData, expected
      'title assign' => ['assign', 'title', 'new title', ['title' => 'my problem'], [], 'new title'],
      'title assign no exists' => ['assign', 'title', 'new title', [], [], 'new title'],

      'title append' => ['append', 'title', 'test', ['title' => 'my problem'], [], 'my problemtest'],
      'title append no exists' => ['append', 'title', 'test', [], [], 'test'],

      'requester assign' => ['assign_dropdown', 'requester', '5', ['requester' => [1]], [], [5]],
      'requester assign no exists' => ['assign_dropdown', 'requester', '5', [], [], [5]],

      'requester append' => ['append_dropdown', 'requester', '5', ['requester' => [1]], [], [1, 5]],
      'requester append order different' => [
        'append_dropdown',
        'requester',
        '5',
        ['requester' => [6,10]], [], [6, 10, 5]
      ],
      'requester append no exists' => ['append_dropdown', 'requester', '5', [], [], [5]],

      'title regex result' =>
          ['regex_result', 'title', '#0', ['title' => 'my problem'], ['blem'], 'blem'],
      'title regex result with predefined value' =>
          ['regex_result', 'title', 'my #0', ['title' => 'my problem'], ['blem'], 'my blem'],
      'title regex result with predefined value and second not exists' =>
          ['regex_result', 'title', 'my #0 to #1', ['title' => 'my problem'], ['blem'], 'my blem to #1'],

      'title append regex result' =>
          ['append_regex_result', 'title', '#0', ['title' => 'my problem'], ['blem'], 'my problemblem'],
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
    $action->fieldviewfield = [];

    $this->assertNotNull($action, 'create action failed');

    $ticket = new \App\Models\Ticket();
    $definitions = $ticket->getDefinitions();
    foreach ($definitions as $def)
    {
      if ($def['name'] == $field)
      {
        $action->fieldviewfield = $def;
        break;
      }
    }

    $ret = $ctrl->runAction($action, $preparedData, $regexResults);

    $this->assertEquals($expected, $ret[$field]);
  }
}
