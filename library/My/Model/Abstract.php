<?php

/**
 * Class My_Model_Abstract
 * A generic model that has the logic for filtering and validating data
 *
 * @author "Lee Robert"
 */
abstract class My_Model_Abstract extends stdClass
{
    protected $_filters;
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