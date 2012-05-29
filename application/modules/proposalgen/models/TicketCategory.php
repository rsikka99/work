<?php

/**
 * Class Proposalgen_Model_TicketCategory
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_TicketCategory extends Tangent_Model_Abstract
{    
    
    const PRINTFLEET_DEVICE_SUPPORT = 1;
    
    protected $CategoryId;
    protected $CategoryName;
    
    
	/**
     * @return the $CategoryId
     */
    public function getCategoryId ()
    {
        if (!isset($this->CategoryId))
        {
        	
        	$this->CategoryId = null;
        }	
        return $this->CategoryId;
    }

	/**
     * @param field_type $CategoryId
     */
    public function setCategoryId ($CategoryId)
    {
        $this->CategoryId = $CategoryId;
        return $this;
    }

	/**
     * @return the $CategoryName
     */
    public function getCategoryName ()
    {
        if (!isset($this->CategoryName))
        {
        	
        	$this->CategoryName = null;
        }	
        return $this->CategoryName;
    }

	/**
     * @param field_type $CategoryName
     */
    public function setCategoryName ($CategoryName)
    {
        $this->CategoryName = $CategoryName;
        return $this;
    }
}