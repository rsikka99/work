<?php

namespace MPSToolbox\Forms;

use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * Class DeviceAttributesForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class HardwareAttributesForm extends \My_Form_Form
{

    protected function arrToMulti($arr) {
        $result = [];
        foreach ($arr as $value) $result[$value] = $value;
        return $result;
    }

    protected $_isAllowedToEditFields = false;

    /**
     * @param null $options
     * @param bool $isAllowedToEditFields
     */
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        $this->_isAllowedToEditFields = $isAllowedToEditFields;
        if (null !== $this->getView())
        {
            $this->getView()->addHelperPath(
                'ZendX/JQuery/View/Helper',
                'ZendX_JQuery_View_Helper'
            );
        }

        $this->addPrefixPath(
            'ZendX_JQuery_Form_Element',
            'ZendX/JQuery/Form/Element',
            'element'
        );

        $this->addElementPrefixPath(
            'ZendX_JQuery_Form_Decorator',
            'ZendX/JQuery/Form/Decorator',
            'decorator'
        );

        $this->addDisplayGroupPrefixPath(
            'ZendX_JQuery_Form_Decorator',
            'ZendX/JQuery/Form/Decorator'
        );

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'hardwareAttributes');

        $this->addElement('select', 'grade', [
            'label'      => 'Grade',
            'required'   => false,
            'allowEmpty' => true,
            'multiOptions' => $this->arrToMulti([
                '','Good','Better','Best'
            ])
        ]);

        /*
        * Launch Date
        */
        $minYear           = 1950;
        $maxYear           = ((int)date('Y')) + 2;
        $launchDateElement = $this->createElement('DatePicker', 'launchDate', [
            'label'      => 'Launch Date',
            'decorators' => ['UiWidgetElement'],
            'required'   => $this->_isAllowedToEditFields,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'),
            ],
        ]);
        $launchDateElement->setJQueryParam('dateFormat', 'yy-mm-dd')
            ->setJQueryParam('changeYear', 'true')
            ->setJqueryParam('changeMonth', 'true')
            ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}");

        if (!$this->_isAllowedToEditFields)
        {
            $launchDateElement->setAttrib('disabled', true);
        }

        $this->addElement($launchDateElement);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/hardware-attributes-form.phtml']]]);
    }
}
