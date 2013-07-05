<?php

/**
 * Class Proposalgen_Form_HardwareConfigurations
 */
class Proposalgen_Form_HardwareConfigurations extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_deviceConfigurationId;

    /**
     * If this is set to false it the form will display a dropdown to select a device.
     *
     * @var string
     */
    protected $_masterDeviceId;

    /**
     * An array of elements used to display options
     *
     * @var Zend_Form_Element
     */
    protected $_optionElements = array();

    /**
     * @param int  $deviceConfigurationId
     * @param int  $masterDeviceId
     * @param null $options
     *
     * @internal param int $id
     */
    public function __construct ($deviceConfigurationId = 0, $masterDeviceId = 0, $options = null)
    {
        $this->_deviceConfigurationId = $deviceConfigurationId;
        $this->_masterDeviceId        = $masterDeviceId;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'hardwareConfigurationsname', array(
                                                                     'label'     => 'Name:',
                                                                     'class'     => 'span2',
                                                                     'required'  => true,
                                                                     'maxlength' => 255,
                                                                     'filters'   => array(
                                                                         'StringTrim',
                                                                         'StripTags'
                                                                     ),
                                                                     'validator' => 'StringLength',
                                                                     'options'   => array(
                                                                         1,
                                                                         255
                                                                     )
                                                                ));

        $this->addElement('textarea', 'hardwareConfigurationsdescription', array(
                                                                                'label'     => 'Description:',
                                                                                'class'     => 'span2',
                                                                                'required'  => true,
                                                                                'style'     => 'height: 100px',
                                                                                'maxlength' => 255,
                                                                                'filters'   => array(
                                                                                    'StringTrim',
                                                                                    'StripTags'
                                                                                ),
                                                                                'validator' => 'StringLength',
                                                                                'options'   => array(
                                                                                    1,
                                                                                    255
                                                                                )
                                                                           ));
        /**
         * Options
         */
        if ($this->_masterDeviceId > 0)
        {
            $device = Quotegen_Model_Mapper_Device::getInstance()->find(array($this->_masterDeviceId, Zend_Auth::getInstance()->getIdentity()->dealerId));

            if ($device)
            {
                $deviceConfigurationOptionMapper = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance();

                /* @var $deviceOption Quotegen_Model_DeviceOption */
                foreach ($device->getDeviceOptions() as $deviceOption)
                {
                    $deviceConfigurationOption = $deviceConfigurationOptionMapper->find(array($this->_deviceConfigurationId, $deviceOption->getOption()->id));
                    $optionElement             = $this->createElement('text', "hardwareConfigurationsoption{$deviceOption->optionId}", array(
                                                                                                                                            'label'      => $deviceOption->getOption()->name,
                                                                                                                                            'value'      => ($deviceConfigurationOption ? $deviceConfigurationOption->quantity : 0),
                                                                                                                                            'class'      => 'span4',
                                                                                                                                            'maxlength'  => 8,
                                                                                                                                            'required'   => false,
                                                                                                                                            'allowEmpty' => true,
                                                                                                                                            'filters'    => array(
                                                                                                                                                'StringTrim',
                                                                                                                                                'StripTags'
                                                                                                                                            ),
                                                                                                                                            'validators' => array(
                                                                                                                                                array(
                                                                                                                                                    'validator' => 'Between',
                                                                                                                                                    'options'   => array('min' => 0, 'max' => 1000),
                                                                                                                                                ),
                                                                                                                                                'int'
                                                                                                                                            ),
                                                                                                                                       )
                    );
                    $optionElement->setAttrib('class', 'span1');
                    $optionElement->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
                    $this->_optionElements [] = $optionElement;
                }

                if (count($this->_optionElements) > 0)
                {
                    $this->addDisplayGroup($this->_optionElements, "optionsGroup");
                    $optionsGroup = $this->getDisplayGroup("optionsGroup");
                    $optionsGroup->setDescription("Quantity");
                    $optionsGroup->setDecorators(array(
                                                      'FormElements',
                                                      array('HtmlTag', array('tag' => 'div', 'class' => 'myClass')),
                                                      array('Description', array('tag' => 'h3', 'placement' => 'prepend', 'class' => 'text-center'))
                                                 ));
                }
            }
        }
        $this->addElement('hidden', 'hardwareConfigurationsid', array());
    }
}