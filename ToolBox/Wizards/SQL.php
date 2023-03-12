<?php

namespace App\ToolBox\Wizards;

use App\Helper\StrHelper;
use Illuminate\Support\Facades\DB;

class SQL
{
    public static function insert(string $table, array $resources){

    }

    /**
     * @param string $table
     * @param array  $resources
     * @param array  $updatedFields
     *
     * @return void
     */
    public static function insertOnDuplicateKeyUpdate(string $table, array $resources, array $updatedFields = [])
    {
        $fields = array_keys($resources[0]);

        $fieldsList = implode(", ", $fields);

        $query = "INSERT INTO " . $table . " ($fieldsList) VALUES ";
        foreach ($resources as $resource) {
            $query .= "(";
            foreach ($fields as $field) {
                $value = isset($resource[$field]) ? "'$resource[$field]'" : "NULL";
                $query .= " " . $value . ",";

            }

            $query = self::rmCommaInEnd($query);
            $query .= "),";

        }

        $query = self::rmCommaInEnd($query);

        if (!empty($updatedFields)) {
            $query .= " ON DUPLICATE KEY UPDATE";

            foreach ($updatedFields as $field) {
                $query .= " $field = VALUES($field),";
            }

            $query = self::rmCommaInEnd($query);
        }

        DB::statement($query);
    }


    # region helpers

    protected function makeInsertPart(string $table, array $resources){

    }

    /**
     * @param string $query
     *
     * @return false|string
     */
    protected static function rmCommaInEnd(string $query)
    {
        return StrHelper::rmLast($query);
    }

    # endregion helpers
}
