<?php

/**
 * User Form: used for adding/editing users in the sytem
 *
 * @author Chris Garrah
 * @version v1.0
 */
class Proposalgen_Form_User extends Zend_Form
{

    /**
     * Constructor builds the form
     * 
     * @param $options -
     *            not used (required)
     * @param $type -
     *            can be set to 'edit', or null. Differnt form elements are added for editing an instructor and adding a
     *            new instructor.
     * @return HTML markup for the from is automatically returned by zend_form
     */
    public function __construct ($options = null, $type = null)
    {
        // call parent constructor
        parent::__construct($options);
        $elements = array ();
        $elementCounter = 0;
        
        $this->setName('user_form');
        //$this->setAttrib('class', 'outlined');
        

        //add location drop down
        $location = new Zend_Form_Element_Select('select_user');
        $location->setLabel('Select User:')
            ->setAttrib('style', 'width:150px')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('class', 'select_user')
            ->setAttrib('id', 'select_user')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'select_user-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $location);
        $elementCounter ++;
        
        //privileges
        $privileges = new Zend_Form_Element_Select('privileges');
        $privileges->setLabel('* Privilege Setting:')
            ->setAttrib('style', 'width:150px')
            ->setAttrib('maxlength', 100)
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'privileges-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $privileges);
        $elementCounter ++;
        
        if ($type == 'dealer')
        {
            $company = new Zend_Form_Element_Text('select_company');
            $company->setLabel('* Company:')
                ->setAttrib('maxlength', 20)
                ->setAttrib('style', 'width:100px')
                ->setAttrib('disabled', 'disabled')
                ->setAttrib('style', 'border:none; background-color:#ffffff')
                ->setOrder($elementCounter)
                ->setDecorators(array (
                    'ViewHelper', 
                    array (
                            'Description', 
                            array (
                                    'escape' => false, 
                                    'tag' => false 
                            ) 
                    ), 
                    'Errors', 
                    array (
                            'HtmlTag', 
                            array (
                                    'tag' => 'dd', 
                                    'id' => 'select_company-element' 
                            ) 
                    ), 
                    array (
                            'Label', 
                            array (
                                    'tag' => 'dt', 
                                    'class' => 'forms_label' 
                            ) 
                    ) 
            ));
        }
        else
        {
            $company = new Zend_Form_Element_Select('select_company');
            $company->setLabel('* Company:')
                ->setAttrib('style', 'width:150px')
                ->setRequired(true)
                ->setOrder($elementCounter)
                ->setAttrib('id', 'select_company')
                ->setDecorators(array (
                    'ViewHelper', 
                    array (
                            'Description', 
                            array (
                                    'escape' => false, 
                                    'tag' => false 
                            ) 
                    ), 
                    'Errors', 
                    array (
                            'HtmlTag', 
                            array (
                                    'tag' => 'dd', 
                                    'id' => 'select_company-element' 
                            ) 
                    ), 
                    array (
                            'Label', 
                            array (
                                    'tag' => 'dt', 
                                    'class' => 'forms_label' 
                            ) 
                    ) 
            ));
        }
        array_push($elements, $company);
        $elementCounter ++;
        
        $userName = new Zend_Form_Element_Text('username');
        $userName->setLabel('* Username:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'username-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $userName);
        $elementCounter ++;
        
        $firstName = new Zend_Form_Element_Text('userFirstName');
        $firstName->setLabel('* First Name:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'userFirstName-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $firstName);
        $elementCounter ++;
        
        $lastName = new Zend_Form_Element_Text('userLastName');
        $lastName->setLabel('* Last Name:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'userLastName-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $lastName);
        $elementCounter ++;
        
        $phone = new Zend_Form_Element_Text('userPhone');
        $phone->setLabel('* Phone:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty')
            ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
            ->addValidator('Phone')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'userPhone-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $phone);
        $elementCounter ++;
        
        $email = new Zend_Form_Element_Text('userEmail');
        $email->setLabel('* Email:')
            ->setAttrib('maxlength', 60)
            ->setAttrib('style', 'width:200px')
            ->setAttrib('maxlength', 100)
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress', true)
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'userEmail-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $email);
        $elementCounter ++;
        
        //override auto-password toggle
        $update_password = new Zend_Form_Element_Checkbox('update_password');
        $update_password->setLabel('Update Password:')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'update_password-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $update_password);
        $elementCounter ++;
        
        //password
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('* Change Password:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setAttrib('autocomplete', 'off')
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty', true)
            ->setDescription('<a href="javascript: void(0);" onclick="javascript: toggle_password(false)">Generate Password</a>')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'password-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $password);
        $elementCounter ++;
        
        //confirm password
        $passwordConfirm = new Zend_Form_Element_Password('passwordConfirm');
        $passwordConfirm->setLabel('* Password Confirmation:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setAttrib('autocomplete', 'off')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'passwordConfirm-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ))
            ->setOrder($elementCounter)
            ->addValidator('NotEmpty', true)
            ->addValidator('identical', false, array (
                'token' => 'password' 
        ));
        //change identical validator message
        $validator = $passwordConfirm->getValidator('identical');
        $validator->setMessage('Passwords did not match. Please Re-enter.', Zend_Validate_Identical::NOT_SAME);
        array_push($elements, $passwordConfirm);
        $elementCounter ++;
        
        //auto-password
        $autoPassword = new Zend_Form_Element_Text('auto_password');
        $autoPassword->setLabel('Generated Password:')
            ->setAttrib('maxlength', 20)
            ->setAttrib('style', 'width:100px')
            ->setAttrib('readonly', true)
            ->setDescription('<a href="javascript: void(0);" onclick="javascript: toggle_password(true)">Change Password</a>')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'auto_password-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $autoPassword);
        $elementCounter ++;
        
        $hiddenMode = new Zend_Form_Element_Hidden('password_mode');
        $hiddenMode->setValue("true");
        $hiddenMode->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $hiddenMode);
        $elementCounter ++;
        
        //change password on first login
        $must_change = new Zend_Form_Element_Checkbox('must_change');
        $must_change->setLabel('Require password change on next sign in:')
            ->setOrder($elementCounter)
            ->setAttrib('checked', 'checked')
            ->setAttrib('class', 'inline')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'must_change-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label inline' 
                        ) 
                ) 
        ));
        array_push($elements, $must_change);
        $elementCounter ++;
        
        //restricted date
        $element = new Zend_Form_Element_Text('login_restricted_date');
        $element->setLabel('Login Restricted Until Date:')
            ->setRequired(true)
            ->setAttrib('size', 20)
            ->setAttrib('maxlength', 30)
            ->setAttrib('id', 'login_restricted_date')
            ->setDescription('mm/dd/yyyy')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'login_restricted_date-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ))
            ->setOrder($elementCounter);
        //array_push($elements,$element);
        //$elementCounter++;
        

        //activated
        $is_activated = new Zend_Form_Element_Checkbox('is_activated');
        $is_activated->setLabel('Activated:')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'inline')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'is_activated-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label inline' 
                        ) 
                ) 
        ));
        array_push($elements, $is_activated);
        $elementCounter ++;
        
        //save button
        $update = new Zend_Form_Element_Submit('save_user', array (
                'disableLoadDefaultDecorators' => true 
        ));
        $update->setLabel('Save')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'btn btn-primary')
            ->setAttrib('onclick', 'javascript: enable_fields();')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ), 
                array (
                        array (
                                'row' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'tr', 
                                'openOnly' => true 
                        ) 
                ) 
        ));
        array_push($elements, $update);
        $elementCounter ++;
        
        //delete button
        $element = new Zend_Form_Element_Submit('delete_user', array (
                'disableLoadDefaultDecorators' => true 
        ));
        $element->setLabel('Delete')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'btn')
            ->setAttrib('onclick', 'javascript: return confirm("Deleting a user will also remove any reports associated with the user. Are you sure you want to delete this user?"); enable_fields();')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //back button
        $back = new Zend_Form_Element_Button('back_button');
        $back->setLabel('Done')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'btn')
            ->setAttrib('onClick', 'javascript: document.location.href = "../admin";')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ), 
                array (
                        array (
                                'row' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'tr', 
                                'closeOnly' => 'true' 
                        ) 
                ) 
        ));
        array_push($elements, $back);
        $elementCounter ++;
        
        // add all defined elements to the form        
        $this->addElements($elements);
        
        $this->setDecorators(array (
                'FormElements', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'table', 
                                'class' => 'button_menu' 
                        ) 
                ), 
                'Form' 
        ));
    } // end function __construct

    public function set_validation ($data)
    {
        if ($data ['password_mode'] == "false")
        {
            $this->password->setRequired(true);
            $this->passwordConfirm->setRequired(true);
        }
        else
        {
            $this->password->setRequired(false);
            $this->passwordConfirm->setRequired(false);
        }
        return $data;
    }
} // end class forms_instructorForm


?>