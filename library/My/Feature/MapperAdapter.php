<?php

/**
 * Class My_Feature_MapperAdapter
 */
class My_Feature_MapperAdapter implements My_Feature_AdapterInterface
{
    /**
     * @var My_Feature_MapperInterface
     */
    protected $_mapper;

    /**
     * @var string
     */
    protected $_mapperClassName;

    /**
     * Sets the mapperClassName
     *
     * @param $options
     */
    public function __construct ($options)
    {
        $this->_mapperClassName = $options['mapperClassName'];
    }

    /**
     * Getter for _mapper
     *
     * @throws My_Feature_InvalidClassException
     * @return \My_Feature_MapperInterface
     */
    public function getMapper ()
    {
        if (!isset($this->_mapper))
        {
            if (class_exists($this->_mapperClassName))
            {
                $this->_mapper = new $this->_mapperClassName();
            }
            else
            {
                throw new My_Feature_InvalidClassException("Invalid mapper class name '" . $this->_mapperClassName . "'");
            }
        }

        return $this->_mapper;
    }

    /**
     * Setter for _mapper
     *
     * @param \My_Feature_MapperInterface $mapper
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
    }

    /**
     * @return string []
     */
    public function getFeatures ()
    {
        return $this->getMapper()->getFeatures();
    }
}