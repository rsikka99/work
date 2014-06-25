<?php

/**
 * Class Application_Model_Dealer_Branding
 */
class Application_Model_Dealer_Branding extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var string
     */
    public $dealerName;

    /**
     * @var string
     */
    public $shortDealerName;

    /**
     * @var string
     */
    public $mpsProgramName = 'Toolbox MPS';

    /**
     * @var string
     */
    public $shortMpsProgramName = 'TMPS';

    /**
     * @var string
     */
    public $jitProgramName = 'Toolbox JIT';

    /**
     * @var string
     */
    public $shortJitProgramName = 'TJIT';

    /**
     * @var string
     */
    public $titlePageTitleFontColor = '#FFFFFF';

    /**
     * @var string
     */
    public $titlePageTitleBackgroundColor = '#4C4C4C';

    /**
     * @var string
     */
    public $titlePageInformationFontColor = '#FFFFFF';

    /**
     * @var string
     */
    public $titlePageInformationBackgroundColor = '#7F7F7F';

    /**
     * @var string
     */
    public $h1FontColor = '#0194D2';

    /**
     * @var string
     */
    public $h1BackgroundColor = "#FFFFFF";

    /**
     * @var string
     */
    public $h2FontColor = '#FFFFFF';

    /**
     * @var string
     */
    public $h2BackgroundColor = '#0194D2';

    /**
     * @var string
     */
    public $graphCustomerColor = '#225ea8';

    /**
     * @var string
     */
    public $graphDealerColor = '#fe9929';

    /**
     * @var string
     */
    public $graphPositiveColor = '#0194D2';

    /**
     * @var string
     */
    public $graphNegativeColor = '#d7301f';

    /**
     * @var string
     */
    public $graphPurchasedDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphLeasedDeviceColor = '#d7301f';

    /**
     * @var string
     */
    public $graphExcludedDeviceColor = '#666666';

    /**
     * @var string
     */
    public $graphIndustryAverageColor = '#94d201';

    /**
     * @var string
     */
    public $graphKeepDeviceColor = '#94d201';

    /**
     * @var string
     */
    public $graphReplacedDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphDoNotRepairDeviceColor = '#FFD000';

    /**
     * @var string
     */
    public $graphRetireDeviceColor = '#d7301f';

    /**
     * @var string
     */
    public $graphManagedDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphManageableDeviceColor = '#E21736';

    /**
     * @var string
     */
    public $graphFutureReviewDeviceColor = '#ABABAB';

    /**
     * @var string
     */
    public $graphJitCompatibleDeviceColor = '#FFD000';

    /**
     * @var string
     */
    public $graphCompatibleDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphNotCompatibleDeviceColor = '#d7301f';

    /**
     * @var string
     */
    public $graphCurrentSituationColor = '#EF6B18';

    /**
     * @var string
     */
    public $graphNewSituationColor = '#0194D2';

    /**
     * @var string
     */
    public $graphOptimalSituationColor = '#94d201';

    /**
     * @var string
     */
    public $graphAgeOfDevices1 = '#0570b0';

    /**
     * @var string
     */
    public $graphAgeOfDevices2 = '#74a9cf';

    /**
     * @var string
     */
    public $graphAgeOfDevices3 = '#bdc9e1';

    /**
     * @var string
     */
    public $graphAgeOfDevices4 = '#f1eef6';

    /**
     * @var string
     */
    public $graphMonoDeviceColor = '#d7301f';

    /**
     * @var string
     */
    public $graphColorDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphCopyCapableDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphDuplexCapableDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $graphFaxCapableDeviceColor = '#0194D2';

    /**
     * @var string
     */
    public $assessmentTitle = "Assessment";

    /**
     * @var string
     */
    public $customerCostAnalysisTitle = "Cost Analysis";

    /**
     * @var string
     */
    public $customerOptimizationTitle = "Hardware Optimization";

    /**
     * @var string
     */
    public $healthCheckTitle = "Business Review";

    /**
     * @var string
     */
    public $leaseQuoteTitle = "Hardware Quote";

    /**
     * @var string
     */
    public $purchaseQuoteTitle = "Hardware Quote";

    /**
     * @var string
     */
    public $solutionTitle = "Solution";

    /**
     * @var Application_Model_Dealer
     */
    protected $_dealer;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->dealerName) && !is_null($params->dealerName))
        {
            $this->dealerName = $params->dealerName;
        }

        if (isset($params->shortDealerName) && !is_null($params->shortDealerName))
        {
            $this->shortDealerName = $params->shortDealerName;
        }

        if (isset($params->mpsProgramName) && !is_null($params->mpsProgramName))
        {
            $this->mpsProgramName = $params->mpsProgramName;
        }

        if (isset($params->shortMpsProgramName) && !is_null($params->shortMpsProgramName))
        {
            $this->shortMpsProgramName = $params->shortMpsProgramName;
        }

        if (isset($params->jitProgramName) && !is_null($params->jitProgramName))
        {
            $this->jitProgramName = $params->jitProgramName;
        }

        if (isset($params->shortJitProgramName) && !is_null($params->shortJitProgramName))
        {
            $this->shortJitProgramName = $params->shortJitProgramName;
        }

        if (isset($params->titlePageTitleFontColor) && !is_null($params->titlePageTitleFontColor))
        {
            $this->titlePageTitleFontColor = $params->titlePageTitleFontColor;
        }

        if (isset($params->titlePageTitleBackgroundColor) && !is_null($params->titlePageTitleBackgroundColor))
        {
            $this->titlePageTitleBackgroundColor = $params->titlePageTitleBackgroundColor;
        }

        if (isset($params->titlePageInformationFontColor) && !is_null($params->titlePageInformationFontColor))
        {
            $this->titlePageInformationFontColor = $params->titlePageInformationFontColor;
        }

        if (isset($params->titlePageInformationBackgroundColor) && !is_null($params->titlePageInformationBackgroundColor))
        {
            $this->titlePageInformationBackgroundColor = $params->titlePageInformationBackgroundColor;
        }

        if (isset($params->h1FontColor) && !is_null($params->h1FontColor))
        {
            $this->h1FontColor = $params->h1FontColor;
        }

        if (isset($params->h1BackgroundColor) && !is_null($params->h1BackgroundColor))
        {
            $this->h1BackgroundColor = $params->h1BackgroundColor;
        }

        if (isset($params->h2FontColor) && !is_null($params->h2FontColor))
        {
            $this->h2FontColor = $params->h2FontColor;
        }

        if (isset($params->h2BackgroundColor) && !is_null($params->h2BackgroundColor))
        {
            $this->h2BackgroundColor = $params->h2BackgroundColor;
        }

        if (isset($params->graphCustomerColor) && !is_null($params->graphCustomerColor))
        {
            $this->graphCustomerColor = $params->graphCustomerColor;
        }

        if (isset($params->graphDealerColor) && !is_null($params->graphDealerColor))
        {
            $this->graphDealerColor = $params->graphDealerColor;
        }

        if (isset($params->graphPositiveColor) && !is_null($params->graphPositiveColor))
        {
            $this->graphPositiveColor = $params->graphPositiveColor;
        }

        if (isset($params->graphNegativeColor) && !is_null($params->graphNegativeColor))
        {
            $this->graphNegativeColor = $params->graphNegativeColor;
        }

        if (isset($params->graphPurchasedDeviceColor) && !is_null($params->graphPurchasedDeviceColor))
        {
            $this->graphPurchasedDeviceColor = $params->graphPurchasedDeviceColor;
        }

        if (isset($params->graphLeasedDeviceColor) && !is_null($params->graphLeasedDeviceColor))
        {
            $this->graphLeasedDeviceColor = $params->graphLeasedDeviceColor;
        }

        if (isset($params->graphExcludedDeviceColor) && !is_null($params->graphExcludedDeviceColor))
        {
            $this->graphExcludedDeviceColor = $params->graphExcludedDeviceColor;
        }

        if (isset($params->graphIndustryAverageColor) && !is_null($params->graphIndustryAverageColor))
        {
            $this->graphIndustryAverageColor = $params->graphIndustryAverageColor;
        }

        if (isset($params->graphKeepDeviceColor) && !is_null($params->graphKeepDeviceColor))
        {
            $this->graphKeepDeviceColor = $params->graphKeepDeviceColor;
        }

        if (isset($params->graphReplacedDeviceColor) && !is_null($params->graphReplacedDeviceColor))
        {
            $this->graphReplacedDeviceColor = $params->graphReplacedDeviceColor;
        }

        if (isset($params->graphDoNotRepairDeviceColor) && !is_null($params->graphDoNotRepairDeviceColor))
        {
            $this->graphDoNotRepairDeviceColor = $params->graphDoNotRepairDeviceColor;
        }

        if (isset($params->graphRetireDeviceColor) && !is_null($params->graphRetireDeviceColor))
        {
            $this->graphRetireDeviceColor = $params->graphRetireDeviceColor;
        }

        if (isset($params->graphManagedDeviceColor) && !is_null($params->graphManagedDeviceColor))
        {
            $this->graphManagedDeviceColor = $params->graphManagedDeviceColor;
        }

        if (isset($params->graphManageableDeviceColor) && !is_null($params->graphManageableDeviceColor))
        {
            $this->graphManageableDeviceColor = $params->graphManageableDeviceColor;
        }

        if (isset($params->graphFutureReviewDeviceColor) && !is_null($params->graphFutureReviewDeviceColor))
        {
            $this->graphFutureReviewDeviceColor = $params->graphFutureReviewDeviceColor;
        }

        if (isset($params->graphJitCompatibleDeviceColor) && !is_null($params->graphJitCompatibleDeviceColor))
        {
            $this->graphJitCompatibleDeviceColor = $params->graphJitCompatibleDeviceColor;
        }

        if (isset($params->graphCompatibleDeviceColor) && !is_null($params->graphCompatibleDeviceColor))
        {
            $this->graphCompatibleDeviceColor = $params->graphCompatibleDeviceColor;
        }

        if (isset($params->graphNotCompatibleDeviceColor) && !is_null($params->graphNotCompatibleDeviceColor))
        {
            $this->graphNotCompatibleDeviceColor = $params->graphNotCompatibleDeviceColor;
        }

        if (isset($params->graphCurrentSituationColor) && !is_null($params->graphCurrentSituationColor))
        {
            $this->graphCurrentSituationColor = $params->graphCurrentSituationColor;
        }

        if (isset($params->graphNewSituationColor) && !is_null($params->graphNewSituationColor))
        {
            $this->graphNewSituationColor = $params->graphNewSituationColor;
        }

        if (isset($params->graphAgeOfDevices1) && !is_null($params->graphAgeOfDevices1))
        {
            $this->graphAgeOfDevices1 = $params->graphAgeOfDevices1;
        }

        if (isset($params->graphAgeOfDevices2) && !is_null($params->graphAgeOfDevices2))
        {
            $this->graphAgeOfDevices2 = $params->graphAgeOfDevices2;
        }

        if (isset($params->graphAgeOfDevices3) && !is_null($params->graphAgeOfDevices3))
        {
            $this->graphAgeOfDevices3 = $params->graphAgeOfDevices3;
        }

        if (isset($params->graphAgeOfDevices4) && !is_null($params->graphAgeOfDevices4))
        {
            $this->graphAgeOfDevices4 = $params->graphAgeOfDevices4;
        }

        if (isset($params->graphMonoDeviceColor) && !is_null($params->graphMonoDeviceColor))
        {
            $this->graphMonoDeviceColor = $params->graphMonoDeviceColor;
        }

        if (isset($params->graphColorDeviceColor) && !is_null($params->graphColorDeviceColor))
        {
            $this->graphColorDeviceColor = $params->graphColorDeviceColor;
        }

        if (isset($params->graphCopyCapableDeviceColor) && !is_null($params->graphCopyCapableDeviceColor))
        {
            $this->graphCopyCapableDeviceColor = $params->graphCopyCapableDeviceColor;
        }

        if (isset($params->graphDuplexCapableDeviceColor) && !is_null($params->graphDuplexCapableDeviceColor))
        {
            $this->graphDuplexCapableDeviceColor = $params->graphDuplexCapableDeviceColor;
        }

        if (isset($params->graphFaxCapableDeviceColor) && !is_null($params->graphFaxCapableDeviceColor))
        {
            $this->graphFaxCapableDeviceColor = $params->graphFaxCapableDeviceColor;
        }

        if (isset($params->assessmentTitle) && !is_null($params->assessmentTitle))
        {
            $this->assessmentTitle = $params->assessmentTitle;
        }

        if (isset($params->customerCostAnalysisTitle) && !is_null($params->customerCostAnalysisTitle))
        {
            $this->customerCostAnalysisTitle = $params->customerCostAnalysisTitle;
        }

        if (isset($params->customerOptimizationTitle) && !is_null($params->customerOptimizationTitle))
        {
            $this->customerOptimizationTitle = $params->customerOptimizationTitle;
        }

        if (isset($params->healthCheckTitle) && !is_null($params->healthCheckTitle))
        {
            $this->healthCheckTitle = $params->healthCheckTitle;
        }

        if (isset($params->leaseQuoteTitle) && !is_null($params->leaseQuoteTitle))
        {
            $this->leaseQuoteTitle = $params->leaseQuoteTitle;
        }

        if (isset($params->purchaseQuoteTitle) && !is_null($params->purchaseQuoteTitle))
        {
            $this->purchaseQuoteTitle = $params->purchaseQuoteTitle;
        }

        if (isset($params->solutionTitle) && !is_null($params->solutionTitle))
        {
            $this->solutionTitle = $params->solutionTitle;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId"                            => $this->dealerId,
            "dealerName"                          => $this->dealerName,
            "shortDealerName"                     => $this->shortDealerName,
            "mpsProgramName"                      => $this->mpsProgramName,
            "shortMpsProgramName"                 => $this->shortMpsProgramName,
            "jitProgramName"                      => $this->jitProgramName,
            "shortJitProgramName"                 => $this->shortJitProgramName,
            "titlePageTitleFontColor"             => $this->titlePageTitleFontColor,
            "titlePageTitleBackgroundColor"       => $this->titlePageTitleBackgroundColor,
            "titlePageInformationFontColor"       => $this->titlePageInformationFontColor,
            "titlePageInformationBackgroundColor" => $this->titlePageInformationBackgroundColor,
            "h1FontColor"                         => $this->h1FontColor,
            "h1BackgroundColor"                   => $this->h1BackgroundColor,
            "h2FontColor"                         => $this->h2FontColor,
            "h2BackgroundColor"                   => $this->h2BackgroundColor,
            "graphCustomerColor"                  => $this->graphCustomerColor,
            "graphDealerColor"                    => $this->graphDealerColor,
            "graphPositiveColor"                  => $this->graphPositiveColor,
            "graphNegativeColor"                  => $this->graphNegativeColor,
            "graphPurchasedDeviceColor"           => $this->graphPurchasedDeviceColor,
            "graphLeasedDeviceColor"              => $this->graphLeasedDeviceColor,
            "graphExcludedDeviceColor"            => $this->graphExcludedDeviceColor,
            "graphIndustryAverageColor"           => $this->graphIndustryAverageColor,
            "graphKeepDeviceColor"                => $this->graphKeepDeviceColor,
            "graphReplacedDeviceColor"            => $this->graphReplacedDeviceColor,
            "graphDoNotRepairDeviceColor"         => $this->graphDoNotRepairDeviceColor,
            "graphRetireDeviceColor"              => $this->graphRetireDeviceColor,
            "graphManagedDeviceColor"             => $this->graphManagedDeviceColor,
            "graphManageableDeviceColor"          => $this->graphManageableDeviceColor,
            "graphFutureReviewDeviceColor"        => $this->graphFutureReviewDeviceColor,
            "graphJitCompatibleDeviceColor"       => $this->graphJitCompatibleDeviceColor,
            "graphCompatibleDeviceColor"          => $this->graphCompatibleDeviceColor,
            "graphNotCompatibleDeviceColor"       => $this->graphNotCompatibleDeviceColor,
            "graphCurrentSituationColor"          => $this->graphCurrentSituationColor,
            "graphNewSituationColor"              => $this->graphNewSituationColor,
            "graphAgeOfDevices1"                  => $this->graphAgeOfDevices1,
            "graphAgeOfDevices2"                  => $this->graphAgeOfDevices2,
            "graphAgeOfDevices3"                  => $this->graphAgeOfDevices3,
            "graphAgeOfDevices4"                  => $this->graphAgeOfDevices4,
            "graphMonoDeviceColor"                => $this->graphMonoDeviceColor,
            "graphColorDeviceColor"               => $this->graphColorDeviceColor,
            "graphCopyCapableDeviceColor"         => $this->graphCopyCapableDeviceColor,
            "graphDuplexCapableDeviceColor"       => $this->graphDuplexCapableDeviceColor,
            "graphFaxCapableDeviceColor"          => $this->graphFaxCapableDeviceColor,
            "assessmentTitle"                     => $this->assessmentTitle,
            "customerCostAnalysisTitle"           => $this->customerCostAnalysisTitle,
            "customerOptimizationTitle"           => $this->customerOptimizationTitle,
            "healthCheckTitle"                    => $this->healthCheckTitle,
            "leaseQuoteTitle"                     => $this->leaseQuoteTitle,
            "purchaseQuoteTitle"                  => $this->purchaseQuoteTitle,
            "solutionTitle"                       => $this->solutionTitle,
        );
    }

    /**
     * Gets the dealer
     *
     * @return Application_Model_Dealer
     */
    public function getDealer ()
    {
        if (!isset($this->_dealer))
        {
            $this->_dealer = Application_Model_Mapper_Dealer::getInstance()->find($this->dealerId);
        }

        return $this->_dealer;
    }

    /**
     * Sets the dealer
     *
     * @param Application_Model_Dealer $dealer
     *
     * @return $this
     */
    public function setDealer ($dealer)
    {
        $this->_dealer = $dealer;

        return $this;
    }

}