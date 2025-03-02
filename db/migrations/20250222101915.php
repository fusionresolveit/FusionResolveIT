<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class V20250222101915 extends AbstractMigration
{
  /**
   * Convert ruleactions table with action_type from string to int
   */
  public function up(): void
  {
    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);

    // Convert data
    $actions = $this->fetchAll('SELECT * FROM ruleactions');
    foreach ($actions as $action)
    {
      $type = null;
      switch ($action['action_type'])
      {
        case 'assign':
          $type = 0;
            break;

        case 'assign_dropdown':
          $type = 1;
            break;

        case 'append':
          $type = 2;
            break;

        case 'append_dropdown':
          $type = 3;
            break;

        case 'regex_result':
          $type = 4;
            break;

        case 'append_regex_result':
          $type = 5;
            break;
      }
      if (is_null($type))
      {
        // set assign by default, but disable rule
        $this->execute('UPDATE ruleactions SET action_type = 0 WHERE id = ?', [$action['id']]);
        $this->execute('UPDATE rules SET is_active = ? WHERE id = ?', [false, $action['id']]);
      } else {
        $this->execute('UPDATE ruleactions SET action_type = ? WHERE id = ?', [$type, $action['id']]);
      }
    }

    // modify the database filed type
    $ruleactions = $this->table('ruleactions');
    if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
    {
      $ruleactions->changeColumn(
        'action_type',
        Literal::from('integer USING action_type::integer'),
        ['null' => false, 'default' => 0]
      )
                  ->save();
    } else {
      $ruleactions->changeColumn('action_type', 'integer', ['null' => false, 'default' => 0])
                  ->save();
    }
  }

  /**
   * Migrate Down.
   */
  public function down(): void
  {
    $ruleactions = $this->table('ruleactions');
    $ruleactions->changeColumn('action_type', 'string', ['null' => true])
                ->save();
  }
}
