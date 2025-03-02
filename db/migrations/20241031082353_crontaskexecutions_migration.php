<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class CrontaskexecutionsMigration extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('crontasklogs');
    $table->rename('crontaskexecutions')
          ->update();

    $table = $this->table('crontaskexecutions');
    $table->removeColumn('crontasklog_id')
          ->renameColumn('date', 'created_at')
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->removeColumn('elapsed')
          ->removeColumn('volume')
          ->removeColumn('content')
          ->addColumn('execution_duration', 'integer', ['null' => false, 'default' => 0])
          ->update();

    $table = $this->table('crontaskexecutionlogs');
    $table->addColumn('crontaskexecution_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('ended_at', 'timestamp', ['null' => true])
          ->addColumn('volume', 'integer', ['null' => false])
          ->addColumn('content', 'string', ['null' => true])
          ->create();

          // `crontask_id` int(11) NOT NULL,
          // // `crontasklog_id` int(11) NOT NULL,
          // // `date` timestamp NOT NULL DEFAULT current_timestamp(),
          // ->addColumn('created_at', 'timestamp', ['null' => true])
          // ->addColumn('updated_at', 'timestamp', ['null' => true])

          // `state` int(11) NOT NULL,
          // // `elapsed` float NOT NULL,
          // // `volume` int(11) NOT NULL,
          // // `content` varchar(255) DEFAULT NULL,
          // execution_duration

          // // `crontask_id` int(11) NOT NULL,
          // // `crontasklog_id` int(11) NOT NULL,
          // `crontaskexecution_id` int(11) NOT NULL,
          // // `date` timestamp NOT NULL DEFAULT current_timestamp(),
          // ->addColumn('created_at', 'timestamp', ['null' => true])
          // ->addColumn('updated_at', 'timestamp', ['null' => true])
          // ->addColumn('ended_at', 'timestamp', ['null' => true])
          // // `state` int(11) NOT NULL,
          // // `elapsed` float NOT NULL,
          // `volume` int(11) NOT NULL,
          // `content` varchar(255) DEFAULT NULL,
  }

  public function down(): void
  {
    $table = $this->table('crontaskexecutions');
    $table->rename('crontasklogs')
          ->update();

    $table = $this->table('crontasklogs');
    $table->addColumn('crontasklog_id', 'integer', ['null' => false])
          ->renameColumn('created_at', 'date')
          ->removeColumn('updated_at')
          ->addColumn('elapsed', 'float', ['null' => false])
          ->addColumn('volume', 'integer', ['null' => false])
          ->addColumn('content', 'string', ['null' => true])
          ->removeColumn('execution_duration')
          ->update();

    $this->table('crontaskexecutionlogs')->drop()->save();
  }
}
