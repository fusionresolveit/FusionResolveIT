<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250327110324 extends AbstractMigration
{
  public function change(): void
  {
    $sections = $this->table('sections');
    $sections->removeColumn('order')
             ->addColumn('comment', 'text', ['null' => true])
             ->save();
  }
}
