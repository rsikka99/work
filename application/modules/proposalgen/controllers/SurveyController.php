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
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Company();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function generalAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_GENERAL);
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_General();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function financeAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_FINANCE);
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Finance();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function purchasingAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_PURCHASING);
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Purchasing();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function itAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_IT);
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_It();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function usersAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_USERS);
        
        $request = $this->getRequest();
        $form = new Proposalgen_Form_Survey_Users();
        
        if ($request->isPost())
        {
            try
            {
                $values = $request->getPost();
                if ($form->isValid($values))
                {
                }
                else
                {
                    throw new Zend_Validate_Exception("Form Validation Failed");
                }
            }
            catch ( Zend_Validate_Exception $e )
            {
                $form->buildBootstrapErrorDecorators();
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * This is one of the pages in the survey
     */
    public function verifyAction ()
    {
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_SURVEY_VERIFY);
    }
}
