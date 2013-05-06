<?php
require_once('Tangent/Accounting.php');
class Library_Tangent_AccountingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provides data for applyMargin
     */
    public function addMarginData ()
    {
        return array(
            array(1, 20, 1.25,),
            array(4, 99.2, 500,),
            array(-1, 20, 0,),
            array(1, -20, 0.8,),
            array(-1, -20, 0,),
        );
    }

    /**
     * @dataProvider addMarginData
     */
    public function testApplyMargin ($cost, $marginPercent, $expectedResult)
    {
        $this->assertEquals($expectedResult, Tangent_Accounting::applyMargin($cost, $marginPercent), "apply margin gives incorrect value!");
    }

    /**
     * Provides data for applyMarginoutOfBounds
     */
    public function outOfBoundsMarginData ()
    {
        return array(
            array(1, -150,),
            array(1, 150,),
        );
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     * @dataProvider outOfBoundsMarginData
     */
    public function testApplyMarginOutOfBounds ($cost, $marginPercent)
    {
        Tangent_Accounting::applyMargin($cost, $marginPercent);
    }

    /**
     * Provides data for applyMargin
     */
    public function reverseMarginData ()
    {
        return array(
            array(1, 1.25, 20,),
            array(-1, 1.25, 0,),
            array(1, -1.25, 0,),
            array(-1, -1.25, 0),
        );
    }

    /**
     * @dataProvider reverseMarginData
     */
    public function testReverseMargin ($cost, $price, $expectedResult)
    {
        $this->assertEquals($expectedResult, Tangent_Accounting::reverseEngineerMargin($cost, $price), "Reverse Engineering Margin is incorrect");
    }
}

