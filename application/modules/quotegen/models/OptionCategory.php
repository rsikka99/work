<?php

/**
 * Quotegen_Model_OptionCategory
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_OptionCategory extends My_Model_Abstract
{
    
    /**
     * The id of the category
     *
     * @var int
     */
    protected $_categoryId = 0;
    
    /**
     * The id of the option
     *
     * @var int
     */
    protected $_optionId = 0;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->categoryId) && ! is_null($params->categoryId))
            $this->setCategoryId($params->categoryId);
        if (isset($params->optionId) && ! is_null($params->optionId))
            $this->setOptionId($params->optionId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'categoryId' => $this->getCategoryId(), 
                'optionId' => $this->getOptionId() 
        );
    }

    /**
     * Gets the categoryId of the object
     *
     * @return number The categoryId of the object
     */
    public function getCategoryId ()
    {
        return $this->_categoryId;
    }

    /**
     * Sets the categoryId of the object
     *
     * @param number $_categoryId
     *            the new categoryId
     */
    public function setCategoryId ($_categoryId)
    {
        $this->_categoryId = $_categoryId;
    }

    /**
     * Gets the option id
     *
     * @return number The option id
     */
    public function getOptionId ()
    {
        return $this->_optionId;
    }

    /**
     * Sets the option id
     *
     * @param number $_optionId
     *            The new option id
     */
    public function setOptionId ($_optionId)
    {
        $this->_optionId = $_optionId;
        return $this;
    }
}
