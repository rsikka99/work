<?php
/**
 * Class Admin_Form_UserTest
 */
class Admin_Form_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_goodData;

    /**
     * @var array
     */
    protected $_badData;

    /**
     * @var Admin_Form_User
     */
    protected $_form;

    /**
     * Builds the form to be used for testing
     *
     * @param      $formMode
     * @param bool $isAdmin
     *
     * @return \Admin_Form_User
     */
    public function getForm ($formMode, $isAdmin = false)
    {
        $this->_form = new Admin_Form_User($formMode, $isAdmin);

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
                Admin_Form_User::MODE_CREATE,
                'dealerId'
            ),
            array(
                'Zend_Form_Element_Select',
                Admin_Form_User::MODE_EDIT,
                'dealerId'
            ),
            array('Zend_Form_Element_Text',
                  Admin_Form_User::MODE_EDIT,
                  'loginAttempts',
            ),
            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_EDIT,
                  'resetLoginAttempts',
            ),
            array('My_Form_Element_DateTimePicker',
                  Admin_Form_User::MODE_EDIT,
                  'frozenUntil',
            ),

            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_EDIT,
                  'locked',
            ),
            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_EDIT,
                  'reset_password',
            ),
            array(
                'Zend_Form_Element_Checkbox',
                Admin_Form_User::MODE_CREATE,
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
                  Admin_Form_User::MODE_CREATE,
                  'loginAttempts',
            ),
            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_CREATE,
                  'resetLoginAttempts',
            ),
            array('My_Form_Element_DateTimePicker',
                  Admin_Form_User::MODE_CREATE,
                  'frozenUntil',
            ),

            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_CREATE,
                  'locked',
            ),
            array('Zend_Form_Element_Checkbox',
                  Admin_Form_User::MODE_CREATE,
                  'reset_password',
            ),
            array(
                'Zend_Form_Element_Checkbox',
                Admin_Form_User::MODE_EDIT,
                'resetPasswordOnNextLogin',
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
            $data[(string)$row->formMode][] = (array)$row->data;
        }

        return $data;
    }

    public function testFormRejectsBadData ()
    {
        $data = $this->getBadData();

        foreach ($data['create'] as $data)
        {
            $this->assertFalse($this->getForm(Admin_Form_User::MODE_CREATE)->isValid((array)$data));
        }
        foreach ($data['edit'] as $data)
        {
            $this->assertFalse($this->getForm(Admin_Form_User::MODE_EDIT)->isValid((array)$data));
        }
    }

    public function getGoodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_userFormTest.xml");
        $data = array();
        foreach ($xml->user as $row)
        {
            $data[(string)$row->formMode][] = (array)$row->data;
        }

        return $data;
    }

    public function testFormAcceptsValidData ()
    {
        $data = $this->getGoodData();

        foreach ($data['create'] as $data)
        {
            $form = $this->getForm(Admin_Form_User::MODE_CREATE);
            $this->assertTrue($form->isValid((array)$data), "data: " . implode(" ", $data) . " errors: " . implode(' ', $form->getErrorMessages()));
        }

        foreach ($data['edit'] as $data)
        {
            $form = $this->getForm(Admin_Form_User::MODE_EDIT);
            $this->assertTrue($form->isValid((array)$data), "data: " . implode(' ', $data) . " errors: " . implode(' ', $form->getErrorMessages()));
        }
    }
}