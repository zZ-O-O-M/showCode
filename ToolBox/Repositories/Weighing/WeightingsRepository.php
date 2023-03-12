<?php

namespace App\ToolBox\Repositories\Weighing;

use App\Animal;
use App\BaseModel;
use App\ToolBox\Repositories\ExternalRepository;
use App\Weighing;
use App\WeighingEntity;
use App\WeighingGroup;
use App\WeighingSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class WeightingsRepository extends ExternalRepository
{
    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->queryDecorator(parent::query());
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function queryDecorator(Builder $query): Builder
    {
        $entityIds = array_column($this->weighingTypeClass()::query()->select('id')
                                       ->get()
                                       ->toArray(), 'id');
        return $query->whereIn(Weighing::ATTRIBUTE_ENTITY_ID, $entityIds);
    }

    /**
     * @return string
     */
    protected function modelClass(): string
    {
        return Weighing::class;
    }

    /**
     * @return string
     */
    protected function table(): string
    {
        return Weighing::TABLE_NAME;
    }

    /**
     * @return array
     */
    protected function resourceFields(): array
    {
        return [
            BaseModel::ATTRIBUTE_ID         => BaseModel::ATTRIBUTE_ID,
            Weighing::ATTRIBUTE_EXTERNAL_ID => Weighing::ATTRIBUTE_EXTERNAL_ID,
            Weighing::ATTRIBUTE_ANIMAL_ID   => Weighing::ATTRIBUTE_ANIMAL_ID,
            Weighing::ATTRIBUTE_WEIGHT      => Weighing::ATTRIBUTE_WEIGHT,
            Weighing::ATTRIBUTE_ENTITY_ID   => $this->entityName() . "_id",
            Model::CREATED_AT               => Model::CREATED_AT,
            Model::UPDATED_AT               => Model::UPDATED_AT
        ];
    }

    /**
     * @return array[]
     */
    protected function externalResources(): array
    {
        return [
            Weighing::ATTRIBUTE_ANIMAL_ID => [
                'table' => Animal::TABLE_NAME,
                'name'  => Weighing::ATTRIBUTE_ANIMAL_ID
            ],

            Weighing::ATTRIBUTE_ENTITY_ID => [
                'table' => WeighingEntity::TABLE_NAME,
                'name'  => $this->entityName() . "_id"
            ]
        ];
    }

    /**
     * @return string
     * @see WeighingSession
     * @see WeighingGroup
     */
    protected abstract function weighingTypeClass(): string;

    /**
     * @return string
     */
    protected abstract function entityName(): string;
}
