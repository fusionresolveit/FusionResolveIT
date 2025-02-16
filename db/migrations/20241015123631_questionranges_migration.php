<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class QuestionrangesMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('questions');
    $table->addColumn('range_min', 'string', ['null' => true])
          ->addColumn('range_max', 'string', ['null' => true])
          ->update();


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
    $item = $this->table('questions');

    if ($this->isMigratingUp())
    {
      if (!$this->hasTable('glpi_plugin_formcreator_questionranges'))
      {
        return;
      }

      $query = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_questionranges');
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
        $stmt = $pdo->query(
          'SELECT * FROM glpi_plugin_formcreator_questionranges ORDER BY id LIMIT 5000 OFFSET ' . ($i * 5000)
        );

        if ($stmt === false)
        {
          throw new \Exception('Error', 500);
        }
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $this->execute(
            'UPDATE questions SET range_min = ? WHERE id = ?',
            [$row['range_min'], $row['plugin_formcreator_questions_id']]
          );
          $this->execute(
            'UPDATE questions SET range_max = ? WHERE id = ?',
            [$row['range_max'], $row['plugin_formcreator_questions_id']]
          );
        }
      }
    }
  }
}
