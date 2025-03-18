<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250315155202 extends AbstractMigration
{
  public function up(): void
  {
    $table = $this->table('events');
    $table
      ->rename('audits')
      ->update();

    $table = $this->table('audits');
    $table
      ->addColumn('user_id', 'integer', ['null' => false, 'default' => 0])
      ->addColumn('username', 'string', ['null' => true])
      ->addColumn('ip', 'string', ['null' => true])
      ->addColumn('httpmethod', 'string', ['null' => true])
      ->addColumn('endpoint', 'string', ['null' => true])
      ->addColumn('httpcode', 'integer', ['null' => true])
      ->renameColumn('type', 'action')
      ->addColumn('item_type', 'string', ['null' => true])
      ->removeColumn('service')
      ->removeColumn('level')
      ->addColumn('subaction', 'string', ['null' => true, 'after' => 'action'])
      ->addIndex(['action', 'subaction', 'created_at'])
      ->update();

    // change display preferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Audit", "App\Models\Event"]
    );
    // change profilerights
    $this->execute('UPDATE profilerights SET model = ? WHERE model = ?', ["App\Models\Audit", "App\Models\Event"]);
  }

  public function down(): void
  {
    $table = $this->table('audits');
    $table
      ->rename('events')
      ->update();

    $table = $this->table('events');
    $table
      ->removeColumn('user_id')
      ->removeColumn('username')
      ->removeColumn('ip')
      ->removeColumn('httpmethod')
      ->removeColumn('endpoint')
      ->removeColumn('httpcode')
      ->renameColumn('action', 'type')
      ->removeColumn('item_type')
      ->addColumn('service', 'string', ['null' => true])
      ->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
      ->removeColumn('subaction')
      ->update();

    // change display preferences
    $this->execute(
      'UPDATE displaypreferences SET itemtype = ? WHERE itemtype = ?',
      ["App\Models\Event", "App\Models\Audit"]
    );
    // change profilerights
    $this->execute('UPDATE profilerights SET model = ? WHERE model = ?', ["App\Models\Event", "App\Models\Audit"]);
  }
}
