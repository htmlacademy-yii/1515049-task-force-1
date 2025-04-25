<?php

namespace app\customComponents\ActionButtonsWidget;

use app\logic\Actions\AbstractAction;
use app\logic\AvailableActions;
use app\models\Response;
use app\models\Task;
use yii\base\Widget;
use yii\helpers\Html;

final class ActionButtonsWidget extends Widget
{
    public AvailableActions $availableActions;
    public int $currentUserId;
    public Task $task;

    public function run(): string
    {
        $actions = $this->availableActions->getAvailableActions($this->currentUserId);

        $buttons = [];

        foreach ($actions as $action) {
            if ($action instanceof AbstractAction) {
                if ($action->getInternalName() === 'act_response' && $this->hasResponded()) {
                    continue;
                }
                $buttons[] = $this->generateButton($action);
            }
        }

        return implode(PHP_EOL, $buttons);
    }

    private function hasResponded(): bool
    {
        return Response::find()->where(['task_id' => $this->task->id, 'executor_id' => $this->currentUserId])->exists();
    }

    /**
     * Генерирует HTML-код кнопки действия
     *
     * @param AbstractAction $action
     * @return string
     */
    private function generateButton(AbstractAction $action): string
    {
        // [!] АВТОРСКИЙ КОД [!]
        // Student: Романова Наталья
        // Course: Профессия "PHP-разработчик#1"
        // Task: модуль 2, задание module7-task2
        //  выполнено 24.04.2025

        $label = $action->getName();
        $actionName = $action->getInternalName();
        $colorClass = $this->getButtonColor($actionName);

        return Html::a(
            $label,
            '#',
            [
                'class' => "button button--{$colorClass} action-btn",
                'data-action' => $actionName,
            ]
        );
    }

    private function getButtonColor(string $actionName): string
    {
        switch ($actionName) {
            case 'act_response':
                return 'blue';
            case 'refusal':
                return 'orange';
            case 'completion':
                return 'pink';
            default:
                return 'default';
        }
    }
}
