<?php
class Preferences_QuoteController extends Zend_Controller_Action
{
    /**
     * This is where the user can edit their quote preferences
     */
    public function indexAction ()
    {
        // Find client and pass form object
        $form = new Preferences_Form_QuoteSetting(true);

        $quoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);

        $form->populate($quoteSetting->toArray());

        // Update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        foreach ($values as &$value)
                        {
                            if (strlen($value) === 0)
                            {
                                $value = new Zend_Db_Expr('NULL');
                            }
                        }
                        $quoteSetting->populate($values);
                        Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($quoteSetting);

                        // Redirect user with message
                        $this->_helper->flashMessenger(array(
                                                            'success' => "Your quote settings were updated successfully."
                                                       ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => 'Please correct the errors below.'
                                                       ));
                    }
                }
                catch (Exception $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => 'Error editing quote setting.  Please try again.'
                                                   ));
                }
            }
            else // Client hit cancel redirect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * This is where the admin can edit the system quote preferences
     */
    public function systemAction ()
    {
        $quoteSettingId = 1;

        // Find client and pass form object
        $form               = new Preferences_Form_QuoteSetting();
        $quoteSettingMapper = Quotegen_Model_Mapper_QuoteSetting::getInstance();
        $quoteSetting       = $quoteSettingMapper->find($quoteSettingId);

        $form->populate($quoteSetting->toArray());
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Set nulls where needed.
                        foreach ($values as $key => &$value)
                        {
                            if (empty($value))
                            {
                                // Only admin and service cost per page should be allowed to be set to 0?
                                if (!((float)$value === 0.0 && ($key === "adminCostPerPage" || $key === "serviceCostPerPage")))
                                {
                                    $value = new Zend_Db_Expr('NULL');
                                }
                            }
                        }
                        // Update quoteSetting and message to confirm
                        $quoteSetting->populate($values);
                        $quoteSetting->id = $quoteSettingId;

                        $quoteSettingMapper->save($quoteSetting, $quoteSettingId);
                        $this->_helper->flashMessenger(array(
                                                            'success' => "Default quote settings were updated successfully."
                                                       ));

                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => 'Please correct the errors below'
                                                       ));
                    }
                }
                catch (Exception $e)
                {
                    My_Log::logException($e);
                    $this->_helper->flashMessenger(array(
                                                        'danger' => 'Error saving configuration.  Please try again.'
                                                   ));
                }
            }
            else // Client hit cancel redirect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
    }
}