<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FillemptydatabaseMigration extends AbstractMigration
{
  public function change(): void
  {
    if ($this->isMigratingUp())
    {
      $builderSelect = $this->getQueryBuilder('select');
      $statement = $builderSelect->select('*')->from('entities')->execute();
      $items = $statement->fetchAll('assoc');
      if (count($items) > 0)
      {
        return;
      }
      // Now Fill with default data

      $item = $this->table('entities');
      $data = [
        [
          'name' => 'main',
          'treepath' => 00001,
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create user
      $item = $this->table('users');
      $data = [
        [
          'name' => 'admin',
          'entity_id' => 1,
          'lastname' => 'Administrator',
          'password' => \App\v1\Controllers\Token::generateDBHashPassword('adminIT'),
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create profile
      $item = $this->table('profiles');
      $data = [
        [
          'name' => 'super-admin',
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create profile rights
      $item = $this->table('profilerights');
      $profileright = new \App\v1\Controllers\Profile();
      $models = $profileright->getRigthCategories();
      $data = [];
      foreach ($models as $modellist)
      {
        foreach ($modellist as $model)
        {
          $data[] = [
            'model'       => ltrim($model, '\\'),
            'profile_id'  => 1,
            'read'        => true,
            'create'      => true,
            'update'      => true,
            'softdelete'  => true,
            'delete'      => true,
          ];
        }
      }
      $item->insert($data)
           ->saveData();

      // Add profile to admin
      $item = $this->table('profile_user');
      $data = [
        [
          'user_id'    => 1,
          'profile_id' => 1,
        ]
      ];
      $item->insert($data)
           ->saveData();
    }
  }
}
