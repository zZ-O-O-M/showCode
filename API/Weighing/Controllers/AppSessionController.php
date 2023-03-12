<?php

namespace App\API\Sections\Weighing\Controllers;

use App\API\Controllers\ApiController;
use App\API\Sections\Weighing\Toolbox\Data\WeighingSessionsDataProvider;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\UnprocessableEntityHttpExceptionWithErrors;
use App\Helper\ApiResponseGenerator;
use App\ToolBox\Management\Sections\Weighing\Sessions\WeighingSessionManager;
use App\ToolBox\Repositories\Weighing\SessionsRepository;
use App\WeighingEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppSessionController extends ApiController
{
    const METHOD_SYNC = 'sync';

    const FIELD_ID = 'id';

    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    /** @var array */
    public $fieldsMap = [
        self::METHOD_SYNC => [
            self::FIELD_ID         => WeighingEntity::ATTRIBUTE_EXTERNAL_ID,
            self::FIELD_CREATED_AT => Model::CREATED_AT,
            self::FIELD_UPDATED_AT => Model::UPDATED_AT
        ]
    ];

    /**
     * @param array $params
     *
     * @return array[][]
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
                    "*." . self::FIELD_ID . ".required" => "Необходимо указать id сессии",
                    "*." . self::FIELD_ID . ".distinct" => "ID дублируется в рамках данного запроса",

                    "*." . self::FIELD_CREATED_AT . ".required"    => 'Необходимо указать дату создания сессии',
                    "*." . self::FIELD_CREATED_AT . ".date_format" => 'Указан некорректный формат (Y-m-d H:i:s)',

                    "*." . self::FIELD_UPDATED_AT . ".required"    => 'Необходимо указать дату обновления сессии',
                    "*." . self::FIELD_UPDATED_AT . ".date_format" => 'Указан некорректный формат (Y-m-d H:i:s)',
                ]
            ]
        ];
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getSessions(Request $request): JsonResponse
    {
        $repository   = new SessionsRepository();
        $dataProvider = new WeighingSessionsDataProvider($repository->selectAsExternal());

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
        $this->isJsonRequest = true;
        $this->currentMethod = self::METHOD_SYNC;
        $requestData         = $this->extractAndValidate($request);

        $manager = new WeighingSessionManager();
        $manager->sync($requestData);

        return ApiResponseGenerator::successResponse();
    }
}
