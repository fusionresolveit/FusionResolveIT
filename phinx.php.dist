<?php

return
[
  'paths' => [
    'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
    'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
  ],
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_environment' => 'production',
    'production' => [
      'adapter' => 'mysql',
      'host' => '127.0.0.1',
      'name' => 'fusionresolveit',
      'user' => 'fusionresolveit',
      'pass' => 'mypass',
      'port' => '3306',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
    ],
    'productionpgsql' => [
      'adapter' => 'pgsql',
      'host' => 'localhost',
      'name' => 'fusionresolveit',
      'user' => 'fusionresolveit',
      'pass' => 'mypass',
      'port' => '5432',
      'charset' => 'utf8',
      'collation' => 'utf8_general_ci',
    ],
    // 'old' => [
    //   'adapter' => 'mysql',
    //   'host' => '127.0.0.1',
    //   'name' => 'glpi',
    //   'user' => 'glpi',
    //   'pass' => 'glpi',
    //   'port' => '3306',
    //   'charset' => 'utf8mb4',
    //   'collation' => 'utf8_unicode_ci',
    //  ],
    'tests' => [
      'adapter' => 'mysql',
      'host' => '127.0.0.1',
      'name' => 'frittests',
      'user' => 'frittests',
      'pass' => 'frittests',
      'port' => '3306',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
    ],
  ],
  'version_order' => 'creation'
];
