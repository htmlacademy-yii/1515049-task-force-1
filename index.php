<?php
require_once ('vendor/autoload.php');

use App\Logics\Task;

// Тест: новая задача должна иметь действия "Откликнуться" и "Отменить" для разных пользователей
$task = new Task(1);

assert($task->getAvailableActions(1) === [Task::ACTION_CANCEL, Task::ACTION_ASSIGN], 'Ошибка: Для заказчика должны быть доступны "Отменить" и "Выбрать исполнителя".');
assert($task->getAvailableActions(2) === [Task::ACTION_RESPOND], 'Ошибка: Для исполнителя должно быть доступно "Откликнуться".');

// Тест: отмена задачи должна приводить к статусу "отменено"
assert($task->getNextStatus(Task::ACTION_CANCEL) === Task::STATUS_CANCELLED, 'Ошибка: Отмена задачи должна привести к статусу "Отменено".');

// Тест: выбор исполнителя должен перевести задачу в статус "в работе"
assert($task->getNextStatus(Task::ACTION_ASSIGN) === Task::STATUS_IN_PROGRESS, 'Ошибка: Выбор исполнителя должен перевести задачу в "В работе".');

// Тест: выполнение задачи должно перевести её в статус "выполнено"
$taskInProgress = new Task(1, Task::STATUS_IN_PROGRESS, 2);
assert($taskInProgress->getNextStatus(Task::ACTION_EXECUTE) === Task::STATUS_COMPLETED, 'Ошибка: Выполнение задачи должно перевести её в статус "Выполнено".');

// Тест: отказ от выполнения должен привести к "провалено"
assert($taskInProgress->getNextStatus(Task::ACTION_FAIL) === Task::STATUS_FAILED, 'Ошибка: Отказ от выполнения должен привести к "Провалено".');

// Тест: У задачи без исполнителя нельзя завершить работу
$taskWithoutExecutor = new Task(1, Task::STATUS_IN_PROGRESS);
assert(!in_array(Task::ACTION_EXECUTE, $taskWithoutExecutor->getAvailableActions(1)), 'Ошибка: У задачи без исполнителя нельзя завершить работу.');

// Тест: заказчик выбирает исполнителя
$taskNew = new Task(1, Task::STATUS_NEW);
assert($taskNew->getNextStatus(Task::ACTION_ASSIGN) === Task::STATUS_IN_PROGRESS, 'Ошибка: Выбор исполнителя должен перевести задачу в "В работе".');

// Проверяем доступные действия для завершенной задачи
$taskCompleted = new Task(1, Task::STATUS_COMPLETED);
assert(empty($taskCompleted->getAvailableActions(1)), 'Ошибка: Завершенная задача не должна иметь доступных действий.');

// Проверяем доступные действия для отмененной задачи
$taskCancelled = new Task(1, Task::STATUS_CANCELLED);
assert(empty($taskCancelled->getAvailableActions(1)), 'Ошибка: Отмененная задача не должна иметь доступных действий.');

// Проверяем доступные действия для проваленной задачи
$taskFailed = new Task(1, Task::STATUS_FAILED);
assert(empty($taskFailed->getAvailableActions(1)), 'Ошибка: Проваленная задача не должна иметь доступных действий.');

// Тест: карта статусов
assert(Task::getStatusMap()[Task::STATUS_NEW] === 'Новое', 'Ошибка: Неверное имя статуса "Новое".');
assert(Task::getStatusMap()[Task::STATUS_COMPLETED] === 'Выполнено', 'Ошибка: Неверное имя статуса "Выполнено".');

// Тест: карта действий
assert(Task::getActionsMap()[Task::ACTION_EXECUTE] === 'Выполнено', 'Ошибка: Неверное имя действия "Выполнено".');
assert(Task::getActionsMap()[Task::ACTION_FAIL] === 'Отказаться', 'Ошибка: Неверное имя действия "Отказаться".');

// Тест: неизвестное действие не меняет статус
assert($taskNew->getNextStatus('неизвестное') === null, 'Ошибка: Неизвестное действие не должно менять статус.');

echo "Все тесты пройдены!";
