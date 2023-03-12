<?php

namespace App\ToolBox\Management\Sections\Weighing\Weightings;

use App\Animal;
use App\ToolBox\Management\Sections\Weighing\Weightings\Tools\WeightingsDataToSync;
use App\ToolBox\Management\Tools\Extensions\SynchronizationManagement;
use App\ToolBox\Repositories\AnimalsRepository;
use App\ToolBox\Repositories\Weighing\GroupsRepository;
use App\Weighing;
use App\WeighingEntity;

class WeightingsManager
{
    use SynchronizationManagement;

    /**
     * @param array $data
     *
     * @return array
     */
    public static function externalToInternal(array $data): array
    {
        $data = self::prepareInternalGroupIds($data);

        return self::prepareInternalAnimalIds($data);
    }

    /**
     * @param array $data
     * @param array $savedFields
     *
     * @return void
     */
    public function syncByExternalData(array $data, array $savedFields = [])
    {
        $this->sync(self::externalToInternal($data), $savedFields);
    }

    /**
     * @param array $data
     * @param array $savedFields
     *
     * @return void
     */
    public function sync(array $data, array $savedFields = [])
    {
        $dataToSync = new WeightingsDataToSync($data, $savedFields);

        $this->_sync(Weighing::TABLE_NAME, $dataToSync);
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
    protected static function prepareInternalGroupIds(array $requestData): array
    {
        $externalIds = array_column($requestData, 'entity_id');

        $repository = new GroupsRepository();

        $rows = $repository->query()
                           ->select(['id', 'external_id'])
                           ->whereIn(WeighingEntity::ATTRIBUTE_EXTERNAL_ID, $externalIds)
                           ->get()
                           ->groupBy('external_id')
                           ->toArray();

        $result = [];
        foreach ($requestData as $datum) {
            $externalId         = $datum['entity_id'];
            $datum['entity_id'] = $rows[$externalId][0]['id'];
            $result[]           = $datum;
        }

        return $result;
    }

    /**
     * @param array $requestData
     *
     * @return array
     */
    protected static function prepareInternalAnimalIds(array $requestData): array
    {
        $externalAnimalIds = array_column($requestData, 'animal_id');

        $repository = new AnimalsRepository();

        $rows   = $repository->query()
                             ->select(['id', 'external_id'])
                             ->whereIn(Animal::ATTRIBUTE_EXTERNAL_ID, $externalAnimalIds)
                             ->get()
                             ->groupBy('external_id')
                             ->toArray();
        $result = [];

        foreach ($requestData as $datum) {
            $externalId         = $datum['animal_id'];
            $datum['animal_id'] = $rows[$externalId][0]['id'];
            $result[]           = $datum;
        }

        return $result;
    }
}
