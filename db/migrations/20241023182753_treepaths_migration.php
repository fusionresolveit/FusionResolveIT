<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class TreepathsMigration extends AbstractMigration
{
  protected $treepaths = [];

  public function change(): void
  {
    // we set fields and recalculate treepath for items in tree
    $table = $this->table('entities');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('categories');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('groups');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('knowbaseitemcategories');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('locations');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('softwarelicensetypes');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('softwarecategories');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('states');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('businesscriticities');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('documentcategories');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    $table = $this->table('ipnetworks');
    $table->addColumn('treepath', 'string', ['null' => true])
          ->update();

    if ($this->isMigratingUp())
    {
      $this->treepaths = [];
      $this->generateTreepath('-1', 'entities', 'entity_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'categories', 'category_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'groups', 'group_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'knowbaseitemcategories', 'knowbaseitemcategory_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'locations', 'location_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'softwarelicensetypes', 'softwarelicensetype_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'softwarecategories', 'softwarecategory_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'states', 'state_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'businesscriticities', 'businesscriticity_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'documentcategories', 'documentcategory_id');

      $this->treepaths = [];
      $this->generateTreepath('0', 'ipnetworks', 'ipnetwork_id');
    }


    $table = $this->table('entities');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('groups');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('knowbaseitemcategories');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('locations');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('softwarelicensetypes');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('softwarecategories');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('states');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('businesscriticities');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('documentcategories');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();

    $table = $this->table('ipnetworks');
    $table->removeColumn('level')
          ->removeColumn('sons_cache')
          ->removeColumn('ancestors_cache')
          ->update();
  }

  private function generateTreepath($id, $table, $foreignkey)
  {
    $builderSelect = $this->getQueryBuilder('select');
    $statement = $builderSelect->select('*')->from($table)->where($foreignkey . " = '" . $id . "'")->execute();
    $items = $statement->fetchAll('assoc');
    $itemList = [];
    foreach ($items as $item)
    {
      $itemList[] = $item['id'];
      // generate treepath
      $treepath = sprintf("%05d", $item['id']);
      if (isset($this->treepaths[$item[$foreignkey]]))
      {
        $treepath = $this->treepaths[$item[$foreignkey]] . $treepath;
      }
      $this->treepaths[$item['id']] = $treepath;

      $builderUpdate = $this->getQueryBuilder('update');
      $builderUpdate
        ->update($table)
        ->set('treepath', $treepath)
        ->where(['id' => $item['id']])
        ->execute();
    }
    foreach ($itemList as $itemId)
    {
      $this->generateTreepath($itemId, $table, $foreignkey);
    }
  }
}
