<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250410104828 extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('knowledgebasearticle_revision');
    $table->rename('knowledgebasearticlerevisions')
          ->renameColumn('answer', 'article')
          ->save();
  }
}
