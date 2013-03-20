<?php

class Quotegen_QuotesettingController extends Tangent_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Edits a quoteSetting
     */
    public function editAction ()
    {
        // Find client and pass form object
        $form = new Quotegen_Form_QuoteSetting(true);
        
        $quoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        
        $form->populate($quoteSetting->toArray());
        
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        foreach ( $values as &$value )
                        {
                            if (strlen($value) === 0)
                            {
                                $value = new Zend_Db_Expr('NULL');
                            }
                        }
                        $quoteSetting->populate($values);
                        Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($quoteSetting);
                        
                        // Rediret user with message
                        $this->_helper->flashMessenger(array (
                                'success' => "Quote setting was updated sucessfully." 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below.' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Error editing quote setting.  Please try again.' 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edits a quoteSetting
     */
    public function editdefaultAction ()
    {
        $quoteSettingId = 1;
        
        // Find client and pass form object
        $form = new Quotegen_Form_QuoteSetting();
        $quoteSettingMapper = Quotegen_Model_Mapper_QuoteSetting::getInstance();
        $quoteSetting = $quoteSettingMapper->find($quoteSettingId);
        
        $form->populate($quoteSetting->toArray());
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Set nulls where needed.
                        foreach ( $values as $key => &$value )
                        {
                            if (empty($value))
                            {
                                // Only admin and service cost per page should be allowed to be set to 0?
                                if (! ((float)$value === 0.0 && ($key === "adminCostPerPage" || $key === "laborCostPerPage" || $key === "partsCostPerPage")))
                                {
                                    $value = new Zend_Db_Expr('NULL');
                                }
                            }
                        }
                        // Update quoteSetting and message to comfirm
                        $quoteSetting->populate($values);
                        $quoteSetting->id = $quoteSettingId;
                        
                        $quoteSettingMapper->save($quoteSetting, $quoteSettingId);
                        $this->_helper->flashMessenger(array (
                                'success' => "Default quote setting was updated sucessfully." 
                        ));
                        
                        $this->redirector('editdefault');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    My_Log::logException($e);
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Error saving configuration.  Please try again.' 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }
}

