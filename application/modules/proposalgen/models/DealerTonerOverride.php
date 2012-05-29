<?php
/**
 * Class Proposalgen_Model_DealerTonerOverride
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_DealerTonerOverride extends Tangent_Model_Abstract
{
    protected $DealerCompanyId;
    protected $TonerId;
    protected $OverrideTonerPrice;
    
    
	/**
	 * @return the $DealerCompanyId
	 */
	public function getDealerCompanyId() {
		if (!isset($this->DealerCompanyId))
		{
			
			$this->DealerCompanyId = null;
		}	
		return $this->DealerCompanyId;
	}

	/**
	 * @param field_type $DealerCompanyId
	 */
	public function setDealerCompanyId($DealerCompanyId) {
		$this->DealerCompanyId = $DealerCompanyId;
		return $this;
	}

	/**
	 * @return the $TonerId
	 */
	public function getTonerId() {
		if (!isset($this->TonerId))
		{
			
			$this->TonerId = null;
		}	
		return $this->TonerId;
	}

	/**
	 * @param field_type $TonerId
	 */
	public function setTonerId($TonerId) {
		$this->TonerId = $TonerId;
		return $this;
	}

	/**
	 * @return the $OverrideTonerPrice
	 */
	public function getOverrideTonerPrice() {
		if (!isset($this->OverrideTonerPrice))
		{
			
			$this->OverrideTonerPrice = null;
		}	
		return $this->OverrideTonerPrice;
	}

	/**
	 * @param field_type $OverrideTonerPrice
	 */
	public function setOverrideTonerPrice($OverrideTonerPrice) {
		$this->OverrideTonerPrice = $OverrideTonerPrice;
		return $this;
	}

}