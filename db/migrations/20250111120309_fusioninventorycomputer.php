<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class Fusioninventorycomputer extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('deviceprocessors');
    $table->addColumn('cpuid', 'string', ['null' => true])
          ->addColumn('stepping', 'integer', ['null' => true])
          ->save();

    $table = $this->table('operatingsystemversions');
    $table->addColumn('is_lts', 'boolean', ['null' => false, 'default' => false])
          ->addIndex(['name', 'is_lts'], ['unique' => true])
          ->save();
  }
}
