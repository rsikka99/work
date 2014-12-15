<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContractTemplateSectionMapper;
use My_Model_Abstract;

/**
 * Class ContractTemplateModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class ContractTemplateModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $templateName;

    /**
     * @var bool
     */
    public $isSystemTemplate;

    /**
     * @var ContractTemplateSectionModel[]
     */
    public $_contractTemplateSections;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->templateName) && !is_null($params->templateName))
        {
            $this->templateName = $params->templateName;
        }

        if (isset($params->isSystemTemplate) && !is_null($params->isSystemTemplate))
        {
            $this->isSystemTemplate = $params->isSystemTemplate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"               => $this->id,
            "dealerId"         => $this->dealerId,
            "templateName"     => $this->templateName,
            "isSystemTemplate" => $this->isSystemTemplate,
        );
    }


    /**
     * Gets the dealer object
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
     * Sets the dealer object
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
     * Gets the contract template sections
     *
     * @return ContractTemplateSectionModel[]
     */
    public function getContractTemplateSections ()
    {
        if (!isset($this->_contractTemplateSections))
        {
            $this->_contractTemplateSections = ContractTemplateSectionMapper::getInstance()->fetchAllForContractTemplate($this->id);
        }

        return $this->_contractTemplateSections;
    }

    /**
     * Sets the contract template sections
     *
     * @param $contractTemplateSections
     *
     * @return $this
     */
    public function setContractTemplateSections ($contractTemplateSections)
    {
        $this->_contractTemplateSections = $contractTemplateSections;

        return $this;
    }

}