<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250328073102 extends AbstractMigration
{
  public function change(): void
  {
    $sections = $this->table('categories');
    $sections->changeColumn('is_incident', 'boolean', ['null' => false, 'default' => false])
             ->changeColumn('is_request', 'boolean', ['null' => false, 'default' => false])
             ->changeColumn('is_problem', 'boolean', ['null' => false, 'default' => false])
             ->changeColumn('is_change', 'boolean', ['null' => false, 'default' => false])
             ->addColumn('is_knowledge', 'boolean', ['null' => false, 'default' => false])
             ->addColumn('is_form', 'boolean', ['null' => false, 'default' => false])
             ->save();
  }
}
