<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameDevicememorymodel extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('devicememorymodels');
    $table->rename('memorymodels')
          ->update();

    $table = $this->table('memorymodules');
    $table->renameColumn('devicememorymodel_id', 'memorymodel_id')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Memorymodel", "App\Models\Devicememorymodel"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Memorymodel", "App\Models\Devicememorymodel"]
    );
  }

  public function down(): void
  {
    $table = $this->table('memorymodels');
    $table->rename('devicememorymodels')
          ->update();

    $table = $this->table('memorymodules');
    $table->renameColumn('memorymodel_id', 'devicememorymodel_id')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Devicememorymodel", "App\Models\Memorymodel"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Devicememorymodel", "App\Models\Memorymodel"]
    );
  }
}
