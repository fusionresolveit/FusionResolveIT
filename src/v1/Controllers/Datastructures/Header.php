<?php

namespace App\v1\Controllers\Datastructures;

use Illuminate\Support\Pluralizer;

trait Header
{
  public function initHeaderData($item, $request)
  {
    $this->header->title = 'GSIT - ' . $item->getTitle(1);
    $menu = new \App\v1\Controllers\Menu();
    $this->header->menu = $menu->getMenu($request);
    $this->header->rootpath = \App\v1\Controllers\Toolbox::getRootPath($request);
    $this->header->name = $item->getTitle(1);
    $this->header->id = null;
    $this->header->icon = 'vector square';
    $this->header->color = 'blue';
    $this->header->route = Pluralizer::plural(strtolower((new \ReflectionClass($item))->getShortName()));
    $this->header->username = $GLOBALS['username'];

    $this->setProfile();

    $this->addHeaderId($item->id);
    $this->addHeaderIcon($item->getIcon());
    if (property_exists($item, 'getColor'))
    {
      $this->addHeaderColor($item->getColor());
    }
  }

  public function addHeaderTitle($title)
  {
    $this->header->title = $title;
  }

  public function addHeaderMenu($menu)
  {
    $this->header->menu = $menu;
  }

  public function addHeaderRootpath($rootpath)
  {
    $this->header->rootpath = $rootpath;
  }

  public function addHeaderName($name)
  {
    $this->header->name = $name;
  }

  public function addHeaderId($id)
  {
    $this->header->id = $id;
  }

  public function addHeaderIcon($icon)
  {
    $this->header->icon = $icon;
  }

  public function addHeaderColor($color)
  {
    $this->header->color = $color;
  }

  private function setProfile()
  {
    global $translator;

    $profiles = [];
    $entities = [];

    $user = \App\Models\User::find($GLOBALS['user_id']);
    foreach ($user->profiles()->get() as $profile)
    {
      $profiles[$profile->id] = [
        'title' => $profile->name,
      ];
    }

    $dbEntities = \App\Models\Entity::orderBy('treepath')->orderBy('name')->get();
    foreach ($dbEntities as $entity)
    {
      $nb = strlen($entity->treepath) / 5;
      $class = 'treelvl' . $nb;

      $entities[$entity->id] = [
        'title' => $entity->name,
        'class' => $class,
      ];
      if ($GLOBALS['entity_id'] == $entity->id)
      {
        $this->header->entityname = $entity->name;
      }
    }

    $this->header->profile = [
      [
        'id'    => 1,
        'title' => $translator->translatePlural('Profile', 'Profiles', 1),
        'type'  => 'dropdown',
        'name'  => 'changeProfile',
        'values' => $profiles,
        'value' => $GLOBALS['profile_id'],
        'fillable' => true,
      ],
      [
        'id'    => 2,
        'title' => $translator->translatePlural('Entity', 'Entities', 1),
        'type'  => 'dropdown',
        'name'  => 'changeEntity',
        'values' => $entities,
        'value' => $GLOBALS['entity_id'],
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Sub-entities'),
        'type'  => 'boolean',
        'name'  => 'changeEntityRecursive',
        'value' => $GLOBALS['entity_recursive'],
        'fillable' => true,
      ]
    ];
  }
}
