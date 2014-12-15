<?php
use MPSToolbox\Legacy\Mappers\UserActivityMapper;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Models\UserActivityModel;

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
        try
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
                        $userActivity           = new UserActivityModel();
                        $userActivity->userId   = $userIdentity->id;
                        $userActivity->lastSeen = $currentTime;
                        $userActivity->url      = $uri;
                        UserActivityMapper::getInstance()->insert($userActivity);

                        // Save up the last seen field to the user time
                        $user           = UserMapper::getInstance()->find($userIdentity->id);
                        $user->lastSeen = $currentTime;
                        UserMapper::getInstance()->save($user);
                        $userIdentity->lastSeen = $currentTime;
                    }
                }
                else
                {
                    // Insert a new row at the current time into the table
                    $userActivity           = new UserActivityModel();
                    $userActivity->userId   = $userIdentity->id;
                    $userActivity->url      = $uri;
                    $userActivity->lastSeen = $currentTime;

                    UserActivityMapper::getInstance()->insert($userActivity);

                    // Save up the last seen field to the user time
                    $user           = UserMapper::getInstance()->find($userIdentity->id);
                    $user->lastSeen = $currentTime;
                    UserMapper::getInstance()->save($user);

                    $userIdentity->lastSeen = $currentTime;
                }
            }
        }
        catch (Exception $e)
        {
            // Do nothing but log here since we don't want to get in the way of the user when logging activity.
            \Tangent\Logger\Logger::logException($e);
        }
    }
}