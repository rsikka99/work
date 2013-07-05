<?php

class Proposalgen_Service_Toner
{
    /**
     * Returns a list of toners by their color id for manupulation
     *
     * @param $toners Proposalgen_Model_Toner[]
     *
     * @return array
     */
    public function getTonersByColorId ($toners)
    {
        $tonersByColorId = array();
        foreach ($toners as $toner)
        {
            $tonersByColorId [$toner->tonerColorId] = $toner;
        }

        return $tonersByColorId;
    }

    /**
     * Returns an array of toners based on oem manufacturer
     *
     * @param $tonerSets
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getOemTonersByTonerSet ($tonerSets)
    {
        $oemTonerArray = array();

        foreach ($tonerSets as $tonerSet)
        {
            if ($tonerSet['isOem'])
            {
                $oemTonerArray = $tonerSet['toners'];
            }
        }

        return $oemTonerArray;
    }
}