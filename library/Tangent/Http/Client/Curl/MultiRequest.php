<?php

class Tangent_Http_Client_Curl_MultiRequest
{
    /**
     * Current number of tries for this request.
     *
     * @var Integer
     */
    protected $_retryAttempts = 0;

    /**
     * Maximum number of tries.
     *
     * @var Integer
     */
    protected $_maximumRetryAttempts = 1;

    /**
     * Retry flag.
     *
     * @var Boolean
     */
    protected $_doRetry = false;

    /**
     * Current cURL handle.
     *
     * @var null | Resource
     */
    protected $_curlHandle = null;

    /**
     * Current cURL Options.
     *
     * @var Array
     */
    protected $_curlOptions = array(CURLOPT_RETURNTRANSFER => true);

    /**
     * Content fetched from cURL handle.
     *
     * @var null | String
     */
    protected $_content = null;

    /**
     * Callback that should be called after process().
     *
     * @var null | String | Array
     */
    protected $_callback = null;

    /**
     * Callback parameter.
     * Will be the third parameter of callback.
     *
     * @var null | mixed
     */
    protected $_callbackParameter = null;

    /**
     * Info from cURL handle.
     *
     * @var Array
     */
    protected $_info = array();

    /**
     * Error Message from cURL Handle.
     *
     * @var null | String
     */
    protected $_error = null;

    /**
     * Error Number from cURL Handle.
     *
     * @var null | Integer
     */
    protected $_errorNumber = null;

    /**
     * New Request for given url.
     *
     * @param $url string
     */
    public function __construct ($url)
    {
        $this->setUrl($url);
    }

    /**
     * Returns cURL handle info.
     *
     * @param string $key Receive only key if set
     *
     * @return array
     */
    public function getInfo ($key = null)
    {
        if ($key)
        {
            if (isset($this->_info[$key]))
            {
                return $this->_info[$key];
            }

            return false;
        }

        return $this->_info;
    }

    /**
     * Returns cURL handle content.
     *
     * @return String
     */
    public function getContent ()
    {
        return $this->_content;
    }

    /**
     * Returns cURL handle error message.
     *
     * @return String
     */
    public function getError ()
    {
        return $this->_error;
    }

    /**
     * Returns cURL handle error Number.
     *
     * @return Integer
     */
    public function getErrorNumber ()
    {
        return $this->_errorNumber;
    }

    /**
     * Marks this Request so that the Manager executes them again.
     *
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function doRetry ()
    {
        $this->_doRetry = true;

        return $this;
    }

    /**
     * Set maximum count of tries.
     *
     * @param $number Number of Tries
     *
     * @throws InvalidArgumentException
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function setMaximumRetryAttempts ($number)
    {
        if ($number < 1)
        {
            throw new \InvalidArgumentException("Invalid tries number given.");
        }
        $this->_maximumRetryAttempts = $number;

        return $this;
    }

    /**
     * Sets callback for handling at the end of process().
     * Callback will get this Request as first parameter and
     * CurlMultiManager as second.
     *
     * @param $callback Callback
     *
     * @throws InvalidArgumentException
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function setCallback ($callback)
    {
        if (!is_callable($callback))
        {
            throw new \InvalidArgumentException("No callable Callback given.");
        }
        $this->_callback = $callback;

        return $this;
    }

    /**
     * Sets a callback parameter. Will be the third parameter.
     *
     * @param mixed $param
     *
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function setCallbackParameter ($param)
    {
        $this->_callbackParameter = $param;

        return $this;
    }

    /**
     * Shortcut for getting url.
     *
     * @return String
     */
    public function getUrl ()
    {
        return $this->_curlOptions[CURLOPT_URL];
    }

    /**
     * Shortcut for setting url.
     *
     * @param $url String Url
     *
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function setUrl ($url)
    {
        $this->setCurlOption(CURLOPT_URL, $url);

        return $this;
    }

    /**
     * Setting cURL option.
     *
     * @param $key   string cURL Options Key
     * @param $value mixed  cURL option value
     *
     * @return Tangent_Http_Client_Curl_MultiRequest
     */
    public function setCurlOption ($key, $value = null)
    {
        $this->_curlOptions[$key] = $value;

        return $this;
    }

    /**
     * Returns cURL handle.
     *
     * @param $new =false Flag if a new handle should be created
     *
     * @return Resource of cURL Handle
     */
    public function getCurlHandle ($new = false)
    {
        if ($new)
        {
            @curl_close($this->_curlHandle);
            $this->_curlHandle = false;
        }
        if (!is_resource($this->_curlHandle))
        {
            $this->_curlHandle = curl_init();
            curl_setopt_array($this->_curlHandle, $this->_curlOptions);
        }

        return $this->_curlHandle;
    }

    /**
     * Process completed request.
     * Sets data from cURL Handle and calls callback.
     *
     * @param $curlMultiManager Tangent_Http_Client_Curl_MultiManager Instance of Manager
     *
     * @return Boolean
     */
    public function process (Tangent_Http_Client_Curl_MultiManager $curlMultiManager = null)
    {
        // Init
        $this->_retryAttempts++;
        $this->_doRetry = false;

        // Fetch cURL data
        $this->_content     = curl_multi_getcontent($this->_curlHandle);
        $this->_info        = curl_getinfo($this->_curlHandle);
        $this->_error       = curl_error($this->_curlHandle);
        $this->_errorNumber = curl_errno($this->_curlHandle);

        // Callback
        if ($this->_callback)
        {
            call_user_func($this->_callback, $this, $curlMultiManager, $this->_callbackParameter);
        }

        return true;
    }

    /**
     * Returns if this request is configured to be retried.
     *
     * @return Boolean
     */
    public function shouldRetry ()
    {
        if ($this->_retryAttempts >= $this->_maximumRetryAttempts)
        {
            // Reached max tries
            return false;
        }
        if ($this->_doRetry)
        {
            // Retry this request
            return true;
        }

        return false;
    }
}