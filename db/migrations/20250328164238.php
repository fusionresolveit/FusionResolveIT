<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class V20250328164238 extends AbstractMigration
{
  /** @var array<mixed> */
  protected $treepaths = [];

  public function up(): void
  {
    $mapping = [];
    $topParentIds = [];
    $categories = $this->table('categories');


    $stmt = $this->query('SELECT * FROM knowbaseitemcategories ORDER BY treepath', []);
    $rows = $stmt->fetchAll();
    foreach ($rows as $row)
    {
      $data = [
        'name'          => $row['name'],
        'comment'       => $row['comment'],
        'created_at'    => $row['created_at'],
        'updated_at'    => $row['updated_at'],
        'deleted_at'    => $row['deleted_at'],
        'entity_id'     => $row['entity_id'],
        'is_recursive'  => $row['is_recursive'],
        'is_knowledge'  => true,
        // 'treepath'      => $row['name'],
      ];
      if ($row['knowbaseitemcategory_id'] > 0 && isset($mapping[$row['knowbaseitemcategory_id']]))
      {
        $data['category_id'] = $mapping[$row['knowbaseitemcategory_id']];
      }

      $categories->insert($data)->saveData();
      // get ID inserted
      $newId = $this->getAdapter()->getConnection()->lastInsertId();

      $mapping[$row['id']] = $newId;
      if ($row['knowbaseitemcategory_id'] == 0)
      {
        $topParentIds[] = $newId;
        $treepath = sprintf("%05d", $newId);
        $this->treepaths[$newId] = $treepath;
        $this->execute('UPDATE categories SET treepath = ? WHERE id = ?', [$treepath, $newId]);
      }
    }

    foreach ($topParentIds as $id)
    {
      $this->generateTreepath((int) $id);
    }

    // Change the id of forms
    foreach ($mapping as $oldId => $newId)
    {
      $this->execute('UPDATE forms SET category_id = ? WHERE category_id = ?', [$newId, $oldId]);
    }
  }

  public function down(): void
  {

  }

  private function generateTreepath(int $id): void
  {
    $stmt = $this->query('SELECT * FROM categories WHERE category_id = ?', [$id]);
    $items = $stmt->fetchAll();
    $itemList = [];
    foreach ($items as $item)
    {
      $itemList[] = $item['id'];
      // generate treepath
      $treepath = sprintf("%05d", $item['id']);
      if (isset($this->treepaths[$item['category_id']]))
      {
        $treepath = $this->treepaths[$item['category_id']] . $treepath;
      }
      $this->treepaths[$item['id']] = $treepath;
      $this->execute('UPDATE categories SET treepath = ? WHERE id = ?', [$treepath, $item['id']]);
    }
    foreach ($itemList as $itemId)
    {
      $this->generateTreepath($itemId);
    }
  }
}
