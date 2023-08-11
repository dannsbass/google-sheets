<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../examples/config.php';

$bot = new Bot(BOT_TOKEN, BOT_USERNAME);

$bot->start(function () {
    Botsheet::saveUserDetails('Sheet2', 'A1:A');
    return Bot::sendMessage("Welcome to our bot");
});

$bot->text(function($text){
    $result = Botsheet::search($text, 'Sheet1!B2:B');
    if(!$result) return Bot::sendMessage('Not found');
    return Bot::sendMessage($result);
});

$bot->run();

class Botsheet
{
    /**
     * @param string $keyword
     * @param string $range
     * @return string|false
     */
    public static function search(string $keyword, string $range){
        $source = Dannsheet::view($range);
        foreach ($source as $array) {
            foreach($array as $value){
                if(strpos(strtolower($value), strtolower($keyword)) !== false) return $value;
            }
        }
        return false;
    }

    /**
     * @param string $sheet
     * @param string $range
     * @return 
     */
    public static function saveUserDetails(string $sheet, string $range)
    {
        $message = Bot::message();
        $from_id = $message['from']['id'];
        $rows = Dannsheet::view("$sheet!$range");
        $user_row = empty($rows) ? false : self::getUserRow($from_id, $rows);
        if (!$user_row) {
            $user_details = [
                $from_id,
                $message['from']['first_name'],
                json_encode($message),
            ];
            $values = [$user_details];
            return Dannsheet::appendRow($values, $sheet);
        }else{
            return false;
        }
    }

    /**
     * @param string $chat_id
     * @param array $rows
     * @return int|false 
     */
    public static function getUserRow(string $chat_id, array $rows)
    {
        foreach ($rows as $key => $value) {
            if (in_array($chat_id, $value)) return $key + 1;
            return false;
        }
    }
}
