<?php

namespace app\logic\Actions;

use app\logic\Actions\AbstractAction;

class ActionExecute extends AbstractAction
{
    public function getName(): string
    {
        return "Завершить задание";
    }

    public function getInternalName(): string
    {
        return "execute";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId === $customerId;
    }
}
