<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

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

    $items = $this->fetchAll('SELECT * FROM ruleactions');
    foreach ($items as $item)
    {
      if (isset($mapping[$item['field']]))
      {
        $this->execute('UPDATE ruleactions SET field = ? WHERE id = ?', [$mapping[$item['field']], $item['id']]);
      }
    }
  }
}
