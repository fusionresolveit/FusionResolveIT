<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class Menubookmarks extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('menubookmarks');
    $table->addColumn('user_id', 'integer', ['null' => false, 'default' => '0'])
          ->addColumn('endpoint', 'string', ['null' => false])
          ->addColumn('position', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['user_id', 'endpoint'], ['unique' => true])
          ->create();
  }
}
