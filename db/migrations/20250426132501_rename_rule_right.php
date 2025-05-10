<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameRuleRight extends AbstractMigration
{
  public function up(): void
  {
    $this->execute('UPDATE rules SET sub_type = ? WHERE sub_type = ?', ['user', 'RuleRight']);
    $this->execute('UPDATE profilerights SET model = ? WHERE model = ?', ['App\Models\Rules\User', 'App\Models\Rules\Right']);
  }

  public function down(): void
  {
    $this->execute('UPDATE rules SET sub_type = ? WHERE sub_type = ?', ['RuleRight', 'user']);
    $this->execute('UPDATE profilerights SET model = ? WHERE model = ?', ['App\Models\Rules\Right', 'App\Models\Rules\User']);
  }
}
