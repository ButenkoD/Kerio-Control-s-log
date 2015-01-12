<?php

namespace model;


class CellReportModel
{
    private $notification;
    private $timePair;
    private $workedHours = 0;
    const MIN_WORKED_HOURS = 8;

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
     * @return string
     */
    public function getTimePair()
    {
        return $this->timePair;
    }

    /**
     * @param array $timePair
     */
    public function setTimePair($timePair)
    {
        $this->timePair = $timePair;
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
        return $this->workedHours >= self::MIN_WORKED_HOURS;
    }

    /**
     * @param mixed $isWholeDay
     */
    public function setIsWholeDay($isWholeDay)
    {
        $this->isWholeDay = $isWholeDay;
    }


}