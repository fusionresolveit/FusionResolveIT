<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class SoftwarelicensesMigration extends AbstractMigration
{
  public function change(): void
  {
    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');

      $oldEnv = $config->getEnvironment('old');
      if (is_null($oldEnv))
      {
        throw new \Exception('Error', 500);
      }

      $environment = new Environment('old', $oldEnv);
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('softwarelicenses');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_softwarelicenses');
      if ($stmt === false)
      {
        throw new \Exception('Error', 500);
      }
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'software_id'             => $row['softwares_id'],
            'softwarelicense_id'      => $row['softwarelicenses_id'],
            'completename'            => $row['completename'],
            'level'                   => $row['level'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'number'                  => $row['number'],
            'softwarelicensetype_id'  => $row['softwarelicensetypes_id'],
            'name'                    => $row['name'],
            'serial'                  => $row['serial'],
            'otherserial'             => $row['otherserial'],
            'softwareversion_id_buy'  => $row['softwareversions_id_buy'],
            'softwareversion_id_use'  => $row['softwareversions_id_use'],
            'expire'                  => $row['expire'],
            'comment'                 => $row['comment'],
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'is_valid'                => $row['is_valid'],
            'created_at'              => Toolbox::fixDate($row['date_creation']),
            'location_id'             => $row['locations_id'],
            'user_id_tech'            => $row['users_id_tech'],
            'user_id'                 => $row['users_id'],
            'group_id_tech'           => $row['groups_id_tech'],
            'group_id'                => $row['groups_id'],
            'is_helpdesk_visible'     => $row['is_helpdesk_visible'],
            'is_template'             => $row['is_template'],
            'template_name'           => $row['template_name'],
            'state_id'                => $row['states_id'],
            'manufacturer_id'         => $row['manufacturers_id'],
            'contact'                 => $row['contact'],
            'contact_num'             => $row['contact_num'],
            'allow_overquota'         => $row['allow_overquota'],
            'deleted_at'              => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('softwarelicenses_id_seq', (SELECT MAX(id) FROM softwarelicenses)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertIsDeleted(int $is_deleted): string|null
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
  }
}
