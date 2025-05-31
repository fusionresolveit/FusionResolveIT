<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class UserDarkTheme extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('users');
    $table->addColumn('dark_mode', 'boolean', ['null' => false, 'default' => false])
          ->update();
  }
}
