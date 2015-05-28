<?php
use MPSToolbox\Legacy\Modules\Admin\Forms\UserForm;

/**
 * Class Admin_Form_UserTest
 */
class Admin_Form_UserTest extends My_DatabaseTestCase
{

    public $fixtures = ['dealers'];

    /**
     * @var array
     */
    protected $_goodData;

    /**
     * @var array
     */
    protected $_badData;

    /**
     * @var UserForm
     */
    protected $_form;

    /**
     * Builds the form to be used for testing
     *
     * @param      $formMode
     * @param bool $isAdmin
     *
     * @return \MPSToolbox\Legacy\Modules\Admin\Forms\UserForm
     */
    public function getForm ($formMode, $isAdmin = false)
    {
        $this->_form = new UserForm($formMode, $isAdmin);

        return $this->_form;
    }

    /**
     * Data that elements that we shouldn't see based on form mode.
     *
     * @return array
     */
    public function fieldsExist ()
    {
        return array(
            array(
                'Zend_Form_Element_Select',
                UserForm::MODE_CREATE,
                'dealerId'
            ),
            array(
                'Zend_Form_Element_Select',
                UserForm::MODE_EDIT,
                'dealerId'
            ),
            array('Zend_Form_Element_Text',
                  UserForm::MODE_EDIT,
                  'loginAttempts',
            ),
            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_EDIT,
                  'resetLoginAttempts',
            ),
            array('My_Form_Element_DateTimePicker',
                  UserForm::MODE_EDIT,
                  'frozenUntil',
            ),

            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_EDIT,
                  'locked',
            ),
            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_EDIT,
                  'reset_password',
            ),
            array(
                'Zend_Form_Element_Checkbox',
                UserForm::MODE_CREATE,
                'resetPasswordOnNextLogin',
            ),
        );
    }

    /**
     * @dataProvider fieldsExist
     */
    public function testFieldsExists ($elementType, $formMode, $elementName)
    {
        $this->assertInstanceOf($elementType, $this->getForm($formMode)->getElement($elementName));
    }

    /**
     * Data that elements that we shouldn't see based on form mode.
     *
     * @return array
     */
    public function fieldsNotExist ()
    {
        return array(
            array('Zend_Form_Element_Text',
                  UserForm::MODE_CREATE,
                  'loginAttempts',
            ),
            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_CREATE,
                  'resetLoginAttempts',
            ),
            array('My_Form_Element_DateTimePicker',
                  UserForm::MODE_CREATE,
                  'frozenUntil',
            ),

            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_CREATE,
                  'locked',
            ),
            array('Zend_Form_Element_Checkbox',
                  UserForm::MODE_CREATE,
                  'reset_password',
            ),
        );
    }

    /**
     * @dataProvider fieldsNotExist
     */
    public function testFieldsNotExist ($elementType, $formMode, $elementName)
    {
        $this->assertNotInstanceOf($elementType, $this->getForm($formMode)->getElement($elementName));
    }

    public function getBadData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_userFormTest.xml");
        $data = array();
        foreach ($xml->user as $row)
        {
            $data[] = array(
                (array)$row->data,
                (strcasecmp((string)$row->formMode, 'create') === 0) ? 0 : 1
            );
        }

        return $data;
    }

    /**
     * @dataProvider getBadData
     */
    public function testFormRejectsBadData ($data, $formMode)
    {
        $this->assertFalse($this->getForm($formMode)->isValid($data));
    }

    public function getGoodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_userFormTest.xml");
        $data = array();
        foreach ($xml->user as $row)
        {
            $data[] = array(
                (array)$row->data,
                (strcasecmp((string)$row->formMode, 'create') === 0) ? 0 : 1
            );
        }

        return $data;
    }

    /**
     * @dataProvider getGoodData
     */
    public function testFormAcceptsValidData ($data, $formMode)
    {
        $form = $this->getForm($formMode);
        $this->assertTrue($form->isValid((array)$data), "data: " . implode(" ", $data) . " errors: " . implode(' ', $form->getErrorMessages()));
    }
}