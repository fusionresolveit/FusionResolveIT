<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FormsAnswersMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('answers');
    $table->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('form_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('user_id', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['entity_id'])
          ->addIndex(['form_id'])
          ->addIndex(['user_id'])
          ->addIndex(['created_at'])
          ->create();


    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    $item = $this->table('answers');

    if ($this->isMigratingUp())
    {
      $configArray = require('phinx.php');
      $environments = array_keys($configArray['environments']);
      if (in_array('old', $environments))
      {
        // Migration of database
        if (!$this->hasTable('glpi_plugin_formcreator_formanswers'))
        {
          return;
        }
        $config = Config::fromPhp('phinx.php');

        $oldEnv = $config->getEnvironment('old');
        if (is_null($oldEnv))
        {
          throw new \Exception('Error', 500);
        }

        $environment = new Environment('old', $oldEnv);
        $pdo = $environment->getAdapter()->getConnection();

        $query = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_formanswers');
        if ($query === false)
        {
          throw new \Exception('Error', 500);
        }

        $nbRows = $query->fetchColumn();
        if ($nbRows === false || is_null($nbRows))
        {
          throw new \Exception('Error', 500);
        }
        $nbLoops = ceil(intval($nbRows) / 5000);

        for ($i = 0; $i < $nbLoops; $i++)
        {
          $stmt = $pdo->query('SELECT * FROM glpi_plugin_formcreator_formanswers ORDER BY id LIMIT 5000 OFFSET ' .
                  ($i * 5000));
          if ($stmt === false)
          {
            throw new \Exception('Error', 500);
          }
          $rows = $stmt->fetchAll();
          $data = [];
          foreach ($rows as $row)
          {
            $data[] = [
              'entity_id'         => $row['entities_id'],
              'form_id'           => $row['plugin_formcreator_forms_id'],
              'user_id'           => $row['requester_id'],
              'created_at'        => Toolbox::fixDate($row['request_date']),
            ];
          }
          $item->insert($data)
               ->saveData();
        }
        if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
        {
          $this->execute("SELECT setval('answers_id_seq', (SELECT MAX(id) FROM answers)+1)");
        } else {
          return;
        }
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
