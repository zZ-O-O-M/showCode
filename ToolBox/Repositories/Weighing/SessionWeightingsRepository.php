<?php

namespace App\ToolBox\Repositories\Weighing;

use App\WeighingSession;

class SessionWeightingsRepository extends WeightingsRepository
{
    const ENTITY_NAME = 'session';

    protected function weighingTypeClass(): string
    {
        return WeighingSession::class;
    }

    /**
     * @return string
     */
    protected function entityName(): string
    {
        return self::ENTITY_NAME;
    }
}