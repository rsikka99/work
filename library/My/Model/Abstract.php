<?php

/**
 * Class My_Model_Abstract
 * A generic model that has the logic for filtering and validating data
 *
 * @author "Lee Robert"
 */
abstract class My_Model_Abstract extends stdClass
{
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

    abstract public function populate ($params);

    abstract public function toArray ();
}