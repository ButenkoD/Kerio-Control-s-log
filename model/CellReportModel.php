<?php

namespace model;


class CellReportModel
{
    private $notification;
    private $timePairs = array();
    private $workedHours = 0;

    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param mixed $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    public function getTimePairs()
    {
        return $this->timePairs;
    }

    /**
     * @param array $timePairs
     */
    public function setTimePairs($timePairs)
    {
        $this->timePairs = $timePairs;
    }

    /**
     * @return int
     */
    public function getWorkedHours()
    {
        return $this->workedHours;
    }

    /**
     * @param int $workedHours
     */
    public function setWorkedHours($workedHours)
    {
        $this->workedHours = $workedHours;
    }

    public function addWorkedHours($workedHours)
    {
        $this->workedHours += $workedHours;
    }

    /**
     * @return mixed
     */
    public function getIsWholeDay()
    {
        return $this->workedHours >= 8;
    }

    /**
     * @param mixed $isWholeDay
     */
    public function setIsWholeDay($isWholeDay)
    {
        $this->isWholeDay = $isWholeDay;
    }


}