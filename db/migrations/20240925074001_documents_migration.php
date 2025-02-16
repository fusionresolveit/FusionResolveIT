<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DocumentsMigration extends AbstractMigration
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

      $chunkSize = 5000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 3500;
      }
    } else {
      return;
    }
    $item = $this->table('documents');

    if ($this->isMigratingUp())
    {
      $query = $pdo->query('SELECT count(*) FROM glpi_documents');
      if ($query === false)
      {
        throw new \Exception('Error', 500);
      }

      $nbRows = $query->fetchColumn();
      if ($nbRows === false || is_null($nbRows))
      {
        throw new \Exception('Error', 500);
      }
      $nbLoops = ceil(intval($nbRows) / $chunkSize);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_documents ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));

        if ($stmt === false)
        {
          throw new \Exception('Error', 500);
        }
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'                  => $row['id'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'name'                => $row['name'],
            'filename'            => $row['filename'],
            'filepath'            => $row['filepath'],
            'documentcategory_id' => $row['documentcategories_id'],
            'mime'                => $row['mime'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'comment'             => $row['comment'],
            'link'                => $row['link'],
            'user_id'             => $row['users_id'],
            'ticket_id'           => $row['tickets_id'],
            'sha1sum'             => $row['sha1sum'],
            'is_blacklisted'      => $row['is_blacklisted'],
            'tag'                 => $row['tag'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('documents_id_seq', (SELECT MAX(id) FROM documents)+1)");
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
