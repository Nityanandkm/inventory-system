<?php
function numberToWords($number) {
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    );

    $units = array('', 'Thousand', 'Million', 'Billion', 'Trillion');

    if ($number == 0) {
        return 'Zero';
    }

    $number = number_format($number, 2, '.', '');
    $number = explode('.', $number);
    $integer = $number[0];
    $fraction = $number[1];

    $integer = (int) $integer;
    $fraction = (int) $fraction;

    $result = '';

    // Process the integer part
    if ($integer > 0) {
        $result = processNumber($integer, $words, $units);
    }

    // Process the fractional part
    if ($fraction > 0) {
        $result .= ' and ' . processNumber($fraction, $words, $units) . ' Cents';
    } else {
        $result .= ' Only';
    }

    return $result;
}

function processNumber($number, $words, $units) {
    $number = (int) $number;
    $unitIndex = 0;
    $result = '';

    while ($number > 0) {
        $unit = $number % 1000;
        $number = (int) ($number / 1000);

        if ($unit > 0) {
            $result = getWords($unit, $words) . ' ' . $units[$unitIndex] . ' ' . $result;
        }

        $unitIndex++;
    }

    return trim($result);
}

function getWords($number, $words) {
    if ($number < 20) {
        return $words[$number];
    } elseif ($number < 100) {
        return $words[(int)($number / 10) * 10] . ($number % 10 ? '-' . $words[$number % 10] : '');
    } else {
        return $words[(int)($number / 100)] . ' Hundred' . ($number % 100 ? ' and ' . getWords($number % 100, $words) : '');
    }
}
?>
