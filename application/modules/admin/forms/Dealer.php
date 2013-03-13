<?php
class Admin_Form_Dealer extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'form-horizontal form-center-actions');

        $dealerName = $this->createElement('text', 'dealerName')
            ->setRequired(true)
            ->setLabel('Dealer Name')
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addValidator('StringLength', false, array(2, 255));
        $this->addElement($dealerName);

        $userLicences = $this->createElement('text', 'userLicenses')
            ->setRequired(true)
            ->setLabel('# of user licenses:')
            ->setFilters(array('StringTrim', 'StripTags'))
            ->addValidator('Between', false, array('min' => 1, 'max' => 1000));

        $this->addElement($userLicences);

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Submit'
                                              ));
        // Add the submit button
        $this->addElement('submit', 'cancel', array(
                                                   'ignore' => true,
                                                   'label'  => 'Cancel'
                                              ));

        $this->addElement('button', 'submit', array(
                                                   'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                   'label'      => 'Save',
                                                   'type'       => 'submit',

                                              ));
        $this->addElement('button', 'cancel', array(
                                                   'label'      => 'Cancel',
                                                   'type'       => 'submit',

                                              ));
    }
}