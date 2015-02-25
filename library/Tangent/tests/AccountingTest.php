<?php
require_once('Tangent/Accounting.php');

class Library_Tangent_AccountingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provides data for applyMargin
     */
    public function addMarginData ()
    {
        return [
            [1, 20, 1.25,],
            [4, 99.2, 500,],
            [-1, 20, 0,],
            [1, -20, 0.8,],
            [-1, -20, 0,],
        ];
    }

    /**
     * @dataProvider addMarginData
     */
    public function testApplyMargin ($cost, $marginPercent, $expectedResult)
    {
        $this->assertEquals($expectedResult, \Tangent\Accounting::applyMargin($cost, $marginPercent), "apply margin gives incorrect value!");
    }

    /**
     * Provides data for applyMarginoutOfBounds
     */
    public function outOfBoundsMarginData ()
    {
        return [
            [1, -150,],
            [1, 150,],
        ];
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     * @dataProvider outOfBoundsMarginData
     */
    public function testApplyMarginOutOfBounds ($cost, $marginPercent)
    {
        \Tangent\Accounting::applyMargin($cost, $marginPercent);
    }

    /**
     * Provides data for applyMargin
     */
    public function reverseMarginData ()
    {
        return [
            [1, 1.25, 20,],
            [-1, 1.25, 0,],
            [1, -1.25, 0,],
            [-1, -1.25, 0],
        ];
    }

    /**
     * @dataProvider reverseMarginData
     */
    public function testReverseMargin ($cost, $price, $expectedResult)
    {
        $this->assertEquals($expectedResult, \Tangent\Accounting::reverseEngineerMargin($cost, $price), "Reverse Engineering Margin is incorrect");
    }
}

