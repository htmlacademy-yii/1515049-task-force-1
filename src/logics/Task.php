<?php

namespace App\Logics;

/**
 *
 */
class Task
{
    // статусы
    const string STATUS_NEW = 'new';
    const string STATUS_CANCELLED = 'cancelled';
    const string STATUS_IN_PROGRESS = 'in_progress';
    const string STATUS_COMPLETED = 'completed';
    const string STATUS_FAILED = 'failed';

    // действия
    const string ACTION_FAIL = 'fail';
    const string ACTION_CANCEL = 'cancel';
    const string ACTION_ASSIGN = 'assign';
    const string ACTION_RESPOND = 'respond';
    const string ACTION_EXECUTE = 'execute';

    private string $currentStatus;
    private int $customerId;
    private ?int $executorId;

    public function __construct(int $customerId, $currentStatus = self::STATUS_NEW, ?int $executorId = null)
    {
        $this->customerId = $customerId;
        $this->currentStatus = $currentStatus;
        $this->executorId = $executorId;
    }

    /**
     * Карта статусов
     *
     * @return string[]
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELLED => 'Отменено',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_COMPLETED => 'Выполнено',
            self::STATUS_FAILED => 'Провалено'
        ];
    }

    /**
     * Карта действий
     *
     * @return string[]
     */
    public static function getActionsMap(): array
    {
        return [
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_ASSIGN => 'Выбрать исполнителя',
            self::ACTION_EXECUTE => 'Выполнено',
            self::ACTION_RESPOND => 'Откликнуться',
            self::ACTION_FAIL => 'Отказаться'
        ];
    }

    /**
     * Получение статуса, в который он перейдет после выполнения указанного действия
     *
     * @param string $action действие
     * @return string|null следующий статус или null
     */
    public function getNextStatus(string $action): ?string
    {
        $transitions = [
            self::STATUS_NEW => [
                self::ACTION_ASSIGN => self::STATUS_IN_PROGRESS,
                self::ACTION_CANCEL => self::STATUS_CANCELLED,
            ],
            self::STATUS_IN_PROGRESS => [
                self::ACTION_EXECUTE => self::STATUS_COMPLETED,
                self::ACTION_FAIL => self::STATUS_FAILED,
            ],
        ];

        return $transitions[$this->currentStatus][$action] ?? null;
    }

    /**
     * Доступные действия
     *
     * @param int $userId
     * @return array|string[]
     */
    public function getAvailableActions(int $userId): array
    {
        if ($userId === $this->customerId) {
            return $this->currentStatus === self::STATUS_NEW
                ? [self::ACTION_CANCEL, self::ACTION_ASSIGN]
                : [];
        }
        if ($this->executorId === null && $this->currentStatus === self::STATUS_NEW) {
            return [self::ACTION_RESPOND];
        }
        if ($userId === $this->executorId) {
            return $this->currentStatus === self::STATUS_IN_PROGRESS
                ? [self::ACTION_EXECUTE, self::ACTION_FAIL]
                : [];
        }
        return [];
    }
}
