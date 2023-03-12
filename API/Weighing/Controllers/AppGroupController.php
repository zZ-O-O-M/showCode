<?php

namespace App\API\Sections\Weighing\Controllers;

use App\API\Controllers\ApiController;
use App\API\Sections\Weighing\Toolbox\Data\WeighingGroupsDataProvider;
use App\API\Sections\Weighing\Toolbox\Validation;
use App\BaseModel;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\UnprocessableEntityHttpExceptionWithErrors;
use App\Helper\ApiResponseGenerator;
use App\ToolBox\Management\Sections\Weighing\Groups\WeighingGroupManager;
use App\ToolBox\Repositories\Weighing\GroupsRepository;
use App\Validation\Rules;
use App\WeighingEntity;
use App\WeighingEntityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppGroupController extends ApiController
{
    const METHOD_SYNC = 'sync';
    const METHOD_LINK = 'link';

    const FIELD_ID          = 'id';
    const FIELD_EXTERNAL_ID = 'external_id';
    const FIELD_NAME        = 'name';
    const FIELD_CREATED_AT  = 'created_at';
    const FIELD_UPDATED_AT  = 'updated_at';

    /** @var array */
    protected $fieldsMap = [
        self::METHOD_SYNC => [
            self::FIELD_ID         => WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
            self::FIELD_NAME       => WeighingEntity::ATTRIBUTE_NAME,
            self::FIELD_CREATED_AT => Model::CREATED_AT,
            self::FIELD_UPDATED_AT => Model::UPDATED_AT,
        ],
        self::METHOD_LINK => [
            self::FIELD_ID          => BaseModel::ATTRIBUTE_ID,
            self::FIELD_EXTERNAL_ID => WeighingEntity::ATTRIBUTE_EXTERNAL_ID
        ]
    ];

    /**
     * @param array $params
     *
     * @return array[]
     */
    public function validationRules(array $params = []): array
    {
        return [
            self::METHOD_SYNC => [
                'rules' => [
                    "*." . self::FIELD_ID => [
                        'bail',
                        'required',
                        'distinct',
                    ],

                    "*." . self::FIELD_NAME => [
                        'bail',
                        'required',
                        'distinct',
                    ],

                    "*." . self::FIELD_CREATED_AT => [
                        'bail',
                        'required',
                        'string',
                        'date_format:Y-m-d H:i:s'
                    ],

                    "*." . self::FIELD_UPDATED_AT => [
                        'bail',
                        'required',
                        'string',
                        'date_format:Y-m-d H:i:s'
                    ]
                ],

                'messages' => [
                    "*." . self::FIELD_ID . ".required" => "Необходимо указать id группы",
                    "*." . self::FIELD_ID . ".distinct" => "ID дублируется в рамках данного запроса",

                    "*." . self::FIELD_NAME . ".required" => "Необходимо указать наименование группы",
                    "*." . self::FIELD_NAME . ".distinct" => "Наименование дублируется в рамках данного запроса",

                    "*." . self::FIELD_CREATED_AT . ".required"    => "Необходимо указать дату создания группы",
                    "*." . self::FIELD_CREATED_AT . ".date_format" => "Некорректный формат (Y-m-d H:i:s)",

                    "*." . self::FIELD_UPDATED_AT . ".required"    => "Необходимо указать дату последнего обновления группы",
                    "*." . self::FIELD_UPDATED_AT . ".date_format" => "Некорректный формат (Y-m-d H:i:s)"
                ]
            ],

            self::METHOD_LINK => [
                'rules' => [
                    "*." . self::FIELD_ID => [
                        'bail',
                        'required',
                        'distinct',
                        Rules::exists(WeighingEntity::TABLE_NAME, BaseModel::ATTRIBUTE_ID)
                             ->where(WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID, WeighingEntityType::TYPE_GROUP)
                             ->where(WeighingEntity::ATTRIBUTE_EXTERNAL_ID, 'NULL')
                    ],

                    "*." . self::FIELD_EXTERNAL_ID => [
                        'bail',
                        'required',
                        'distinct',
                        Rules::unique(WeighingEntity::TABLE_NAME,
                                      WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
                                      true,
                                      false)
                             ->where(WeighingEntity::ATTRIBUTE_ENTITY_TYPE_ID, WeighingEntityType::TYPE_GROUP)
                    ]
                ],

                'messages' => [
                    "*." . self::FIELD_ID . ".required" => "Необходимо указать id группы",
                    "*." . self::FIELD_ID . ".distinct" => "ID дублируется в рамках данного запроса",
                    "*." . self::FIELD_ID . ".exists"   => "Группа для синхронизации не найдена",

                    "*." . self::FIELD_EXTERNAL_ID . ".required" => "Необходимо указать внешний id группы",
                    "*." . self::FIELD_EXTERNAL_ID . ".distinct" => "ID дублируется в рамках данного запроса",
                    "*." . self::FIELD_EXTERNAL_ID . ".unique"   => "Данный external_id уже присвоен другой группе"
                ]
            ],
        ];
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getGroups(Request $request): JsonResponse
    {
        $repository   = new GroupsRepository();
        $dataProvider = new WeighingGroupsDataProvider($repository->selectAsExternal());

        return ApiResponseGenerator::successResponse($dataProvider);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getUnlinked(Request $request): JsonResponse
    {
        $repository = new GroupsRepository();
        $query      = $repository->select()->where([WeighingEntity::ATTRIBUTE_EXTERNAL_ID => null]);

        $dataProvider = new WeighingGroupsDataProvider($query);

        return ApiResponseGenerator::successResponse($dataProvider);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    public function sync(Request $request): JsonResponse
    {
        $this->currentMethod = self::METHOD_SYNC;
        $this->isJsonRequest = true;

        $requestData = $this->extractAndValidate($request);

        Validation::checkGroupNamesUniqueness($requestData);

        $manager = new WeighingGroupManager();
        $manager->sync($requestData);

        return ApiResponseGenerator::successResponse();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    public function link(Request $request): JsonResponse
    {
        $this->currentMethod = self::METHOD_LINK;
        $this->isJsonRequest = true;

        $requestData = $this->extractAndValidate($request);

        $manager = new WeighingGroupManager();
        $manager->linkWithExternal($requestData);

        return ApiResponseGenerator::successResponse();
    }
}
