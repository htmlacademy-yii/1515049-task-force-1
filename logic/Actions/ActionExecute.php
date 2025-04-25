<?php

namespace app\logic\Actions;

class ActionExecute extends AbstractAction
{
    public function getName(): string
    {
        return "Завершить задание";
    }

    public function getInternalName(): string
    {
        return "completion";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId === $customerId;
    }
}
