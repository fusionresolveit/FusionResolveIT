<?php

require_once __DIR__ . '/../vendor/autoload.php';

global $translator;
global $databaseName;
global $basePath;
global $phpunit;

$phpunit = true;
$basePath = "";

$GLOBALS['user_id'] = 1;
$GLOBALS['username'] = 'Theodore Admin';
$GLOBALS['profile_id'] = 1;
$GLOBALS['entity_id'] = 1;
$GLOBALS['entity_treepath'] = '00001';
$GLOBALS['entity_recursive'] = true;

$lang = new \App\Translation();
$translator = $lang->loadLanguage('en_GB');

$app = (new \App\App())->get();

session_start();
