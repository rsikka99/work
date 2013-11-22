<?php

/**
 * Class Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations
 */
class Proposalgen_Form_MasterDeviceManagement_Delete extends Twitter_Bootstrap_Form_Vertical
{

    /**
     * @param null $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function init ()
    {
        // Add the question
        $note = new My_Form_Element_Paragraph('question');
        $note->setValue("Are you sure you want to delete?");
        $this->addElement($note);

        // Add the cancel button
        $cancel = $this->createElement('button', 'cancel', array(
                                                                'label'        => 'Cancel',
                                                                'buttonType'   => Twitter_Bootstrap_Form_Element_Submit::BUTTON_INVERSE,
                                                                'data-dismiss' => 'modal'
                                                           ));

        $delete = $this->createElement('button', 'delete', array(
                                                                'label'        => 'Delete',
                                                                'buttonType'   => Twitter_Bootstrap_Form_Element_Submit::BUTTON_DANGER,
                                                                'data-dismiss' => 'modal'
                                                           ));
        $this->addElement('hidden', 'deleteId', array());
        $this->addElement('hidden', 'deleteColorId', array());
        $this->addElement('hidden', 'deleteFormName', array());
        $this->addElements(array($cancel, $delete));
    }
}