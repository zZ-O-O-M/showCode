<?php

namespace App\ToolBox\Management\Sections\Weighing\Sessions\Tools\Dto;

use App\ToolBox\Management\Sections\Weighing\Tools\WeighingEntityDataToSync;
use App\WeighingEntity;
use App\WeighingEntityType;
use Illuminate\Database\Eloquent\Model;

class WeighingSessionsDataToSync extends WeighingEntityDataToSync
{
    /** @var array */
    protected $safeFields = [
        WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
        WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID,
        Model::CREATED_AT,
        Model::UPDATED_AT
    ];

    /** @var array */
    protected $updatedFields = [
        Model::CREATED_AT,
        Model::UPDATED_AT
    ];

    /**
     * @return int
     */
    protected function entityType(): int
    {
        return WeighingEntityType::TYPE_SESSION;
    }
}