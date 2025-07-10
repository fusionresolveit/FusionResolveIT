<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class AddFusioninventoryInventoriedAt extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('computers');
    $table->addColumn('fusioninventoried_at', 'timestamp', ['null' => true])
          ->update();

    $table = $this->table('printers');
    $table->addColumn('fusioninventoried_at', 'timestamp', ['null' => true])
          ->update();

    $table = $this->table('networkequipments');
    $table->addColumn('fusioninventoried_at', 'timestamp', ['null' => true])
          ->update();

    $table = $this->table('storages');
    $table->addColumn('fusioninventoried_at', 'timestamp', ['null' => true])
          ->update();

    $table = $this->table('memorymodules');
    $table->addColumn('fusioninventoried_at', 'timestamp', ['null' => true])
          ->update();
  }
}
