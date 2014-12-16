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

    public function __construct($latestDate)
    {
        $this->latestDate = strtotime($latestDate);

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
        $records = explode("\n", $log);

        $result = [];
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
            date_create_from_format('d/M/Y H:i:s', $datetime)->getTimestamp();

            $result[] = [
                'username' => $username,
                'action'   => $actionType,
                'datetime' => $datetime,
            ];
        }

        return $result;
    }
}
