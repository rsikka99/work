<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerConfigMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use My_Brand;
use My_Validate_DateTime;
use Tangent\Validate\FieldDependsOnValue;
use Zend_Validate_GreaterThan;
use Zend_Validate_Between;
use Zend_Validate_Float;
use Zend_Validate_Int;
use Zend_Validate_NotEmpty;
use ZendX_JQuery_Form_Element_DatePicker;

/**
 * Class DeviceSetupForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class DeviceSetupForm extends \My_Form_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');


        /*
         * Manufacturer
         */
        $manufacturers = [];
        /* @var $manufacturer ManufacturerModel */
        foreach (ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers() as $manufacturer)
        {
            $manufacturers [$manufacturer->id] = $manufacturer->fullname;
        }

        $this->addElement('select', 'manufacturerId', [
            'label'        => 'Manufacturer:',
            'class'        => 'span3',
            'multiOptions' => $manufacturers,
        ]);

        /*
         * Printer Model Name
         */
        $this->addElement('text', 'modelName', [
            'label'      => 'Model Name:',
            'class'      => 'span3',
            'required'   => true,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [1, 255],
                ],
            ],
        ]);

        /*
         * Is Quote Gen Device
         */
        $this->addElement('checkbox', 'can_sell', [
            'label'       => 'Can Sell Device:',
            'description' => 'Note: SKU is required when checked.',
            'filters'     => ['Boolean'],
        ]);

        /*
         * SKU
         */
        $this->addElement('text', 'oemSku', [
            'label'      => 'OEM SKU:',
            'class'      => 'span2',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('can_sell', '1',
                    [
                        new Zend_Validate_NotEmpty()
                    ],
                    [
                        'validator' => 'StringLength',
                        'options'   => [1, 255],
                    ]
                ),
            ],
        ]);

        $this->addElement('text', 'dealerSku', [
            'label'      => My_Brand::$dealerSku . ":",
            'class'      => 'span2',
            'maxlength'  => 255,
            'required'   => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new FieldDependsOnValue('can_sell', '1',
                    [
                        new Zend_Validate_NotEmpty()
                    ],
                    [
                        'validator' => 'StringLength',
                        'options'   => [1, 255],
                    ]
                ),
            ],
        ]);

        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', [
            'label'    => 'Standard Features:',
            'style'    => 'height: 100px',
            'required' => false,
            'filters'  => ['StringTrim', 'StripTags']
        ]);

        /*
         * Device Price
         */
        $this->addElement('text', 'cost', [
            'label'      => 'Device Cost:',
            'class'      => 'span1',
            'prepend'    => '$',
            'dimension'  => 1,
            'maxlength'  => 8,
            'required'   => false,
            'allowEmpty' => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new FieldDependsOnValue('can_sell', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                    new Zend_Validate_GreaterThan(['min' => 0]),
                ]),
            ],
        ]);

        /*
         * Toner Configuration
         */
        $tonerConfigs = [];
        /* @var $tonerConfig TonerConfigModel */
        foreach (TonerConfigMapper::getInstance()->fetchAll() as $tonerConfig)
        {
            $tonerConfigs [$tonerConfig->id] = $tonerConfig->name;
        }

        $this->addElement('select', 'tonerConfigId', [
            'label'        => 'Toner Configuration:',
            'class'        => 'span3',
            'required'     => true,
            'multiOptions' => $tonerConfigs,
        ]);

        /*
         * Hidden Toner Configuration This will be used when editing to hold the toner config id when the dropdown is
         * disabled
         */

        /*
         * Is copier
         */
        $this->addElement('checkbox', 'isCopier', [
            'label'   => 'Is Copier:',
            'filters' => ['Boolean'],
        ]);

        /*
         * Is fax
         */
        $this->addElement('checkbox', 'isFax', [
            'label'   => 'Is Fax:',
            'filters' => ['Boolean'],
        ]);

        /*
         * Is duplex
         */
        $this->addElement('checkbox', 'isDuplex', [
            'label'   => 'Is Duplex:',
            'filters' => ['Boolean'],
        ]);

        /*
         * Reports toner levels (JIT Compatible)
         */
        $this->addElement('checkbox', 'reportsTonerLevels', [
            'label'   => 'Reports toner levels:',
            'filters' => ['Boolean'],
        ]);

        /*
         * Printer Wattage (Running)
         */
        $this->addElement('text', 'wattsPowerNormal', [
            'label'      => 'Watts Power Normal:',
            'class'      => 'span1',
            'maxlength'  => 4,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 5000],
                ],
            ],
        ]);

        /*
         * Printer Wattage (Idle)
         */
        $this->addElement('text', 'wattsPowerIdle', [
            'label'      => 'Watts Power Idle:',
            'class'      => 'span1',
            'maxlength'  => 4,
            'filters'    => ['StringTrim', 'StripTags'],
            'append'     => 'watts',
            'dimension'  => 1,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 1, 'max' => 5000],
                ],
            ],
        ]);

        /*
         * Parts Cost Per Page
         */
        $this->addElement('text', 'partsCostPerPage', [
            'label'      => 'Parts Cost Per Page:',
            'class'      => 'span1',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new FieldDependsOnValue('can_sell', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                    new Zend_Validate_Between(['min' => 0, 'max' => 5])
                ]),
            ],
        ]);

        /*
        * Labor Cost Per Page
        */
        $this->addElement('text', 'laborCostPerPage', [
            'label'      => 'Labor Cost Per Page:',
            'class'      => 'span1',
            'maxlength'  => 8,
            'allowEmpty' => false,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new FieldDependsOnValue('can_sell', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Float(),
                    new Zend_Validate_Between(['min' => 0, 'max' => 5,]),
                ]),
            ],
        ]);

        /*
         * Launch Date /
         */
        $minYear    = 1950;
        $maxYear    = ((int)date('Y')) + 2;
        $launchDate = new ZendX_JQuery_Form_Element_DatePicker('launchDate');
        $launchDate->setLabel('Launch Date:')
                   ->setAttrib('class', 'span2')
                   ->setJQueryParam('dateFormat', 'yy-mm-dd')
                   ->setJqueryParam('timeFormat', 'hh:mm')
                   ->setJQueryParam('changeYear', 'true')
                   ->setJqueryParam('changeMonth', 'true')
                   ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                   ->setDescription('yyyy-mm-dd')
                   ->addValidator(new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'))
                   ->setRequired(true)
                   ->setAttrib('maxlength', 10)
                   ->addFilters(['StringTrim', 'StripTags']);
        $this->addElement($launchDate);

        /*
         * Print Speed (Monochrome)
         */
        $this->addElement('text', 'ppmBlack', [
            'label'      => 'Print Speed (Mono):',
            'class'      => 'span1',
            'maxlength'  => 4,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 1000],
                ],
            ],
        ]);

        /*
         * Print Speed (Color)
         */
        $this->addElement('text', 'ppmColor', [
            'label'      => 'Print Speed (Color):',
            'class'      => 'span1',
            'maxlength'  => 4,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 1000],
                ],
            ],
        ]);

        /*
         * Is leased
         */
        $this->addElement('checkbox', 'isLeased', [
            'label'       => 'Is Leased:',
            'description' => 'Note: Leased Toner Yield is required when checked.',
            'filters'     => ['Boolean'],
        ]);

        /*
         * Leased Toner Yield
         */
        $this->addElement('text', 'leasedTonerYield', [
            'label'      => 'Leased Toner Yield:',
            'class'      => 'span1',
            'maxlength'  => 6,
            'filters'    => ['StringTrim', 'StripTags'],
            'allowEmpty' => false,
            'validators' => [
                new FieldDependsOnValue('isLeased', '1', [
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Int(),
                    new Zend_Validate_GreaterThan(['min' => 0]),
                ]),
            ],
        ]);

        $this->addElement('hidden', 'toner_array');

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'label' => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ]);

    }
}
