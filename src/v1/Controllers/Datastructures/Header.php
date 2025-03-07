<?php

declare(strict_types=1);

namespace App\v1\Controllers\Datastructures;

use Illuminate\Support\Pluralizer;
use Psr\Http\Message\ServerRequestInterface as Request;

trait Header
{
  /**
   * @template C of \App\Models\Common
   * @param C $item
   */
  public function initHeaderData($item, Request $request): void
  {
    $this->header->title = 'Fusion Resolve IT - ' . $item->getTitle(1);
    $menu = new \App\v1\Controllers\Menu();
    $this->header->menu = $menu->getMenu($request);
    $this->header->menubookmark = $menu->getMenubookmark($this->header->menu);
    $this->header->rootpath = \App\v1\Controllers\Toolbox::getRootPath($request);
    $this->header->name = $item->getTitle(1);
    $this->header->id = null;
    $this->header->icon = 'vector square';
    $this->header->color = 'blue';
    $this->header->route = Pluralizer::plural(strtolower((new \ReflectionClass($item))->getShortName()));
    $this->header->username = $GLOBALS['username'];
    $this->header->modelname = get_class($item);
    $this->header->trashed = false;
    if (method_exists($item, 'trashed'))
    {
      $this->header->trashed = $item->trashed();
    }

    $this->setProfile();

    if (!is_null($item->getAttribute('id')))
    {
      $this->addHeaderId($item->getAttribute('id'));
    }
    $this->addHeaderIcon($item->getIcon());
    if (method_exists($item, 'getColor'))
    {
      $this->addHeaderColor($item->getColor());
    }
    if ($this->header->trashed)
    {
      $this->header->color = 'red';
    }
  }

  public function addHeaderTitle(string $title): void
  {
    $this->header->title = $title;
  }

  /**
   * @param array<mixed> $menu
   */
  public function addHeaderMenu(array $menu): void
  {
    $this->header->menu = $menu;
  }

  public function addHeaderRootpath(string $rootpath): void
  {
    $this->header->rootpath = $rootpath;
  }

  public function addHeaderName(string $name): void
  {
    $this->header->name = $name;
  }

  public function addHeaderId(int $id): void
  {
    $this->header->id = $id;
  }

  public function addHeaderIcon(string $icon): void
  {
    $this->header->icon = $icon;
  }

  public function addHeaderColor(string $color): void
  {
    $this->header->color = $color;
  }

  public function addHeaderTrashed(): void
  {
    $this->header->trashed = true;
  }

  private function setProfile(): void
  {
    global $translator;

    $profiles = [];
    $entities = [];

    $user = \App\Models\User::where('id', $GLOBALS['user_id'])->first();
    if (is_null($user))
    {
      throw new \Exception('Id not found', 404);
    }
    foreach ($user->profiles()->get() as $profile)
    {
      $profiles[$profile->id] = [
        'title' => $profile->name,
      ];
    }

    $dbEntities = \App\Models\Entity::orderBy('treepath')->orderBy('name')->get();
    foreach ($dbEntities as $entity)
    {
      if (is_null($entity->treepath))
      {
        throw new \Exception('Error in entities', 500);
      }
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
