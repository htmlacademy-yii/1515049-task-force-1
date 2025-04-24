<?php

namespace app\customComponents\ActionButtonsWidget;

use app\logic\Actions\AbstractAction;
use app\logic\AvailableActions;
use yii\base\Widget;
use yii\helpers\Html;

final class ActionButtonsWidget extends Widget
{
    public AvailableActions $availableActions;
    public int $currentUserId;
    public $task;

    public function run(): string
    {
        $actions = $this->availableActions->getAvailableActions($this->currentUserId);

        $buttons = [];

        foreach ($actions as $action) {
            if ($action instanceof AbstractAction) {
                $buttons[] = $this->generateButton($action);
            }
        }

        return implode(PHP_EOL, $buttons);
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
            case 'respond':
                return 'blue';
            case 'fail':
                return 'orange';
            case 'execute':
                return 'pink';
            default:
                return 'default';
        }
    }
}
