<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class QuestionregexesMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('questions');
    $table->addColumn('regex', 'string', ['null' => true])
          ->update();

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
    $item = $this->table('questions');

    if ($this->isMigratingUp())
    {
      if (!$this->hasTable('glpi_plugin_formcreator_questionregexes'))
      {
        return;
      }
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_questionregexes')->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query(
          'SELECT * FROM glpi_plugin_formcreator_questionregexes ORDER BY id LIMIT 5000 OFFSET ' . ($i * 5000)
        );

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $this->execute(
            'UPDATE questions SET regex = ? WHERE id = ?',
            [$row['regex'], $row['plugin_formcreator_questions_id']]
          );
        }
      }
    }
  }
}
