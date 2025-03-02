<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class AnswerquestionsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('answerquestions');
    $table->addColumn('answer_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('question_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('answer', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG])
          ->addIndex(['answer_id', 'question_id'], ['unique' => true])
          ->save();

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
    $item = $this->table('answerquestions');

    if ($this->isMigratingUp())
    {
      if (!$this->hasTable('glpi_plugin_formcreator_formanswers'))
      {
        return;
      }
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
        $stmt = $pdo->query('SELECT * FROM glpi_plugin_formcreator_answers ORDER BY id LIMIT 5000 OFFSET ' .
                ($i * 5000));
        if ($stmt === false)
        {
          throw new \Exception('Error', 500);
        }
        $rows = $stmt->fetchAll();
        foreach ($rows as $row)
        {
          if ($row['plugin_formcreator_formanswers_id'] == 0)
          {
            continue;
          }
          $data = [
            [
              'answer_id'          => $row['plugin_formcreator_formanswers_id'],
              'question_id'       => $row['plugin_formcreator_questions_id'],
              'answer'            => $row['answer'],
            ]
          ];

          $item->insert($data)
               ->saveData();
        }
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('answerquestions_id_seq', (SELECT MAX(id) FROM answerquestions)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
