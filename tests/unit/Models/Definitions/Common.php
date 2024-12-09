<?php

declare(strict_types=1);

namespace Tests\unit\Models\Definitions;

use PHPUnit\Framework\TestCase;

class Common extends TestCase
{
  protected $className = '';

  public function testUniqueIds(): void
  {
    $className = '\\App\\Models\\Definitions\\' . $this->className;
    $def = $className::getDefinition();

    $ids = [];
    foreach ($def as $field)
    {
      if (!isset($ids[$field['id']]))
      {
        $ids[$field['id']] = 0;
      }
      $ids[$field['id']]++;
    }
    $doubles = [];
    foreach ($ids as $id => $nb)
    {
      if ($nb > 1)
      {
        $doubles[] = $id;
      }
    }
    $this->assertEquals([], $doubles, 'Must not have two same id in definition');
  }

  public function testMandatoryFields()
  {
    $className = '\\App\\Models\\Definitions\\' . $this->className;
    $def = $className::getDefinition();

    $ids = [];
    foreach ($def as $field)
    {
      $this->assertArrayHasKey('id', $field, 'id not exists');
      $this->assertArrayHasKey('title', $field, 'title not exists');
      $this->assertArrayHasKey('type', $field, 'type not exists');
      $this->assertArrayHasKey('name', $field, 'name not exists');
    }
  }

  public function testValuesHaveTitle()
  {
    $className = '\\App\\Models\\Definitions\\' . $this->className;
    $def = $className::getDefinition();
    $nbTests = 0;


    foreach ($def as $field)
    {
      if (isset($field['values']))
      {
        $this->assertIsArray($field['values']);
        foreach ($field['values'] as $values)
        {
          $nbTests++;
          $this->assertArrayHasKey('title', $values, 'values must have property title');
        }
      }
    }
    // for the case we not have values in the class definition
    if ($nbTests == 0)
    {
      $this->expectNotToPerformAssertions();
    }
  }

  public function testOptionnalFieldMultiple()
  {
    $className = '\\App\\Models\\Definitions\\' . $this->className;
    $def = $className::getDefinition();
    $nbTests = 0;


    foreach ($def as $field)
    {
      if (isset($field['multiple']))
      {
        $nbTests++;
        $this->assertIsBool($field['multiple'], 'field `multiple` must be a boolean type');
      }
    }
    // for the case we not have values in the class definition
    if ($nbTests == 0)
    {
      $this->expectNotToPerformAssertions();
    }
  }
}
