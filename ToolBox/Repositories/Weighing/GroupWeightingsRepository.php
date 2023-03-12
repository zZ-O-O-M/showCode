<?php

namespace App\ToolBox\Repositories\Weighing;

use App\WeighingGroup;

class GroupWeightingsRepository extends WeightingsRepository
{
    const ENTITY_NAME = 'group';

    protected function weighingTypeClass(): string
    {
        return WeighingGroup::class;
    }

    /**
     * @return string
     */
    protected function entityName(): string
    {
        return self::ENTITY_NAME;
    }
}
