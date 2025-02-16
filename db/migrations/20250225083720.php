<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250225083720 extends AbstractMigration
{
  public function change(): void
  {
    $events = $this->table('events');
    $events->renameColumn('date', 'created_at')
            ->addColumn('updated_at', 'timestamp', ['null' => true])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
           ->save();
  }
}
