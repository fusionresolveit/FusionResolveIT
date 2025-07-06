<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RemoveDevicefirmwaretype extends AbstractMigration
{
  public function up(): void
  {
    $this->execute('DROP TABLE devicefirmwaretypes');

    // remove in profilerights
    $this->execute(
      'DELETE FROM profilerights WHERE model = ?',
      ["App\Models\Devicefirmwaretype"]
    );

    // remove displaypreferences
    $this->execute(
      'DELETE FROM displaypreferences WHERE itemtype = ?',
      ["App\Models\Devicefirmwaretype"]
    );

    $this->execute(
      'DELETE FROM displaypreferences WHERE num=? AND itemtype=?',
      [13, 'App\Models\Firmware']
    );
  }

  public function down(): void
  {
    $table = $this->table('devicefirmwaretypes');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addIndex(['name'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();
  }
}
