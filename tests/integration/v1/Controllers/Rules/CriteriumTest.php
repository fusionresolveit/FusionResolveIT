<?php

declare(strict_types=1);

namespace Tests\integration\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\v1\Controllers\Rules\Common;

#[CoversClass('\App\v1\Controllers\Rules\Criterium')]
#[CoversClass('\App\v1\Controllers\Rules\Common')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Translation')]
#[UsesClass('\App\Models\Category')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Changetemplate')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Knowbaseitemcategory')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Problemtemplate')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\Rule')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Group')]
#[UsesClass('\App\Models\Rules\Rulecriterium')]
#[UsesClass('\App\Models\Ticket')]

final class CriteriumTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // reset all
    \App\Models\Category::truncate();
    \App\Models\Group::truncate();

    // create categories
    $cat0 = \App\Models\Category::create([
      'name'        => 'my very first level',
      'category_id' => 0,
    ]);
    if (is_null($cat0))
    {
      throw new \Exception('Problem create category', 500);
    }
    $cat1 = \App\Models\Category::create([
      'name'        => 'only the second level, app Microsoft',
      'category_id' => $cat0->id,
    ]);
    $cat2 = \App\Models\Category::create([
      'name'        => 'third level, outlook',
      'category_id' => $cat1->id,
    ]);

    // id 1
    \App\Models\Group::create([
      'name'        => 'support lvl 1',
    ]);
    // id 2
    \App\Models\Group::create([
      'name'        => 'support lvl 2 - admins',
    ]);
    // id 3
    $grplvl3 = \App\Models\Group::create([
      'name'        => 'support lvl 3 - devs',
    ]);
    // id 4
    $grpgsit = \App\Models\Group::create([
      'name'        => 'support lvl 3 - GSIT',
      'group_id'    => $grplvl3->id,
    ]);
    // id 5
    \App\Models\Group::create([
      'name'        => 'support lvl 3 - rules',
      'group_id'    => $grpgsit->id,
    ]);
    // id 6
    \App\Models\Group::create([
      'name'        => 'support lvl 3 - Grafana',
      'group_id'    => $grplvl3->id,
    ]);

    \App\Models\Rules\Rule::create([
      'name'      => 'test',
      'sub_type'  => 'RuleTicket',
      'match'     => 'AND',
      'is_active' => true,
    ]);
  }

  public static function tearDownAfterClass(): void
  {
    // delete all categories
    \App\Models\Category::truncate();

    // delete all groups
    \App\Models\Group::truncate();

    // delete rules
    \App\Models\Rules\Rule::truncate();
    \App\Models\Rules\Rulecriterium::truncate();
    \App\Models\Rules\Ruleaction::truncate();
  }

  public static function additionProviderInputname(): array
  {
    return [
      // [criteria, condition, pattern, inputkey, inputvalue, return]
      // PATTERN_IS   not works with this type of data

      // PATTERN_IS_NOT   not works with this type of data

      '[inputname][contain] contain `blem`' =>
          ['name', Common::PATTERN_CONTAIN , 'blem', 'name', 'problem with my computer, it reset each hour',true],
      '[inputname][contain] no name field' =>
          ['name', Common::PATTERN_CONTAIN , 'my', 'content', 'my description', false],
      '[inputname][contain] check `, ` but not in field' =>
          ['name', Common::PATTERN_CONTAIN , ',  ', 'name', 'problem with my computer, it reset each hour', false],
      '[inputname][contain] test uppercase' =>
          ['name', Common::PATTERN_CONTAIN , 'WIT', 'name', 'problem with my computer, it reset each hour', true],

      '[inputname][not contain] not contain value' =>
          ['name', Common::PATTERN_NOT_CONTAIN , 'access', 'name', 'problem with my computer', true],
      '[inputname][not contain] no name field' =>
          ['name', Common::PATTERN_NOT_CONTAIN , 'my', 'content', 'my description', false],
      '[inputname][not contain] contain value' =>
          ['name', Common::PATTERN_NOT_CONTAIN , 'with', 'name', 'problem with my computer, it reset each hour', false],
      '[inputname][not contain] contain uppercase' =>
          ['name', Common::PATTERN_NOT_CONTAIN , 'WITH', 'name', 'problem with my computer, it reset each hour', false],

      '[inputname][begin] value begin' =>
          ['name', Common::PATTERN_BEGIN , 'proble', 'name', 'problem with my computer, it reset each hour', true],
      '[inputname][begin] no name value' =>
          ['name', Common::PATTERN_BEGIN , 'my', 'content', 'my description', false],
      '[inputname][begin] uppercase' =>
          ['name', Common::PATTERN_BEGIN , 'PRo', 'name', 'problem with my computer, it reset each hour', true],

      '[inputname][end] value end' =>
          ['name', Common::PATTERN_END , 'ch hour', 'name', 'problem with my computer, it reset each hour', true],
      '[inputname][end] non name value' =>
          ['name', Common::PATTERN_END , 'tion', 'content', 'my description', false],
      '[inputname][end] value at beginning' =>
          ['name', Common::PATTERN_END , 'problem', 'name', 'problem with my computer, it reset each hour', false],
      '[inputname][end] uppercase' =>
          ['name', Common::PATTERN_END , 'hOUr', 'name', 'problem with my computer, it reset each hour', true],

      '[inputname][regex match] match' =>
          ['name', Common::REGEX_MATCH , 'computer([s]{0,1})', 'name', 'problem with my computer, it reset', true],
      '[inputname][regex match] no name field' =>
          ['name', Common::REGEX_MATCH , 'tion', 'content', 'my description', false],
      '[inputname][regex match] not match' =>
          ['name', Common::REGEX_MATCH , 'computer([s]{2})', 'name', 'problem with my computer, it reset', false],

      '[inputname][regex not match] not match' =>
          ['name', Common::REGEX_NOT_MATCH , 'computer([s]{1})', 'name', 'problem with my computer, it reset', true],
      '[inputname][regex not match] no name field' =>
          ['name', Common::REGEX_NOT_MATCH , 'test', 'content', 'my description', false],
      '[inputname][regex not match] match' =>
          ['name', Common::REGEX_NOT_MATCH , 'computer([s]{0,1})', 'name', 'problem with my computer, it reset', false],

      '[inputname][exists] have name field' =>
          ['name', Common::PATTERN_EXISTS , '', 'name', 'problem with my computer, it reset each hour', true],
      '[inputname][exists] not have name field' =>
          ['name', Common::PATTERN_EXISTS , '', 'content', 'my problem', false],

      '[inputname][not exists] not have name field' =>
          ['name', Common::PATTERN_DOES_NOT_EXISTS , '', 'content', 'problem with my computer', true],
      '[inputname][not exists] have name field' =>
          ['name', Common::PATTERN_DOES_NOT_EXISTS , '', 'name', 'problem with my computer', false],

      // PATTERN_UNDER   not works with this type of data

      // PATTERN_NOT_UNDER   not works with this type of data

      '[inputname][empty] value empty' =>
          ['name', Common::PATTERN_IS_EMPTY , '', 'name', '', true],
      '[inputname][empty] no name field' =>
          ['name', Common::PATTERN_IS_EMPTY , '', 'content', '', false],
      '[inputname][empty] value not empty' =>
          ['name', Common::PATTERN_IS_EMPTY , '', 'name', 'some words', false],

      // PATTERN_FIND TODO
    ];
  }

  public static function additionProviderDropdownValues(): array
  {
    return [
      // [criteria, condition, pattern, inputkey, inputvalue, return]
      '[dropdownvalue][is] same id' =>
          ['status', Common::PATTERN_IS , 3, 'status', 3, true],
      '[dropdownvalue][is] status field not present' =>
          ['status', Common::PATTERN_IS , 3, 'name', 'test', false],
      '[dropdownvalue][is] not same id' =>
          ['status', Common::PATTERN_IS , 3, 'status', 4, false],
      '[dropdownvalue][is] id not exists' =>
          ['status', Common::PATTERN_IS , 3, 'status', 30, false],
      '[dropdownvalue][is] id is a string' =>
          ['status', Common::PATTERN_IS , 3, 'status', '4', false],

      '[dropdownvalue][is not] id not same' =>
          ['status', Common::PATTERN_IS_NOT , 3, 'status', 2, true],
      '[dropdownvalue][is not] status field not present' =>
          ['status', Common::PATTERN_IS_NOT , 3, 'name', 3, false],
      '[dropdownvalue][is not] same id' =>
          ['status', Common::PATTERN_IS_NOT , 3, 'status', 3, false],
      '[dropdownvalue][is not] id not exists' =>
          ['status', Common::PATTERN_IS_NOT , 3, 'status', 30, true],
      '[dropdownvalue][is not] id is a string' =>
          ['status', Common::PATTERN_IS_NOT , 3, 'status', '3', true],

      '[dropdownvalue][contain] contain value' =>
          ['status', Common::PATTERN_CONTAIN , 'cess', 'status', 3, true],
      '[dropdownvalue][contain] status field not present' =>
          ['status', Common::PATTERN_CONTAIN , 'cess', 'name', 'access', false],
      '[dropdownvalue][contain] not contain value' =>
          ['status', Common::PATTERN_CONTAIN , 'kass', 'status', 3, false],
      '[dropdownvalue][contain] id not exists' =>
          ['status', Common::PATTERN_CONTAIN , 'cess', 'status', 30, false],
      '[dropdownvalue][contain] string instead id' =>
          ['status', Common::PATTERN_CONTAIN , 'cess', 'status', 'random access text', false],

      '[dropdownvalue][not contain] not contain value' =>
          ['status', Common::PATTERN_NOT_CONTAIN , 'general', 'status', 3, true],
      '[dropdownvalue][not contain] status field not present' =>
          ['status', Common::PATTERN_NOT_CONTAIN , 'general', 'name', 'access', false],
      '[dropdownvalue][not contain] contain value' =>
          ['status', Common::PATTERN_NOT_CONTAIN , 'cess', 'status', 3, false],
      '[dropdownvalue][not contain] id not exists' =>
          ['status', Common::PATTERN_NOT_CONTAIN , 'cess', 'status', 10000, true],

      '[dropdownvalue][begin] begin by value' =>
          ['status', Common::PATTERN_BEGIN , 'proc', 'status', 3, true],
      '[dropdownvalue][begin] field status not present' =>
          ['status', Common::PATTERN_BEGIN , 'proc', 'name', 'my test', false],
      '[dropdownvalue][begin] id not exists' =>
          ['status', Common::PATTERN_BEGIN , 'proc', 'status', 30, false],
      '[dropdownvalue][begin] begin by value uppercase' =>
          ['status', Common::PATTERN_BEGIN , 'Proc', 'status', 3, true],

      '[dropdownvalue][end] end by value' =>
          ['status', Common::PATTERN_END , 'anned)', 'status', 3, true],

      '[dropdownvalue][regex match] match' =>
          ['status', Common::REGEX_MATCH , 'roce([s]{2})', 'status', 3, true],
      '[dropdownvalue][regex match] match 2' =>
          ['status', Common::REGEX_MATCH , '^(pro|Pro)', 'status', 3, true],

      '[dropdownvalue][regex not match] not match' =>
          ['status', Common::REGEX_NOT_MATCH , 'roce([s]{3})', 'status', 3, true],

      '[dropdownvalue][exists] field status exists' =>
          ['status', Common::PATTERN_EXISTS , '', 'status', 3, true],
      '[dropdownvalue][exists] field status not present' =>
          ['status', Common::PATTERN_EXISTS , '', 'name', 'test', false],

      '[dropdownvalue][not exists] field status not present' =>
          ['status', Common::PATTERN_DOES_NOT_EXISTS , '', 'name', 'test', true],
      '[dropdownvalue][not exists] field status present' =>
          ['status', Common::PATTERN_DOES_NOT_EXISTS , '', 'status', 3, false],

      // PATTERN_UNDER   not works with this type of data

      // PATTERN_NOT_UNDER   not works with this type of data

      '[dropdownvalue][empty] field empty' =>
          ['status', Common::PATTERN_IS_EMPTY , '', 'status', '', true],
      '[dropdownvalue][empty] field with id 0' =>
          ['status', Common::PATTERN_IS_EMPTY , '', 'status', 0, true],
      '[dropdownvalue][empty] field with id exists' =>
          ['status', Common::PATTERN_IS_EMPTY , '', 'status', 4, false],

      // PATTERN_FIND  TODO

    ];
  }

  // dataprovider with single dropdown (category)
  public static function additionProviderDropdown(): array
  {
    return [
      // [criteria, condition, pattern, inputkey, inputvalue, return]

      '[dropdown][is] same id' =>
          ['category', Common::PATTERN_IS , 2, 'category', 2, true],
      '[dropdown][is] ' =>
          ['category', Common::PATTERN_IS , 2, 'name', 'test', false],
      '[dropdown][is] same id' =>
          ['category', Common::PATTERN_IS , 2, 'category', 1, false],

      '[dropdown][is not] not same id' =>
          ['category', Common::PATTERN_IS_NOT , 2, 'category', 3, true],
      '[dropdown][is not] field category not present' =>
          ['category', Common::PATTERN_IS_NOT , 2, 'name', 'test', false],
      '[dropdown][is not] same id' =>
          ['category', Common::PATTERN_IS_NOT , 2, 'category', 2, false],

      '[dropdown][contain] contain value' =>
          ['category', Common::PATTERN_CONTAIN , 'Mic', 'category', 2, true],
      '[dropdown][contain] field category not present' =>
          ['category', Common::PATTERN_CONTAIN , 'Mic', 'name', 'Microphone', false],
      '[dropdown][contain] contain value uppercase' =>
          ['category', Common::PATTERN_CONTAIN , 'mic', 'category', 2, true],

      '[dropdown][not contain] not contain value' =>
          ['category', Common::PATTERN_NOT_CONTAIN , 'outl', 'category', 2, true],
      '[dropdown][not contain] field category not present' =>
          ['category', Common::PATTERN_NOT_CONTAIN , 'outl', 'name', 'test', false],
      '[dropdown][not contain] contain value' =>
          ['category', Common::PATTERN_NOT_CONTAIN , 'micro', 'category', 2, false],

      '[dropdown][begin] begin with value' =>
          ['category', Common::PATTERN_BEGIN , 'Only The', 'category', 2, true],

      '[dropdown][end] end with value' =>
          ['category', Common::PATTERN_END , 'crosoft', 'category', 2, true],

      '[dropdown][regex match] match' =>
          ['category', Common::REGEX_MATCH , '(second|third) level', 'category', 2, true],
      '[dropdown][regex match] match 2' =>
          ['category', Common::REGEX_MATCH , '(second|third) level', 'category', 3, true],
      '[dropdown][regex match] field category not present' =>
          ['category', Common::REGEX_MATCH , '(second|third) level', 'name', 'my test', false],
      '[dropdown][regex match] not match' =>
          ['category', Common::REGEX_MATCH , '(second|third) level', 'category', 1, false],

      '[dropdown][regex not match] not match' =>
          ['category', Common::REGEX_NOT_MATCH , '(second|third) level', 'category', 1, true],
      '[dropdown][regex not match] field category not present' =>
          ['category', Common::REGEX_NOT_MATCH , '(second|third) level', 'name', 'second level', false],
      '[dropdown][regex not match] match' =>
          ['category', Common::REGEX_NOT_MATCH , '(second|third) level', 'category', 2, false],

      '[dropdown][exists] field exists' =>
          ['category', Common::PATTERN_EXISTS , '', 'category', 2, true],
      '[dropdown][exists] field not present' =>
          ['category', Common::PATTERN_EXISTS , '', 'name', 'test', false],
      '[dropdown][exists] field exists but id not exists' =>
          ['category', Common::PATTERN_EXISTS , '', 'category', 100, true],

      '[dropdown][not exists] field category not present' =>
          ['category', Common::PATTERN_DOES_NOT_EXISTS , '', 'name', 'test', true],
      '[dropdown][not exists] field exists' =>
          ['category', Common::PATTERN_DOES_NOT_EXISTS , '', 'category', 2, false],

      '[dropdown][under] category under' =>
          ['category', Common::PATTERN_UNDER , 1, 'category', 3, true],
      '[dropdown][under] category not under' =>
          ['category', Common::PATTERN_UNDER , 2, 'category', 1, false],
      '[dropdown][under] field category not present' =>
          ['category', Common::PATTERN_UNDER , 10, 'category', 3, false],

      '[dropdown][not under] category not under' =>
          ['category', Common::PATTERN_NOT_UNDER , 3, 'category', 1, true],
      '[dropdown][not under] category under' =>
          ['category', Common::PATTERN_NOT_UNDER , 1, 'category', 2, false],
      '[dropdown][not under] category id not exists' =>
          ['category', Common::PATTERN_NOT_UNDER , 10, 'category', 3, true],

      '[dropdown][empty] category id 0' =>
          ['category', Common::PATTERN_IS_EMPTY , '', 'category', 0, true],
      '[dropdown][empty] category id' =>
          ['category', Common::PATTERN_IS_EMPTY , '', 'category', 2, false],

      // PATTERN_FIND

    ];
  }

  // dataprovider with multiple dropdown (requestergroup)
  public static function additionProviderDropdownMultiple(): array
  {
    return [
      // [criteria, condition, pattern, inputkey, inputvalue, return]

      '[dropdownmultiple][is] array has the id' =>
          ['requestergroup', Common::PATTERN_IS , 2, 'requestergroup', [1, 2], true],

      '[dropdownmultiple][is not] array has not the id' =>
          ['requestergroup', Common::PATTERN_IS_NOT , 2, 'requestergroup', [1, 3], true],
      '[dropdownmultiple][is not] array empty' =>
          ['requestergroup', Common::PATTERN_IS_NOT , 2, 'requestergroup', [], true],

      '[dropdownmultiple][contain] contain value' =>
          ['requestergroup', Common::PATTERN_CONTAIN , 'lvl 2', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][not contain] not contain value' =>
          ['requestergroup', Common::PATTERN_NOT_CONTAIN , 'administrator', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][begin] one id have value begin by' =>
          ['requestergroup', Common::PATTERN_BEGIN , 'support', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][end] one id have value end by' =>
          ['requestergroup', Common::PATTERN_END , 's', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][regex match] one id match' =>
          ['requestergroup', Common::REGEX_MATCH , 'lvl (\d+)', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][regex not match] no id match' =>
          ['requestergroup', Common::REGEX_NOT_MATCH , 'lvl (\d{2})', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][exists] field exists' =>
          ['requestergroup', Common::PATTERN_EXISTS , '', 'requestergroup', [1, 2], true],

      '[dropdownmultiple][not exists] field requestergroup not exists' =>
          ['requestergroup', Common::PATTERN_DOES_NOT_EXISTS , '', 'name', 'test', true],

      '[dropdownmultiple][under] id is under' =>
          ['requestergroup', Common::PATTERN_UNDER , 3, 'requestergroup', [1, 2, 5], true],

      '[dropdownmultiple][not under] id is not under' =>
          ['requestergroup', Common::PATTERN_NOT_UNDER , 5, 'requestergroup', [1, 2, 3], true],

      '[dropdownmultiple][empty] array is empty' =>
          ['requestergroup', Common::PATTERN_IS_EMPTY , 5, 'requestergroup', [], true],

      // PATTERN_FIND

    ];
  }

  // test with boolean?????
  // dataprovider with multiple dropdown (requestergroup)
  public static function additionProviderBoolean(): array
  {
    return [
      // [criteria, condition, pattern, inputkey, inputvalue, return]

      '[boolean][is] value is true' =>
          ['is_late', Common::PATTERN_IS , true, 'is_late', true, true],

      '[boolean][is not] value is not true' =>
          ['is_late', Common::PATTERN_IS_NOT , true, 'is_late', false, true],

      // PATTERN_CONTAIN   not works with this type of data

      // PATTERN_NOT_CONTAIN   not works with this type of data

      // PATTERN_BEGIN   not works with this type of data

      // PATTERN_END   not works with this type of data

      // REGEX_MATCH   not works with this type of data

      // REGEX_NOT_MATCH   not works with this type of data

      '[boolean][exists] field exists' =>
          ['is_late', Common::PATTERN_EXISTS , true, 'is_late', true, true],

      '[boolean][not exists] field is_late not present' =>
          ['is_late', Common::PATTERN_DOES_NOT_EXISTS , true, 'name', 'test', true],

      // PATTERN_UNDER   not works with this type of data

      // PATTERN_NOT_UNDER   not works with this type of data

      // PATTERN_IS_EMPTY   not works with this type of data

      // PATTERN_FIND

    ];
  }

  #[DataProvider('additionProviderInputname')]
  #[DataProvider('additionProviderDropdownValues')]
  #[DataProvider('additionProviderDropdown')]
  #[DataProvider('additionProviderDropdownMultiple')]
  #[DataProvider('additionProviderBoolean')]
  public function testCheckCriteria($criterium, $condition, $pattern, $inputkey, $value, $expectedRet): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $crit = new \App\Models\Rules\Rulecriterium();
    $crit->rule_id = 1;
    $crit->criteria = $criterium;
    $crit->condition = $condition;
    $crit->pattern = $pattern;
    $crit->save();
    $crit->refresh();

    $this->assertNotNull($crit, 'create criterium failed');

    $input = [
      $inputkey => $value,
    ];
    $ret = $ctrl->checkCriteria($crit, $input);
    $crit->forceDelete();
    $this->assertEquals($expectedRet, $ret);
  }
}
