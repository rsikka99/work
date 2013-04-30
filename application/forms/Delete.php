<?php

/**
 * Class Application_Form_Delete
 */
class Application_Form_Delete extends EasyBib_Form
{
    protected $_formQuestion;

    /**
     * Creates a deletion form
     *
     * @param string       $message The message to present to the users
     * @param null|array   $options
     *
     * @see EasyBib_Form::__construct()
     */
    public function __construct ($message = "Are you sure you want to delete?", $options = null)
    {
        $this->_formQuestion = $message;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->setAttrib('class', 'form-horizontal');

        $note = new My_Form_Element_Paragraph('question');
        $note->setValue($this->_formQuestion);

        $this->addElement($note);

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Delete'
                                              ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
                                                   'ignore' => true,
                                                   'label'  => 'Cancel'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
