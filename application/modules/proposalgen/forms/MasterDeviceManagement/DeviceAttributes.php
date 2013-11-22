<?php

/**
 * Class Proposalgen_Form_MasterDeviceManagement_DeviceAttributes
 */
class Proposalgen_Form_MasterDeviceManagement_DeviceAttributes extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_isAllowedToEditFields = false;

    /**
     * @param null $options
     * @param bool $isAllowedToEditFields
     */
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        $this->_isAllowedToEditFields = $isAllowedToEditFields;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $isCopierElement = $this->createElement('checkbox', 'isCopier', array(
                                                                             'label'   => 'Can Copy/Scan ',
                                                                             'prepend' => '<div class="switch" data-on="success" data-off="warning">',
                                                                             'append'  => '</div>'
                                                                        ));
        if (!$this->_isAllowedToEditFields)
        {
            $isCopierElement->setAttrib('disabled', 'disabled');
        }

        $isDuplexElement = $this->createElement('checkbox', 'isDuplex', array(
                                                                             'label' => 'Can Duplex '
                                                                        ));

        if (!$this->_isAllowedToEditFields)
        {
            $isDuplexElement->setAttrib('disabled', 'disabled');
        }

        $isFaxElement = $this->createElement('checkbox', 'isFax', array(
                                                                       'label' => 'Can Fax '
                                                                  ));

        if (!$this->_isAllowedToEditFields)
        {
            $isFaxElement->setAttrib('disabled', 'disabled');
        }

        $reportsTonerLevelsElement = $this->createElement('checkbox', 'reportsTonerLevels', array(
                                                                                                 'label' => 'Reports Toner Levels '
                                                                                            ));

        if (!$this->_isAllowedToEditFields)
        {
            $reportsTonerLevelsElement->setAttrib('disabled', 'disabled');
        }

        $isA3Element = $this->createElement('checkbox', 'isA3', array(
                                                                     'label' => 'Can Print A3'
                                                                ));

        if (!$this->_isAllowedToEditFields)
        {
            $isA3Element->setAttrib('disabled', 'disabled');
        }

        $jitCompatibleElement = $this->createElement('checkbox', 'jitCompatibleMasterDevice', array(
                                                                                                   'label' => 'JIT Compatible'
                                                                                              ));

        /*
         * Print Speed Monochrome
         */
        $ppmBlackElement = $this->createElement('text', 'ppmBlack', array(
                                                                         'label'      => 'Print Speed Mono',
                                                                         'class'      => 'span4',
                                                                         'maxlength'  => 8,
                                                                         'allowEmpty' => true,
                                                                         'filters'    => array(
                                                                             'StringTrim',
                                                                             'StripTags'
                                                                         ),
                                                                         'validators' => array(
                                                                             'Int',
                                                                             array(
                                                                                 'validator' => 'Between',
                                                                                 'options'   => array(
                                                                                     'min' => 0,
                                                                                     'max' => 1000
                                                                                 )
                                                                             )
                                                                         )
                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        if (!$this->_isAllowedToEditFields)
        {
            $ppmBlackElement->setAttrib('readonly', 'readonly');
        }
        /*
         * Print Speed Color
         */
        $ppmColorElement = $this->createElement('text', 'ppmColor', array(
                                                                         'label'      => 'Print Speed Color',
                                                                         'class'      => 'span4',
                                                                         'maxlength'  => 8,
                                                                         'allowEmpty' => true,
                                                                         'filters'    => array(
                                                                             'StringTrim',
                                                                             'StripTags'
                                                                         ),
                                                                         'validators' => array(
                                                                             'Int',
                                                                             array(
                                                                                 'validator' => 'Between',
                                                                                 'options'   => array(
                                                                                     'min' => 0,
                                                                                     'max' => 1000
                                                                                 )
                                                                             )
                                                                         )
                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        if (!$this->_isAllowedToEditFields)
        {
            $ppmColorElement->setAttrib('readonly', 'readonly');
        }
        /*
        * Launch Date
        */
        $minYear           = 1950;
        $maxYear           = ((int)date('Y')) + 2;
        $launchDateElement = new ZendX_JQuery_Form_Element_DatePicker('launchDate');

        $launchDateElement->setJQueryParam('dateFormat', 'yy-mm-dd')
                          ->setJqueryParam('timeFormat', 'hh:mm')
                          ->setJQueryParam('changeYear', 'true')
                          ->setJqueryParam('changeMonth', 'true')
                          ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                          ->addValidator(new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'))
                          ->setRequired($this->_isAllowedToEditFields)->setLabel('Launch Date:');
        $launchDateElement->addFilters(array(
                                            'StringTrim',
                                            'StripTags'
                                       ));
//        $launchDateElement->setAttrib('class', 'span6');
        if (!$this->_isAllowedToEditFields)
        {
            $launchDateElement->setAttrib('readonly', 'readonly');
        }


        /*
         * Operating Wattage
         */
        $wattsPowerNormalElement = $this->createElement('text', 'wattsPowerNormal', array(
                                                                                         'label'      => 'Operating Wattage',
                                                                                         'class'      => 'span4',
                                                                                         'maxlength'  => 8,
                                                                                         'allowEmpty' => !$this->_isAllowedToEditFields,
                                                                                         'required'   => $this->_isAllowedToEditFields,
                                                                                         'filters'    => array(
                                                                                             'StringTrim',
                                                                                             'StripTags'
                                                                                         ),
                                                                                    ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        if (!$this->_isAllowedToEditFields)
        {
            $wattsPowerNormalElement->setAttrib('readonly', 'readonly');
        }
        else
        {
            $wattsPowerNormalElement->addValidators(
                                    array(
                                         'float',
                                         array(
                                             'validator' => 'Between',
                                             'options'   => array(
                                                 'min' => 1,
                                                 'max' => 10000
                                             )
                                         )
                                    )
            );
        }

        /*
         * Duty Cycle
         */
        $dutyCycleElement = $this->createElement('text', 'dutyCycle', array(
                                                                           'label'   => 'Duty Cycle',
                                                                           'class'   => 'span4',
                                                                           'filters' => array(
                                                                               'StringTrim',
                                                                               'StripTags'
                                                                           ),
                                                                      ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        if (!$this->_isAllowedToEditFields)
        {
            $dutyCycleElement->setAttrib('readonly', 'readonly');
        }
        else
        {
            $dutyCycleElement->addValidators(
                             array(
                                  'Int',
                                  array(
                                      'validator' => 'greaterThan',
                                      'options'   => array(
                                          0
                                      )
                                  )
                             )
            );
        }

        /*
         * Idle/Sleep Wattage
         */
        $wattsPowerIdleElement = $this->createElement(
                                      'text',
                                          'wattsPowerIdle',
                                          array(
                                               'label'      => 'Idle/Sleep Wattage',
                                               'class'      => 'span4',
                                               'maxlength'  => 8,
                                               'allowEmpty' => false,
                                               'required'   => $this->_isAllowedToEditFields,
                                               'filters'    => array(
                                                   'StringTrim',
                                                   'StripTags'
                                               ),
                                          )
        )                             ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');

        if (!$this->_isAllowedToEditFields)
        {
            $wattsPowerIdleElement->setAttrib('readonly', 'readonly');
        }
        else
        {
            $wattsPowerIdleElement->addValidators(
                                  array(
                                       'float',
                                       array(
                                           'validator' => 'Between',
                                           'options'   => array(
                                               'min' => 0,
                                               'max' => 10000
                                           )
                                       )
                                  )
            );
        }

        $this->addDisplayGroup(array($isCopierElement, $isDuplexElement, $isA3Element, $ppmBlackElement, $ppmColorElement, $dutyCycleElement), 'leftSide');
        $this->addDisplayGroup(array($isFaxElement, $reportsTonerLevelsElement, $jitCompatibleElement, $launchDateElement, $wattsPowerNormalElement, $wattsPowerIdleElement), 'rightSide');

        $this->getElement("isCopier")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("isDuplex")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("isFax")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array(
                          'controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("reportsTonerLevels")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("isA3")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("jitCompatibleMasterDevice")->setDecorators(
             array(
                  "ViewHelper",
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array(
                          'tag'            => 'div',
                          'class'          => 'switch',
                          'data-on-label'  => 'Yes',
                          'data-off-label' => 'No',
                          'data-off'       => 'danger',
                          'data-on'        => 'success'
                      )
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ),
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $this->getElement("launchDate")->addDecorators(
             array(
                  array(
                      array('wrapper' => 'HtmlTag'),
                      array('tag' => 'div')
                  ),
                  array(
                      array('donkeyKong' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'controls'
                      )
                  ), // Shawn said donkey kong was legit
                  array(
                      "label",
                      array('class' => 'control-label')
                  ),
                  array(
                      array('controls' => 'HtmlTag'),
                      array(
                          'tag'   => 'div',
                          'class' => 'control-group'
                      )
                  )
             )
        );

        $leftSide = $this->getDisplayGroup('leftSide');
        $leftSide->setDecorators(
                 array(
                      'FormElements',
                      array(
                          'Fieldset',
                          array('class' => 'pull-left half-width')
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
        $rightSide->setDecorators(
                  array(
                       'FormElements',
                       array(
                           'Fieldset',
                           array('class' => 'pull-right half-width')
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
                  )
        );
    }
}