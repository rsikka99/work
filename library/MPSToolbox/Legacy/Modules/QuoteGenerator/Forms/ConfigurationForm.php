<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use Zend_Auth;

/**
 * Class ConfigurationForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class ConfigurationForm extends Zend_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $masterDeviceList = array();
        foreach (DeviceMapper::getInstance()->fetchQuoteDeviceListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId) as $device)
        {
            $masterDeviceList [$device->masterDeviceId] = $device->getMasterDevice()->getFullDeviceName();
        }
        $this->addElement('select', 'masterDeviceId', array(
            'label'        => 'Master Device',
            'multiOptions' => $masterDeviceList
        ));

        $this->addElement('text', 'name', array(
            'label'     => 'Name',
            'required'  => true,
            'maxlength' => 255,
            'filters'   => array(
                'StringTrim',
                'StripTags'
            )
        ));

        $this->addElement('textarea', 'description', array(
            'label'     => 'Description',
            'id'        => 'description',
            'required'  => true,
            'style'     => 'height: 100px',
            'maxlength' => 255,
            'filters'   => array(
                'StringTrim',
                'StripTags'
            )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true
        ));

    }


}