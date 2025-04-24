<?php

namespace app\logic\Actions;

use app\logic\Actions\AbstractAction;

class ActionFail extends AbstractAction
{
    public function getName(): string
    {
        return "Отказаться от задания";
    }

    public function getInternalName(): string
    {
        return "fail";
    }

    public function isAvailable(int $userId, int $customerId, ?int $executorId): bool
    {
        return $userId === $executorId;
    }
}
