<?php

/**
 * Class My_Model_Abstract
 * A generic model that has the logic for filtering and validating data
 *
 * @author "Lee Robert"
 */
abstract class My_Model_Abstract extends stdClass
{
    static protected $_auth_dealerId;

    /**
     * An array of filters that we can use in setters if we wish
     *
     * @var array
     */
    protected $_filters;

    /**
     * An array of validators that we can use in setters if we wish
     *
     * @var array
     */
    protected $_validators;

    public function __construct (array $options = null)
    {
        if (is_array($options))
        {
            $this->populate($options);
        }
    }

    /**
     * @return mixed
     */
    public static function getAuthDealerId()
    {
        if (self::$_auth_dealerId === null) {
            $user = Zend_Auth::getInstance()->getIdentity();
            self::$_auth_dealerId = $user ? $user->dealerId : 0;
        }
        return self::$_auth_dealerId;
    }

    /**
     * @param mixed $auth_dealerId
     */
    public static function setAuthDealerId($auth_dealerId)
    {
        self::$_auth_dealerId = $auth_dealerId;
    }



    abstract public function populate ($params);

    abstract public function toArray ();
}