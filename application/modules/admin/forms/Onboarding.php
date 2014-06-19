<?php

/**
 * Class Admin_Form_Onboarding
 */
class Admin_Form_Onboarding extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_defaultDealerId = 0;

    /**
     * @param bool|int   $dealerId
     * @param null|array $options
     */
    public function __construct ($dealerId = false, $options = null)
    {
        if ($dealerId > 0)
        {
            $this->_defaultDealerId = $dealerId;
        }

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $firstDealerId = null;
        $dealers       = array();
        foreach (Application_Model_Mapper_Dealer::getInstance()->fetchAll() as $dealer)
        {
            // Use this to grab the first id in the leasing schema dropdown
            if (!$firstDealerId)
            {
                $firstDealerId = $dealer->id;
            }
            $dealers [$dealer->id] = $dealer->dealerName;
        }

        if ($dealers)
        {
            $this->addElement('Select', 'dealerId', array(
                'label'        => 'Dealer:',
                'class'        => 'input-medium',
                'multiOptions' => $dealers,
                'required'     => true,
                'value'        => ($this->_defaultDealerId > 0) ? $this->_defaultDealerId : $firstDealerId,
            ));
        }


        $this->addElement('File', 'oemPricing', array(
            'label' => 'OEM Pricing',
        ));

        $this->addElement('File', 'compPricing', array(
            'label' => 'Compatible Pricing',
        ));


        /**
         * Form Actions
         */
        $cancel = $this->createElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));

        $submit = $this->createElement('submit', 'Upload', array(
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));


        $this->addDisplayGroup(array(
            $submit,
            $cancel
        ), 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            ),
            'class'                        => 'form-actions-center'
        ));
    }

    /**
     * @return Zend_Form_Element_File
     */
    public function getOemPricingElement ()
    {
        return $this->getElement('oemPricing');
    }

    /**
     * @return Zend_Form_Element_File
     */
    public function getCompPricingElement ()
    {
        return $this->getElement('compPricing');
    }
}
