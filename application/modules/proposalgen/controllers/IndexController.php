<?php

/**
 * IndexController - The default error controller class
 *
 * @author Chris Garrah
 */
class Proposalgen_IndexController extends Tangent_Controller_Action
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
            $this->userId = $auth->getIdentity()->id;
            $this->MPSProgramName = $config->app->MPSProgramName;
        }
    } // end init

    
    /**
     * This function handles redirecting the user to starting a new proposal
     */
    public function startProposal ($session, $reportId = null)
    {
        $session->reportId = null;
        
        // Redirect to the survey controller of our current module
        $this->_helper->redirector('index', 'survey');
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
                $session = new Zend_Session_Namespace('proposalgenerator_report');
                
                // Set the session report id and send to the survey index page.
                $session->reportId = $reportId;
                $this->_helper->redirector('index', 'survey');
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
                    "(userId = ? AND questionSetId = 1" => $this->userId, 
                    "reportStage != ?) OR reportStage IS NULL" => "finished" 
            ), array (
                    "dateCreated DESC" 
            ));
            // Tack on a proposal that handles creating a new one
            $newReport = new Proposalgen_Model_Report();
            $newReport->id = 0;
            $newReport->getClient()->companyName = "Start New Proposal";
            array_unshift($reports, $newReport);
        }
        else
        {
            $reports = Proposalgen_Model_Mapper_Report::getInstance()->fetchAll(array (
                    "userId = ? AND questionSetId = 1" => $this->userId, 
                    "reportStage = ?" => "finished" 
            ), array (
                    "dateCreated DESC" 
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
} // end index controller

