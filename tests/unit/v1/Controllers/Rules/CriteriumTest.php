<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass('\App\v1\Controllers\Rules\Criterium')]
#[CoversClass('\App\v1\Controllers\Rules\Common')]
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
  }

  public static function providerPatternIs(): array
  {
    return [
      'value null' => [null, 4, false],
      'value same' => [4, 4, true],
      'value string' => ['4', 4, false],
      'pattern string' => [4, '4', false],
      'value not same' => [4, 40, false],

      'value array not have id' => [[2, 8, 5], 4, false],
      'value array have id' => [[2, 8, 4], 4, true],
      'value array have id but string' => [[2, 8, '4'], 4, false],
    ];
  }

  #[DataProvider('providerPatternIs')]
  public function testPatternIs($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternIs');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternIsNot(): array
  {
    return [
      'value null' => [null, 4, true],
      'value same' => [4, 4, false],
      'value string' => ['4', 4, true],
      'pattern string' => [4, '4', true],
      'value not same' => [4, 40, true],

      'value array not have id' => [[2, 8, 5], 4, true],
      'value array have id' => [[2, 8, 4], 4, false],
      'value array have id but string' => [[2, 8, '4'], 4, true],
    ];
  }

  #[DataProvider('providerPatternIsNot')]
  public function testPatternIsNot($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternIsNot');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternContain(): array
  {
    return [
      'value null' => [null, 'test', false],
      'value contain' => ['my test title', 'test', true],
      'value contain with uppercase' => ['my teST Title', 'tEst', true],
      'value not contain' => ['my test title', 'tet', false],

      'value array not contain' => [['my id', 'my title'], 'test', false],
      'value array contain' => [['my id', 'my title'], 'id', true],
      'value array contain with uppercase' => [['my Id', 'my title'], 'iD', true],
    ];
  }

  #[DataProvider('providerPatternContain')]
  public function testPatternContain($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternContain');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternNotContain(): array
  {
    return [
      'value null' => [null, 'test', true],
      'value contain' => ['my test title', 'test', false],
      'value contain with uppercase' => ['my teST Title', 'tEst', false],
      'value not contain' => ['my test title', 'tet', true],

      'value array not contain' => [['my id', 'my title'], 'test', true],
      'value array contain' => [['my id', 'my title'], 'id', false],
      'value array contain with uppercase' => [['my Id', 'my title'], 'iD', false],
    ];
  }

  #[DataProvider('providerPatternNotContain')]
  public function testPatternNotContain($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternNotContain');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternBegin(): array
  {
    return [
      'value null' => [null, 'test', false],
      'value begin with' => ['my problem', 'my', true],
      'value begin with uppercase' => ['mYi problem', 'My', true],
      'value contain' => ['my problem', 'pro', false],
      'value end' => ['my problem', 'em', false],

      'value array not begin' => [['my id', 'my title'], 'title', false],
      'value array begin' => [['my id', 'my title'], 'my', true],
      'value array begin with uppercase' => [['My id', 'the title'], 'mY', true],
    ];
  }

  #[DataProvider('providerPatternBegin')]
  public function testPatternBegin($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternBegin');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternEnd(): array
  {
    return [
      'value null' => [null, 'test', false],
      'value begin with' => ['my problem', 'my', false],
      'value contain' => ['my problem', 'pro', false],
      'value end' => ['my problem', 'em', true],
      'value end with uppercase' => ['my problem', 'Em', true],

      'value array not end' => [['my id', 'my title'], 'my', false],
      'value array end' => [['my id', 'my title'], 'le', true],
      'value array end with uppercase' => [['my id', 'the titLe'], 'lE', true],
    ];
  }

  #[DataProvider('providerPatternEnd')]
  public function testPatternEnd($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternEnd');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternRegexMatch(): array
  {
    return [
      'value null' => [null, '^(\w+)$', false],
      'value match' => ['my test', '^([ \w]+)$', true],
      'value not match' => ['my test-56', '^([ \w]+)$', false],

      'value array not match' => [['my-test', 'I-am'], '^([ \w]+)$', false],
      'value array match' => [['my-test', 'I am'], '^([ \w]+)$', true],
    ];
  }

  #[DataProvider('providerPatternRegexMatch')]
  public function testPatternRegexMatch($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternRegexMatch');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternRegexNotMatch(): array
  {
    return [
      'value null' => [null, '^(\w+)$', true],
      'value match' => ['my test', '^([ \w]+)$', false],
      'value not match' => ['my test-56', '^([ \w]+)$', true],

      'value array not match' => [['my-test', 'I-am'], '^([ \w]+)$', true],
      'value array match' => [['my-test', 'I am'], '^([ \w]+)$', false],
    ];
  }

  #[DataProvider('providerPatternRegexNotMatch')]
  public function testPatternRegexNotMatch($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternRegexNotMatch');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternUnder(): array
  {
    return [
      'null value' => [null, 1, false],
      'under ok' => [3, 1, true],
      'not under' => [1, 3, false],
      'id not exists' => [10, 3, false],
      'same id' => [1, 1, true],

      'value array under' => [[1, 3], 2, true],
      'value array not under' => [[2, 1], 3, false],
      'value array empty' => [[], 3, false],
    ];
  }

  #[DataProvider('providerPatternUnder')]
  public function testPatternUnder($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternUnder');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, '\App\Models\Category', $value, $pattern);

    $this->assertEquals($ret, $expected);
  }

  public static function providerPatternNotUnder(): array
  {
    return [
      'null value' => [null, 1, true],
      'under ok' => [3, 1, false],
      'not under' => [1, 3, true],
      'id not exists' => [10, 3, true],
      'same id' => [1, 1, false],

      'value array under' => [[1, 3], 2, false],
      'value array not under' => [[2, 1], 3, true],
      'value array empty' => [[], 3, true],
    ];
  }

  #[DataProvider('providerPatternNotUnder')]
  public function testPatternNotUnder($value, $pattern, $expected): void
  {
    $ctrl = new \App\v1\Controllers\Rules\Criterium();
    $reflection = new \ReflectionClass($ctrl);
    $method = $reflection->getMethod('patternNotUnder');
    $method->setAccessible(true);

    $ret = $method->invoke($ctrl, '\App\Models\Category', $value, $pattern);

    $this->assertEquals($ret, $expected);
  }
}
