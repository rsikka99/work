<!doctype html>
<html>
<head>
    <title>Formatting Test</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
</head>
<body>

<?php
$locales = IntlCalendar::getAvailableLocales();

/* @var $formatters NumberFormatter[] */
$formatters = array();
foreach ($locales as $locale)
{
    $formatters[] = new NumberFormatter($locale, NumberFormatter::CURRENCY);
}

$transactionAmounts = array(0.05, 0.99, 1, 1.15, 20, 25.99, 100.99, 195, 1223, 1233.49, 11231123112.43);

?>
<div class="container">
    <p class="lead">Testing currency formatting.</p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Locale</th>
            <th>Formatted Currency</th>
            <th>Formatted Specific Currency</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($locales as $locale) : ?>
            <tr>
                <td><?= $locale ?></td>
                <td><?= NumberFormatter::create($locale, NumberFormatter::CURRENCY)->format(1813400.94) ?></td>
                <td><?= NumberFormatter::create($locale, NumberFormatter::CURRENCY)->formatCurrency(1813400.94, 'USD') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>