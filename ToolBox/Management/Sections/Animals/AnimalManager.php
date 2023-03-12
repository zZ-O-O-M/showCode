<?php

namespace App\ToolBox\Management\Sections\Animals;

use App\Animal;
use App\BaseModel;
use App\ToolBox\Management\Sections\Animals\Tools\Dto\AnimalsDataToSync;
use App\ToolBox\Management\Tools\Extensions\ExternalResourceManagement;
use App\ToolBox\Management\Tools\Extensions\ResourceManagement;
use App\ToolBox\Management\Tools\Extensions\SynchronizationManagement;
use App\ToolBox\Management\Tools\Manager;
use App\ToolBox\Management\Tools\MultipleManager;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AnimalManager extends Manager
{
    use SynchronizationManagement;

    /**
     * @param array $animals
     * @param array $fieldsToSave
     *
     * @return void
     */
    public function sync(array $animals, array $fieldsToSave = [])
    {
        $dataToSync = new AnimalsDataToSync($animals, $fieldsToSave);
        $this->_sync(Animal::TABLE_NAME, $dataToSync);
    }

    /**
     * @param array $animals
     *
     * @return void
     * @throws Exception
     */
    public function switchIds(array $animals)
    {
        DB::beginTransaction();
        try {
            foreach ($animals as $animal) {
                // get and check external_id
                $externalId = $animal['external_id'] ?? null;
                if (!isset($externalId)) {
                    throw new InvalidArgumentException("The animal external id not found");
                }

                // get and check new_id
                $newId = $animal['new_id'] ?? null;
                if (!isset($newId)) {
                    throw new InvalidArgumentException("The animal new external id not found");
                }

                Animal::where([Animal::ATTRIBUTE_EXTERNAL_ID => $externalId])
                      ->update([Animal::ATTRIBUTE_EXTERNAL_ID => $newId]);
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new $exception();
        }
    }
}
