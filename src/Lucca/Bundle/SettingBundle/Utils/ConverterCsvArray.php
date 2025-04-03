<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Utils;

class ConverterCsvArray
{
    /**
     * Convert Csv file in Php array
     */
    public function convert($filename, string $delimiter = ';'): array|bool
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Convert Csv file in Php array
     */
    public function convertOffset($p_filename, string $p_delimiter = ';', int $p_offset = 0): array|bool
    {
        if (!file_exists($p_filename) || !is_readable($p_filename)) {
            return false;
        }

        $header = null;
        $data = [];
        $rowNumber = 1;

        if (($handle = fopen($p_filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $p_delimiter)) !== false) {

                /** If row is under offset -- skip it */
                if ($rowNumber <= $p_offset) {
                    $rowNumber++;
                    continue;
                }

                if (!$header) {
                    $header = $row;
                }
                else {
                    if ($row[0]) { /** If the first elem of row is null dont do the combine */
                        $data[] = array_combine($header, $row);
                    }
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Convert Csv file in Php array
     */
    public function easyConvert($filename, string $delimiter = ';') :array|bool
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 4000, $delimiter)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }
}
