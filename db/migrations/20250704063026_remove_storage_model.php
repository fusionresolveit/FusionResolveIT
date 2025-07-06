<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RemoveStorageModel extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('storages');
    $table->removeColumn('storagemodel_id')
          ->update();

    $this->execute('DROP TABLE storagemodels');

    // remove in profilerights
    $this->execute(
      'DELETE FROM profilerights WHERE model = ?',
      ["App\Models\Storagemodel"]
    );

    // remove displaypreferences
    $this->execute(
      'DELETE FROM displaypreferences WHERE itemtype = ?',
      ["App\Models\Storagemodel"]
    );

    // remove displaypreferences
    $this->execute(
      'DELETE FROM displaypreferences WHERE itemtype = ? and num = ?',
      ["App\Models\Storage", '15']
    );
  }

  public function down(): void
  {
    $table = $this->table('storages');
    $table->addColumn('storagemodel_id', 'integer', ['null' => true])
          ->update();

    $table = $this->table('storagemodels');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('product_number', 'string', ['null' => true])
          ->addIndex(['name'])
          ->addIndex(['product_number'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();
  }
}
