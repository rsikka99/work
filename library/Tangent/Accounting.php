<?php

namespace Tangent;

/**
 * Class Accounting
 *
 * @package Tangent
 */
class Accounting
{

    /**
     * ****************************************************************************************************************
     * MARGINS
     * ****************************************************************************************************************
     */

    /**
     * *************************************************************************************************************
     * Margin calculations
     * *************************************************************************************************************
     * **** Margins must always be between 0 and 100 (not inclusive).
     * 100% margin would mean that it cost you absolutely nothing to have/make an item which could be possible but
     * it's not realistic in a business world.
     *
     * **** Allowing for negative margins requires special treatment. When you have a negative margin you need to
     * treat the number you're applying to it as if it already had a positive margin. $100 with a -20% margin will
     * be $80, $100 with a positive margin will be $125.
     *
     * *************************************************************************************************************
     * Applying margins
     * *************************************************************************************************************
     *
     * To convert a margin to a decimal for use in calculations it's Margin = 1 - (ABS(MarginPercent) / 100).
     *
     * 20% margin = 1 - (ABS(20) / 100) = 0.8
     *
     * To apply a positive margin, you divide, to apply a negative margin, you multiply.
     *
     * $100 with 20% margin = 100 / 0.8 = $125
     *
     * $100 with -20% margin = 100 * 0.8 = $80
     *
     * *************************************************************************************************************
     * Reverse engineering a margin
     * *************************************************************************************************************
     * To reverse engineer a margin we need to figure out the between the price and cost and divide it by the price.
     * If the cost is greater than the price then the margin will be negative. In this case we devide the difference
     * by the cost instead of the price. Be sure to check to see if the price and cost are the same as that is 0%
     * margin.
     *
     * Positive: Margin = ((Price - Cost) / Price) * 100;
     *
     * +20% = (125 - 100) / 125 * 100
     *
     * Negative: $margin = ((Price - Cost) / Cost) * 100;
     *
     * -20% = (80 - 100) / 100 * 100
     *
     * *************************************************************************************************************
     */

    /**
     * Applies a margin to a cost.
     *
     * @param number $cost
     *            A number that is greater than or equal to 0
     * @param number $marginPercent
     *            A number representing a percentage. E.g. If you want 20%, you pass 20 to the function. Must be between
     *            -100 and 100 exclusively.
     *
     * @throws InvalidArgumentException If the margin is out of range this exception will be thrown.
     * @return number The cost with the margin applied. Also known as the price.
     */
    public static function applyMargin ($cost, $marginPercent)
    {
        $price = 0;

        // Validate that margin percentage is correct
        if ($marginPercent <= -100 || $marginPercent >= 100)
        {
            trigger_error('Margin percent must be between -100 and 100 exclusively.', E_USER_NOTICE);

            return $cost;
        }

        // Only apply a margin on something that has a cost > 0.
        if ($cost > 0)
        {
            if ($marginPercent > 0 && $marginPercent < 100)
            {
                // When we have a positive margin, we apply it to the cost
                $margin = 1 - (abs($marginPercent) / 100);
                $price  = $cost / $margin;
            }
            else if ($marginPercent < 0 && $marginPercent > -100)
            {
                // When we have a negative margin, we remove it from the cost
                $margin = 1 - (abs($marginPercent) / 100);
                $price  = $cost * $margin;
            }
            else
            {
                // If for some reason the margin was invalid, we'll set the price to the cost.
                $price = $cost;
            }
        }

        return $price;
    }

    /**
     * Reverse engineers a margin based on the cost and price.
     *
     * @param number $cost
     *            The cost
     * @param number $price
     *            The price
     *
     * @return number The margin percentage. E.g. 20% returns as 20
     */
    public static function reverseEngineerMargin ($cost, $price)
    {
        $margin = 0;

        // Only calculate if we have real numbers to return
        if ($cost > 0 && $price > 0)
        {
            if ($price > $cost)
            {
                // Price is greater than cost. Positive Margin time
                // Margin % = (price - cost) / price * 100
                $margin = (($price - $cost) / $price) * 100;
            }
            else if ($price < $cost)
            {
                // Price is less than cost. Negative margin time.
                // Margin % = (price - cost) / cost * 100
                $margin = (($price - $cost) / $cost) * 100;
            }
            else
            {
                // If the prices are identical, we make 0 margin.
                $margin = 0;
            }
        }

        return $margin;
    }
}