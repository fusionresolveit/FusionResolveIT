<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameDevicememorytype extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('devicememorytypes');
    $table->rename('memorytypes')
          ->update();

    $table = $this->table('memorymodules');
    $table->renameColumn('devicememorytype_id', 'memorytype_id')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Memorytype", "App\Models\Devicememorytype"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Memorytype", "App\Models\Devicememorytype"]
    );
  }

  public function down(): void
  {
    $table = $this->table('memorytypes');
    $table->rename('devicememorytypes')
          ->update();

    $table = $this->table('memorymodules');
    $table->renameColumn('memorytype_id', 'devicememorytype_id')
          ->update();

    // change profilerights
    $this->execute(
      'UPDATE profilerights SET model = ? WHERE model = ?',
      ["App\Models\Devicememorytype", "App\Models\Memorytype"]
    );

    // change displaypreferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Devicememorytype", "App\Models\Memorytype"]
    );
  }
}
