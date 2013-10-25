<?php

/**
 * Class My_Feature_MapperInterface
 */
interface My_Feature_MapperInterface
{
    /**
     * Should get a array of feature names and return them in string form
     *
     * @return string[]
     */
    public function getFeatures ();
}