<?php

namespace app\logic\Actions;

use app\logic\Actions\AbstractAction;

class ActionAssign extends AbstractAction
{
    public function getName(): string
    {
        return "Выбрать исполнителя";
    }

    public function getInternalName(): string
    {
        return "assign";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId === $customerId;
    }
}
