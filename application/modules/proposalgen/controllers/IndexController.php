<?php

/**
 * IndexController - The default error controller class
 *
 * @author Chris Garrah
 */
class Proposalgen_IndexController extends Zend_Controller_Action
{

    function init ()
    {
        $config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $config->app;
        $auth = Zend_Auth::getInstance();
        
        if ($auth->hasIdentity())
        {
            $this->view->user = $auth->getIdentity();
            $this->user_id = $auth->getIdentity()->user_id;
            $this->dealer_company_id = $auth->getIdentity()->dealer_company_id;
            $this->view->privilege = $auth->getIdentity()->privileges;
            $this->MPSProgramName = $config->app->MPSProgramName;
        }
    } // end init

    
    /**
     * This function handles redirecting the user to starting a new proposal
     */
    public function startNewProposal ($session)
    {
        $dealerTable = new Proposalgen_Model_DbTable_DealerCompany();
        $userTable = new Proposalgen_Model_DbTable_Users();
        // getting the pricing config from the user first, then the
        // dealer, then the master
        if ($userTable->fetchRow('user_id =' . $this->user_id)->pricing_config_id)
        {
            $pricingConfig = $userTable->fetchRow('user_id =' . $this->user_id)->pricing_config_id;
        }
        else if ($dealerTable->fetchRow('dealer_company_id =' . $this->dealer_company_id)->pricing_config_id)
        {
            $pricingConfig = $dealerTable->fetchRow('dealer_company_id =' . $this->dealer_company_id)->pricing_config_id;
        }
        else
        {
            $pricingConfig = $dealerTable->fetchRow('company_name = "MASTER"')->pricing_config_id;
        }
        
        // get form values from post
        $formData = $this->_request->getPost();
        
        $reportData = array (
                'user_id' => $this->user_id, 
                'questionset_id' => 1, 
                'report_pricing_config_id' => $pricingConfig, 
                'report_gross_margin_pricing_config_id' => Proposalgen_Model_PricingConfig::COMP, 
                'date_created' => date('Y-m-d H:i:s'), 
                'last_modified' => date('Y-m-d H:i:s'), 
                'report_stage' => 'company' 
        );
        $session->reportData = $reportData;
        $session->report_id = null;
        
        // TODO: Where does this get set?
        header('Location: ' . $this->view->baseUrl('/survey'));
    }

    /**
     * * The default action - This is the main entry point of the applicaiton
     * Show the user what their options are, and allow them to start a new
     * proposal.
     */
    public function indexAction ()
    {
        $reports = array ();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            // If we posted our form, figure out if its to change the dropdown or to view/create a report
            if ($this->_getParam('start_survey', false))
            {
                // We start the survey and redirect
                $reportId = (int)$this->_getParam('select_proposal', 0);
                $session = new Zend_Session_Namespace('report');
                if ($reportId === 0)
                {
                    $this->startNewProposal($session);
                    
                }
                else
                {
                    
                    $session->userid = Proposalgen_Model_User::getCurrentUserId();
                    $session->dealerid = Proposalgen_Model_User::getCurrentUser()->getDealerCompanyId();
                    $session->report_id = $reportId;
                    header('Location: ' . $session->url . "/report");
                }
            }
        }
        
        // If we haven't redirected yet, we get our form ready to be displayed
        $proposalOptions = $this->_getParam('proposalOptions', 'ViewUnfinishedStartNewProposal');
        
        $selectForm = new Proposalgen_Form_SelectReportType();
        $selectForm->populate(array (
                'proposalOptions' => $proposalOptions 
        ));
        $this->view->selectForm = $selectForm;
        
        // Get the list of reports
        if (strcmp($proposalOptions, 'ViewUnfinishedStartNewProposal') === 0)
        {
            $reports = Proposalgen_Model_Mapper_Report::getInstance()->fetchAll(array (
                    "(user_id = ? AND questionset_id = 1" => Proposalgen_Model_User::getCurrentUserId(), 
                    "report_stage != ?) OR report_stage IS NULL" => "finished" 
            ), array (
                    "date_created DESC" 
            ));
            // Tack on a proposal that handles creating a new one
            $newReport = new Proposalgen_Model_Report();
            $newReport->setReportId(0);
            $newReport->setCustomerCompanyName("Start New Proposal");
            array_unshift($reports, $newReport);
        }
        else
        {
            $reports = Proposalgen_Model_Mapper_Report::getInstance()->fetchAll(array (
                    "user_id = ? AND questionset_id = 1" => Proposalgen_Model_User::getCurrentUserId(), 
                    "report_stage = ?" => "finished" 
            ), array (
                    "date_created DESC" 
            ));
        
        }
        
        $this->view->selectReportForm = new Proposalgen_Form_SelectReport($reports);
    
    } // end indexAction


    /**
     * This action deals with setting/removing a session variable called emulateUserId
     *
     * This allows us to see other users reports when we are the root user of the system
     */
    public function emulateAction ()
    {
        if ($this->view->User()->user_id === 1)
        {
            $pgenNamespace = new Zend_Session_Namespace('pgen');
            if ($this->_hasParam("stopEmulation"))
            {
                if (isset($pgenNamespace->emulateUserId))
                {
                    unset($pgenNamespace->emulateUserId);
                }
            }
            else if ($this->_hasParam("emulateUserId"))
            {
                $emulatedUser = Proposalgen_Model_Mapper_User::getInstance()->find($this->_getParam("emulateUserId"));
                if ($emulatedUser)
                {
                    if ($emulatedUser->getUserId() !== 1)
                    {
                        $pgenNamespace->emulateUserId = $emulatedUser->getUserId();
                    }
                }
            }
        }
        
        // Go back home
        $this->_redirect("/");
    }

    public function oldindexAction ()
    {
        
        $this->user_id = Proposalgen_Model_User::getCurrentUserId();
        $this->dealer_company_id = Proposalgen_Model_User::getCurrentUser()->getDealerCompanyId();
        // Tangent_Log::message("emerg", "emerg");
        // Tangent_Log::message("info", "info");
        $questionSetID = 1;
        
        $elementCounter = 0;
        $elements = array ();
        $dealerTable = new Proposalgen_Model_DbTable_DealerCompany();
        $userTable = new Proposalgen_Model_DbTable_Users();
        $session = new Zend_Session_Namespace('report');
        $session->userid = $this->user_id;
        $session->dealerid = $this->dealer_company_id;
        $this->view->title = "Home";
        $form = new Zend_Form();
        $finishedForm = new Zend_Form();
        $proposalForm = new Zend_Form();
        $db = Zend_Db_Table::getDefaultAdapter();
        $report_array = array ();
        $existingProposals = array ();
        $reportsTable = new Proposalgen_Model_DbTable_Reports();
        $order = array (
                'customer_company_name', 
                'last_modified' 
        );
        $where = $reportsTable->getAdapter()->quoteInto('user_id = ? AND (report_stage != "finished" OR report_stage IS NULL) AND questionset_id =' . $questionSetID, $this->user_id);
        $result = $reportsTable->fetchAll($where, $order);
        
        // empty out any arrays
        $mapping_array = new Zend_Session_Namespace('mapping_array');
        unset($mapping_array->array);
        
        if (count($result) > 0)
        {
            $report_array [0] = "Start New Proposal";
            foreach ( $result as $row )
            {
                $report_array [$row ['report_id']] = $row ['customer_company_name'] . ' (' . strftime("%x", strtotime($row ['last_modified'])) . ')';
            }
        }
        else
        {
            $report_array [0] = "Start New Proposal";
        }
        $existingProposals = $report_array;
        $report_array = null;
        $where = $reportsTable->getAdapter()->quoteInto('user_id = ? AND report_stage="finished" AND questionset_id =' . $questionSetID, $this->user_id);
        $order = 'customer_company_name';
        $result = $reportsTable->fetchAll($where, $order);
        
        if (count($result) > 0)
        {
            foreach ( $result as $row )
            {
                $report_array [$row ['report_id']] = $row ['customer_company_name'] . ' (' . strftime("%x", strtotime($row ['last_modified'])) . ')';
            }
        }
        else
        {
            $report_array [0] = "No Completed Reports";
        }
        $finishedProposals = $report_array;
        
        // FIXME The form should be in a file, not in a controller!
        $proposalQst = "Select:";
        
        $reportType = "Proposal";
        
        $proposalOptions = array (
                'View Unfinished/Start New ' . $reportType => 'View Unfinished/Start New ' . $reportType, 
                'View Finished ' . $reportType => 'View Finished ' . $reportType 
        );
        $proposalForm->addElements(array (
                new Zend_Form_Element_Radio('proposalOptions', array (
                        'multiOptions' => $proposalOptions, 
                        'required' => true, 
                        'filters' => array (
                                'StringTrim' 
                        ), 
                        'validators' => array (
                                array (
                                        'InArray', 
                                        false, 
                                        array (
                                                array_keys($proposalOptions) 
                                        ) 
                                ) 
                        ) 
                )) 
        ));
        $proposalForm->getElement('proposalOptions')
            ->setAttrib('onClick', 'this.form.submit()')
            ->setAttrib('class', 'radiobuttons')
            ->setValue('View Unfinished/Start New ' . $reportType)
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
                        array (
                                'dt' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dt', 
                                'class' => 'radiobuttons' 
                        ) 
                ), 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'select_proposal' 
                        ) 
                ) 
        ));
        $proposalForm->getElement('proposalOptions')->class = "radiobuttons";
        
        $proposalSelector = new Zend_Form_Element_Select('select_proposal');
        $proposalSelector->setAttrib('class', 'select_proposal')
            ->setMultiOptions($existingProposals)
            ->setAttrib('style', 'width: 300px')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dt' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dt', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $proposalSelector);
        $elementCounter ++;
        
        $viewReport = new Zend_Form_Element_Submit('start_survey');
        $viewReport->setLabel('Next')->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dt' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dt', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $viewReport);
        $elementCounter ++;
        $form->addElements($elements);
        
        $elementCounter = 0;
        $elements = array ();
        
        $viewProposalSelector = new Zend_Form_Element_Select('select_complete_proposal');
        $viewProposalSelector->setAttrib('class', 'select_complete_proposal')
            ->setMultiOptions($finishedProposals)
            ->setAttrib('style', 'width: 300px')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dt' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dt', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $viewProposalSelector);
        $elementCounter ++;
        
        $viewFinishedReport = new Zend_Form_Element_Submit('view_proposal');
        $viewFinishedReport->setLabel('Next')->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dt' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dt', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $viewFinishedReport);
        $elementCounter ++;
        $finishedForm->addElements($elements);
        
        // echo $finishedProposals[1];exit;
        if (count($result) == 0)
            $finishedForm->view_proposal->setAttrib('disabled', 'disabled');
        
        $this->view->proposalForm = $proposalForm;
        $this->view->form = $form;
        
        if ($this->_request->isPost())
        {
            if (isset($_POST ['proposalOptions']))
            {
                $formData = $this->_request->getPost();
                $proposalForm->getElement('proposalOptions')->setValue($formData ['proposalOptions']);
                if ($formData ['proposalOptions'] == 'View Unfinished/Start New ' . $reportType)
                {
                    $this->view->form = $form;
                    $this->view->proposalForm = $proposalForm;
                }
                else
                {
                    $this->view->form = $finishedForm;
                    $this->view->proposalForm = $proposalForm;
                }
            }
            else
            {
                if (isset($_POST ['view_proposal']))
                {
                    $formData = $this->_request->getPost();
                    $session->report_id = $formData ["select_complete_proposal"];
                    header('Location: ' . $session->url . "/report");
                
                }
                else
                {
                    // getting the pricing config from the user first, then the
                    // dealer, then the master
                    if ($userTable->fetchRow('user_id =' . $this->user_id)->pricing_config_id)
                    {
                        $pricingConfig = $userTable->fetchRow('user_id =' . $this->user_id)->pricing_config_id;
                    }
                    else if ($dealerTable->fetchRow('dealer_company_id =' . $this->dealer_company_id)->pricing_config_id)
                    {
                        $pricingConfig = $dealerTable->fetchRow('dealer_company_id =' . $this->dealer_company_id)->pricing_config_id;
                    }
                    else
                    {
                        $pricingConfig = $dealerTable->fetchRow('company_name = "MASTER"')->pricing_config_id;
                    }
                    
                    // get form values from post
                    $formData = $this->_request->getPost();
                    $db->beginTransaction();
                    try
                    {
                        // creating a new report if the user does not select
                        // one.
                        if (! $formData ["select_proposal"])
                        {
                            $reportData = array (
                                    'user_id' => $this->user_id, 
                                    'questionset_id' => $questionSetID, 
                                    'report_pricing_config_id' => $pricingConfig, 
                                    'report_gross_margin_pricing_config_id' => Proposalgen_Model_PricingConfig::COMP,  // Set
                                    // default
                                    // to
                                    // COMP
                                    'date_created' => date('Y-m-d H:i:s'), 
                                    'last_modified' => date('Y-m-d H:i:s'), 
                                    'report_stage' => 'company' 
                            );
                            $session->reportData = $reportData;
                            $session->report_id = null;
                        }
                        else
                        {
                            // if the user selects an existing report, the 'last
                            // modified' field in the database
                            // is updated.
                            $session->report_id = $formData ["select_proposal"];
                            $reportData = array (
                                    'last_modified' => date('Y-m-d H:i:s') 
                            );
                            $where = ('report_id=' . $session->report_id);
                            $reportsTable->update($reportData, $where);
                        }
                        $db->commit();
                        
                        header('Location: ' . $session->surveyurl);
                    
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                        throw new Exception("Error saving new report", 0, $e);
                    
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        throw new Exception("Error saving new report", 0, $e);
                    
                    }
                }
            }
        }
    
    } // end oldindexAction


} // end index controller

