<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250314072916 extends AbstractMigration
{
  public function change(): void
  {
    $users = $this->table('users');
    $users
      ->addColumn('security_attempt', 'integer', ['null' => false, 'default' => 0])
      ->addColumn('security_last_attempt', 'timestamp', ['null' => true])
      ->save();
  }
}
