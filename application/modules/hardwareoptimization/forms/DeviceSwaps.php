<?php
class Hardwareoptimization_Form_DeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('reportSettingsForm form-center-actions');
        $this->setAttrib('id', 'deviceSwap');

        $numberValidator = array(
            array(
                'validator' => 'greaterThan',
                'options'   => array(
                    'min'       => 0,
                    'inclusive' => true
                )
            ),
            'Int'
        );

        $this->addElement("text", "masterDeviceId", array(
                                                         "label"       => "Device Name",
                                                         "class"       => "input-xlarge",
                                                         "description" => 'Only "can sell" devices can be used as device swaps.'
                                                    ));

        $this->addElement("text", "minimumPageCount", array(
                                                           "label"      => "Max Page Count Volume",
                                                           "class"      => "span2",
                                                           "validators" => $numberValidator,
                                                      ));

        $this->addElement("text", "maximumPageCount", array(
                                                           "label"      => "Min Page Count Volume",
                                                           "class"      => "span2",
                                                           "validators" => $numberValidator
                                                      ));
    }
}