<?php

namespace model;

use \model\CellReportModel;
use \model\TimePeriod;
use service\KDateUtil;

class UserActionModel
{
    private $registeredLogIns;
    private $registeredLogOuts;
    private $workedSeconds = 0;
    private $username;
    private $date;

    const SECONDS_IN_HOUR = 3600;

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
        return $this->workedSeconds / self::SECONDS_IN_HOUR;
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


    private function getTimePeriod()
    {
        $timePeriod = new TimePeriod();
        $logIn = $this->getFirstDailyLogin();
        // находим первый логаут после логина
        $logOut = $this->findLogoutAfterLastLogin($logIn);
        $timePeriod->setTimeIn($logIn);
        $timePeriod->setTimeOut($logOut);
        // Пропускаем логауты, если они были раньше логина
        $this->workedSeconds += $timePeriod->getWorkedSeconds();
        return $timePeriod->toString();
    }

    public function calculateDailyReport()
    {
        $dayReport = new CellReportModel();
        $dayReport->setTimePair($this->getTimePeriod());
        $dayReport->setWorkedHours($this->getWorkedHours());
        return $dayReport;
    }


    /**
     * @return mixed
     */
    private function findLogoutAfterLastLogin($logIn)
    {
        $elem2 = current($this->registeredLogOuts);
        while ($elem2 && $elem2 < $logIn) {
            $elem2 = next($this->registeredLogOuts);
        }
        return $elem2;
    }

    /**
     * @return mixed
     */
    private function getFirstDailyLogin()
    {
        $firstLogin = $logIn = current($this->registeredLogIns);
        $minLogTime = KDateUtil::toMinLogTime($logIn);
        while ($logIn && ($logIn < $minLogTime)) {
            $logIn = next($this->registeredLogIns);
        }
        if (!$logIn) {
            $logIn = $firstLogin;
        }
        return $logIn;
    }


}