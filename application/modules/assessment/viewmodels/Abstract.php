<?php
use MPSToolbox\Legacy\Modules\Assessment\Mappers\AssessmentMapper;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;

/**
 * Class Assessment_ViewModel_Abstract
 */
class Assessment_ViewModel_Abstract
{
    /**
     * @var Assessment_ViewModel_Devices
     */
    protected $_devices;

    /**
     * @var AssessmentModel
     */
    public $assessment;

    /**
     * The cost page setting when displaying numbers to a customer
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForCustomer;
    /**
     * The cost page setting when displaying numbers to a dealer
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForDealer;
    /**
     * The cost page setting when selecting replacement devices
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForReplacements;

    protected $PageCoverageBlackAndWhite;
    protected $PageCoverageColor;
    protected $ReportQuestions;


    /**
     * Constructor
     *
     * @param int|AssessmentModel $report The report model, or the id of our report
     */
    public function __construct ($report)
    {
        if ($report instanceof AssessmentModel)
        {
            $this->assessment = $report;
        }
        else
        {
            $this->assessment = AssessmentMapper::getInstance()->find($report);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Assessment_ViewModel_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $clientSettings = $this->assessment->getClient()->getClientSettings();
            $this->_devices = new Assessment_ViewModel_Devices(
                $this->assessment->rmsUploadId,
                $clientSettings->currentFleetSettings->defaultLaborCostPerPage,
                $clientSettings->currentFleetSettings->defaultPartsCostPerPage,
                $clientSettings->currentFleetSettings->adminCostPerPage
            );
        }

        return $this->_devices;
    }

    /**
     * Gets the cost per page settings for the customers point of view
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSettingForCustomer ()
    {
        if (!isset($this->_costPerPageSettingForCustomer))
        {
            $this->_costPerPageSettingForCustomer = new CostPerPageSettingModel();

            $clientSettings = $this->assessment->getClient()->getClientSettings();

            $this->_costPerPageSettingForCustomer->adminCostPerPage       = $clientSettings->currentFleetSettings->adminCostPerPage;
            $this->_costPerPageSettingForCustomer->pageCoverageMonochrome = $clientSettings->currentFleetSettings->defaultMonochromeCoverage;
            $this->_costPerPageSettingForCustomer->pageCoverageColor      = $clientSettings->currentFleetSettings->defaultColorCoverage;

            $this->_costPerPageSettingForCustomer->monochromeTonerRankSet = $clientSettings->currentFleetSettings->getMonochromeRankSet();
            $this->_costPerPageSettingForCustomer->colorTonerRankSet      = $clientSettings->currentFleetSettings->getColorRankSet();
            
            $this->_costPerPageSettingForCustomer->useCustomerCostPerPageForManagedDevices = true;
            
            $this->_costPerPageSettingForCustomer->useDevicePageCoverages = $clientSettings->currentFleetSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForCustomer->pricingMargin          = $clientSettings->genericSettings->tonerPricingMargin;
        }

        return $this->_costPerPageSettingForCustomer;
    }

    /**
     * Gets the cost per page settings for the dealers point of view
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSettingForDealer ()
    {
        if (!isset($this->_costPerPageSettingForDealer))
        {
            $this->_costPerPageSettingForDealer = new CostPerPageSettingModel();

            $clientSettings                                             = $this->assessment->getClient()->getClientSettings();
            $this->_costPerPageSettingForDealer->adminCostPerPage       = $clientSettings->proposedFleetSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome = $clientSettings->proposedFleetSettings->defaultMonochromeCoverage;
            $this->_costPerPageSettingForDealer->pageCoverageColor      = $clientSettings->proposedFleetSettings->defaultColorCoverage;
            $this->_costPerPageSettingForDealer->partsCostPerPage       = $clientSettings->proposedFleetSettings->defaultPartsCostPerPage;
            $this->_costPerPageSettingForDealer->laborCostPerPage       = $clientSettings->proposedFleetSettings->defaultLaborCostPerPage;

            $this->_costPerPageSettingForDealer->monochromeTonerRankSet = $clientSettings->proposedFleetSettings->getMonochromeRankSet();
            $this->_costPerPageSettingForDealer->colorTonerRankSet      = $clientSettings->proposedFleetSettings->getColorRankSet();
            $this->_costPerPageSettingForDealer->useDevicePageCoverages = $clientSettings->proposedFleetSettings->useDevicePageCoverages;
        }

        return $this->_costPerPageSettingForDealer;
    }

    /**
     * @return float
     */
    public function getPageCoverageBlackAndWhite ()
    {
        if (!isset($this->PageCoverageBlackAndWhite))
        {
            $this->PageCoverageBlackAndWhite = $this->assessment->getClient()->getSurvey()->pageCoverageMonochrome;
        }

        return $this->PageCoverageBlackAndWhite;
    }

    /**
     * @return float
     */
    public function getPageCoverageColor ()
    {
        if (!isset($this->PageCoverageColor))
        {
            $this->PageCoverageColor = $this->assessment->getClient()->getSurvey()->pageCoverageColor;
        }

        return $this->PageCoverageColor;
    }
}