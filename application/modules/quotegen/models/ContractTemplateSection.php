<?php

/**
 * Class Quotegen_Model_ContractTemplateSection
 */
class Quotegen_Model_ContractTemplateSection extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $contractTemplateId;

    /**
     * @var int
     */
    public $contractSectionId;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var string
     */
    public $sectionName;

    /**
     * @var string
     */
    public $sectionText;

    /**
     * @var Quotegen_Model_ContractTemplate
     */
    protected $_contractTemplate;

    /**
     * @var Quotegen_Model_ContractSection
     */
    protected $_contractSection;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->contractTemplateId) && !is_null($params->contractTemplateId))
        {
            $this->contractTemplateId = $params->contractTemplateId;
        }

        if (isset($params->contractSectionId) && !is_null($params->contractSectionId))
        {
            $this->contractSectionId = $params->contractSectionId;
        }

        if (isset($params->enabled) && !is_null($params->enabled))
        {
            $this->enabled = $params->enabled;
        }

        if (isset($params->sectionName) && !is_null($params->sectionName))
        {
            $this->sectionName = $params->sectionName;
        }

        if (isset($params->sectionText) && !is_null($params->sectionText))
        {
            $this->sectionText = $params->sectionText;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "contractTemplateId" => $this->contractTemplateId,
            "contractSectionId"  => $this->contractSectionId,
            "enabled"            => $this->enabled,
            "sectionName"        => $this->sectionName,
            "sectionText"        => $this->sectionText,
        );
    }

    /**
     * Gets the contract template
     *
     * @return Quotegen_Model_ContractTemplate
     */
    public function getContractTemplate ()
    {
        if (!isset($this->_contractTemplate))
        {
            $this->_contractTemplate = Quotegen_Model_Mapper_ContractTemplate::getInstance()->find($this->contractTemplateId);
        }

        return $this->_contractTemplate;
    }

    /**
     * Sets the contract template
     *
     * @param Quotegen_Model_ContractTemplate $contractTemplate
     *
     * @return $this
     */
    public function setContractTemplate ($contractTemplate)
    {
        $this->_contractTemplate = $contractTemplate;

        return $this;
    }


    /**
     * Gets the contract template
     *
     * @return Quotegen_Model_ContractSection
     */
    public function getContractSection ()
    {
        if (!isset($this->_contractSection))
        {
            $this->_contractSection = Quotegen_Model_Mapper_ContractSection::getInstance()->find($this->contractSectionId);
        }

        return $this->_contractSection;
    }

    /**
     * Sets the contract section
     *
     * @param Quotegen_Model_ContractSection $contractSection
     *
     * @return $this
     */
    public function setContractSection ($contractSection)
    {
        $this->_contractSection = $contractSection;

        return $this;
    }


    /**
     * Gets the section name. Falls back to the default name if the user has left it blank
     *
     * @return string
     */
    public function getContractSectionName ()
    {
        if (strlen($this->sectionName) < 1)
        {
            return $this->getContractSection()->sectionDefaultName;
        }

        return $this->sectionName;
    }


    /**
     * Gets the section text. Falls back to the default text if the user has left it blank
     *
     * @return string
     */
    public function getContractSectionText ()
    {
        if (strlen($this->sectionText) < 1)
        {
            return $this->getContractSection()->sectionDefaultText;
        }

        return $this->sectionText;
    }
}