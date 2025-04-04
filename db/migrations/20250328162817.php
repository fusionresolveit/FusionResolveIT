<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250328162817 extends AbstractMigration
{
  public function change(): void
  {
    $knowbaseitem = $this->table('knowbaseitems');
    $knowbaseitem->renameColumn('knowbaseitemcategory_id', 'category_id')
                 ->save();
  }
}
