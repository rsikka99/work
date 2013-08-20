<?php
class Hardwareoptimization_Form_DeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('form-center-actions');
        $this->setAttrib('id', 'deviceSwap');

        $masterDeviceElement = $this->createElement("text", "masterDeviceId", array(
                                                                                   "required" => true,
                                                                                   "label"    => "Device Name",
                                                                                   "class"    => "input-xlarge",
                                                                                   "filters"  => array(
                                                                                       'StringTrim',
                                                                                       'StripTags'
                                                                                   ),
                                                                              )
        );

        $maxPageCountElement = $this->createElement("text", "maximumPageCount", array(
                                                                                     "required"   => true,
                                                                                     "label"      => "Max Page Volume",
                                                                                     "class"      => "span4",
                                                                                     "filters"    => array(
                                                                                         'StringTrim',
                                                                                         'StripTags'
                                                                                     ),
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
                                                                                )
        );

        $minPageCountElement = $this->createElement("text", "minimumPageCount", array(
                                                                                     "required"   => true,
                                                                                     "label"      => "Min Page Volume",
                                                                                     "class"      => "span4",
                                                                                     "filters"    => array(
                                                                                         'StringTrim',
                                                                                         'StripTags'
                                                                                     ),
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
                                                                                     ),
                                                                                )
        );

        $deviceTypeElement = $this->createElement("text", "deviceType", array(
                                                                             "label"   => "Device Type",
                                                                             "class"   => "span4",
                                                                             "filters" => array(
                                                                                 'StringTrim',
                                                                                 'StripTags'
                                                                             ),
                                                                             'attribs' => array('disabled' => 'disabled'),
                                                                        )
        );


        $this->addElement($maxPageCountElement);
        $minPageCountElement->addValidator(new My_Validate_LessThanFormValue($maxPageCountElement));
        $this->addElement($minPageCountElement);

        $this->addDisplayGroup(array($masterDeviceElement, $minPageCountElement, $maxPageCountElement, $deviceTypeElement), 'devicesSwaps');
    }
}