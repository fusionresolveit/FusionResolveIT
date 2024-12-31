<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Ticket')]
#[CoversClass('\App\v1\Controllers\Common')]
#[CoversClass('\App\Models\Definitions\Ticket')]
#[CoversClass('\App\Models\Rules\Ruleaction')]
#[CoversClass('\App\Models\Rules\Rulecriterium')]
#[CoversClass('\App\Models\Rules\Ticket')]
#[CoversClass('\App\v1\Controllers\Rules\Action')]
#[CoversClass('\App\v1\Controllers\Rules\Common')]
#[CoversClass('\App\v1\Controllers\Rules\Criterium')]
#[UsesClass('\App\Route')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\ItemTicket')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Notification')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\ProjecttaskTicket')]
#[UsesClass('\App\Models\Definitions\Rule')]
#[UsesClass('\App\Models\Definitions\Solution')]
#[UsesClass('\App\Models\Definitions\Ticketcost')]
#[UsesClass('\App\Models\Definitions\TicketValidation')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Followup')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\Solution')]
#[UsesClass('\App\Models\Ticket')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Log')]
#[UsesClass('\App\v1\Controllers\Profile')]
#[UsesClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\v1\Controllers\Toolbox')]


final class TicketTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // create rules
    $rule = \App\Models\Rules\Rule::create([
      'name'      => 'test priority',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
    \App\Models\Rules\Rulecriterium::create([
      'rule_id'   => $rule->id,
      'criteria'  => 'name',
      'condition' => \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
      'pattern'   => 'priority',
    ]);
    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 'assign_dropdown',
      'field'       => 'priority',
      'value'       => '4',
    ]);

    $userDavid = \App\Models\User::create(['name' => 'david']);
    $userJohn = \App\Models\User::create(['name' => 'john']);

    $rule = \App\Models\Rules\Rule::create([
      'name'      => 'test requester 1',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
    \App\Models\Rules\Rulecriterium::create([
      'rule_id'   => $rule->id,
      'criteria'  => 'name',
      'condition' => \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
      'pattern'   => 'requester 1',
    ]);
    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 'assign_dropdown',
      'field'       => 'requester',
      'value'       => $userJohn->id,
    ]);

    $rule = \App\Models\Rules\Rule::create([
      'name'      => 'test requester 2',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
    \App\Models\Rules\Rulecriterium::create([
      'rule_id'   => $rule->id,
      'criteria'  => 'name',
      'condition' => \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN,
      'pattern'   => 'requester 2',
    ]);
    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 'append_dropdown',
      'field'       => 'requester',
      'value'       => $userJohn->id,
    ]);

    $rule = \App\Models\Rules\Rule::create([
      'name'      => 'test title regex',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
    \App\Models\Rules\Rulecriterium::create([
      'rule_id'   => $rule->id,
      'criteria'  => 'content',
      'condition' => \App\v1\Controllers\Rules\Common::REGEX_MATCH,
      'pattern'   => '(SAP|outlook|mariadb)',
    ]);
    \App\Models\Rules\Ruleaction::create([
      'rule_id'     => $rule->id,
      'action_type' => 'append_regex_result',
      'field'       => 'name',
      'value'       => ' #0',
    ]);
  }

  public static function tearDownAfterClass(): void
  {
    // delete rules
    \App\Models\Rules\Rule::truncate();
    \App\Models\Rules\Rulecriterium::truncate();
    \App\Models\Rules\Ruleaction::truncate();

    $user = \App\Models\User::where('name', 'david')->first();
    $user->forceDelete();
    $user = \App\Models\User::where('name', 'john')->first();
    $user->forceDelete();
  }

  public static function providerPriority(): array
  {
    return [
      'no rule triggered' => [['name' => 'my title', 'priority' => 3], 3],
      'no rule triggered with no priority' => [['name' => 'my title'], 3],
      'no rule triggered special' => [['name' => 'my title prio rity'], 3],
      'rule with priority' => [['name' => 'my title priority', 'priority' => 3], 4],
      'rule without priority' => [['name' => 'my title priority'], 4],
    ];
  }

  public static function providerRequester(): array
  {
    return [
      'no rule triggered' => [['name' => 'my title requester', 'requester' => 'david,john'], ['david', 'john']],
      'no rule triggered with no requester' => [['name' => 'my title requester'], []],
      'rule replace user' => [['name' => 'my title requester 1', 'requester' => 'david,john'], ['john']],
      'rule replace user empty' => [['name' => 'my title requester 1', 'requester' => ''], ['john']],
      'rule append user' => [['name' => 'my title requester 2', 'requester' => 'david'], ['david', 'john']],
      'rule append user same' => [['name' => 'my title requester 2', 'requester' => 'john'], ['john']],
    ];
  }

  public static function titleAppendRegex(): array
  {
    return [
      'content has SAP word' =>
          [['name' => 'App problem', 'content' => 'I have a problem with SAP, it crash when...'], 'App problem SAP'],
      'content has SAP word and outlook' =>
          [['name' => 'App problem', 'content' => 'problem with SAP and outlook, it crash when...'], 'App problem SAP'],
    ];
  }

  #[DataProvider('providerPriority')]
  public function testRulePriorityCreation($fields, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Ticket();
    $data = (object) $fields;
    $id = $ctrl->saveItem($data);
    $ticket = \App\Models\Ticket::find($id);
    $this->assertEquals($expected, $ticket->priority);
  }

  #[DataProvider('providerPriority')]
  public function testRulePriorityUpdate($fields, $expected): void
  {
    $ticket = \App\Models\Ticket::create($fields);

    $ctrl = new \App\v1\Controllers\Ticket();
    $data = (object) [
      'name' => $fields['name'],
    ];
    $id = $ctrl->saveItem($data, $ticket->id);
    $ticket->refresh();
    $this->assertEquals($expected, $ticket->priority);
  }

  #[DataProvider('providerRequester')]
  public function testRuleRequesterCreation($fields, $expected): void
  {
    $userDavid = \App\Models\User::where('name', 'david')->first();
    $userJohn = \App\Models\User::where('name', 'john')->first();

    if (isset($fields['requester']))
    {
      $fields['requester'] = str_replace('david', (string) $userDavid->id, $fields['requester']);
      $fields['requester'] = str_replace('john', (string) $userJohn->id, $fields['requester']);
    }

    $ctrl = new \App\v1\Controllers\Ticket();
    $data = (object) $fields;
    $id = $ctrl->saveItem($data);
    $ticket = \App\Models\Ticket::find($id);

    $requester = [];
    foreach ($ticket->requester as $user)
    {
      $requester[] = $user->id;
    }

    foreach ($expected as $key => $value)
    {
      if ($value == 'david')
      {
        $expected[$key] = $userDavid->id;
      }
      if ($value == 'john')
      {
        $expected[$key] = $userJohn->id;
      }
    }
    $this->assertEquals($expected, $requester);
  }

  // public function testRuleRequesterUpdate(): void
  // {

  // }

  #[DataProvider('titleAppendRegex')]
  public function testRuleTitleRegexCreation($fields, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Ticket();
    $data = (object) $fields;
    $id = $ctrl->saveItem($data);
    $ticket = \App\Models\Ticket::find($id);
    $this->assertEquals($expected, $ticket->name);
  }

  #[DataProvider('titleAppendRegex')]
  public function testRuleTitleRegexUpdate($fields, $expected): void
  {
    $ticket = \App\Models\Ticket::create([
      'name'    => $fields['name'],
      'content' => 'no more content :D',
    ]);

    $this->assertEquals($fields['name'], $ticket->name);

    $ctrl = new \App\v1\Controllers\Ticket();
    $data = (object) [
      'content' => $fields['content'],
    ];
    $ctrl->saveItem($data, $ticket->id);
    $ticket->refresh();
    $this->assertEquals($expected, $ticket->name);
  }
}
