<?php
namespace service;

use \model\UserActionModel;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.01.15
 * Time: 17:11
 */
class UserActionService
{
    private $userDailyActions = array();

    public function getUserDailyAction($key)
    {
        return $this->userDailyActions[$key];
    }

    public function addUserDailyAction($row, $date)
    {
        $key = $row['username'] . $date;
        $row['date'] = $date;
        if (isset($this->userDailyActions[$key])) {
            $userAction = $this->userDailyActions[$key];
            $userAction->addAction($row['action_type'], $row['date_time']);
        } else {
            $userAction = new UserActionModel($row);
        }
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