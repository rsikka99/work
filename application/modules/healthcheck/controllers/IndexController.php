<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\SelectRmsUploadService;

/**
 * Class Healthcheck_IndexController
 */
class Healthcheck_IndexController extends Healthcheck_Library_Controller_Action
{
    /**
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getHealthcheck()->stepName);
    }

    /**
     * Selects a upload to use for the Healthcheck
     * @deprecated
     */
    public function selectUploadAction ()
    {
        throw new Exception('Deprecated');
    }

    /**
     * Users can upload/see uploaded data on this step
     */
    public function settingsAction ()
    {
        $this->_pageTitle = ['Healthcheck', 'Settings'];
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_SETTINGS);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData['goBack']))
            {
                $this->saveClientSettingsForm($postData);
                $this->saveHealthcheck();

                if (isset($postData['saveAndContinue']))
                {
                    $this->updateHealthcheckStepName();
                    $this->saveHealthcheck();
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
            else
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }
        else
        {
            $this->showClientSettingsForm();
        }
    }
}