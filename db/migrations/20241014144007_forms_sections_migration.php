<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FormsSectionsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('form_section');
    $table->addColumn('form_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('section_id', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['form_id', 'section_id'], ['unique' => true])
          ->create();


    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');
      $environment = new Environment('old', $config->getEnvironment('old'));
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('form_section');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_sections')->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query(
          'SELECT * FROM glpi_plugin_formcreator_sections ORDER BY id LIMIT 5000 OFFSET ' . ($i * 5000)
        );
        $rows = $stmt->fetchAll();
        foreach ($rows as $row)
        {
          $data = [
            [
              'form_id'       => $row['plugin_formcreator_forms_id'],
              'section_id'    => $row['id'],
            ]
          ];

          $item->insert($data)
                    ->saveData();
        }
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}