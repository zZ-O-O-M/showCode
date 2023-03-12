<?php

namespace App\API\Sections\Weighing\Toolbox;

use App\Animal;
use App\API\Sections\Weighing\Controllers\AppWeightingsController;
use App\Exceptions\UnprocessableEntityHttpExceptionWithErrors;
use App\Validation\Rules;
use App\WeighingEntity;
use App\WeighingGroup;
use InvalidArgumentException;

class Validation
{
    /**
     * @param string $entityName
     * @param int    $typeId
     *
     * @return array
     */
    public static function syncWeightingValidationRules(string $entityName, int $typeId): array
    {
        $entityIdFieldName = self::prepareEntityIdField($entityName);

        return [
            'rules' => [
                "*." . AppWeightingsController::FIELD_ID => [
                    'bail',
                    'required',
                    'distinct',
                ],

                "*." . AppWeightingsController::FIELD_ANIMAL_ID => [
                    'bail',
                    'max:50',
                    Rules::exists(Animal::TABLE_NAME, Animal::ATTRIBUTE_EXTERNAL_ID),
                    'required',
                ],

                "*." . AppWeightingsController::FIELD_WEIGHT => [
                    'bail',
                    'required',
                    'integer',
                    'max:1000000000'
                ],

                "*." . $entityIdFieldName => [
                    'bail',
                    'required',
                    'integer',
                    Rules::exists(WeighingEntity::TABLE_NAME, WeighingEntity::ATTRIBUTE_EXTERNAL_ID)
                         ->where(WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID, $typeId)
                ],
            ],

            'messages' => [
                "*." . AppWeightingsController::FIELD_ID . ".required" => "Необходимо указать id взвешивания",
                "*." . AppWeightingsController::FIELD_ID . ".distinct" => "ID дублируется в рамках данного запроса",

                "*." . AppWeightingsController::FIELD_ANIMAL_ID . ".required" => "Необходимо указать ID животного",
                "*." . AppWeightingsController::FIELD_ANIMAL_ID . ".max"      => "Слишком длинное значение",
                "*." . AppWeightingsController::FIELD_ANIMAL_ID . ".exists"   => "Животное не найдено",

                "*." . AppWeightingsController::FIELD_ANIMAL_ID . ".date_format" => "Некорректный формат даты (Y-m-d)",

                "*." . AppWeightingsController::FIELD_WEIGHT . ".required" => "Необходимо указать вес замеса",
                "*." . AppWeightingsController::FIELD_WEIGHT . ".integer"  => "Необходимо указать целочисленное значение",
                "*." . AppWeightingsController::FIELD_WEIGHT . ".max"      => "Слишком большое значение",

                "*." . $entityIdFieldName . ".required" => "Необходимо указать group_id",
                "*." . $entityIdFieldName . ".exists"   => "Некорректный $entityIdFieldName"
            ]
        ];
    }

    /**
     * @param array $requestData
     *
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    public static function checkGroupNamesUniqueness(array $requestData)
    {
        $externalIds = array_column($requestData, WeighingEntity::ATTRIBUTE_EXTERNAL_ID);
        $names       = array_column($requestData, WeighingEntity::ATTRIBUTE_NAME);

        $query = WeighingGroup::query()
                              ->whereIn(WeighingEntity::ATTRIBUTE_NAME, $names)
                              ->whereNotIn(WeighingEntity::ATTRIBUTE_EXTERNAL_ID, $externalIds);

        $groups = $query->get()->all();

        if (!empty($groups)) {
            $names              = array_column($groups, WeighingEntity::ATTRIBUTE_NAME);
            $errorInfo['names'] = $names;

            throw new UnprocessableEntityHttpExceptionWithErrors($errorInfo, "Наименование групп должно быть уникальным");
        }
    }


    # region helpers

    /**
     * @param string $entityName
     *
     * @return string
     */
    protected static function prepareEntityIdField(string $entityName): string
    {
        if (!in_array($entityName, ['group', 'session'])) {
            throw new InvalidArgumentException("Incorrect entity name (allowed: group, session)");
        }

        return $entityName . "_id";
    }

    # endregion helpers
}
