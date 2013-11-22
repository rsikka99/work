<?php
/**
 * Class Memjetoptimization_Form_Setting
 */
class Memjetoptimization_Form_Setting extends Twitter_Bootstrap_Form_Horizontal
{
    public $allowsNull = false;

    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm form-center-actions MemjetoptimizationSettings');
        $this->addPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator');

        $costValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min' => 0,
                    'max' => PHP_INT_MAX,
                    'inclusive'
                )
            ),
            'Float'
        );

        $cppValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min'       => 0,
                    'max'       => 5,
                    'inclusive' => true
                )
            ),
            'Float'
        );

        $marginValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min' => -100,
                    'max' => 100
                )
            ),
            'Float'
        );

        $coverageValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min' => 0,
                    'max' => 100
                )
            ),
            'Float'
        );


        $this->addElement('text', 'name', array(
                                               'label'  => 'Report Name',
                                               'append' => ' ',
                                          ));
        // Memjet Optimization Elements

        $this->addElement('text', 'pageCoverageMonochrome', array(
                                                                 'label'      => 'Page Coverage Monochrome',
                                                                 'append'     => '%',
                                                                 'validators' => $coverageValidator
                                                            ));

        $this->addElement('text', 'pageCoverageColor', array(
                                                            'label'      => 'Page Coverage Color',
                                                            'append'     => '%',
                                                            'validators' => $coverageValidator
                                                       ));

        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator,
                                                      ));
        $this->addElement('text', 'partsCostPerPage', array(
                                                           'label'      => 'Parts Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator,
                                                      ));
        $this->addElement('text', 'laborCostPerPage', array(
                                                           'label'      => 'Labor Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator,
                                                      ));


        $this->addElement('text', 'targetMonochromeCostPerPage', array(
                                                                      'label'      => 'Target Monochrome Cost Per Page',
                                                                      'append'     => '$ / page',
                                                                      'validators' => $cppValidator
                                                                 ));
        $this->addElement('text', 'targetColorCostPerPage', array(
                                                                 'label'      => 'Target Color Cost Per Page',
                                                                 'append'     => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));

        $this->addElement('text', 'blackToColorRatio', array(
                                                            'label'      => 'Black to Color Page Ratio',
                                                            'append'     => '%',
                                                            'validators' => $coverageValidator
                                                       ));

        $this->addElement('text', 'lossThreshold', array(
                                                        'label'      => 'Loss Threshold',
                                                        'append'     => '$',
                                                        'validators' => $costValidator
                                                   ));

        $replacementMonochromeVendor = $this->createElement('multiselect', 'replacementMonochromeRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $replacementColorVendor      = $this->createElement('multiselect', 'replacementColorRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $dealerMonochromeVendor      = $this->createElement('multiselect', 'dealerMonochromeRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $dealerColorVendor           = $this->createElement('multiselect', 'dealerColorRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));

        // Set a span 2 to all elements that do not have a class
        /* @var $element Zend_Form_Element_Text */
        foreach ($this->getElements() as $element)
        {
            $class = $element->getAttrib('class');
            if (!$class)
            {
                $element->setAttrib('class', 'span2');
            }
            $element->setRequired(true);
        }


        $this->addDisplayGroup(array('name', 'pageCoverageMonochrome', 'pageCoverageColor', 'costThreshold', 'partsCostPerPage', 'laborCostPerPage', 'adminCostPerPage', 'targetMonochromeCostPerPage', 'targetColorCostPerPage', 'lossThreshold', 'blackToColorRatio', $replacementMonochromeVendor, $replacementColorVendor, $dealerMonochromeVendor, $dealerColorVendor), 'Memjetoptimization');

        $group = $this->getDisplayGroup('Memjetoptimization');
        $group->setDecorators(array(
                                   'FormElements',
                                   array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
                                   array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                   array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                   'Fieldset'
                              ));

        $this->setElementDecorators(array(
                                         'FieldSize',
                                         'ViewHelper',
                                         'Addon',
                                         'ElementErrors',
                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                         'Wrapper',
                                         array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'value')),
                                         array('Description', array('tag' => 'td', 'placement' => 'prepend', 'class' => 'description')),
                                         array('Label', array('tag' => 'td')),
                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'control-group')),
                                    ));
        $this->tonerSelectElementsDisplayGroups(2);

        $tonerVendors = Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown();
        $replacementMonochromeVendor->setMultiOptions($tonerVendors);
        $replacementColorVendor->setMultiOptions($tonerVendors);
        $dealerMonochromeVendor->setMultiOptions($tonerVendors);
        $dealerColorVendor->setMultiOptions($tonerVendors);

        Memjetoptimization_Form_Memjet_Optimization_Navigation::addFormActionsToForm(Memjetoptimization_Form_Memjet_Optimization_Navigation::BUTTONS_ALL, $this);
    }

    /**
     *  This is used to set up the form with a three column header.
     */
    public function setUpFormWithDefaultDecorators ()
    {
        $group = $this->getDisplayGroup('Memjetoptimization');
        $group->setDecorators(array(
                                   'FormElements',
                                   array('ColumnHeader', array('data' => array('Property', 'Default', 'Value'), 'class' => array('property', 'default', 'value'), 'placement' => 'prepend')),
                                   array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                   'Fieldset'
                              ));
        $this->tonerSelectElementsDisplayGroups(3);
    }

    public function tonerSelectElementsDisplayGroups ($colSpan)
    {

        $this->getElement("dealerMonochromeRankSetArray")->setDecorators(array(
                                                                              'FieldSize',
                                                                              'ViewHelper',
                                                                              'Addon',
                                                                              array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                              'ElementErrors',
                                                                              array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                              array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                              array('AddRowData', array('header'    => 'Dealer Monochrome Toner Preference',
                                                                                                        "trClass"   => "control-group",
                                                                                                        "tdAttr"    => "colspan={$colSpan}",
                                                                                                        "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
                                                                              array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                                                                                       "tdClass"   => "short-row",
                                                                                                                                       "tdAttr"    => "colspan={$colSpan}",
                                                                                                                                       "placement" => Zend_Form_Decorator_Abstract::APPEND))
                                                                         ));
        $this->getElement("dealerColorRankSetArray")->setDecorators(array(
                                                                         'FieldSize',
                                                                         'ViewHelper',
                                                                         'Addon',
                                                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                         'ElementErrors',
                                                                         array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                         array('AddRowData', array("header"    => "Dealer Color Toner Preference",
                                                                                                   "trClass"   => "control-group",
                                                                                                   "tdAttr"    => "colspan={$colSpan}",
                                                                                                   "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
                                                                         array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                                                                                  "tdClass"   => "short-row",
                                                                                                                                  "tdAttr"    => "colspan={$colSpan}",
                                                                                                                                  "placement" => Zend_Form_Decorator_Abstract::APPEND))
                                                                    ));
        $this->getElement("replacementMonochromeRankSetArray")->setDecorators(array(
                                                                                   'FieldSize',
                                                                                   'ViewHelper',
                                                                                   'Addon',
                                                                                   array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                                   'ElementErrors',
                                                                                   array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                                   array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                                   array('AddRowData', array("header"    => "Replacement Monochrome Toner Preference",
                                                                                                             "trClass"   => "control-group",
                                                                                                             "tdAttr"    => "colspan={$colSpan}",
                                                                                                             "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
                                                                                   array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                                                                                            "tdClass"   => "short-row",
                                                                                                                                            "tdAttr"    => "colspan={$colSpan}",
                                                                                                                                            "placement" => Zend_Form_Decorator_Abstract::APPEND))
                                                                              ));
        $this->getElement("replacementColorRankSetArray")->setDecorators(array(
                                                                              'FieldSize',
                                                                              'ViewHelper',
                                                                              'Addon',
                                                                              array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                              'ElementErrors',
                                                                              array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                              array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                              array('AddRowData', array("header"    => "Replacement Color Toner Preference",
                                                                                                        "trClass"   => "control-group",
                                                                                                        "tdAttr"    => "colspan={$colSpan}",
                                                                                                        "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
                                                                              array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                                                                                       "tdClass"   => "short-row",
                                                                                                                                       "tdAttr"    => "colspan={$colSpan}",
                                                                                                                                       "placement" => Zend_Form_Decorator_Abstract::APPEND))
                                                                         ));
    }

    /**
     * Allows the form to allow null values
     */
    public function allowNullValues ()
    {
        /* @var Zend_Form_Element_Text $element */
        foreach ($this->getElements() as $element)
        {
            $element->setRequired(false);
        }
        $this->allowsNull = true;
    }
}