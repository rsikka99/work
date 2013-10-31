<?php

/**
 * Class Proposalgen_Form_MasterDeviceManagement_SuppliesAndService
 */
class Proposalgen_Form_MasterDeviceManagement_SuppliesAndService extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_isAllowed;
    protected $_isQuoteDevice;

    /**
     * @param null $options
     * @param bool $isAllowed
     * @param bool $isQuoteDevice
     */
    public function __construct ($options = null, $isAllowed = false, $isQuoteDevice = false)
    {
        $this->_isAllowed     = $isAllowed;
        $this->_isQuoteDevice = $isQuoteDevice;
        parent::__construct($options);
    }

    public function init ()
    {

        $this->setMethod('post');
        $tonerConfigurationElement = $this->createElement('select', 'tonerConfigId', array(
                                                                                          'label'    => 'Toner Configuration: ',
                                                                                          'required' => false
                                                                                     ));
        if (!$this->_isAllowed)
        {
            $tonerConfigurationElement->setAttrib('disabled', 'disabled');
        }


        $isLeasedElement = $this->createElement('checkbox', 'isLeased', array(
                                                                             'label' => 'Is Leased: '
                                                                        ));

        if (!$this->_isAllowed)
        {
            $isLeasedElement->setAttrib('disabled', 'disabled');
        }

        /*
         * Leased Toner Yield
         */
        $leasedTonerYieldElement = $this->createElement('text', 'leasedTonerYield', array(
                                                                                         'label'      => 'Leased Toner Yield',
                                                                                         'filters'    => array(
                                                                                             'StringTrim',
                                                                                             'StripTags'
                                                                                         ),
                                                                                         'allowEmpty' => false,
                                                                                         'validators' => array(
                                                                                             array(
                                                                                                 'validator' => 'Between',
                                                                                                 'options'   => array('min' => 0, 'max' => 100000),
                                                                                             ),
                                                                                         ),
                                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        if (!$this->_isAllowed)
        {
            $leasedTonerYieldElement->setAttrib('disabled', 'disabled');
        }
        /*
         * Parts Cost Per Page
         */
        $dealerPartsCPPElement = $this->createElement('text', 'dealerPartsCostPerPage', array(
                                                                                             'label'      => 'Dealer Parts CPP:',
                                                                                             'class'      => 'span4',
                                                                                             'maxlength'  => 8,
                                                                                             'required'   => $this->_isQuoteDevice,
                                                                                             'validators' => array(
                                                                                                 array(
                                                                                                     'validator' => 'Between',
                                                                                                     'options'   => array('min' => 0, 'max' => 5),
                                                                                                 ),
                                                                                             ),
                                                                                             'filters'    => array(
                                                                                                 'StringTrim',
                                                                                                 'StripTags'
                                                                                             ),
                                                                                        ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        /*
        * Labor Cost Per Page
        */
        $dealerLaborCPPElement = $this->createElement('text', 'dealerLaborCostPerPage', array(
                                                                                             'label'      => 'Dealer Labor CPP:',
                                                                                             'class'      => 'span4',
                                                                                             'maxlength'  => 8,
                                                                                             'required'   => $this->_isQuoteDevice,
                                                                                             'validators' => array(
                                                                                                 'float',
                                                                                             ),
                                                                                             'filters'    => array(
                                                                                                 'StringTrim',
                                                                                                 'StripTags'
                                                                                             ),
                                                                                        ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        /*
         * Parts Cost Per Page
         */
        $systemPartsCPPElement = $this->createElement('text', 'partsCostPerPage', array(
                                                                                       'label'      => 'System Parts CPP:',
                                                                                       'class'      => 'span4',
                                                                                       'maxlength'  => 8,
                                                                                       'allowEmpty' => true,
                                                                                       'filters'    => array(
                                                                                           'StringTrim',
                                                                                           'StripTags'
                                                                                       ),
                                                                                       'validators' => array(
                                                                                           array(
                                                                                               'validator' => 'Between',
                                                                                               'options'   => array('min' => 0, 'max' => 5),
                                                                                           ),
                                                                                       ),
                                                                                  ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        /*
        * Labor Cost Per Page
        */
        $systemLaborCPPElement = $this->createElement('text', 'laborCostPerPage', array(
                                                                                       'label'      => 'System Labor CPP:',
                                                                                       'class'      => 'span4',
                                                                                       'maxlength'  => 8,
                                                                                       'allowEmpty' => true,
                                                                                       'filters'    => array(
                                                                                           'StringTrim',
                                                                                           'StripTags'
                                                                                       ),
                                                                                       'validators' => array(
                                                                                           'float',
                                                                                           array(
                                                                                               'validator' => 'Between',
                                                                                               'options'   => array('min' => 0, 'max' => 5),
                                                                                           ),
                                                                                       ),
                                                                                  ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        if (!$this->_isAllowed)
        {
            $systemPartsCPPElement->setAttrib('disabled', 'disabled');
            $systemLaborCPPElement->setAttrib('disabled', 'disabled');
        }

        /*
        * Labor Cost Per Page
        */
        $leaseBuybackPriceElement = $this->createElement('text', 'leaseBuybackPrice', array(
                                                                                           'label'      => 'Lease Buyback Price:',
                                                                                           'class'      => 'span4',
                                                                                           'maxlength'  => 8,
                                                                                           'required'   => $this->_isQuoteDevice,
                                                                                           'validators' => array(
                                                                                               'float',
                                                                                           ),
                                                                                           'filters'    => array(
                                                                                               'StringTrim',
                                                                                               'StripTags'
                                                                                           ),
                                                                                      ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        $this->addDisplayGroup(array($dealerPartsCPPElement, $systemPartsCPPElement, $tonerConfigurationElement, $leaseBuybackPriceElement), 'leftSide');
        $this->addDisplayGroup(array($dealerLaborCPPElement, $systemLaborCPPElement, $isLeasedElement, $leasedTonerYieldElement), 'rightSide');
        $this->getElement("isLeased")->setDecorators(array("ViewHelper",
                                                           array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'switch', 'data-on-label' => 'Yes', 'data-off-label' => 'No', 'data-off' => 'danger', 'data-on' => 'success')),
                                                           array(array('donkeyKong' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                           array("label", array('class' => 'control-label')),
                                                           array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group'))));
        $leftSide = $this->getDisplayGroup('leftSide');
        $leftSide->setDecorators(array(

                                      'FormElements',
                                      array(
                                          'Fieldset',
                                          array(
                                              'class' => 'pull-left half-width'
                                          )
                                      ),
                                      array(
                                          'HtmlTag',
                                          array(
                                              'tag'       => 'div',
                                              'openOnly'  => true,
                                              'class'     => 'clearfix',
                                              'placement' => Zend_Form_Decorator_Abstract::PREPEND
                                          )
                                      )
                                 ));
        $rightSide = $this->getDisplayGroup('rightSide');
        $rightSide->setDecorators(array(
                                       'FormElements',

                                       array(
                                           'Fieldset',
                                           array(
                                               'class' => 'pull-right half-width'
                                           )
                                       ),
                                       array(
                                           'HtmlTag',

                                           array(
                                               'tag'       => 'div',
                                               'closeOnly' => true,
                                               'class'     => 'clearfix',
                                               'placement' => Zend_Form_Decorator_Abstract::APPEND
                                           )
                                       )
                                  ));
        $tonerConfigMapper       = Proposalgen_Model_Mapper_TonerConfig::getInstance();
        $tonerConfigs            = $tonerConfigMapper->fetchAll(null, 'id asc');
        $tonerConfigMultiOptions = array();
        foreach ($tonerConfigs as $tonerConfig)
        {
            $tonerConfigMultiOptions [$tonerConfig->tonerConfigId] = $tonerConfig->tonerConfigName;
        }

        /**
         * Data for select box
         */
        $tonerConfigurationElement->addMultiOptions($tonerConfigMultiOptions);

        $leasedTonerYieldElement->addValidators(array(new Tangent_Validate_FieldDependsOnValue('isLeased', '1', array(
                                                                                                                     new Zend_Validate_NotEmpty(),
                                                                                                                     new Zend_Validate_Int(),
                                                                                                                ))));

    }
}