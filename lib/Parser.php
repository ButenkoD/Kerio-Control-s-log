<?php


use \service\KDateUtil;

/**
 * Class Parser
 */
class Parser
{
    const SECONDS_IN_HOUR = 3600;
    const LOGGED_IN_ACTION = 'logged in';
    /**
     * Конфиг синтаксиса лога
     */
    const INDEX_DATE = 1;
    const INDEX_IP4 = 2;
    const INDEX_MAC = 3;
    const INDEX_USERNAME = 4;
    const INDEX_ACTION = 5;

    private $vpnList;

    // Регекс [дата] [IPv4 xxx.xxx.xxx.xxx] [MAC yy-yy-yy-yy-yy-yy] (([User] или User logged (in или out) ) или Host registered)
    const PRE_MATCH_USER_LOG = '/\[(\d{2}\/\w{3}\/\d{4} \d{2}:\d{2}:\d{2})\].*\[IPv4\](\s\d+\.\d+\.\d+\.\d+).*\[MAC\]\s(\w{2}\-\w{2}\-\w{2}\-\w{2}\-\w{2}\-\w{2}).*(?:\[?User\]?\s([\w\.]+)\s.*(logged\s\w+)|(?:Host\sregistered)).*/i';

    private $databaseHandler;

    public function __construct(Database $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
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

        $ipMacMap = array();

        preg_match_all(self::PRE_MATCH_USER_LOG, $log, $records, PREG_SET_ORDER);
        $existLogMap = $this->getExistingLogsMap($records, $logRepository);
        $userNum = 0;
        $skipped = 0;
        $created = 0;

        foreach ($this->filterByVpn($records) as $record) {
            $timestamp = KDateUtil::toTimestampLOG($record[self::INDEX_DATE]);
            $dateOnly = KDateUtil::toDateOnly($timestamp);

            //array_map()
            $ipKey = $record[self::INDEX_IP4] . $record[self::INDEX_MAC] . $dateOnly;
            $hostTime = $ipMacMap[$ipKey];
            // если масив имеет меньше 5 єлементов значит регистрация хоста
            if (count($record) < 5) {
                // сохраняем время регистрации если регистрация хоста раньше логина но не раньше минимального времени (7:30)
                if (!isset($hostTime) || ($hostTime > $timestamp && $hostTime > KDateUtil::toMinLogTimeStr($dateOnly))) {
                    $ipMacMap[$ipKey] = $timestamp;
                }
            } else {
                // если логин/логаут пользователя
                // выгребаем имя пользователя
                $username = $record[self::INDEX_USERNAME];
                $action = $record[self::INDEX_ACTION];

                // если есть регистация хоста раньше логина то используем ее
                if ($hostTime && ($action == self::LOGGED_IN_ACTION) && ($timestamp > $hostTime)) {
                    $timestamp = $hostTime;
                }
                if (in_array($timestamp, $existLogMap[$username . $record[self::INDEX_ACTION]])) {
                    $skipped++;
                } else {
                    if (!in_array($username, $users)) {
                        $users[$nextUserId + $userNum] = $username;
                        $newlyCreatedUsers[] = ['username' => $username];
                        $userNum++;
                    }

                    $result[] = [
                        'username' => $username,
                        'user_id' => array_search($username, $users),
                        'action' => $action,
                        'datetime' => KDateUtil::toStringLOG($timestamp)
                    ];
                    $created++;
//            if ((count($result) % 10000) == 0){
//                $this->databaseHandler->saveParsedData($result);
//                var_dump(date('H:i:s', time()));
//                $result = [];
//            }
                }
            }

        }
//        $this->databaseHandler->saveParsedData($result);
//        $this->databaseHandler->saveUsers($newlyCreatedUsers);
        // Чистим временные массивы
        unset($existLogMap);
        unset($ipMacMap);
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
     * Формирует набор существующих логов в индексированный массив
     *
     * @param $records
     * @param $logRepository
     * @return existLogMap
     */
    private function getExistingLogsMap($records, $logRepository)
    {
        $min = KDateUtil::toTimestampLOG($records[0][self::INDEX_DATE]);
        $max = $min;
        // ищем диапазон дат
        foreach ($records as $record) {
            $current = KDateUtil::toTimestampLOG($record[self::INDEX_DATE]);
            if ($current > $max) {
                $max = $current;
            }
            if ($current < $min) {
                $min = $current;
            }
        }
        $existLogs = $logRepository->getUsers(KDateUtil::toStringSQL($min), KDateUtil::toStringSQL($max));
        $existLogMap = array();
        foreach ($existLogs as $existLog) {
            $existLogMap[$existLog['username'] . $existLog['action_type']][] = KDateUtil::toTimestampSQL($existLog['date_time']);
        }
        // чистим временный массив
        unset($existLogs);
        return $existLogMap;
    }

    private function getVpnList()
    {
        if (!isset($this->vpnList)) {
            $this->vpnList = parse_ini_file(CONFIG_PATH . 'vpn_ignore.ini');
        }
        return $this->vpnList['vpn'];

    }

    /**
     * @param $records
     * @return array
     */
    private function filterByVpn($records)
    {
        // получаем список vpn
        $listVpn = $this->getVpnList();
        $records = array_filter($records, function ($var) use ($listVpn) {
            foreach ($listVpn as $vpn) {
                // если находим совпадение - исключаем
                if (strpos($var[self::INDEX_IP4], $vpn)) {
                    return false;
                }
            }
            return true;
        });
        return $records;
    }

}
