<?php
namespace Tangent\Format;

/**
 * Converts a number into a word version of it
 * Allows a precision to be set
 *
 * Example:
 * 12345 turns into '12 thousand'
 * If we send it a precision of 2 we get '12.34 thousand'
 */
class NumberToWord
{
    public $numberNameArray = array(
        "Hundred",
        "Thousand",
        "Million",
        "Billion",
        "Trillion",
        "Quadrillion",
        "Quintillion",
        "Sextillion",
        "Septillion",
        "Octillion",
        "Nonillion",
        "Decillion",
        "Undecillion",
        "Duodecillion",
        "Tredecillion",
        "Quattuordecillion",
        "Quinquadecillion",
        "Sedecillion",
        "Septendecillion",
        "Octodecillion",
        "Novendecillion",
        "Vigintillion",
        "Unvigintillion",
        "Duovigintillion",
        "Tresvigintillion",
        "Quattuorvigintillion",
        "Quinquavigintillion",
        "Sesvigintillion",
        "Septemvigintillion",
        "Octovigintillion",
        "Novemvigintillion",
        "Trigintillion",
        "Untrigintillion",
        "Duotrigintillion",
        "Trestrigintillion",
        "Quattuortrigintillion",
        "Quinquatrigintillion",
        "Sestrigintillion",
        "Septentrigintillion",
        "Octotrigintillion",
        "Noventrigintillion",
        "Quadragintillion",
        "Quinquagintillion",
        "Sexagintillion",
        "Septuagintillion",
        "Octogintillion",
        "Nonagintillion",
        "Centillion",
        "Uncentillion",
        "Duocentillion",
        "Trescentillion",
        "Decicentillion",
        "Undecicentillion",
        "Viginticentillion",
        "Unviginticentillion",
        "Trigintacentillion",
        "Quadragintacentillion",
        "Quinquagintacentillion",
        "Sexagintacentillion",
        "Septuagintacentillion",
        "Octogintacentillion",
        "Nonagintacentillion",
        "Ducentillion",
        "Trecentillion",
        "Quadringentillion",
        "Quingentillion",
        "Sescentillion",
        "Septingentillion",
        "Octingentillion",
        "Nongentillion",
        "Millinillion",
    );

    /**
     *  Convert a number to a word representation
     *
     * @param float $number
     * @param int   $precision
     *
     * @throws InvalidArgumentException
     * @return float
     */
    public function format ($number, $precision)
    {
        // FIXME lrobert: Limited to quintillion
        $number = (int)$number;
        $word   = $number;
        $length = strlen($number);

        if ($length >= 4)
        {
            $position = (int)(($length - 1) / 3);
            if ($position < 0)
            {
                $position = 0;
            }

            $numberOfExtraZeros = ($length + 2) % 3 + 1;
            $word               = substr($number, 0, $numberOfExtraZeros);

            if ($precision > 0)
            {
                $word .= "." . substr($number, $numberOfExtraZeros, $precision);
            }

            if (!isset($this->numberNameArray[$position]))
            {
                throw new InvalidArgumentException("No name for " . $number);
            }

            $word .= " " . $this->numberNameArray[$position];
        }

        return $word;
    }
}

