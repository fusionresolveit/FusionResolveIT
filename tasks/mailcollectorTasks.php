<?php

// tasks/backupTasks.php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$store = new FlockStore(__DIR__ . '/../files/_lock');
$schedule = new Schedule();

$task = $schedule->run(function ()
{
  \Tasks\Myapp::loadCapsule();
  \App\v1\Controllers\Mailcollector::scheduleCollects();
});

$task
  ->everyMinute()
  ->description('Run actions in the queue')
  ->preventOverlapping($store);

return $schedule;
