<?php

namespace model;

use \model\CellReportModel;
use \model\TimePeriod;

class UserActionModel
{
    private $registeredLogIns;
    private $registeredLogOuts;
    private $workedSeconds = 0;
    private $username;
    private $date;

    function __construct($username, $date)
    {
        $this->registeredLogIns = array();
        $this->registeredLogOuts = array();
        $this->setUsername($username);
        $this->setDate($date);
        return $this;
    }

    public function getWorkedHours()
    {
        return $this->workedSeconds / 3600;
    }


    public function getRegisteredLogIns()
    {
        return $this->registeredLogIns;
    }

    public function setRegisteredLogIns(array $registeredLogIns)
    {
        $this->registeredLogIns = $registeredLogIns;
    }


    /**
     * @return mixed
     */
    public function getRegisteredLogOuts()
    {
        return $this->registeredLogOuts;
    }

    /**
     * @param mixed $registeredLogOuts
     */
    public function setRegisteredLogOuts(array $registeredLogOuts)
    {
        $this->registeredLogOuts = $registeredLogOuts;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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

    public function addAction($action, $time)
    {
        if ($action == 'logged in') {
            $this->registeredLogIns[] = $time;
        } else {
            $this->registeredLogOuts[] = $time;
        }
    }


    private function getTimePeriods($logPair = array())
    {
        $timePeriod = new TimePeriod();
        $logIn = current($this->registeredLogIns);
        // находим первый логаут после логина
        $logOut = $this->findNextLoginAfterLogout(false);
        $timePeriod->setTimeIn($logIn);
        $timePeriod->setTimeOut($logOut);
        // Пропускаем логауты, если они были раньше логина
        $this->workedSeconds += $timePeriod->getWorkedSeconds();
        // сохраняем текущую пару логина-логаута
        $logPair[] = $timePeriod->toString();
        // если есть еще логины то рекурсивно записываем следующие
        if ($this->findNextLoginAfterLogout(true)) {
            $logPair = $this->getTimePeriods($logPair);
        }
        return $logPair;
    }

    public function calculateDailyReport()
    {
        $dayReport = new CellReportModel();
        $dayReport->setTimePairs($this->getTimePeriods());
        $dayReport->setWorkedHours($this->getWorkedHours());
        return $dayReport;
    }


    /**
     * @param $seekLogin
     * @return mixed
     */
    private function findNextLoginAfterLogout($seekLogin)
    {
        if ($seekLogin) {
            $elem1 = current($this->registeredLogOuts);
            $elem2 = next($this->registeredLogIns);
        } else {
            $elem2 = current($this->registeredLogOuts);
            $elem1 = current($this->registeredLogIns);
        }
        while ($elem2 && $elem2 < $elem1) {
            $elem2 = $seekLogin ? next($this->registeredLogIns) : next($this->registeredLogOuts);
        }
        return $elem2;
    }


}