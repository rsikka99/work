<?php

/**
 * Class Admin_Form_MemjetDeviceSwaps
 */
class Admin_Form_MemjetDeviceSwaps extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod("POST");
        $this->_addClassNames('form-center-actions');
        $this->setAttrib('id', 'deviceSwap');
        $isAdmin = $this->getView()->IsAllowed(Admin_Model_Acl::RESOURCE_ADMIN_MEMJETDEVICESWAPS_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
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
                                                                                     "required"   => $isAdmin,
                                                                                     "label"      => "System Max Page Volume",
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
                                                                                     "required"   => $isAdmin,
                                                                                     "label"      => "System Min Page Volume",
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

        $dealerMaxPageCountElement = $this->createElement("text", "dealerMaximumPageCount", array(
                                                                                                 "required"   => !$isAdmin,
                                                                                                 "label"      => "Dealer Max Page Volume",
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

        $dealerMinPageCountElement = $this->createElement("text", "dealerMinimumPageCount", array(
                                                                                                 "required"   => !$isAdmin,
                                                                                                 "label"      => "Dealer Min Page Volume",
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

        // If we are not an admin then disable masterDeviceElement
        if (!$isAdmin)
        {
            $masterDeviceElement->setAttrib('disabled', 'disabled');
            $minPageCountElement->setAttrib('disabled', 'disabled');
            $maxPageCountElement->setAttrib('disabled', 'disabled');
        }

        $this->addElement($maxPageCountElement);
        $minPageCountElement->addValidator(new Tangent_Validate_LessThanFormValue($maxPageCountElement));
        $this->addElement($minPageCountElement);

        $this->addElement($dealerMaxPageCountElement);
        $dealerMinPageCountElement->addValidator(new Tangent_Validate_LessThanFormValue($dealerMaxPageCountElement));
        $this->addElement($dealerMinPageCountElement);


        $this->addDisplayGroup(array($masterDeviceElement, $minPageCountElement, $maxPageCountElement, $dealerMinPageCountElement, $dealerMaxPageCountElement, $deviceTypeElement), 'memjetDevicesSwaps');
    }
}