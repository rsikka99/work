<div class="container">
<p class="lead">Testing currency formatting.</p>

<div role="tabpanel">

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation">
        <a href="#code" aria-controls="home" role="tab" data-toggle="tab">Code</a>
    </li>
    <li role="presentation" class="active">
        <a href="#results" aria-controls="profile" role="tab" data-toggle="tab">Results</a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane" id="code"><pre class="brush: php">&lt;!doctype html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Formatting Test&lt;/title&gt;
    &lt;meta charset=&quot;UTF-8&quot;&gt;
    &lt;link rel=&quot;stylesheet&quot; href=&quot;//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css&quot;&gt;
&lt;/head&gt;
&lt;body&gt;

&lt;?php
$locales = IntlCalendar::getAvailableLocales();

/* @var $formatters NumberFormatter[] */
$formatters = array();
foreach ($locales as $locale)
{
    $formatters[] = new NumberFormatter($locale, NumberFormatter::CURRENCY);
}

$transactionAmounts = array(0.05, 0.99, 1, 1.15, 20, 25.99, 100.99, 195, 1223, 1233.49, 11231123112.43);

?&gt;
&lt;div class=&quot;container&quot;&gt;
    &lt;p class=&quot;lead&quot;&gt;Testing currency formatting.&lt;/p&gt;
    &lt;table class=&quot;table table-bordered&quot;&gt;
        &lt;thead&gt;
        &lt;tr&gt;
            &lt;th&gt;Locale&lt;/th&gt;
            &lt;th&gt;Formatted Currency&lt;/th&gt;
            &lt;th&gt;Formatted Specific Currency&lt;/th&gt;
        &lt;/tr&gt;
        &lt;/thead&gt;
        &lt;tbody&gt;
        &lt;?php foreach ($locales as $locale) : ?&gt;
            &lt;tr&gt;
                &lt;td&gt;&lt;?= $locale ?&gt;&lt;/td&gt;
                &lt;td&gt;&lt;?= NumberFormatter::create($locale, NumberFormatter::CURRENCY)-&gt;format(1813400.94) ?&gt;&lt;/td&gt;
                &lt;td&gt;&lt;?= NumberFormatter::create($locale, NumberFormatter::CURRENCY)-&gt;formatCurrency(1813400.94, 'USD') ?&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;?php endforeach; ?&gt;
    &lt;/table&gt;
&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>
</div>
<div role="tabpanel" class="tab-pane active" id="results">
<table class="table table-bordered table-condensed">
<thead>
<tr>
    <th>Locale</th>
    <th>Formatted Currency</th>
    <th>Formatted Specific Currency</th>
</tr>
</thead>
<tbody>
<tr>
    <td>af</td>
    <td>¤1 813 400,94</td>
    <td>US$1 813 400,94</td>
</tr>
<tr>
    <td>af_NA</td>
    <td>$ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>af_ZA</td>
    <td>R1 813 400,94</td>
    <td>US$1 813 400,94</td>
</tr>
<tr>
    <td>agq</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>agq_CM</td>
    <td>1 813 401FCFA</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>ak</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ak_GH</td>
    <td>GH₵1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>am</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>am_ET</td>
    <td>ብር1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ar</td>
    <td>¤ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_001</td>
    <td>¤ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_AE</td>
    <td>د.إ.‏ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_BH</td>
    <td>د.ب.‏ ١٬٨١٣٬٤٠٠٫٩٤٠</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_DJ</td>
    <td>Fdj ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_DZ</td>
    <td>د.ج.‏ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ar_EG</td>
    <td>ج.م.‏ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_EH</td>
    <td>د.م.‏ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ar_ER</td>
    <td>Nfk ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_IL</td>
    <td>₪ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_IQ</td>
    <td>د.ع.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_JO</td>
    <td>د.أ.‏ ١٬٨١٣٬٤٠٠٫٩٤٠</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_KM</td>
    <td>ف.ج.ق.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_KW</td>
    <td>د.ك.‏ ١٬٨١٣٬٤٠٠٫٩٤٠</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_LB</td>
    <td>ل.ل.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_LY</td>
    <td>د.ل.‏ 1.813.400,940</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ar_MA</td>
    <td>د.م.‏ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ar_MR</td>
    <td>أ.م.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_OM</td>
    <td>ر.ع.‏ ١٬٨١٣٬٤٠٠٫٩٤٠</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_PS</td>
    <td>₪ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_QA</td>
    <td>ر.ق.‏ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_SA</td>
    <td>ر.س.‏ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_SD</td>
    <td>ج.س. ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_SO</td>
    <td>S ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_SS</td>
    <td>£ ١٬٨١٣٬٤٠٠٫٩٤</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_SY</td>
    <td>ل.س.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_TD</td>
    <td>FCFA ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>ar_TN</td>
    <td>د.ت.‏1813400,940</td>
    <td>US$1813400,94</td>
</tr>
<tr>
    <td>ar_YE</td>
    <td>ر.ي.‏ ١٬٨١٣٬٤٠١</td>
    <td>US$ ١٬٨١٣٬٤٠٠٫٩٤</td>
</tr>
<tr>
    <td>as</td>
    <td>¤ ১৮,১৩,৪০০.৯৪</td>
    <td>US$ ১৮,১৩,৪০০.৯৪</td>
</tr>
<tr>
    <td>as_IN</td>
    <td>₹ ১৮,১৩,৪০০.৯৪</td>
    <td>US$ ১৮,১৩,৪০০.৯৪</td>
</tr>
<tr>
    <td>asa</td>
    <td>1,813,400.94 ¤</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>asa_TZ</td>
    <td>1,813,401 TSh</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>az</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>az_Cyrl</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>az_Cyrl_AZ</td>
    <td>ман. 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>az_Latn</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>az_Latn_AZ</td>
    <td>man. 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>bas</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>bas_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>be</td>
    <td>¤1 813 400,94</td>
    <td>$1 813 400,94</td>
</tr>
<tr>
    <td>be_BY</td>
    <td>р.1 813 401</td>
    <td>$1 813 400,94</td>
</tr>
<tr>
    <td>bem</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>bem_ZM</td>
    <td>K1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>bez</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>bez_TZ</td>
    <td>1,813,401TSh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>bg</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 щ.д.</td>
</tr>
<tr>
    <td>bg_BG</td>
    <td>1 813 400,94 лв.</td>
    <td>1 813 400,94 щ.д.</td>
</tr>
<tr>
    <td>bm</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>bm_ML</td>
    <td>CFA1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>bn</td>
    <td>১৮,১৩,৪০০.৯৪¤</td>
    <td>১৮,১৩,৪০০.৯৪US$</td>
</tr>
<tr>
    <td>bn_BD</td>
    <td>১৮,১৩,৪০০.৯৪৳</td>
    <td>১৮,১৩,৪০০.৯৪US$</td>
</tr>
<tr>
    <td>bn_IN</td>
    <td>১৮,১৩,৪০০.৯৪₹</td>
    <td>১৮,১৩,৪০০.৯৪US$</td>
</tr>
<tr>
    <td>bo</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>bo_CN</td>
    <td>¥ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>bo_IN</td>
    <td>₹ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>br</td>
    <td>¤ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>br_FR</td>
    <td>€ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>brx</td>
    <td>¤ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>brx_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>bs</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>bs_Cyrl</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>bs_Cyrl_BA</td>
    <td>1.813.400,94 КМ</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>bs_Latn</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>bs_Latn_BA</td>
    <td>KM 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ca</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>ca_AD</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>ca_ES</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>ca_FR</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>ca_IT</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>cgg</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>cgg_UG</td>
    <td>USh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>chr</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>chr_US</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>cs</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>cs_CZ</td>
    <td>1 813 400,94 Kč</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>cy</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>cy_GB</td>
    <td>£1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>da</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>da_DK</td>
    <td>1.813.400,94 kr</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>da_GL</td>
    <td>1.813.400,94 kr</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>dav</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>dav_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>de</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>de_AT</td>
    <td>€ 1.813.400,94</td>
    <td>$ 1.813.400,94</td>
</tr>
<tr>
    <td>de_BE</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>de_CH</td>
    <td>CHF 1'813'400.94</td>
    <td>$ 1'813'400.94</td>
</tr>
<tr>
    <td>de_DE</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>de_LI</td>
    <td>CHF 1'813'400.94</td>
    <td>$ 1'813'400.94</td>
</tr>
<tr>
    <td>de_LU</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>dje</td>
    <td>1 813 400.94¤</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>dje_NE</td>
    <td>1 813 401CFA</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>dua</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>dua_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>dyo</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>dyo_SN</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>dz</td>
    <td>¤༡༨,༡༣,༤༠༠.༩༤</td>
    <td>US$༡༨,༡༣,༤༠༠.༩༤</td>
</tr>
<tr>
    <td>dz_BT</td>
    <td>Nu.༡༨,༡༣,༤༠༠.༩༤</td>
    <td>US$༡༨,༡༣,༤༠༠.༩༤</td>
</tr>
<tr>
    <td>ebu</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ebu_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ee</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ee_GH</td>
    <td>GH₵1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ee_TG</td>
    <td>CFA1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>el</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>el_CY</td>
    <td>€1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>el_GR</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>en</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_001</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_150</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>en_AG</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_AI</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_AS</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_AU</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_BB</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_BE</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>en_BM</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_BS</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_BW</td>
    <td>P1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_BZ</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_CA</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_CC</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_CK</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_CM</td>
    <td>FCFA1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_CX</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_DG</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_DM</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_ER</td>
    <td>Nfk1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_FJ</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_FK</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_FM</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_GB</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_GD</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_GG</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_GH</td>
    <td>GH₵1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_GI</td>
    <td>£1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_GM</td>
    <td>D1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_GU</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_GY</td>
    <td>$1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_HK</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_IE</td>
    <td>€1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_IM</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>en_IO</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_JE</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_JM</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_KI</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_KN</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_KY</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_LC</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_LR</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_LS</td>
    <td>R1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MG</td>
    <td>Ar1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MH</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_MO</td>
    <td>MOP$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MP</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_MS</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MT</td>
    <td>€1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MU</td>
    <td>Rs1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_MW</td>
    <td>MK1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NA</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NF</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NG</td>
    <td>₦1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NR</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NU</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_NZ</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_PG</td>
    <td>K1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_PH</td>
    <td>₱1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_PK</td>
    <td>Rs1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_PN</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_PR</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_PW</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_RW</td>
    <td>RF1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SB</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SC</td>
    <td>SR1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SD</td>
    <td>SDG1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SG</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SH</td>
    <td>£1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_SL</td>
    <td>Le1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SS</td>
    <td>£1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SX</td>
    <td>NAf.1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_SZ</td>
    <td>E1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TC</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TK</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TO</td>
    <td>T$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TT</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TV</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_UG</td>
    <td>USh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_UM</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_US</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_US_POSIX</td>
    <td>$ 1813400.94</td>
    <td>$ 1813400.94</td>
</tr>
<tr>
    <td>en_VC</td>
    <td>EC$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_VG</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_VI</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>en_VU</td>
    <td>VT1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_WS</td>
    <td>WS$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_ZA</td>
    <td>R1 813 400,94</td>
    <td>US$1 813 400,94</td>
</tr>
<tr>
    <td>en_ZM</td>
    <td>K1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>en_ZW</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>eo</td>
    <td>¤ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>es</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>es_419</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_AR</td>
    <td>$1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>es_BO</td>
    <td>Bs1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>es_CL</td>
    <td>$1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>es_CO</td>
    <td>$1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>es_CR</td>
    <td>₡1.813.401</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>es_CU</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>es_DO</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>es_EA</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>es_EC</td>
    <td>$1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>es_ES</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>es_GQ</td>
    <td>FCFA1 813 401</td>
    <td>$1 813 400,94</td>
</tr>
<tr>
    <td>es_GT</td>
    <td>Q1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_HN</td>
    <td>L1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_IC</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>es_MX</td>
    <td>$1,813,400.94</td>
    <td>USD1,813,400.94</td>
</tr>
<tr>
    <td>es_NI</td>
    <td>C$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_PA</td>
    <td>B/.1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_PE</td>
    <td>S/.1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_PH</td>
    <td>1 813 400,94 ₱</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>es_PR</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_PY</td>
    <td>₲ 1.813.401</td>
    <td>$ 1.813.400,94</td>
</tr>
<tr>
    <td>es_SV</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_US</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>es_UY</td>
    <td>$ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>es_VE</td>
    <td>Bs.1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>et</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>et_EE</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>eu</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>eu_ES</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ewo</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>ewo_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>fa</td>
    <td>‎¤۱٬۸۱۳٬۴۰۰٫۹۴</td>
    <td>‎$۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>fa_AF</td>
    <td>‎؋۱٬۸۱۳٬۴۰۱</td>
    <td>‎$۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>fa_IR</td>
    <td>‎ریال۱٬۸۱۳٬۴۰۱</td>
    <td>‎$۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>ff</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>ff_SN</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>fi</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>fi_FI</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>fil</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>fil_PH</td>
    <td>₱1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>fo</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>fo_FO</td>
    <td>kr1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>fr</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_BE</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $US</td>
</tr>
<tr>
    <td>fr_BF</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_BI</td>
    <td>1 813 401 FBu</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_BJ</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_BL</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CA</td>
    <td>1 813 400,94 $</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CD</td>
    <td>1 813 400,94 FC</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CF</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CG</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CH</td>
    <td>CHF 1'813'400.94</td>
    <td>$US 1'813'400.94</td>
</tr>
<tr>
    <td>fr_CI</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_DJ</td>
    <td>1 813 401 Fdj</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_DZ</td>
    <td>1 813 400,94 DA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_FR</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_GA</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_GF</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_GN</td>
    <td>1 813 401 FG</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_GP</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_GQ</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_HT</td>
    <td>1 813 400,94 G</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_KM</td>
    <td>1 813 401 CF</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_LU</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $US</td>
</tr>
<tr>
    <td>fr_MA</td>
    <td>1 813 400,94 MAD</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MC</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MF</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MG</td>
    <td>1 813 401 Ar</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_ML</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MQ</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MR</td>
    <td>1 813 401 UM</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_MU</td>
    <td>1 813 401 Rs</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_NC</td>
    <td>1 813 401 FCFP</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_NE</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_PF</td>
    <td>1 813 401 FCFP</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_PM</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_RE</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_RW</td>
    <td>1 813 401 RF</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_SC</td>
    <td>1 813 400,94 SR</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_SN</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_SY</td>
    <td>1 813 401 LS</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_TD</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_TG</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_TN</td>
    <td>1 813 400,940 DT</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_VU</td>
    <td>1 813 401 VT</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_WF</td>
    <td>1 813 401 FCFP</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>fr_YT</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 $US</td>
</tr>
<tr>
    <td>ga</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ga_IE</td>
    <td>€1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>gl</td>
    <td>¤1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>gl_ES</td>
    <td>€1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>gsw</td>
    <td>1’813’400.94 ¤</td>
    <td>1’813’400.94 $</td>
</tr>
<tr>
    <td>gsw_CH</td>
    <td>1’813’400.94 CHF</td>
    <td>1’813’400.94 $</td>
</tr>
<tr>
    <td>gsw_LI</td>
    <td>1’813’400.94 CHF</td>
    <td>1’813’400.94 $</td>
</tr>
<tr>
    <td>gu</td>
    <td>¤18,13,400.94</td>
    <td>US$18,13,400.94</td>
</tr>
<tr>
    <td>gu_IN</td>
    <td>₹18,13,400.94</td>
    <td>US$18,13,400.94</td>
</tr>
<tr>
    <td>guz</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>guz_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>gv</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>gv_IM</td>
    <td>£1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ha</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ha_Latn</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ha_Latn_GH</td>
    <td>GH₵ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ha_Latn_NE</td>
    <td>CFA 1,813,401</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ha_Latn_NG</td>
    <td>₦ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>haw</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>haw_US</td>
    <td>$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>he</td>
    <td>1,813,400.94 ¤</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>he_IL</td>
    <td>1,813,400.94 ₪</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>hi</td>
    <td>¤18,13,400.94</td>
    <td>$18,13,400.94</td>
</tr>
<tr>
    <td>hi_IN</td>
    <td>₹18,13,400.94</td>
    <td>$18,13,400.94</td>
</tr>
<tr>
    <td>hr</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>hr_BA</td>
    <td>1.813.400,94 KM</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>hr_HR</td>
    <td>1.813.400,94 kn</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>hu</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>hu_HU</td>
    <td>1 813 401 Ft</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>hy</td>
    <td>1813400,94 ¤</td>
    <td>1813400,94 $</td>
</tr>
<tr>
    <td>hy_AM</td>
    <td>1813401 դր.</td>
    <td>1813400,94 $</td>
</tr>
<tr>
    <td>id</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>id_ID</td>
    <td>Rp1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>ig</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ig_NG</td>
    <td>₦1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ii</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>ii_CN</td>
    <td>¥ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>is</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>is_IS</td>
    <td>1.813.401 kr</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>it</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>it_CH</td>
    <td>CHF 1'813'400.94</td>
    <td>US$ 1'813'400.94</td>
</tr>
<tr>
    <td>it_IT</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>it_SM</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ja</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>ja_JP</td>
    <td>￥1,813,401</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>jgo</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>jgo_CM</td>
    <td>FCFA 1.813.401</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>jmc</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>jmc_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ka</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>ka_GE</td>
    <td>1 813 400,94 GEL</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>kab</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>kab_DZ</td>
    <td>1 813 400,94DA</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>kam</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kam_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kde</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kde_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kea</td>
    <td>1.813.400,94¤</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>kea_CV</td>
    <td>1,813,400$94 CVE</td>
    <td>1,813,400$94 US$</td>
</tr>
<tr>
    <td>khq</td>
    <td>1 813 400.94¤</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>khq_ML</td>
    <td>1 813 401CFA</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>ki</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ki_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kk</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>kk_Cyrl</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>kk_Cyrl_KZ</td>
    <td>1 813 400,94 ₸</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>kkj</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>kkj_CM</td>
    <td>FCFA 1.813.401</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>kl</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>kl_GL</td>
    <td>kr1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>kln</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kln_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>km</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>km_KH</td>
    <td>៛1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>kn</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kn_IN</td>
    <td>₹1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ko</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ko_KP</td>
    <td>KPW1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ko_KR</td>
    <td>₩1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kok</td>
    <td>¤ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>kok_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>ks</td>
    <td>¤ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
    <td>US$ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>ks_Arab</td>
    <td>¤ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
    <td>US$ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>ks_Arab_IN</td>
    <td>₹ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
    <td>US$ ۱۸٬۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>ksb</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>ksb_TZ</td>
    <td>1,813,401TSh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>ksf</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>ksf_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>kw</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>kw_GB</td>
    <td>£1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ky</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>ky_Cyrl</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>ky_Cyrl_KG</td>
    <td>1 813 400,94 сом</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>lag</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>lag_TZ</td>
    <td>TSh 1,813,401</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>lg</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>lg_UG</td>
    <td>1,813,401USh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>lkt</td>
    <td>¤ 1,813,400.94</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>lkt_US</td>
    <td>$ 1,813,400.94</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>ln</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ln_AO</td>
    <td>1.813.400,94 Kz</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ln_CD</td>
    <td>1.813.400,94 FC</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ln_CF</td>
    <td>1.813.401 FCFA</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>ln_CG</td>
    <td>1.813.401 FCFA</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>lo</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>lo_LA</td>
    <td>₭1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>lt</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>lt_LT</td>
    <td>1 813 400,94 Lt</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>lu</td>
    <td>1.813.400,94¤</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>lu_CD</td>
    <td>1.813.400,94FC</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>luo</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>luo_KE</td>
    <td>1,813,400.94Ksh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>luy</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>luy_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>lv</td>
    <td>¤1 813 400,94</td>
    <td>$1 813 400,94</td>
</tr>
<tr>
    <td>lv_LV</td>
    <td>€1 813 400,94</td>
    <td>$1 813 400,94</td>
</tr>
<tr>
    <td>mas</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mas_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mas_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mer</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mer_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mfe</td>
    <td>¤ 1 813 400.94</td>
    <td>US$ 1 813 400.94</td>
</tr>
<tr>
    <td>mfe_MU</td>
    <td>Rs 1 813 401</td>
    <td>US$ 1 813 400.94</td>
</tr>
<tr>
    <td>mg</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mg_MG</td>
    <td>Ar1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mgh</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>mgh_MZ</td>
    <td>MTn 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>mgo</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>mgo_CM</td>
    <td>FCFA 1,813,401</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>mk</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>mk_MK</td>
    <td>ден 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ml</td>
    <td>18,13,400.94¤</td>
    <td>18,13,400.94$</td>
</tr>
<tr>
    <td>ml_IN</td>
    <td>18,13,400.94₹</td>
    <td>18,13,400.94$</td>
</tr>
<tr>
    <td>mn</td>
    <td>¤ 1,813,400.94</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>mn_Cyrl</td>
    <td>¤ 1,813,400.94</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>mn_Cyrl_MN</td>
    <td>₮ 1,813,401</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>mr</td>
    <td>¤१,८१३,४००.९४</td>
    <td>$१,८१३,४००.९४</td>
</tr>
<tr>
    <td>mr_IN</td>
    <td>₹१,८१३,४००.९४</td>
    <td>$१,८१३,४००.९४</td>
</tr>
<tr>
    <td>ms</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ms_Latn</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ms_Latn_BN</td>
    <td>$ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>ms_Latn_MY</td>
    <td>RM1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ms_Latn_SG</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mt</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mt_MT</td>
    <td>€1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>mua</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>mua_CM</td>
    <td>FCFA1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>my</td>
    <td>¤ ၁,၈၁၃,၄၀၀.၉၄</td>
    <td>US$ ၁,၈၁၃,၄၀၀.၉၄</td>
</tr>
<tr>
    <td>my_MM</td>
    <td>K ၁,၈၁၃,၄၀၁</td>
    <td>US$ ၁,၈၁၃,၄၀၀.၉၄</td>
</tr>
<tr>
    <td>naq</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>naq_NA</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>nb</td>
    <td>¤ 1 813 400,94</td>
    <td>USD 1 813 400,94</td>
</tr>
<tr>
    <td>nb_NO</td>
    <td>kr 1 813 400,94</td>
    <td>USD 1 813 400,94</td>
</tr>
<tr>
    <td>nb_SJ</td>
    <td>kr 1 813 400,94</td>
    <td>USD 1 813 400,94</td>
</tr>
<tr>
    <td>nd</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>nd_ZW</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ne</td>
    <td>¤१,८१३,४००.९४</td>
    <td>US$१,८१३,४००.९४</td>
</tr>
<tr>
    <td>ne_IN</td>
    <td>₹१,८१३,४००.९४</td>
    <td>US$१,८१३,४००.९४</td>
</tr>
<tr>
    <td>ne_NP</td>
    <td>नेरू१,८१३,४००.९४</td>
    <td>US$१,८१३,४००.९४</td>
</tr>
<tr>
    <td>nl</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_AW</td>
    <td>Afl. 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_BE</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>nl_BQ</td>
    <td>$ 1.813.400,94</td>
    <td>$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_CW</td>
    <td>NAf. 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_NL</td>
    <td>€ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_SR</td>
    <td>$ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nl_SX</td>
    <td>NAf. 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nmg</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>nmg_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>nn</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>nn_NO</td>
    <td>1 813 400,94 kr</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>nnh</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nnh_CM</td>
    <td>FCFA 1.813.401</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>nus</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>nus_SD</td>
    <td>SDG1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>nyn</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>nyn_UG</td>
    <td>USh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>om</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>om_ET</td>
    <td>Br1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>om_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>or</td>
    <td>¤ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>or_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>pa</td>
    <td>¤ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>pa_Arab</td>
    <td>¤ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
    <td>US$ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>pa_Arab_PK</td>
    <td>ر ۱٬۸۱۳٬۴۰۱</td>
    <td>US$ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>pa_Guru</td>
    <td>¤ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>pa_Guru_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>US$ 18,13,400.94</td>
</tr>
<tr>
    <td>pl</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>pl_PL</td>
    <td>1 813 400,94 zł</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>ps</td>
    <td>۱٬۸۱۳٬۴۰۰٫۹۴ ¤</td>
    <td>۱٬۸۱۳٬۴۰۰٫۹۴ US$</td>
</tr>
<tr>
    <td>ps_AF</td>
    <td>۱٬۸۱۳٬۴۰۱ ؋</td>
    <td>۱٬۸۱۳٬۴۰۰٫۹۴ US$</td>
</tr>
<tr>
    <td>pt</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>pt_AO</td>
    <td>1 813 400,94 Kz</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_BR</td>
    <td>R$1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>pt_CV</td>
    <td>1 813 400$94 CVE</td>
    <td>1 813 400$94 US$</td>
</tr>
<tr>
    <td>pt_GW</td>
    <td>1 813 401 CFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_MO</td>
    <td>1 813 400,94 MOP$</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_MZ</td>
    <td>1 813 400,94 MTn</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_PT</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_ST</td>
    <td>1 813 401 Db</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>pt_TL</td>
    <td>1 813 400,94 US$</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>rm</td>
    <td>1’813’400.94 ¤</td>
    <td>1’813’400.94 US$</td>
</tr>
<tr>
    <td>rm_CH</td>
    <td>1’813’400.94 CHF</td>
    <td>1’813’400.94 US$</td>
</tr>
<tr>
    <td>rn</td>
    <td>1.813.400,94¤</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>rn_BI</td>
    <td>1.813.401FBu</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>ro</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>ro_MD</td>
    <td>1.813.400,94 L</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>ro_RO</td>
    <td>1.813.400,94 RON</td>
    <td>1.813.400,94 USD</td>
</tr>
<tr>
    <td>rof</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>rof_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ru</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_BY</td>
    <td>1 813 401 р.</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_KG</td>
    <td>1 813 400,94 сом</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_KZ</td>
    <td>1 813 400,94 ₸</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_MD</td>
    <td>1 813 400,94 L</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_RU</td>
    <td>1 813 400,94 руб.</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>ru_UA</td>
    <td>1 813 400,94 ₴</td>
    <td>1 813 400,94 $</td>
</tr>
<tr>
    <td>rw</td>
    <td>¤ 1.813.400,94</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>rw_RW</td>
    <td>RF 1.813.401</td>
    <td>US$ 1.813.400,94</td>
</tr>
<tr>
    <td>rwk</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>rwk_TZ</td>
    <td>1,813,401TSh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>saq</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>saq_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sbp</td>
    <td>1,813,400.94¤</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>sbp_TZ</td>
    <td>1,813,401TSh</td>
    <td>1,813,400.94US$</td>
</tr>
<tr>
    <td>seh</td>
    <td>1.813.400,94¤</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>seh_MZ</td>
    <td>1.813.400,94MTn</td>
    <td>1.813.400,94US$</td>
</tr>
<tr>
    <td>ses</td>
    <td>1 813 400.94¤</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>ses_ML</td>
    <td>1 813 401CFA</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>sg</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>sg_CF</td>
    <td>FCFA1.813.401</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>shi</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>shi_Latn</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>shi_Latn_MA</td>
    <td>1 813 400,94MAD</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>shi_Tfng</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>shi_Tfng_MA</td>
    <td>1 813 400,94MAD</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>si</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>si_LK</td>
    <td>රු.1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sk</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>sk_SK</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>sl</td>
    <td>¤1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>sl_SI</td>
    <td>€1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>sn</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sn_ZW</td>
    <td>US$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>so</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>so_DJ</td>
    <td>Fdj1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>so_ET</td>
    <td>Br1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>so_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>so_SO</td>
    <td>S1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sq</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>sq_AL</td>
    <td>1 813 401 Lekë</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>sq_MK</td>
    <td>1 813 400,94 den</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>sq_XK</td>
    <td>1 813 400,94 €</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>sr</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Cyrl</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Cyrl_BA</td>
    <td>1.813.400,94 КМ</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Cyrl_ME</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Cyrl_RS</td>
    <td>1.813.401 дин.</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Cyrl_XK</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Latn</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Latn_BA</td>
    <td>1.813.400,94 KM</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Latn_ME</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Latn_RS</td>
    <td>1.813.401 din.</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sr_Latn_XK</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>sv</td>
    <td>1 813 400:94 ¤</td>
    <td>1 813 400:94 US$</td>
</tr>
<tr>
    <td>sv_AX</td>
    <td>1 813 400:94 €</td>
    <td>1 813 400:94 US$</td>
</tr>
<tr>
    <td>sv_FI</td>
    <td>1 813 400:94 €</td>
    <td>1 813 400:94 US$</td>
</tr>
<tr>
    <td>sv_SE</td>
    <td>1 813 400:94 kr</td>
    <td>1 813 400:94 US$</td>
</tr>
<tr>
    <td>sw</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sw_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sw_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>sw_UG</td>
    <td>USh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>swc</td>
    <td>¤1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>swc_CD</td>
    <td>FC1.813.400,94</td>
    <td>US$1.813.400,94</td>
</tr>
<tr>
    <td>ta</td>
    <td>¤ 18,13,400.94</td>
    <td>$ 18,13,400.94</td>
</tr>
<tr>
    <td>ta_IN</td>
    <td>₹ 18,13,400.94</td>
    <td>$ 18,13,400.94</td>
</tr>
<tr>
    <td>ta_LK</td>
    <td>Rs. 18,13,400.94</td>
    <td>$ 18,13,400.94</td>
</tr>
<tr>
    <td>ta_MY</td>
    <td>RM 1,813,400.94</td>
    <td>$ 1,813,400.94</td>
</tr>
<tr>
    <td>ta_SG</td>
    <td>$ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>te</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>te_IN</td>
    <td>₹1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>teo</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>teo_KE</td>
    <td>Ksh1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>teo_UG</td>
    <td>USh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>th</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>th_TH</td>
    <td>฿1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ti</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ti_ER</td>
    <td>Nfk1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>ti_ET</td>
    <td>Br1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>to</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>to_TO</td>
    <td>T$ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>tr</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>tr_CY</td>
    <td>1.813.400,94 €</td>
    <td>1.813.400,94 $</td>
</tr>
<tr>
    <td>tr_TR</td>
    <td>₺1.813.400,94</td>
    <td>$1.813.400,94</td>
</tr>
<tr>
    <td>twq</td>
    <td>1 813 400.94¤</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>twq_NE</td>
    <td>1 813 401CFA</td>
    <td>1 813 400.94US$</td>
</tr>
<tr>
    <td>tzm</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>tzm_Latn</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>tzm_Latn_MA</td>
    <td>1 813 400,94 MAD</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>uk</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>uk_UA</td>
    <td>1 813 400,94 ₴</td>
    <td>1 813 400,94 USD</td>
</tr>
<tr>
    <td>ur</td>
    <td>¤1,813,400.94‎</td>
    <td>$1,813,400.94‎</td>
</tr>
<tr>
    <td>ur_IN</td>
    <td>₹ ۱۸,۱۳,۴۰۰.۹۴</td>
    <td>$ ۱۸,۱۳,۴۰۰.۹۴</td>
</tr>
<tr>
    <td>ur_PK</td>
    <td>Rs1,813,401‎</td>
    <td>$1,813,400.94‎</td>
</tr>
<tr>
    <td>uz</td>
    <td>¤ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>uz_Arab</td>
    <td>¤ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
    <td>US$ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>uz_Arab_AF</td>
    <td>؋ ۱٬۸۱۳٬۴۰۱</td>
    <td>US$ ۱٬۸۱۳٬۴۰۰٫۹۴</td>
</tr>
<tr>
    <td>uz_Cyrl</td>
    <td>¤ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>uz_Cyrl_UZ</td>
    <td>сўм 1 813 401</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>uz_Latn</td>
    <td>¤ 1 813 400,94</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>uz_Latn_UZ</td>
    <td>soʻm 1 813 401</td>
    <td>US$ 1 813 400,94</td>
</tr>
<tr>
    <td>vai</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vai_Latn</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vai_Latn_LR</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vai_Vaii</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vai_Vaii_LR</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vi</td>
    <td>1.813.400,94 ¤</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>vi_VN</td>
    <td>1.813.401 ₫</td>
    <td>1.813.400,94 US$</td>
</tr>
<tr>
    <td>vun</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>vun_TZ</td>
    <td>TSh1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>xog</td>
    <td>1,813,400.94 ¤</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>xog_UG</td>
    <td>1,813,401 USh</td>
    <td>1,813,400.94 US$</td>
</tr>
<tr>
    <td>yav</td>
    <td>1 813 400,94 ¤</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>yav_CM</td>
    <td>1 813 401 FCFA</td>
    <td>1 813 400,94 US$</td>
</tr>
<tr>
    <td>yo</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>yo_BJ</td>
    <td>CFA1,813,401</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>yo_NG</td>
    <td>₦1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zgh</td>
    <td>1 813 400,94¤</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>zgh_MA</td>
    <td>1 813 400,94MAD</td>
    <td>1 813 400,94US$</td>
</tr>
<tr>
    <td>zh</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hans</td>
    <td>¤ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hans_CN</td>
    <td>￥ 1,813,400.94</td>
    <td>US$ 1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hans_HK</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hans_MO</td>
    <td>MOP$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hans_SG</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hant</td>
    <td>¤1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hant_HK</td>
    <td>$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hant_MO</td>
    <td>MOP$1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zh_Hant_TW</td>
    <td>NT$1,813,400.94</td>
    <td>$1,813,400.94</td>
</tr>
<tr>
    <td>zu</td>
    <td>¤1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
<tr>
    <td>zu_ZA</td>
    <td>R1,813,400.94</td>
    <td>US$1,813,400.94</td>
</tr>
</table>
</div>
</div>

</div>
</div>
