<?php

namespace App\Logics;

class ActionAssign extends Action
{
    public function getName(): string
    {
        return "Выбрать исполнителя";
    }

    public function getInternalName(): string
    {
        return "assign";
    }

    public function isAvailable(int $customerId, int $userId, ?int $executorId): bool
    {
        return $userId === $customerId;
    }
}
