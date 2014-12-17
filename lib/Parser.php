<?php

/**
 * Class Parser
 */
class Parser
{
    /**
     * Конфиг синтаксиса лога
     */
    const PRE_USERNAME_STRING_WHILE_LOG_IN  = "[User]";
    const PRE_USERNAME_STRING_WHILE_LOG_OUT = 'User';
    const PRE_LOG_IN_STRING                 = 'logged in';
    const PRE_LOG_OUT_STRING                = 'logged out';
    const TIME_START_STRING                 = '[';
    const TIME_END_STRING                   = ']';

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
//        $records = explode("\n", $log);
        $records = $log;
//
        $result = [];
        $usersData = $this->databaseHandler->getUsers();

        $users = [];
        foreach ($usersData as $user){
            $users[$user['id']] = $user['username'];
        }
        $nextUserId = $this->databaseHandler->getNextUserId();
        $newlyCreatedUsers = [];

        foreach($records as $record) {
            if (!(strpos($record, self::PRE_LOG_IN_STRING))
                && !(strpos($record, self::PRE_LOG_OUT_STRING))) {
                continue;
            }

            if ($begining = strpos($record, self::PRE_USERNAME_STRING_WHILE_LOG_IN)) {
                $actionType = self::PRE_LOG_IN_STRING;
                $tail = substr($record, $begining + strlen(self::PRE_USERNAME_STRING_WHILE_LOG_IN) + 1);
            } elseif ($begining = strpos($record, self::PRE_USERNAME_STRING_WHILE_LOG_OUT)) {
                $actionType = self::PRE_LOG_OUT_STRING;
                $tail = substr($record, $begining + strlen(self::PRE_USERNAME_STRING_WHILE_LOG_OUT) + 1);
            }

            // выгребаем имя пользователя
            $username = substr($tail, 0, strpos($tail, ' '));

            $datetime = substr(
                $record,
                strpos($record, self::TIME_START_STRING) + strlen(self::TIME_START_STRING),
                strpos($record, self::TIME_END_STRING) - strlen(self::TIME_END_STRING)
            );
            // @TODO check if there's need to continue parsing
//            date_create_from_format('d/M/Y H:i:s', $datetime)->getTimestamp();

            if (!in_array($username, $users)){
                $users[(max(array_keys($users))+1)] = $username;
                $this->databaseHandler->saveUser(['username' => $username]);
            }
            $result[] = [
                'username' => $username,
                'action'   => $actionType,
                'datetime' => $datetime,
            ];
//            if ((count($result) % 10000) == 0){
//                $this->databaseHandler->saveParsedData($result);
//                var_dump(date('H:i:s', time()));
//                $result = [];
//            }
        }
//        $this->databaseHandler->saveParsedData($result);
        echo 'Hell yeah!';

        return $result;
    }

    public function getLog($path)
    {
        set_time_limit(60);
        $fp = fopen($path, 'r');

        $pos = -2; // Skip final new line character (Set to -1 if not present)

        $lines = array();
        $currentLine = '';

//        while (-1 !== fseek($fp, $pos, SEEK_END)) {
        while (-1 !== fseek($fp, $pos, SEEK_END)) {
            $char = fgetc($fp);
            if (PHP_EOL == $char) {
                yield $currentLine;
//                $lines[] = $currentLine;
                $currentLine = '';
            } else {
                $currentLine = $char . $currentLine;
            }
            $pos--;
        }

//        $lines[] = $currentLine; // Grab final line

//        return $lines;
    }
}
