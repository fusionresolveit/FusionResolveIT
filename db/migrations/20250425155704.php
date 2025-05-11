<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250425155704 extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('authssoscopes');
    $table->addColumn('mapping_field', 'string', ['null' => true])
          ->save();
  }
}
