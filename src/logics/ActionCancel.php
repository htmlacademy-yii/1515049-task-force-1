<?php

namespace App\Logics;

class ActionCancel extends AbstractAction
{
    public function getName(): string
    {
        return "Отменить";
    }

    public function getInternalName(): string
    {
        return "cancel";
    }

    public function isAvailable(int $customerId, int $userId, ?int $executorId): bool
    {
        return $userId === $customerId;
    }
}
