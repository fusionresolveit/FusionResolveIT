<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class HomeMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('homes');
    $table->addColumn('module', 'string', ['null' => true])
          ->addColumn('profile_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('user_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('column', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('row', 'integer', ['null' => false, 'default' => 0])
          ->create();

    $table = $this->table('homes');

    $data = [
      [
        'module'     => 'mytickets',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ]

      ,
      [
        'module'     => 'groupstickets',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'todayincidents',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'lastproblems',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'lastknowledgeitems',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'linkedincidents',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'lastescaladedtickets',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'knowledgelink',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],
      [
        'module'     => 'forms',
        'profile_id' => 0,
        'user_id'    => 0,
        'column'     => 1,
        'row'        => 1,
      ],


    ];
    $table->insert($data)
          ->saveData();
  }
}
