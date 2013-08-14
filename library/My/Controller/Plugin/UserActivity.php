<?php
/**
 * Class My_Controller_Plugin_UserActivity
 */
class My_Controller_Plugin_UserActivity extends Zend_Controller_Plugin_Abstract
{
    /**
     * The amount minimum time that we use to cut a user activity record
     */
    const RECORD_TIME_DELAY_SECONDS = 10;

    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        // Check if the user is logged in
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            $userIdentity = $auth->getIdentity();
            $uri          = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $currentTime  = date('Y-m-d H:i:s');

            // Do we have a user activity for this user
            if (isset($userIdentity->lastSeen))
            {
                // If that time difference is greater than an hour since the last record cut a new record
                $timeDiff = strtotime($currentTime) - strtotime($userIdentity->lastSeen);

                if ($timeDiff > self::RECORD_TIME_DELAY_SECONDS)
                {
                    // Get the latest time from the database for the user activity
                    $userActivity           = new Application_Model_User_Activity();
                    $userActivity->userId   = $userIdentity->id;
                    $userActivity->lastSeen = $currentTime;
                    $userActivity->url      = $uri;
                    Application_Model_Mapper_User_Activity::getInstance()->insert($userActivity);

                    // Save up the last seen field to the user time
                    $user           = Application_Model_Mapper_User::getInstance()->find($userIdentity->id);
                    $user->lastSeen = $currentTime;
                    Application_Model_Mapper_User::getInstance()->save($user);
                    $userIdentity->lastSeen = $currentTime;
                }
            }
            else
            {
                // Insert a new row at the current time into the table
                $userActivity           = new Application_Model_User_Activity();
                $userActivity->userId   = $userIdentity->id;
                $userActivity->url      = $uri;
                $userActivity->lastSeen = $currentTime;

                Application_Model_Mapper_User_Activity::getInstance()->insert($userActivity);

                // Save up the last seen field to the user time
                $user           = Application_Model_Mapper_User::getInstance()->find($userIdentity->id);
                $user->lastSeen = $currentTime;
                Application_Model_Mapper_User::getInstance()->save($user);

                $userIdentity->lastSeen = $currentTime;
            }
        }
    }
}