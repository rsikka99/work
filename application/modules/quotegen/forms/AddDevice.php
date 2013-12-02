<?php

/**
 * Class Quotegen_Form_AddDevice
 */
class Quotegen_Form_AddDevice extends Twitter_Bootstrap_Form_Inline
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // An add configuration button for the favorite devices
        $submitButton = $this->createElement('submit', 'addDevice', array(
                                                                         'ignore' => true,
                                                                         'label'  => 'Add',
                                                                         'class'  => 'btn btn-success'
                                                                    ));

        // Get configurations from database
        $devices = Quotegen_Model_Mapper_Device::getInstance()->fetchQuoteDeviceListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
        // Populate array with configurations
        $data        = array();
        $data ['-1'] = 'Select Device...';

        /* @var $device Quotegen_Model_Device */
        foreach ($devices as $device)
        {
            $data [$device->masterDeviceId] = $device->getMasterDevice()->getFullDeviceName();
        }

        // This is a list of favorite devices that the user can add
        $this->addElement('select', 'masterDeviceId', array(
                                                           'label'        => 'Add Device',
                                                           'multiOptions' => $data,
                                                           'prepend'      => $submitButton
                                                      ));
    }
}
