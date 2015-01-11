<?php

namespace model;


class TimePeriod
{
    private $timeIn = null;
    private $timeOut = null;

    /**
     * @return mixed
     */
    public function getTimeIn()
    {
        return $this->timeIn;
    }

    /**
     * @param mixed $timeIn
     */
    public function setTimeIn($timeIn)
    {
        if ($timeIn) {
            $this->timeIn = $timeIn;
        }

    }

    /**
     * @return mixed
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * @param mixed $timeOut
     */
    public function setTimeOut($timeOut)
    {
        if ($timeOut) {
            $this->timeOut = $timeOut;
        }

    }

    public function getWorkedSeconds()
    {
        return (empty($this->timeIn) || empty($this->timeOut)) ? 0 : $this->timeOut - $this->timeIn;
    }

    private function getTimeToString($time)
    {
        return isset($time) ? date('H:i', $time) : '';
    }

    public function toString()
    {
        return $this->getTimeToString($this->timeIn) . ' -- ' . $this->getTimeToString($this->timeOut);
    }


}