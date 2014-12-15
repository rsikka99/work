<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Mappers\DealerMapper;
use Zend_Form;
use Zend_Form_Element_File;

/**
 * Class OnboardingForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class OnboardingForm extends Zend_Form
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
        foreach (DealerMapper::getInstance()->fetchAll() as $dealer)
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
        $this->addElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));

        $this->addElement('submit', 'upload', array(
            'ignore' => true,
            'label'  => 'Upload',
        ));

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/admin/onboarding-form.phtml'
                )
            )
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
