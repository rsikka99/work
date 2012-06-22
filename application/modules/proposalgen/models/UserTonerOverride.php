<?php

/**
 * Class Proposalgen_Model_UserTonerOverride
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_UserTonerOverride extends Tangent_Model_Abstract
{
    // Database Fields
    protected $UserId;
    protected $TonerId;
    protected $OverrideTonerPrice;

    /**
     *
     * @return the $UserId
     */
    public function getUserId ()
    {
        if (! isset($this->UserId))
        {
            
            $this->UserId = null;
        }
        return $this->UserId;
    }

    /**
     *
     * @param field_type $UserId            
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     *
     * @return the $TonerId
     */
    public function getTonerId ()
    {
        if (! isset($this->TonerId))
        {
            
            $this->TonerId = null;
        }
        return $this->TonerId;
    }

    /**
     *
     * @param field_type $TonerId            
     */
    public function setTonerId ($TonerId)
    {
        $this->TonerId = $TonerId;
        return $this;
    }

    /**
     *
     * @return the $OverrideTonerPrice
     */
    public function getOverrideTonerPrice ()
    {
        if (! isset($this->OverrideTonerPrice))
        {
            
            $this->OverrideTonerPrice = null;
        }
        return $this->OverrideTonerPrice;
    }

    /**
     *
     * @param field_type $OverrideTonerPrice            
     */
    public function setOverrideTonerPrice ($OverrideTonerPrice)
    {
        $this->OverrideTonerPrice = $OverrideTonerPrice;
        return $this;
    }
}