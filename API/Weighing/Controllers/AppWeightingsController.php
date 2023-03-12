<?php

namespace App\API\Sections\Weighing\Controllers;

use App\API\Controllers\ApiController;
use App\API\Sections\Weighing\Toolbox\Data\WeightingsDataProvider;
use App\API\Sections\Weighing\Toolbox\Validation;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\UnprocessableEntityHttpExceptionWithErrors;
use App\Helper\ApiResponseGenerator;
use App\ToolBox\Management\Sections\Weighing\Weightings\WeightingsManager;
use App\ToolBox\Repositories\Weighing\GroupWeightingsRepository;
use App\ToolBox\Repositories\Weighing\SessionWeightingsRepository;
use App\Weighing;
use App\WeighingEntityType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppWeightingsController extends ApiController
{
    const FIELD_ID         = 'id';
    const FIELD_ANIMAL_ID  = 'animal_id';
    const FIELD_WEIGHT     = 'weight';
    const FIELD_GROUP_ID   = 'group_id';
    const FIELD_SESSION_ID = 'session_id';

    protected const METHOD_LOAD_GROUP_WEIGHTINGS   = 'loadGroupWeightings';
    protected const METHOD_LOAD_SESSION_WEIGHTINGS = 'loadSessionWeightings';

    /** @var array[] */
    protected $fieldsMap = [
        self::METHOD_LOAD_GROUP_WEIGHTINGS   => [
            self::FIELD_ID        => Weighing::ATTRIBUTE_EXTERNAL_ID,
            self::FIELD_ANIMAL_ID => Weighing::ATTRIBUTE_ANIMAL_ID,
            self::FIELD_WEIGHT    => Weighing::ATTRIBUTE_WEIGHT,
            self::FIELD_GROUP_ID  => Weighing::ATTRIBUTE_ENTITY_ID,
        ],
        self::METHOD_LOAD_SESSION_WEIGHTINGS => [
            self::FIELD_ID         => Weighing::ATTRIBUTE_EXTERNAL_ID,
            self::FIELD_ANIMAL_ID  => Weighing::ATTRIBUTE_ANIMAL_ID,
            self::FIELD_WEIGHT     => Weighing::ATTRIBUTE_WEIGHT,
            self::FIELD_SESSION_ID => Weighing::ATTRIBUTE_ENTITY_ID,
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
            self::METHOD_LOAD_SESSION_WEIGHTINGS =>
                Validation::syncWeightingValidationRules('session', WeighingEntityType::TYPE_SESSION),

            self::METHOD_LOAD_GROUP_WEIGHTINGS =>
                Validation::syncWeightingValidationRules('group', WeighingEntityType::TYPE_GROUP),
        ];
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    public function loadGroupWeightings(Request $request): JsonResponse
    {
        $this->isJsonRequest = true;
        $this->currentMethod = self::METHOD_LOAD_GROUP_WEIGHTINGS;

        return $this->load($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getGroupWeightings(Request $request): JsonResponse
    {
        $repository = new GroupWeightingsRepository();
        $provider   = new WeightingsDataProvider($repository->selectAsExternal(), WeightingsDataProvider::TYPE_GROUP);

        return ApiResponseGenerator::successResponse($provider);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getSessionWeightings(Request $request): JsonResponse
    {
        $repository = new SessionWeightingsRepository();
        $provider   = new WeightingsDataProvider($repository->selectAsExternal(), WeightingsDataProvider::TYPE_SESSION);

        return ApiResponseGenerator::successResponse($provider);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    public function loadSessionWeightings(Request $request): JsonResponse
    {
        $this->isJsonRequest = true;
        $this->currentMethod = self::METHOD_LOAD_SESSION_WEIGHTINGS;

        return $this->load($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    protected function load(Request $request): JsonResponse
    {
        $data = $this->extractAndValidate($request);

        DB::beginTransaction();
        try {
            $manager = new WeightingsManager();
            $manager->syncByExternalData($data);
            DB::commit();
            return ApiResponseGenerator::successResponse();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}
