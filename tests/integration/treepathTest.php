<?php

declare(strict_types=1);

namespace Tests\integration;

use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Traits\HttpTestTrait;

#[CoversClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\DataInterface\Definition')]
#[UsesClass('\App\DataInterface\DefinitionCollection')]
#[UsesClass('\App\Events\EntityCreating')]
#[UsesClass('\App\Events\TreepathCreated')]
#[UsesClass('\App\Events\TreepathUpdating')]
#[UsesClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Knowledgebasearticle')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Traits\Relationships\Documents')]
#[UsesClass('\App\Traits\Relationships\Entity')]
#[UsesClass('\App\Traits\Relationships\Knowledgebasearticles')]
#[UsesClass('\App\Traits\Relationships\Notes')]

class treepathTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    // Clean entities
    $entities = \App\Models\Entity::where('id', '>', 1)->get();
    foreach ($entities as $entity)
    {
      $entity->forceDelete();
    }
    $dbConfig = Manager::connection()->getConfig();
    if ($dbConfig['driver'] == 'mysql')
    {
      Manager::connection()->statement("ALTER TABLE entities AUTO_INCREMENT = 123;");
    } else { //postgresql
      Manager::connection()->statement("ALTER SEQUENCE entities_id_seq RESTART WITH 123;");
    }
  }

  public static function tearDownAfterClass(): void
  {
    // Clean entities
    $entities = \App\Models\Entity::where('id', '>', 1)->get();
    foreach ($entities as $entity)
    {
      $entity->forceDelete();
    }
  }

  public function testTreepathFilled(): void
  {
    // create sub entity
    $entity2 = \App\Models\Entity::create([
      'name'      => 'ent 2',
      'entity_id' => 1
    ]);
    $this->assertNotNull($entity2, 'ent 2 not created');
    $entity2->refresh();
    $this->assertEquals(10, strlen($entity2->treepath));
    $this->assertStringStartsWith('00001', $entity2->treepath);
    $this->assertEquals('0000100123', $entity2->treepath);

    // create sub-sub entity
    $entity3 = \App\Models\Entity::create([
      'name'      => 'ent 3',
      'entity_id' => $entity2->id,
    ]);
    $this->assertNotNull($entity3, 'ent 3 not created');
    $entity3->refresh();
    $this->assertEquals(15, strlen($entity3->treepath));
    $this->assertStringStartsWith('00001', $entity3->treepath);
    $this->assertEquals('000010012300124', $entity3->treepath);
  }

  /**
   * @depends testTreepathFilled
   */
  public function testMoveThirdOnFirst()
  {
    $entity3 = \App\Models\Entity::where('name', 'ent 3')->first();
    $this->assertNotNull($entity3);

    $entity3->entity_id = 1;
    $entity3->save();
    $entity3->refresh();
    $this->assertEquals('0000100124', $entity3->treepath);
  }

  /**
   * @depends testTreepathFilled
   */
  public function testNoErrorOnlyWhenChangeName()
  {
    $entity2 = \App\Models\Entity::where('name', 'ent 2')->first();
    $this->assertNotNull($entity2);

    $entity2->name = 'ent 2.0';
    $entity2->save();
    $entity2->refresh();
    $this->assertEquals('0000100123', $entity2->treepath);
    $this->assertEquals('ent 2.0', $entity2->name);
  }
}
