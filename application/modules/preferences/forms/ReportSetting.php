<?php
/**
 * Class Preferences_Form_ReportSetting
 */
class Preferences_Form_ReportSetting extends Twitter_Bootstrap_Form_Horizontal
{
    public $allowsNull = false;
    /**
     * @var Assessment_Model_Assessment_Setting
     */
    protected $_reportSetting;
    /**
     * @var int
     */
    protected $_reportSettingId;

    /**
     * @param int  $reportSettingId
     * @param null $options
     */
    public function __construct ($reportSettingId, $options = null)
    {
        $this->_reportSettingId = $reportSettingId;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm');
        $this->addPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator');

        $coverageValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min'       => 0,
                    'max'       => 100,
                    'inclusive' => false
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

        $costValidator = array(
            array(
                'validator' => 'greaterThan',
                'options'   => array(
                    'min' => 0
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

        // Survey Elements
        $this->addElement('text', 'pageCoverageMono', array(
                                                           'label'      => 'Page Coverage Monochrome',
                                                           'append'     => '%',
                                                           'validators' => $coverageValidator
                                                      ));
        $this->addElement('text', 'pageCoverageColor', array(
                                                            'label'      => 'Page Coverage Color',
                                                            'append'     => '%',
                                                            'validators' => $coverageValidator
                                                       ));
        // Assessment Elements
        $this->addElement('text', 'assessmentReportMargin', array(
                                                                 'label'      => 'Pricing Margin',
                                                                 'append'     => '%',
                                                                 'validators' => $marginValidator
                                                            ));
        $this->addElement('text', 'monthlyLeasePayment', array(
                                                              'label'      => 'Monthly Lease Payment',
                                                              'append'     => '$ / device',
                                                              'validators' => $costValidator
                                                         ));

        $this->addElement('text', 'defaultPrinterCost', array(
                                                             'label'      => 'Default Printer Cost',
                                                             'append'     => '$ / device',
                                                             'validators' => $costValidator
                                                        ));
        $this->addElement('text', 'leasedBwCostPerPage', array(
                                                              'label'      => 'Leased Monochrome Cost',
                                                              'append'     => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'leasedColorCostPerPage', array(
                                                                 'label'      => 'Leased Color Cost',
                                                                 'append'     => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));
        $this->addElement('text', 'mpsBwCostPerPage', array(
                                                           'label'      => 'Monochrome Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'mpsColorCostPerPage', array(
                                                              'label'      => 'Color Cost',
                                                              'append'     => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'kilowattsPerHour', array(
                                                           'label'      => 'Energy Cost',
                                                           'append'     => '/ KWh',
                                                           'validators' => $costValidator
                                                      ));


        // Gross margin elements
        $this->addElement('text', 'actualPageCoverageMono', array(
                                                                 'label'      => 'Page Coverage Monochrome',
                                                                 'append'     => '%',
                                                                 'validators' => $coverageValidator
                                                            ));
        $this->addElement('text', 'actualPageCoverageColor', array(
                                                                  'label'      => 'Page Coverage Color',
                                                                  'append'     => '%',
                                                                  'validators' => $coverageValidator
                                                             ));
        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'laborCostPerPage', array(
                                                           'label'      => 'Labor Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'partsCostPerPage', array(
                                                           'label'      => 'Parts Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));

        $customerMonochromeVendor = $this->createElement('multiselect', 'customerMonochromeRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $customerColorVendor = $this->createElement('multiselect', 'customerColorRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));

        $dealerMonochromeVendor = $this->createElement('multiselect', 'dealerMonochromeRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $dealerColorVendor = $this->createElement('multiselect', 'dealerColorRankSetArray',
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
                $element->setAttrib('class', 'span2 ');
            }
            $element->setRequired(true);
        }

        $this->addDisplayGroup(array('pageCoverageMono', 'pageCoverageColor'), 'survey', array('legend' => 'Survey Settings'));
        $this->addDisplayGroup(array('assessmentReportMargin',
                                     'monthlyLeasePayment',
                                     'defaultPrinterCost',
                                     'leasedBwCostPerPage',
                                     'leasedColorCostPerPage',
                                     'mpsBwCostPerPage',
                                     'mpsColorCostPerPage',
                                     'kilowattsPerHour',
                                     $customerMonochromeVendor,
                                     $customerColorVendor,
                               ), 'assessment', array('legend' => 'Assessment Settings',));

        $this->addDisplayGroup(array('actualPageCoverageMono', 'actualPageCoverageColor', 'adminCostPerPage', 'laborCostPerPage', 'partsCostPerPage', $dealerMonochromeVendor, $dealerColorVendor), 'grossMargin', array('legend' => 'Gross Margin Settings'));
        $this->setElementDecorators(array(
                                         'FieldSize',
                                         'ViewHelper',
                                         'Addon',
                                         'ElementErrors',
                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                         'Wrapper',
                                         array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                                         array('Description', array('tag' => 'td', 'placement' => 'prepend', 'class' => 'description')),
                                         array('Label', array('tag' => 'td')),
                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'control-group')),
                                    ));

        $this->tonerSelectElementsDisplayGroups(2);

        // Form Buttons
        $submitButton = $this->createElement('submit', 'submit', array(
                                                                      'label' => 'Submit',

                                                                 ));
        $submitButton->setDecorators(array(
                                          'FieldSize',
                                          'ViewHelper',
                                          'Addon',
                                          'ElementErrors',
                                     ));
        $this->addElement($submitButton);

        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'Fieldset'
                                         ));

        $tonerVendors = Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown();

        $customerColorVendor->setMultiOptions($tonerVendors);
        $customerMonochromeVendor->setMultiOptions($tonerVendors);
        $dealerMonochromeVendor->setMultiOptions($tonerVendors);
        $dealerColorVendor->setMultiOptions($tonerVendors);
    }

    /**
     *  This is used to set up the form with a three column header.
     */
    public function setUpFormWithDefaultDecorators ()
    {
        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array('ColumnHeader', array('data' => array('Property', 'Default', 'Value'), 'placement' => 'prepend')),
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'Fieldset'
                                         ));
        $this->tonerSelectElementsDisplayGroups(3);
    }

    /**
     *
     * @param $colSpan int the number of columns to span the toner vendors element
     */
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
                                                                        array('AddRowData', array('header'    => 'Monochrome Toner Preference',
                                                                                                  "trClass"   => "control-group",
                                                                                                  "tdAttr"    => "colspan={$colSpan}",
                                                                                                  "placement" => Zend_Form_Decorator_Abstract::PREPEND))
                                                                   ));

        $this->getElement("dealerColorRankSetArray")->setDecorators(array(
                                                                   'FieldSize',
                                                                   'ViewHelper',
                                                                   'Addon',
                                                                   array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                   'ElementErrors',
                                                                   array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                   array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                   array('AddRowData', array('header'    => 'Color Toner Preference',
                                                                                             "trClass"   => "control-group",
                                                                                             "tdAttr"    => "colspan={$colSpan}",
                                                                                             "placement" => Zend_Form_Decorator_Abstract::PREPEND))
                                                              ));
        $this->getElement("customerMonochromeRankSetArray")->setDecorators(array(
                                                                          'FieldSize',
                                                                          'ViewHelper',
                                                                          'Addon',
                                                                          array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                          'ElementErrors',
                                                                          array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                          array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                          array('AddRowData', array('header'    => 'Monochrome Toner Preference',
                                                                                                    "trClass"   => "control-group",
                                                                                                    "tdAttr"    => "colspan={$colSpan}",
                                                                                                    "placement" => Zend_Form_Decorator_Abstract::PREPEND))
                                                                     ));
        $this->getElement("customerColorRankSetArray")->setDecorators(array(
                                                                     'FieldSize',
                                                                     'ViewHelper',
                                                                     'Addon',
                                                                     array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                     'ElementErrors',
                                                                     array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                     array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                     array('AddRowData', array('header'    => 'Color Toner Preference',
                                                                                               "trClass"   => "control-group",
                                                                                               "tdAttr"    => "colspan={$colSpan}",
                                                                                               "placement" => Zend_Form_Decorator_Abstract::PREPEND))
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

    /**
     * @return Assessment_Model_Assessment_Setting
     */
    protected function getReportSetting ()
    {
        if (!isset($this->_reportSetting))
        {
            $this->_reportSetting = Assessment_Model_Mapper_Assessment_Setting::getInstance()->find($this->_reportSettingId);
        }

        return $this->_reportSetting;
    }
}