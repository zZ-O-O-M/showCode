<?php

namespace App\API\Controllers;

use App\Exceptions\InvalidConfigException;
use App\Exceptions\UnprocessableEntityHttpExceptionWithErrors;
use App\Helper\ArrayHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /** @var array */
    protected $fieldsMap = [];

    /** @var string */
    protected $currentMethod;

    /** @var bool */
    protected $isJsonRequest = false;

    /**
     * @param array $params
     *
     * @return array
     */
    public function validationRules(array $params = []): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param bool    $hydrate
     * @param bool    $hydrateAfterValidation
     *
     * @return array
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    protected function extractAndValidate(Request $request,
                                          bool    $hydrate = true,
                                          bool    $hydrateAfterValidation = true): array
    {
        $requestData = $this->extractRequestData($request, !$hydrateAfterValidation && $hydrate);
        $this->validateRequestData($requestData);

        if ($hydrate && $hydrateAfterValidation) {
            return $this->hydrate($requestData);
        }

        return $requestData;
    }

    /**
     * @param array $data
     * @param array $params
     *
     * @return void
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    protected function validateRequestData(array $data, array $params = [])
    {
        $this->checkValidationRulesConfig();

        if (isset($this->validationRules($params)[$this->currentMethod])) {
            $rulesData = $this->validationRules($params)[$this->currentMethod];

            $this->validateData($data, $rulesData['rules'], $rulesData['messages'] ?? []);
        }
    }

    /**
     * @param array          $data
     * @param callable|array $rules
     * @param array          $messages
     *
     * @throws InvalidConfigException
     * @throws UnprocessableEntityHttpExceptionWithErrors
     */
    protected function validateData(array $data, $rules, array $messages = [])
    {
        if (is_callable($rules)) {
            $validator = $rules();

            if (!($validator instanceof \Illuminate\Contracts\Validation\Validator)) {
                throw new InvalidConfigException('The function specified for the rules should return ' .
                                                 \Illuminate\Contracts\Validation\Validator::class);
            }
        }
        else {
            $validator = Validator::make($data, $rules, $messages ?? []);
        }

        if ($validator->fails()) {
            throw new UnprocessableEntityHttpExceptionWithErrors($validator->errors()->toArray());
        }
    }

    /**
     * @param Request $request
     * @param bool    $hydrate
     *
     * @return array
     */
    protected function extractRequestData(Request $request, bool $hydrate = true): array
    {
        if ($this->isJsonRequest) {
            $data = $request->json()->all();
        }
        else {
            $data = $request->all();
        }

        $data = $this->filter($data);

        if ($hydrate) {
            return $this->hydrate($data);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function filter(array $data): array
    {
        if (!isset($this->fieldsMap[$this->currentMethod])) {
            return $data;
        }

        $map = $this->fieldsMap[$this->currentMethod];

        $result = [];

        if ($this->isJsonRequest) {
            foreach ($data as $key => $datum) {
                if (is_string($key) && isset($map[$key])) {
                    if (is_array($map[$key])) {
                        if (is_array($datum)) {
                            $items = [];
                            foreach ($datum as $item) {
                                $items[] = Arr::only($item, array_keys($map[$key]));
                            }
                            $result[$key] = $items;
                        }
                        else {
                            $result[$key] = Arr::only($datum, array_keys($map[$key]));
                        }
                    }
                    elseif (is_string($map[$key])) {
                        $result[$map[$key]] = $datum;
                    }
                }
                else {
                    $result[$key] = Arr::only($datum, array_keys($map));
                }
            }
        }
        else {
            $result = Arr::only($data, $map);
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function hydrate(array $data): array
    {
        $map = $this->fieldsMap[$this->currentMethod];

        $result = [];

        if ($this->isJsonRequest) {
            foreach ($data as $key => $datum) {
                if (is_string($key) && isset($map[$key])) {
                    if (is_array($map[$key])) {
                        $result[$key] = ArrayHelper::hydrate($datum, $map[$key]);
                    }
                    elseif (is_string($map[$key])) {
                        $result[$map[$key]] = $datum;
                    }
                }
                else {
                    $result[$key] = ArrayHelper::hydrate($datum, $map);
                }
            }
        }
        else {
            $result = ArrayHelper::hydrate($data, $map);
        }

        return $result;
    }

    # region helpers

    /**
     * @return void
     * @throws InvalidConfigException
     */
    private function checkValidationRulesConfig()
    {
        $validationRules = $this->validationRules();

        if (!isset($validationRules[$this->currentMethod])) {
            throw new InvalidConfigException("validationRules for '" . $this->currentMethod . "' method is not set");
        }

        $methodRules = $validationRules[$this->currentMethod];

        if (!isset($methodRules['rules'])) {
            throw new InvalidConfigException("Rules for '" . $this->currentMethod . "' method is not set");
        }
    }

    # endregion helpers
}
