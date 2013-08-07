<?php

/**
 * Class My_Filter_StringReplace
 */
class My_Filter_StringReplace implements Zend_Filter_Interface
{

    /**
     * @var string
     */
    protected $_find;

    /**
     * @var string
     */
    protected $_replace;

    /**
     * @param array $options
     *
     * @throws Zend_Filter_Exception
     */
    public function __construct ($options)
    {
        if ($options instanceof Zend_Config)
        {
            $options = $options->toArray();
        }
        else if (!is_array($options))
        {
            $options      = func_get_args();
            $temp['find'] = array_shift($options);
            if (!empty($options))
            {
                $temp['replace'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('find', $options))
        {
            throw new Zend_Filter_Exception("Missing option. 'find' has to be given");
        }

        $this->_find    = $options['find'];
        $this->_replace = array_key_exists("replace", $options) ? $options['replace'] : "";
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     *
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter ($value)
    {
        return str_replace($this->_find, $this->_replace, $value);
    }

}