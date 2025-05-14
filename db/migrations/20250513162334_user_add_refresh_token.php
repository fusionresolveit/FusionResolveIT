<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class UserAddRefreshToken extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('users');
    $table->addColumn('refreshtoken', 'string', ['null' => true])
          ->save();
  }
}
