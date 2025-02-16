<?php

declare(strict_types=1);

namespace App\v1\Controllers;

final class Crontaskexecution extends Common
{
  protected $model = \App\Models\Crontaskexecution::class;

  /** @var int */
  protected $startTime = 0;

  public function createExecution(\App\Models\Crontask $crontask): int
  {
    $crontaskexecution = new \App\Models\Crontaskexecution();
    $crontaskexecution->crontask_id = $crontask->id;
    $crontaskexecution->state = \App\Models\Crontaskexecution::STATE_RUN;
    $crontaskexecution->save();

    $this->startTime = time();
    return $crontaskexecution->id;
  }

  public function endExecution(int $id): void
  {
    $crontaskexecution = \App\Models\Crontaskexecution::where('id', $id)->first();
    if (is_null($crontaskexecution))
    {
      throw new \Exception('Id not found', 404);
    }
    $crontaskexecution->execution_duration = (time() - $this->startTime);
    $crontaskexecution->state = \App\Models\Crontaskexecution::STATE_STOP;
    $crontaskexecution->save();
  }
}
