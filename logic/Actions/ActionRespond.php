<?php

namespace app\logic\Actions;

use app\logic\Actions\AbstractAction;

class ActionRespond extends AbstractAction
{
    public function getName(): string
    {
        return "Откликнуться на задание";
    }

    public function getInternalName(): string
    {
        return "respond";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId !== $customerId && $executorId === null;
    }
}
