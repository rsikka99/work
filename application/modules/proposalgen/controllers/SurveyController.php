<?php

/**
 * Description of SurveyController:
 * This controller handles the survey/questionaire.
 * User should
 * be quided through a series of forms where they are asked to answer
 * questions about their existing fleet of printers.
 *
 * @author Chris Garrah
 */
class Proposalgen_SurveyController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The index action.
     * Not used for anything yet.
     */
    public function indexAction ()
    {
    }

    /**
     * This is one of the pages in the survey
     */
    public function companyAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY);
        
        $form = new Proposalgen_Form_Survey_Company();
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function generalAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_GENERAL);
        
        $form = new Proposalgen_Form_Survey_General();
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function financeAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_FINANCE);
    }

    /**
     * This is one of the pages in the survey
     */
    public function purchasingAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_PURCHASING);
    }

    /**
     * This is one of the pages in the survey
     */
    public function itAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_IT);
    }

    /**
     * This is one of the pages in the survey
     */
    public function usersAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_USERS);
    }

    /**
     * This is one of the pages in the survey
     */
    public function verifyAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_VERIFY);
    }
}
