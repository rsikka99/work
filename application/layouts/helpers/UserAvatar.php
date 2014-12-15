<?php

/**
 * Class App_View_Helper_UserAvatar
 */
class App_View_Helper_UserAvatar extends Zend_View_Helper_Abstract
{
    /**
     * @param int   $size
     *
     * @param array $classes
     *
     * @return string
     */
    public function UserAvatar ($size = 64, $classes = [])
    {
        if (!is_array($classes))
        {
            $classes = [$classes];
        }

        array_push($classes, 'user-avatar');

        $auth  = Zend_Auth::getInstance();
        $email = "guest@mpstoolbox.com";
        if ($auth->hasIdentity())
        {
            $email = $auth->getIdentity()->email;
        }

        $gravatar = new \lrobert\Gravatar\Gravatar();
        $gravatar->setDefaultImage(\lrobert\Gravatar\Gravatar::FALLBACK_MYSTERY_MAN);
        $gravatar->setSize($size);

        return sprintf('<img class="%3$s" src="%1$s" width="%2$s" height="%2$s" alt="User Avatar" />', $gravatar->getUrl($email), $size, implode(' ', $classes));
    }
}
