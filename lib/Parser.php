<?php


use \model\SqlDateFormat;
use \model\LogDateFormat;

/**
 * Class Parser
 */
class Parser
{
    /**
     * Конфиг синтаксиса лога
     */
    const INDX_DATE = 1;
    const INDX_UNAME = 2;
    const INDX_ACTION = 3;

    const PRE_MATCH_USER_LOG = '/\[(\d{2}\/\w{3}\/\d{4} \d{2}:\d{2}:\d{2})\].*\[IPv4\]\s192\.168\.\d+\.\d+.*(?:\[?User\]?\s+([\w\.]+)\s.*(logged\s+\w+)).*/';
    //'/\[(\d{2}\/\w{3}\/\d{4} \d{2}:\d{2}:\d{2})\].*\[IPv4\]\s(\d+\.\d+\.\d+\.\d+).*\[MAC\]\s(\w{2}\-\w{2}\-\w{2}\-\w{2}\-\w{2}\-\w{2}).*(?:\[User\]\s([\w\.]+)\s.*(logged \w+)|(?:Host\sregistered)).*/';


    private $latestDate;
    private $databaseHandler;

    public function __construct(Database $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
//        $this->latestDate = strtotime($latestDate);

        return $this;
    }

    /**
     * Парсинг лога
     * @param string $log
     * @return array
     */
    public function parseString($log)
    {
        // разбиваем на строки
        //$records = explode("\n", $log);
//        $records = $log;
//
        $result = [];
        $userHandler = new UserRepository();
        $logRepository = new LogRepository();
//        $usersData = $this->databaseHandler->getUsers();
        $usersData = $userHandler->getUsers();

        $users = [];
        foreach ($usersData as $user) {
            $users[$user['id']] = $user['username'];
        }
        $newlyCreatedUsers = [];
//        $nextUserId = $this->databaseHandler->getNextUserId();
        $nextUserId = $userHandler->getNextUserId();

        //counter for newly created users' ids

        $logFormatter = new LogDateFormat();
        $sqlFormatter = new SqlDateFormat();

        preg_match_all(self::PRE_MATCH_USER_LOG, $log, $records, PREG_SET_ORDER);
        $existLogMap = $this->getExistingLogsMap($records, $logRepository, $logFormatter, $sqlFormatter);
        $userNum = 0;
        $skipped = 0;
        $created = 0;

        foreach ($records as $record) {
            // выгребаем имя пользователя
            $username = $record[self::INDX_UNAME];
            if (in_array($logFormatter->stringToTimestamp($record[self::INDX_DATE]), $existLogMap[$username . $record[self::INDX_ACTION]])) {
                $skipped++;
            } else {
                // @TODO check if there's need to continue parsing
                //date_create_from_format('d/M/Y H:i:s', $datetime)->getTimestamp();
                if (!in_array($username, $users)) {
                    $users[$nextUserId + $userNum] = $username;
                    $newlyCreatedUsers[] = ['username' => $username];
                    $userNum++;
                }

                $result[] = [
                    'username' => $username,
                    'user_id' => array_search($username, $users),
                    'action' => $record[self::INDX_ACTION],
                    'datetime' => $record[self::INDX_DATE]
                ];
                $created++;
//            if ((count($result) % 10000) == 0){
//                $this->databaseHandler->saveParsedData($result);
//                var_dump(date('H:i:s', time()));
//                $result = [];
//            }
            }
        }
//        $this->databaseHandler->saveParsedData($result);
//        $this->databaseHandler->saveUsers($newlyCreatedUsers);
        $userHandler->saveUsers($newlyCreatedUsers);
        echo "Created: $userNum user records <br> Created: $created log records <br> Skipped: $skipped log records <br>";
        return $result;
    }

    /**
     * Method for yielding log data line by line
     * @param string $path
     * @param string $fromDate
     * @return string $currentLine
     */
    public function getLogFromDate($path, $fromDate = null)
    {
        set_time_limit(60);
        $fp = fopen($path, 'r');

        $pos = -2; // Skip final new line character (Set to -1 if not present)

        $lines = [];
        $currentLine = '';

        while (-1 !== fseek($fp, $pos, SEEK_END)) {
            $char = fgets($fp);
            if (PHP_EOL == $char) {
                $lines[] = $currentLine;
                $currentLine = '';
            } else {
                $currentLine = $char . $currentLine;
            }
            if ($fromDate && strpos($currentLine, $fromDate)) {
                break;
            }
            $pos--;
        }
        fclose($fp);
        //$lines[] = $currentLine;
        return implode(PHP_EOL, $lines);
    }

    /**
     * @param $records
     * @param $logRepository
     * @return existLogMap
     */
    private function getExistingLogsMap($records, $logRepository, $logFormatter, $sqlFormatter)
    {
        $min = $logFormatter->stringToTimestamp($records[0][self::INDX_DATE]);
        $max = $min;
        // ищем диапазон дат
        foreach ($records as $record) {
            $current = $logFormatter->stringToTimestamp($record[self::INDX_DATE]);
            if ($current > $max) {
                $max = $current;
            }
            if ($current < $min) {
                $min = $current;
            }
        }
        $existLogs = $logRepository->getUsers($sqlFormatter->timestampToString($min), $sqlFormatter->timestampToString($max));
        $existLogMap = array();
        foreach ($existLogs as $existLog) {
            $existLogMap[$existLog['username'] . $existLog['action_type']][] = $sqlFormatter->stringToTimestamp($existLog['date_time']);
        }
        unset($existLogs);
        return $existLogMap;
    }

}
