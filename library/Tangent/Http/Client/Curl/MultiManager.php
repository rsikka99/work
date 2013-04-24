<?php
class Tangent_Http_Client_Curl_MultiManager
{
    /**
     * Maximum number of all requests.
     *
     * @var int
     */
    private $_maxRequests = 0;

    /**
     * Array of cURL options for all requests.
     *
     * @var array
     */
    private $_curlOptions = array(CURLOPT_RETURNTRANSFER => true);

    /**
     * Current running requests.
     *
     * @var Tangent_Http_Client_Curl_MultiRequest[]
     */
    private $_requests = array();

    /**
     * Array of failed requests that need to be tried again.
     *
     * @var Tangent_Http_Client_Curl_MultiRequest[]
     */
    private $_retryRequests = array();

    /**
     * cURL Multi handle.
     *
     * @var null| Resource
     */
    private $_multiHandle;


    public function __construct ($maxRequests = null)
    {
        if (!is_null($maxRequests))
        {

        }
    }

    /**
     * Factory method for a Request.
     *
     * @param $url string
     *
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function newRequest ($url)
    {
        return new Tangent_Http_Client_Curl_MultiRequest($url);
    }

    /**
     * Set cURL Option used on all started Requests.
     *
     * @param string $key   cURL Option Key
     * @param mixed  $value Value
     *
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    public function setCurlOption ($key, $value)
    {
        $this->_curlOptions[$key] = $value;

        return $this;
    }

    /**
     * Set maximum Number of requests.
     * Zero means 'UNLIMITED' (default).
     *
     * @param $maxRequests Number of maximal parallel Requests.
     *
     * @throws InvalidArgumentException
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    public function setMaxRequests ($maxRequests)
    {
        $maxRequests = (int)$maxRequests;
        if ($maxRequests < 0)
        {
            throw new \InvalidArgumentException("MaxRequests has to be >= 0");
        }
        $this->_maxRequests = $maxRequests;

        return $this;
    }

    /**
     * Returns maximum number of parallel requests.
     *
     * @return Integer
     */
    public function getMaxRequests ()
    {
        return $this->_maxRequests;
    }

    /**
     * Initializes cURL Multi handle.
     *
     * @throws Exception
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    protected function initCurl ()
    {
        $this->_multiHandle = curl_multi_init();
        if (!$this->_multiHandle)
        {
            throw new \Exception("Could not create Curl multi handle");
        }

        return $this;
    }

    /**
     * Start a new Requests or wait till a slot becomes free.
     *
     * @param Tangent_Http_Client_Curl_MultiRequest $request      Request to start
     * @param boolean                               $useNewHandle Should a new cURL Handle be used.
     *
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    public function startRequest (Tangent_Http_Client_Curl_MultiRequest $request, $useNewHandle = false)
    {

        // Wait for free slots
        if ($this->getMaxRequests() > 0)
        {
            $this->waitForMaxActive($this->getMaxRequests() - 1);
        }

        // Fetch request curl handle
        $ch = $request->getCurlHandle($useNewHandle);

        // Apply global cURL Options
        curl_setopt_array($ch, $this->_curlOptions);

        // Add Curl Handle
        curl_multi_add_handle($this->_multiHandle, $ch);

        // Save Curl Handle
        $ch_id = (int)$ch;
        // Casting the cURL Resource to int returns the resource id.
        // Every Resource in PHP has a unique id.
        $this->_requests[$ch_id] = $request;

        // Process
        $this->processRequests();

        return $this;
    }

    /**
     * Finishes all requests and start retries.
     *
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    public function finishAllRequests ()
    {

        // Wait for all requests to finish.
        $this->waitForMaxActive(0);

        // Retry failed Requests
        $requestsToRetry = $this->_retryRequests;

        // Delete old array so there is no mix up when starting retries
        $this->_retryRequests = array();

        if ($requestsToRetry)
        {
            // Perform retries
            foreach ($requestsToRetry as $request)
            {
                $this->startRequest($request, true);
            }
            // Wait for retries to finish
            $this->finishAllRequests();
        }

        return $this;
    }

    /**
     * Process cURL multi handle and handle finished.
     *
     * @throws Exception
     * @return Tangent_Http_Client_Curl_MultiManager|bool
     */
    protected function processRequests ()
    {

        // Call with empty timeout if there is anything to do
        // A timeout > would block.
        if (curl_multi_select($this->_multiHandle, 0.0) === -1)
        {
            // Nothing to do
            return false;
        }

        // Process what is active
        do
        {
            $mrc = curl_multi_exec($this->_multiHandle, $active);
            // Sleep some milliseconds to avoid CPU usage.
            usleep(200);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        // Process completed Requests
        do
        {
            // Fetch finished cURL requests
            $info = curl_multi_info_read($this->_multiHandle);
            if ($info)
            {

                // MAYBE: Use $info['result']

                // Fetch Curl Handle
                $ch = $info['handle'];

                // Check
                $ch_id = (int)$ch;
                if (!isset($this->_requests[$ch_id]))
                {
                    throw new \Exception("Unknown Curl Handle index: $ch_id");
                }

                // Process
                $request = $this->_requests[$ch_id];
                $request->process($this);

                // Order retries
                if ($request->shouldRetry())
                {
                    $this->_retryRequests[] = $request;
                }

                // Clean up
                curl_multi_remove_handle($this->_multiHandle, $ch);
                curl_close($ch);
                unset($this->_requests[$ch_id]);
            }
        } while ($info);

        return $this;
    }

    /**
     * Wait till specified amount of requests is active.
     *
     * @param int $max Maximum Number if active requests
     *
     * @return Tangent_Http_Client_Curl_MultiManager
     */
    protected function waitForMaxActive ($max)
    {
        while (count($this->_requests) > $max)
        {
            $this->processRequests();
        }

        return $this;
    }

    /**
     * Wait for all requests to finish on destruction.
     */
    public function __destruct ()
    {
        $this->finishAllRequests();
    }
}