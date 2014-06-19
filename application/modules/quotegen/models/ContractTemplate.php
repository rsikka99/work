<?php

/**
 * Class Quotegen_Model_ContractTemplate
 */
class Quotegen_Model_ContractTemplate extends My_Model_Abstract
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
     * @var Quotegen_Model_ContractTemplateSection[]
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
     * @return Application_Model_Dealer
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = Application_Model_Mapper_Dealer::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }

    /**
     * Sets the dealer object
     *
     * @param Application_Model_Dealer $dealer
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
     * @return Quotegen_Model_ContractTemplateSection[]
     */
    public function getContractTemplateSections ()
    {
        if (!isset($this->_contractTemplateSections))
        {
            $this->_contractTemplateSections = Quotegen_Model_Mapper_ContractTemplateSection::getInstance()->fetchAllForContractTemplate($this->id);
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