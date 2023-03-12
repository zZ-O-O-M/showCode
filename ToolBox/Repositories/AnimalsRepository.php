<?php

namespace App\ToolBox\Repositories;

use App\Animal;
use App\BaseModel;
use Illuminate\Database\Eloquent\Model;

class AnimalsRepository extends ExternalRepository
{
    /**
     * @return string
     */
    protected function modelClass(): string
    {
        return Animal::class;
    }

    /**
     * @return string
     */
    protected function table(): string
    {
        return Animal::TABLE_NAME;
    }

    /**
     * @return array
     */
    protected function resourceFields(): array
    {
        return [
            BaseModel::ATTRIBUTE_ID       => BaseModel::ATTRIBUTE_ID,
            Animal::ATTRIBUTE_EXTERNAL_ID => Animal::ATTRIBUTE_EXTERNAL_ID,
            Animal::ATTRIBUTE_DOB         => Animal::ATTRIBUTE_DOB,
            Model::CREATED_AT             => Model::CREATED_AT,
            Model::UPDATED_AT             => Model::UPDATED_AT
        ];
    }
}
