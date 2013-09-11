<?php

/**
 * Class Preferences_Service_QuoteSetting
 */
class Preferences_Service_QuoteSetting
{
    /**
     * Default report settings and survey settings combined into an array
     *
     * @var Quotegen_Model_QuoteSetting|null
     */
    protected $_defaultSettings;

    /**
     * Gets the report setting form.
     *
     * @var Preferences_Form_QuoteSetting
     */
    protected $_form;

    /**
     * Gets the report settings from the system
     *
     * @var Quotegen_Model_QuoteSetting
     */
    protected $_systemQuoteSetting;


    /**
     * Default settings should be an array of settings that are being used
     *
     * @param $defaultSettings Quotegen_Model_QuoteSetting
     */
    public function __construct ($defaultSettings = null)
    {
        $this->_systemQuoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find(1);
        $this->_defaultSettings    = $defaultSettings;
    }

    /**
     * Gets the report setting form with default values populated
     *
     * @param $dealerQuoteSetting Quotegen_Model_QuoteSetting
     *
     * @return Preferences_Form_QuoteSetting
     */
    public function getFormWithDefaults ($dealerQuoteSetting)
    {
        if (!isset($this->_form))
        {
            $this->_form      = new Preferences_Form_QuoteSetting();
            $populateSettings = $dealerQuoteSetting->toArray();

            // User form will populate the description with defaults
            if (is_array($this->_defaultSettings->toArray()))
            {
                $this->_form->getElement('pageCoverageMonochrome')->setDescription($populateSettings['pageCoverageMonochrome']);
                $this->_form->getElement('pageCoverageColor')->setDescription($populateSettings['pageCoverageColor']);
                $this->_form->getElement('deviceMargin')->setDescription($populateSettings['deviceMargin']);
                $this->_form->getElement('pageMargin')->setDescription($populateSettings['pageMargin']);
                $this->_form->getElement('adminCostPerPage')->setDescription(number_Format($populateSettings["adminCostPerPage"],4));

                // Re-load the settings into report settings
                $populateSettings = $this->_defaultSettings->toArray();
            }
            // This function sets up the third row column header decorator
            $this->_form->allowNullValues();
            $this->_form->setUpFormWithDefaultDecorators();

            $this->_form->populate($populateSettings);
        }

        return $this->_form;
    }

    /**
     * Gets the report setting form
     *
     * @return Preferences_Form_QuoteSetting
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Preferences_Form_QuoteSetting();
            if ($this->_defaultSettings)
            {
                // Get the user settings for population
                $this->_systemQuoteSetting->populate($this->_defaultSettings->toArray());
                $this->_form->populate($this->_defaultSettings->getTonerRankSets());
            }
            else
            {
                $this->_form->populate($this->_systemQuoteSetting->getTonerRankSets());
            }

            // Get the current class of the element and adds default settings
            foreach ($this->_form->getElements() as $element)
            {
                $currentClass = $element->getAttrib('class');
                $element->setAttrib('class', "{$currentClass} defaultSettings ");
            }

            $this->_form->populate($this->_systemQuoteSetting->toArray());
        }

        return $this->_form;
    }

    /**
     * Validates the data with the form
     *
     * @param array $data
     *            The array of data to validate
     *
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($data)
    {
        $validData = false;
        $form      = $this->getForm();

        if ($form->isValid($data))
        {
            if ($this->_form->allowsNull)
            {
                foreach ($data as $key => $value)
                {
                    if ($value === "")
                    {
                        $data [$key] = new Zend_Db_Expr("NULL");
                    }
                }

                $validData = $data;
            }
            else
            {
                $validData = $form->getValues();
            }
        }

        return $validData;
    }

    /**
     * Updates the report's settings
     *
     * @param array $data
     *
     * @return boolean
     */
    public function update ($data)
    {
        $validData = $this->validateAndFilterData($data);
        if ($validData)
        {
            foreach ($validData as $key => $value)
            {
                if (empty($value) && $value != 0)
                {
                    unset($validData [$key]);
                }
            }

            $quoteSetting     = new Quotegen_Model_QuoteSetting();
            $rankingSetMapper = Proposalgen_Model_Mapper_Toner_Vendor_Ranking_Set::getInstance();

            if (isset($validData['dealerColorRankSetArray']))
            {
                $quoteSetting->dealerColorRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerColorRankSetId, $validData['dealerColorRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerColorRankSetId);
            }

            if (isset($validData['dealerMonochromeRankSetArray']))
            {
                $quoteSetting->dealerMonochromeRankSetId = $rankingSetMapper->saveRankingSets($this->_defaultSettings->dealerMonochromeRankSetId, $validData['dealerMonochromeRankSetArray']);
            }
            else
            {
                Proposalgen_Model_Mapper_Toner_Vendor_Ranking::getInstance()->deleteByTonerVendorRankingId($this->_defaultSettings->dealerMonochromeRankSetId);
            }

            $quoteSetting->populate($validData);

            if ($this->_defaultSettings)
            {
                $quoteSetting->id = $this->_defaultSettings->id;
            }
            else
            {
                $quoteSetting->id = $this->_systemQuoteSetting->id;
            }

            Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($quoteSetting);

            return true;
        }

        return false;
    }
}
