<?php

class Dannsheet
{

    public static $credentials = '';
    public static $spreadsheetId = '';

    public static function setCredentials($credentials)
    {
        self::$credentials = $credentials;
    }

    public static function getCredentials()
    {
        return self::$credentials;
    }

    public static function setSpreasheetId($spreadsheetId)
    {
        self::$spreadsheetId = $spreadsheetId;
    }

    public static function getSpreadsheetId()
    {
        return self::$spreadsheetId;
    }

    public static function getClient()
    {
        $client = new \Google\Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(self::$credentials);
        return $client;
    }

    public static function getService()
    {
        $client = self::getClient();
        return new \Google\Service\Sheets($client);
    }

    public static function getSpreadsheet()
    {
        $service = self::getService();
        return $service->spreadsheets->get(self::$spreadsheetId);
    }

    /**
     * @param string $range
     */
    public static function quickShow(string $range)
    {
        return self::prettyShow(self::view($range));
    }

    /**
     * 
     */
    public static function printSheetTitles()
    {
        $ss = self::getSpreadsheet();
        for ($i = 0; $i < count($ss); $i++) {
            echo $ss[$i]->properties->title . "\n";
        }
    }

    /**
     * 
     */
    public static function sheetExists(string $title)
    {
        $spreadsheet = self::getSpreadsheet();
        for ($i = 0; $i < count($spreadsheet); $i++) {
            if ($spreadsheet[$i]->properties->title == $title) return $spreadsheet[$i];
        }
        return false;
    }

    /**
     * @param string $range
     * @return array|false
     */
    public static function view(string $range)
    {
        $sheet_title = explode('!', $range)[0];
        $spreadsheet = self::sheetExists($sheet_title);
        if($spreadsheet){
            $service = self::getService();
            $spreadsheetId = self::getSpreadsheetId();
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues();
        }
        return false;
    }

    /**
     * @param string $title
     */
    public static function addSheet(string $title)
    {
        $body = new Google\Service\Sheets\BatchUpdateSpreadsheetRequest();
        $body->setRequests([
            'addSheet' => [
                'properties' => [
                    'title' => $title,
                ]
            ]
        ]);

        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        return $service->spreadsheets->batchUpdate($spreadsheetId, $body);
    }

    /**
     * @param string $range
     */
    public static function clear($range)
    {
        $clear = new \Google\Service\Sheets\ClearValuesRequest();
        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        $service->spreadsheets_values->clear($spreadsheetId, $range, $clear);
    }

    /**
     * @param int $sheetId
     */
    public static function deleteSheetById(int $sheetId)
    {
        $body = new Google\Service\Sheets\BatchUpdateSpreadsheetRequest();
        // Delete Sheet
        $body->setRequests([
            'deleteSheet' => [
                'sheetId' => $sheetId,
            ]
        ]);

        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        return $service->spreadsheets->batchUpdate($spreadsheetId, $body);
    }

    /**
     * @param int $id
     * @return int | false
     */
    public static function deleteSheetByNumber(int $id)
    {
        $spreadsheet = self::getSpreadsheet();
        if (count($spreadsheet) <= $id) return false;
        $sheetId = $spreadsheet[$id]->properties->sheetId;
        return self::deleteSheetById($sheetId);
    }

    /**
     * 
     */
    public static function deleteSheetByTitle(string $title)
    {
        $spreadsheet = self::getSpreadsheet();
        if (!empty($spreadsheet)) {
            foreach ($spreadsheet as $key => $sheet) {
                if ($sheet->properties->title == $title) {
                    return self::deleteSheetByNumber($key);
                }
            }
            return false;
        }
    }

    /**
     * @param int $number
     * @param string $new_title
     * @return object | false 
     */
    public static function renameSheetByNumber(int $number, string $new_title)
    {
        $spreadsheet = self::getSpreadsheet();
        if (count($spreadsheet) <= $number) return false;
        $body = new Google\Service\Sheets\BatchUpdateSpreadsheetRequest();
        $body->setRequests([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $spreadsheet[$number]->properties->sheetId,
                    'title' => $new_title,
                ],
                'fields' => "title",
            ]
        ]);

        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();

        return $service->spreadsheets->batchUpdate($spreadsheetId, $body);
    }

    /**
     * 
     */
    public static function renameSheetByTitle(string $current_title, string $new_title)
    {
        $spreadsheet = self::getSpreadsheet();
        if (!empty($spreadsheet)) {
            foreach ($spreadsheet as $key => $sheet) {
                if ($sheet->properties->title == $current_title) {
                    return self::renameSheetByNumber($key, $new_title);
                }
            }
            return false;
        }
    }

    /**
     * @param array $rows
     * @param string $range
     * @return object
     */
    public static function appendRow(array $rows, string $range)
    {
        $valueRange = new \Google\Service\Sheets\ValueRange();
        $valueRange->setValues($rows);
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        return $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
    }

    /**
     * @param array $rows
     * @param string $range
     */
    public static function update(array $rows, string $range)
    {
        $valueRange = new \Google\Service\Sheets\ValueRange();
        $valueRange->setValues($rows);
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $service = Dannsheet::getService();
        $spreadsheetId = Dannsheet::getSpreadsheetId();
        return $service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $options);
    }

    /**
     * 
     */
    public static function prettyShow($values)
    {
        if(false === $values) return "Sheet not exist\n";
        if (empty($values)) {
            return "No data found\n";
        } else {
            $len = [];
            foreach ($values as $column) {
                foreach ($column as $value) {
                    $len[] = strlen($value);
                }
            }
            $max = max($len);
            $result = '';
            foreach ($values as $key => $column) {
                $abjad = "A";
                $abj = '';
                $count = count($column);
                $cols = '';
                $mask = '';
                for ($i = 0; $i < $count; $i++) {
                    $mask .= "%-{$max}s ";
                    $cols .= ", \$column[$i]";
                    $abj .= ", \$abjad++";
                }
                $mask .= "\n";
                if ($key == 0) {
                    $result .= $key . " ";
                    eval('$result .= sprintf($mask' . $abj . ');');
                    eval('$result .= $key + 1 . " ";');
                }
                $key++;
                eval('$result .= sprintf($mask' . $cols . ');');
                $result .= $key + 1 . " ";
            }
            return $result;
        }
    }
}
