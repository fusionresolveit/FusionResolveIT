<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use PhpParser\Node\Stmt;

final class V20250221062313 extends AbstractMigration
{
  public function up(): void
  {
    // if table exists, drop it
    $this->table('alerts')->drop()->save();

    $table = $this->table('alerts');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('message', 'string', ['null' => true])
          ->addColumn('begin_date', 'date', ['null' => true])
          ->addColumn('end_date', 'date', ['null' => true])
          ->addColumn('type', 'integer', ['null' => false, 'default' => '1'])
          ->addColumn('is_displayed_onlogin', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('is_displayed_oncentral', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('is_active', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addIndex(['name'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_recursive'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    if ($this->isMigratingUp())
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

        $chunkSize = 2000;
        if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
        {
          $chunkSize = 1500;
        }
      } else {
        return;
      }

      try {
        $result = $pdo->query("SELECT 1 FROM glpi_plugin_news_alerts LIMIT 1");
      }
      catch (Exception $e)
      {
        // the table not exists, so no data to migrate
        return;
      }
      $alerts = $this->table('alerts');
      $stmt = $pdo->query('SELECT * FROM glpi_plugin_news_alerts ORDER BY id');
      if ($stmt !== false)
      {
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'                      => $row['id'],
            'name'                    => $row['name'],
            'comment'                 => $row['comment'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'message'                 => $row['message'],
            'begin_date'              => Toolbox::fixDate($row['date_start']),
            'end_date'                => Toolbox::fixDate($row['date_end']),
            'type'                    => $row['type'],
            'is_displayed_onlogin'    => $row['is_displayed_onlogin'],
            'is_displayed_oncentral'  => $row['is_displayed_oncentral'],
            'is_activate'             => $row['is_activate'],
            'created_at'              => Toolbox::fixDate($row['date_mod']),
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'deleted_at'              => self::convertIsDeleted($row['is_deleted']),
          ];
        }
        $alerts->insert($data)
               ->saveData();
        if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
        {
          $this->execute("SELECT setval('alerts_id_seq', (SELECT MAX(id) FROM alerts)+1)");
        }
      }
    }
  }

  public function down(): void
  {
    $alerts = $this->table('alerts');
    // rollback
    $alerts->truncate();
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
