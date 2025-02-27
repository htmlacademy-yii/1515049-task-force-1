<?php

require_once 'src/logics/Task.php';

$task = new Task(1, 2);
assert($task->getNextStatus(Task::ACTION_CANCEL) === Task::STATUS_CANCELLED,
    'Отмена действия должна привести к отменённому статусу');
assert(
    $task->getAvailableActions() === [Task::ACTION_RESPOND, Task::ACTION_CANCEL],
    'Новая задача должна иметь действия запуска и отмены.'
);

echo "Все тесты пройдены!" . PHP_EOL;
