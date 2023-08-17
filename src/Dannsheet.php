<?php

use \Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use \Google\Service\Sheets\ClearValuesRequest;
use \Google\Service\Sheets\ValueRange;
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

    /**
     * To get client
     */
    public static function getClient()
    {
        $client = new \Google\Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(self::$credentials);
        return $client;
    }

    /**
     * To get service
     */
    public static function getService()
    {
        $client = self::getClient();
        return new \Google\Service\Sheets($client);
    }

    /**
     * To get spreadsheet
     */
    public static function getSpreadsheet()
    {
        $service = self::getService();
        return $service->spreadsheets->get(self::$spreadsheetId);
    }

    /**
     * To check if sheet exists by title
     * if so return object
     * if not return false
     * @param string $title
     * @return object|false
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
     * To get values of range
     * if data found return the values
     * if not return false
     * @param string $range
     * @return array|false
     */
    public static function getValues(string $range)
    {
        $sheet_title = explode('!', $range)[0];
        if(self::sheetExists($sheet_title)){
            $service = self::getService();
            $spreadsheetId = self::getSpreadsheetId();
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues();
        }
        return false;
    }


    /**
     * To get string of data
     * @param string $range
     */
    public static function quickShow(string $range)
    {
        return self::prettyShow(self::view($range));
    }

    /**
     * To print data
     */
    public static function prettyPrint(string $range){
        echo self::quickShow($range);
    }

    /**
     * To print all sheet titles
     */
    public static function printSheetTitles()
    {
        $ss = self::getSpreadsheet();
        for ($i = 0; $i < count($ss); $i++) {
            echo $ss[$i]->properties->title . "\n";
        }
    }

    /**
     * alias function
     * @param string $range
     */
     public static function view(string $range){
        return self::getValues($range);
     }


    /**
     * @param string $title
     */
    public static function addSheet(string $title)
    {
        $body = new BatchUpdateSpreadsheetRequest();
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
    public static function clear(string $range)
    {
        $clear = new ClearValuesRequest();
        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        return $service->spreadsheets_values->clear($spreadsheetId, $range, $clear);
    }

    /**
     * 
     */
    public static function batchUpdate(BatchUpdateSpreadsheetRequest $body)
    {
        $service = self::getService();
        $spreadsheetId = self::getSpreadsheetId();
        return $service->spreadsheets->batchUpdate($spreadsheetId, $body);
    }

    /**
     * 
     */
    public static function deleteRow(int $startIndex, int $endIndex, string $sheet_name)
    {
        $sheet = self::sheetExists($sheet_name);
        if(!$sheet) return false;
        $body = new BatchUpdateSpreadsheetRequest();
        $body->setRequests([
            'deleteDimension' => [
                'range' => [
                    'sheetId' => $sheet->properties->sheetId,
                    "dimension" => "ROWS",
                    "startIndex" => $startIndex,
                    "endIndex" => $endIndex,
                ]
            ]
        ]);
        return self::batchUpdate($body);
    }
    // {
    //     "requests": [
    //       {
    //         "deleteDimension": {
    //           "range": {
    //             "sheetId": sheetId,
    //             "dimension": "ROWS",
    //             "startIndex": 5,
    //             "endIndex": 6
    //           }
    //         }
    //       }
    //     ],
    //   }

    /**
     * 
     */
    public static function findRowByValue($value, $range){
        $values = Dannsheet::getValues($range);
        if(!empty($values)){
            foreach ($values as $key => $array) {
                foreach ($array as $k => $v) {
                    if($v == $value) return $key + 1;
                }
            }
        }
        return false;
    }

    /**
     * @param int $sheetId
     */
    public static function deleteSheetById(int $sheetId)
    {
        $body = new BatchUpdateSpreadsheetRequest();
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
        $body = new BatchUpdateSpreadsheetRequest();
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
        $valueRange = new ValueRange();
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
        $valueRange = new ValueRange();
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
