<?php

namespace App\ToolBox\Repositories\Weighing;

use App\BaseModel;
use App\ToolBox\Repositories\ExternalRepository;
use App\WeighingEntity;
use App\WeighingSession;
use Illuminate\Database\Eloquent\Model;

class SessionsRepository extends ExternalRepository
{
    /**
     * @return string
     */
    protected function modelClass(): string
    {
        return WeighingSession::class;
    }

    /**
     * @return string
     */
    protected function table(): string
    {
        return WeighingEntity::TABLE_NAME;
    }

    /**
     * @return array
     */
    protected function resourceFields(): array
    {
        return [
            BaseModel::ATTRIBUTE_ID               => BaseModel::ATTRIBUTE_ID,
            WeighingEntity::ATTRIBUTE_EXTERNAL_ID => WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
            Model::CREATED_AT                     => Model::CREATED_AT,
            Model::UPDATED_AT                     => Model::UPDATED_AT
        ];
    }
}
