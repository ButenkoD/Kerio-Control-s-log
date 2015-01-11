<?php
namespace service;

use \model\UserActionModel;

class UserActionService
{
    const SPLIT_HOUR = 3;


    private $userDailyActions = array();

    public function getUserDailyAction($key)
    {
        return $this->userDailyActions[$key];
    }

    public function addUserDailyAction($row)
    {
        $date = $row['date_time'];
        $time = strtotime($date);
        // если действие было до xх часов ночи, относим его к предыдущему дню
        if (date('H', $time) < self::SPLIT_HOUR) {
            $dateKey = date('Y-m-d', strtotime($row['date_time'] . ' -1 day'));
        } else {
            $dateKey = date('Y-m-d', $time);
        };
        $key = $row['username'] . $dateKey;
        if (isset($this->userDailyActions[$key])) {
            $userAction = $this->userDailyActions[$key];
        } else {
            $userAction = new UserActionModel($row['username'], $dateKey);
        }
        $userAction->addAction($row['action_type'], $time);
        $this->userDailyActions[$key] = $userAction;
    }

    /**
     * @return mixed
     */
    public function getUserDailyActions()
    {
        return $this->userDailyActions;
    }

    /**
     * @param UserActionModel $userDailyActions
     */
    public function setUserDailyActions(UserActionModel $userDailyActions)
    {
        $this->userDailyActions = $userDailyActions;
    }


}