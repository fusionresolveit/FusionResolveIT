<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class TicketsMigration extends AbstractMigration
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

      $chunkSize = 2000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 1500;
      }
    } else {
      return;
    }
    $ticket = $this->table('tickets');

    if ($this->isMigratingUp())
    {
      $query = $pdo->query('SELECT count(*) FROM glpi_tickets');
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
        $stmt = $pdo->query('SELECT * FROM glpi_tickets ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));
        if ($stmt === false)
        {
          throw new \Exception('Error', 500);
        }
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $closed_at = null;
          $solved_at = null;
          $updated_at = null;
          $time_to_resolve = null;
          $time_to_own = null;
          $begin_waiting_date = null;
          $ola_ttr_begin_date = null;
          $internal_time_to_resolve = null;
          $internal_time_to_own = null;
          $created_at = null;
          if (!is_null($row['closedate']))
          {
            $closed_at = Toolbox::fixDate($row['closedate']);
          }
          if (!is_null($row['solvedate']))
          {
            $solved_at = Toolbox::fixDate($row['solvedate']);
          }
          if (!is_null($row['date_mod']))
          {
            $updated_at = Toolbox::fixDate($row['date_mod']);
          }
          if (!is_null($row['time_to_resolve']))
          {
            $time_to_resolve = Toolbox::fixDate($row['time_to_resolve']);
          }
          if (!is_null($row['time_to_own']))
          {
            $time_to_own = Toolbox::fixDate($row['time_to_own']);
          }
          if (!is_null($row['begin_waiting_date']))
          {
            $begin_waiting_date = Toolbox::fixDate($row['begin_waiting_date']);
          }
          if (!is_null($row['ola_ttr_begin_date']))
          {
            $ola_ttr_begin_date = Toolbox::fixDate($row['ola_ttr_begin_date']);
          }
          if (!is_null($row['internal_time_to_resolve']))
          {
            $internal_time_to_resolve = Toolbox::fixDate($row['internal_time_to_resolve']);
          }
          if (!is_null($row['internal_time_to_own']))
          {
            $internal_time_to_own = Toolbox::fixDate($row['internal_time_to_own']);
          }
          if (!is_null($row['date']))
          {
            $created_at = Toolbox::fixDate($row['date']);
          }

          $data[] = [
            'id'                          => $row['id'],
            'entity_id'                   => ($row['entities_id'] + 1),
            'name'                        => $row['name'],
            'closed_at'                   => $closed_at,
            'solved_at'                   => $solved_at,
            'updated_at'                  => $updated_at,
            'user_id_lastupdater'         => $row['users_id_lastupdater'],
            'status'                      => $row['status'],
            'user_id_recipient'           => $row['users_id_recipient'],
            'requesttype_id'              => $row['requesttypes_id'],
            'content'                     => Toolbox::convertHtmlToMarkdown($row['content']),
            'urgency'                     => $row['urgency'],
            'impact'                      => $row['impact'],
            'priority'                    => $row['priority'],
            'category_id'                 => $row['itilcategories_id'],
            'type'                        => $row['type'],
            'global_validation'           => $row['global_validation'],
            'sla_id_ttr'                  => $row['slas_id_ttr'],
            'sla_id_tto'                  => $row['slas_id_tto'],
            'slalevel_id_ttr'             => $row['slalevels_id_ttr'],
            'time_to_resolve'             => $time_to_resolve,
            'time_to_own'                 => $time_to_own,
            'begin_waiting_date'          => $begin_waiting_date,
            'sla_waiting_duration'        => $row['sla_waiting_duration'],
            'ola_waiting_duration'        => $row['ola_waiting_duration'],
            'ola_id_tto'                  => $row['olas_id_tto'],
            'ola_id_ttr'                  => $row['olas_id_ttr'],
            'olalevel_id_ttr'             => $row['olalevels_id_ttr'],
            'ola_ttr_begin_date'          => $ola_ttr_begin_date,
            'internal_time_to_resolve'    => $internal_time_to_resolve,
            'internal_time_to_own'        => $internal_time_to_own,
            'waiting_duration'            => $row['waiting_duration'],
            'close_delay_stat'            => $row['close_delay_stat'],
            'solve_delay_stat'            => $row['solve_delay_stat'],
            'takeintoaccount_delay_stat'  => $row['takeintoaccount_delay_stat'],
            'actiontime'                  => $row['actiontime'],
            'location_id'                 => $row['locations_id'],
            'validation_percent'          => $row['validation_percent'],
            'created_at'                  => $created_at,
            'deleted_at'                  => self::convertIsDeleted($row['is_deleted']),
          ];
        }
        $ticket->insert($data)
              ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('tickets_id_seq', (SELECT MAX(id) FROM tickets)+1)");
      }
    } else {
      // rollback
      $ticket->truncate();
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
