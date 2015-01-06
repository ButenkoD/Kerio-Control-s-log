<?php

namespace model;

use \model\CellReportModel;

class UserActionModel
{
    private $logins;
    private $logouts;
    private $logPairs;
    private $workedSeconds = 0;
    private $username;
    private $date;

    function __construct($row)
    {
        $this->logins = array();
        $this->logouts = array();
        $this->setUsername($row['username']);
        $this->addAction($row['action_type'], $row['date_time']);
        $this->date = $row['date'];
        return $this;
    }

    public function getWorkedHours()
    {
        return $this->workedSeconds / 3600;
    }


    public function generateKey()
    {
        return $this->date . $this->username;
    }

    public function getLogins()
    {
        return $this->logins;
    }

    public function setLogins(array $logins)
    {
        $this->logins = $logins;
    }


    /**
     * @return mixed
     */
    public function getLogouts()
    {
        return $this->logouts;
    }

    /**
     * @param mixed $logouts
     */
    public function setLogouts(array $logouts)
    {
        $this->logouts = $logouts;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        return $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function addAction($action, $date)
    {
        $date = date('H:i', strtotime($date));
        if ($action == 'logged in') {
            $this->logins[] = $date;
        } else {
            $this->logouts[] = $date;
        }
    }


    private function getLogTimePairs($logins, $logouts, $logPairs = array())
    {
        $exit = false;

        $firstLogIn = date($logins[0]);
        $firstLogOut = date($logouts[0]);
        if (empty($firstLogIn) || empty($firstLogOut)) {
            $logPairs[] = empty($firstLogIn) ? 'Logout: ' . $firstLogOut : 'Login: ' . $firstLogIn;

        } else {
            //если нет первого лога
            // Проверяем был ли логаут до логина
            while ($firstLogIn > $firstLogOut) {
                // ищем первый логаут после логина
                array_shift($logouts);
                if (null == $logouts) {
                    // выходим если нет логаутов
                    break;
                }
            }
            // если это был последний логин ставим самый поздний логаут
            if (count($logins) == 1 && count($logouts) > 1) {
                $firstLogOut = max($logouts);
            } else {
                // находим и убираем логины до логаута
                $tempLog = $logins[0];
                while ($tempLog < $firstLogOut) {
                    array_shift($logins);
                    if (null == $logins) {
                        // выходим если нет логаутов
                        break;
                    }
                    $tempLog = $logins[0];
                }
            }
            $this->workedSeconds += strtotime($firstLogOut) - strtotime($firstLogIn);
            $logPairs[] = $firstLogIn . ' -- ' . $firstLogOut;
            $exit = true;

        }
        return $logPairs;
    }

    public function calculateDailyReport()
    {

        $dayReport = new CellReportModel();
        //если записаны только логины или логауты
        if (empty($this->logins) xor empty($this->logouts)) {
            if (empty($this->logins)) {
                $dayReport->setMessages($this->logouts);
                $dayReport->setNotification('Only Logouts');
            } else {
                $dayReport->setMessages($this->logins);
                $dayReport->setNotification('Only Logins');
            }
        } else {// если есть и логин и логаут
            asort($this->logins);
            asort($this->logouts);
            $logins = new \ArrayObject($this->logins);
            $logouts = new \ArrayObject($this->logouts);
            $pairs = $this->getLogTimePairs($logins->getArrayCopy(), $logouts->getArrayCopy());
            $dayReport->setMessages($pairs);
            $dayReport->setWorkedHours($this->getWorkedHours());
        }
        return $dayReport;
    }

}