<?php
class Hardwareoptimization_Form_DeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('reportSettingsForm form-center-actions');
        $this->setAttrib('id', 'deviceSwap');

        $this->addElement("text", "masterDeviceId", array(
                                                         "label"       => "Device Name",
                                                         "class"       => "input-xlarge",
                                                         "description" => 'Only "can sell" devices can be used as device swaps.'
                                                    ));

        $maxPageCountElement = $this->createElement("text", "maximumPageCount", array(
                                                                                     "label"      => "Max Page Volume",
                                                                                     "class"      => "span2",
                                                                                     "validators" => array(
                                                                                         array(
                                                                                             'validator' => 'Between',
                                                                                             'options'   => array(
                                                                                                 'min'       => 0,
                                                                                                 'max'       => PHP_INT_MAX,
                                                                                                 'inclusive' => true
                                                                                             )
                                                                                         ),
                                                                                         'Int'
                                                                                     ),
                                                                                ));


        $this->addElement("text", "minimumPageCount", array(
                                                           "label"      => "Min Page Volume",
                                                           "class"      => "span2",
                                                           "validators" => array(
                                                               array(
                                                                   'validator' => 'Between',
                                                                   'options'   => array(
                                                                       'min'       => 0,
                                                                       'max'       => PHP_INT_MAX,
                                                                       'inclusive' => true
                                                                   )
                                                               ),
                                                               'Int',
                                                               /**
                                                                * FIXME: Fix less than form value
                                                                */
//                                                               new My_Validate_LessThanFormValue($maxPageCountElement),
                                                           ),
                                                      ));

        $this->addElement($maxPageCountElement);


    }
}