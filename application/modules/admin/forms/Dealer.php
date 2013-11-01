<?php
/**
 * Class Admin_Form_Dealer
 */
class Admin_Form_Dealer extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        $this->setMethod('POST');

        $this->_addClassNames('form-center-actions');

        $this->addElement('text', 'dealerName', array(
                                                     'required'    => true,
                                                     'label'       => 'Dealer Name',
                                                     'description' => 'The name of the dealership',
                                                     'filters'     => array(
                                                         'StringTrim',
                                                         'StripTags',
                                                     ),
                                                     'validators'  => array(
                                                         array(
                                                             'validator' => 'StringLength',
                                                             'options'   => array('min' => 2, 'max' => 255),
                                                         ),
                                                     ),
                                                ));

        $this->addElement('text', 'userLicenses', array(
                                                       'required'    => true,
                                                       'label'       => '# of user licenses',
                                                       'description' => 'The number of users a dealer can create. The dealer administrator counts towards these licenses',
                                                       'filters'     => array(
                                                           'StringTrim',
                                                           'StripTags',
                                                       ),
                                                       'validators'  => array(
                                                           array(
                                                               'validator' => 'Between',
                                                               'options'   => array('min' => 1, 'max' => 1000),
                                                           ),
                                                       ),
                                                  ));

        /**
         * File element. Can only upload a single file. Max size is 16mb.
         */
        $this->addElement('file', 'dealerLogoImage', array(
                                                          'label'       => 'Dealer Logo',
                                                          'description' => 'An image to use for customer facing output. 595px by 300px max. Must be jpg or png.',
                                                          'filters'     => array(
                                                              'StringTrim',
                                                              'StripTags',
                                                          ),
                                                          'validators'  => array(
                                                              array(
                                                                  'validator' => 'Count',
                                                                  'options'   => array('count' => 1),
                                                              ),
                                                              array(
                                                                  'validator' => 'Size',
                                                                  'options'   => array('size' => 16777216),
                                                              ),
                                                              array(
                                                                  'validator' => 'Extension',
                                                                  'options'   => array('extension' => 'jpg,png'),
                                                              ),
                                                          ),
                                                     ));

        $featuresList   = Application_Model_Mapper_Feature::getInstance()->fetchAllAsStringArray();
        $dealerFeatures = $this->createElement('multiCheckbox', 'dealerFeatures', array('label' => 'Features:'));
        if (count($featuresList) > 0)
        {
            // This removes the br that is put in between each element
            foreach ($featuresList as $feature)
            {
                $dealerFeatures->addMultiOption($feature, $feature);
            }

            $this->addElement($dealerFeatures);
        }

        // Add the submit button
        $submit = $this->createElement('submit', 'submit', array(
                                                                'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                                'ignore'     => true,
                                                                'label'      => 'Save'
                                                           ));
        // Add the submit button
        $cancel = $this->createElement('submit', 'cancel', array(
                                                                'ignore' => true,
                                                                'label'  => 'Cancel'
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
    public function getDealerLogoImage ()
    {
        return $this->getElement('dealerLogoImage');
    }
}