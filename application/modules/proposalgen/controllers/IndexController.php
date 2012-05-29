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
            $this->user_id = $auth->getIdentity()->id;
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
            
            try
            {
                $reports = Proposalgen_Model_Mapper_Report::getInstance()->fetchAll(array (
                        "(user_id = ? AND questionset_id = 1" => Proposalgen_Model_User::getCurrentUserId(), 
                        "report_stage != ?) OR report_stage IS NULL" => "finished" 
                ), array (
                        "date_created DESC" 
                ));
            }
            catch ( Exception $e )
            {
                $reports = array ();
            }
            // Tack on a proposal that handles creating a new one
            $newReport = new Proposalgen_Model_Report();
            $newReport->setReportId(0);
            $newReport->setCustomerCompanyName("Start New Proposal");
            array_unshift($reports, $newReport);
        }
        else
        {
            // Get the list of user reports
            try
            {
                $reports = Proposalgen_Model_Mapper_Report::getInstance()->fetchAll(array (
                        "user_id = ? AND questionset_id = 1" => Proposalgen_Model_User::getCurrentUserId(), 
                        "report_stage = ?" => "finished" 
                ), array (
                        "date_created DESC" 
                ));
            }
            catch ( Exception $e )
            {
                $reports = array ();
            }
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
} // end index controller

