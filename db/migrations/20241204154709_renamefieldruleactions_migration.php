<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class RenamefieldruleactionsMigration extends AbstractMigration
{
  public function change(): void
  {

    // ruleactions field mapping
    $mapping = [
      'entities_id'            => 'entity',
      'locations_id'           => 'location',
      '_groups_id_assign'      => 'techniciangroup',
      '_groups_id_requester'   => 'requestergroup',
      '_users_id_observer'     => 'watcher',
      'itilcategories_id'      => 'category',
      // olas_id_ttr            =>
      // slas_id_ttr            =>
      // slas_id_tto            =>
      '_groups_id_observer'    => 'watchergroup',
      '_users_id_requester'    => 'requester',
    ];

    $builderSelect = $this->getQueryBuilder('select');
    $statement = $builderSelect
      ->select('*')
      ->from('ruleactions')
      ->execute();
    $items = $statement->fetchAll('assoc');
    foreach ($items as $item)
    {
      if (isset($mapping[$item['field']]))
      {
        $builderUpdate = $this->getQueryBuilder('update');
        $builderUpdate
          ->update('ruleactions')
          ->set('field', $mapping[$item['field']])
          ->where(['id' => $item['id']])
          ->execute();
      }
    }
  }
}
