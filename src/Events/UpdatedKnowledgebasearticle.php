<?php

declare(strict_types=1);

namespace App\Events;

final class UpdatedKnowledgebasearticle
{
  /**
   * @template C of \App\Models\Common
   * @param C $model
   */
  public static function run($model): void
  {
    if (get_class($model) != \App\Models\Knowledgebasearticle::class)
    {
      return;
    }

    $lastRevision = \App\Models\Knowledgebasearticlerevision::
        where('knowledgebasearticle_id', $model->id)
      ->orderBy('revision', 'desc')
      ->first();

    $nextRevisionNumber = 1;
    if (!is_null($lastRevision))
    {
      $nextRevisionNumber = $lastRevision->getAttribute('revision') + 1;
    }

    // INSERT REVISION
    \App\Models\Knowledgebasearticlerevision::create([
      'knowledgebasearticle_id' => $model->id,
      'article'                 => $model->getAttribute('article'),
      'user_id'                 => $GLOBALS['user_id'],
      'revision'                => $nextRevisionNumber,
    ]);
  }
}
