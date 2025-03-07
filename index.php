<?php

require_once ('vendor/autoload.php');

use App\Logics\Task;

$task = new Task(1, 2);

assert($task->getNextStatus('cancel') === Task::STATUS_CANCELLED,
    'Отмена действия должна привести к отменённому статусу');
assert(
    $task->getAvailableActions('new') === ['respond', 'cancel'],
    'Новая задача должна иметь действия запуска и отмены.'
);

echo "Все тесты пройдены!" . PHP_EOL;
