<?php

namespace MPSToolbox\Legacy\Modules\Admin\Models;

use ArrayObject;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsProviderMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel;
use My_Model_Abstract;

/**
 * Class DealerRmsProviderModel
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Models
 */
class DealerRmsProviderModel extends My_Model_Abstract
{
    /**
     * The id of the dealer
     *
     * @var int
     */
    public $dealerId;

    /**
     * The id of the RMS Provider
     *
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var DealerModel
     */
    protected $_dealer;

    /**
     * @var RmsProviderModel
     */
    protected $_rmsProvider;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "dealerId"       => $this->dealerId,
            "rmsProviderId" => $this->rmsProviderId,
        ];
    }

    /**
     * Gets the dealer
     *
     * @return DealerModel
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = DealerMapper::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }

    /**
     * Sets the dealer
     *
     * @param DealerModel $dealer
     *
     * @return $this
     */
    public function setDealer ($dealer)
    {
        $this->_dealer = $dealer;

        return $this;
    }

    /**
     * Gets the rmsProvider
     *
     * @return RmsProviderModel
     */
    public function getRmsProvider ()
    {
        if (!isset($this->_rmsProvider))
        {
            $this->_rmsProvider = RmsProviderMapper::getInstance()->find($this->rmsProviderId);
        }

        return $this->_rmsProvider;
    }

    /**
     * Sets the rmsProvider
     *
     * @param RmsProviderModel $rmsProvider
     *
     * @return $this
     */
    public function setRmsProvider ($rmsProvider)
    {
        $this->_rmsProvider = $rmsProvider;

        return $this;
    }
}