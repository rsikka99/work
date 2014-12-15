<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Mappers\FeatureMapper;
use Zend_Form;
use Zend_Form_Element_File;
use Zend_Form_Element_Multi;

/**
 * Class DealerForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class DealerForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('POST');
        $this->setAttrib('accept-charset', 'UTF-8');

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

        $featuresList = FeatureMapper::getInstance()->fetchAllAsStringArray();
        /* @var $dealerFeatures Zend_Form_Element_Multi */
        $dealerFeatures = $this->createElement('multiCheckbox', 'dealerFeatures', array('label' => 'Features:'));
        if (count($featuresList) > 0)
        {
            // This removes the br that is put in between each element
            foreach ($featuresList as $feature)
            {
                $dealerFeatures->addMultiOption($feature['id'], $feature['name']);
            }

            $this->addElement($dealerFeatures);
        }

        // Add the submit button
        $submit = $this->createElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Save'
        ));
        // Add the submit button
        $cancel = $this->createElement('submit', 'cancel', array(
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ));
        $this->addDisplayGroup(array($submit, $cancel), 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array('Actions'),
            'class'                        => 'form-actions-center'
        ));

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/admin/dealer-form.phtml'
                )
            )
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