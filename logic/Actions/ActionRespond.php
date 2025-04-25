<?php

namespace app\logic\Actions;

class ActionRespond extends AbstractAction
{
    public function getName(): string
    {
        return "Откликнуться на задание";
    }

    public function getInternalName(): string
    {
        return "act_response";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId !== $customerId && $executorId === null;
    }
}
