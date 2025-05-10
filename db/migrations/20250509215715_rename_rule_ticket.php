<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class RenameRuleTicket extends AbstractMigration
{
  public function up(): void
  {
    $this->execute('UPDATE rules SET sub_type = ? WHERE sub_type = ?', ['ticket', 'RuleTicket']);
  }

  public function down(): void
  {
    $this->execute('UPDATE rules SET sub_type = ? WHERE sub_type = ?', ['RuleTicket', 'ticket']);
  }
}
