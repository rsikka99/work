<?php

function curl_post($url, array $post = NULL, array $headers, array $options = array())
{
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 1,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POSTFIELDS => http_build_query($post),
        CURLOPT_HTTPHEADER => $headers
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));

    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('output.txt', 'wb');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

$headers = <<<HEADER
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0
Accept: */*
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
X-Requested-With: XMLHttpRequest
Referer: http://mycarbonsix.com/reportEdit.aspx?reportId=d3ff46b4-373f-4a99-a817-33ccfd2a392c
Content-Length: 1735
Cookie: loginEmail=; ASP.NET_SessionId=jzaygr1q3tvr1bsl0iqfotd1; sv_LoginPage=; sv_LogoutURL=reportEdit.aspx?reportId=d3ff46b4-373f-4a99-a817-33ccfd2a392c
Connection: keep-alive
Pragma: no-cache
Cache-Control: no-cache
HEADER;

$post_str = <<<POST
jsonString={"ReportId":"d3ff46b4-373f-4a99-a817-33ccfd2a392c","Name":"MPS Toolbox Export","Description":"","ReportType":"standard","ReportTitleTemplate":"MPS Toolbox Report Builder Export","ReportSubtitleTemplate":"","ReportCommentTemplate":"","ReportOptions":{"ShowCounts":false},"ChartingOptions":{"ChartType":"none","ChartXField":"","ChartY1Fields":null},"IsOfficial":false,"Dataset":"c9ec36ff-cf39-4b6b-9ec0-aa690b9fa519","FieldItems":[{"NameCustom":"rmsVendorName-PrintFleet3Standard-1.0","FieldType":"Common","NameInternal":"Group","Label":"","SchemaId":"60db1145-0c9d-4cd1-a301-ec616428e42c","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"Manufacturer","FieldType":"Model","NameInternal":"Manufacturer","Label":"","SchemaId":"405b1620-7d3a-11de-8a39-0800200c9a66","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0}],"GroupFieldCount":"0","SortField":"rmsVendorName-PrintFleet3Standard-1.0","SortOrder":"ascending","ShowRecordCounts":false,"PfSqlNode":"","PfSqlMaster":"","CoverFrontId":"00000000-0000-0000-0000-000000000001","CoverBackId":"00000000-0000-0000-0000-000000000001","ChildReport":[]}
POST;

$new_str = <<<NEW
jsonString={"ReportId":"20578654-0753-45e3-a58a-01809b3d9fae","Name":"MPS Toolbox Export","Description":"","ReportType":"standard","ReportTitleTemplate":"MPS Toolbox Report Builder Export","ReportSubtitleTemplate":"","ReportCommentTemplate":"","ReportOptions":{"ShowCounts":false},"ChartingOptions":{"ChartType":"none","ChartXField":"","ChartY1Fields":null},"IsOfficial":false,"Dataset":"c9ec36ff-cf39-4b6b-9ec0-aa690b9fa519","FieldItems":[{"NameCustom":"rmsVendorName-PrintFleet3Standard-1.0","FieldType":"Common","NameInternal":"Group","Label":"","SchemaId":"60db1145-0c9d-4cd1-a301-ec616428e42c","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"Manufacturer","FieldType":"Model","NameInternal":"Manufacturer","Label":"","SchemaId":"405b1620-7d3a-11de-8a39-0800200c9a66","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"modelName","FieldType":"Model","NameInternal":"Model Name","Label":"","SchemaId":"d73583b0-7d39-11de-8a39-0800200c9a66","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"deviceId","FieldType":"Common","NameInternal":"Device Id","Label":"","SchemaId":"bd84e5e2-2fc3-4ee7-85d5-071c43a3b562","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"rawModelName","FieldType":"Common","NameInternal":"Device String","Label":"","SchemaId":"9ba70787-e243-4327-bccd-69ebac743133","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"Management Status","FieldType":"Common","NameInternal":"Management Status","Label":"","SchemaId":"4f71d57d-2c51-49bf-8538-6d1c3424493c","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"serialNumber","FieldType":"Common","NameInternal":"Serial Number","Label":"","SchemaId":"7b41cc2f-15fc-4cb7-957b-1922c46e7a43","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"ipAddress","FieldType":"Common","NameInternal":"IP Address","Label":"","SchemaId":"dafe9dcb-c04b-4bdb-ba8c-2e1dde4eaad7","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isColor","FieldType":"Model","NameInternal":"Is Color","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb1076","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isCopier","FieldType":"Model","NameInternal":"Is Copier","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb1077","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isFax","FieldType":"Model","NameInternal":"Is Fax","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb1079","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isScanner","FieldType":"Model","NameInternal":"Is Scanner","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb1078","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isPlotter","FieldType":"Model","NameInternal":"Is Plotter","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb107b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"isLabelPrinter","FieldType":"Model","NameInternal":"Is Label Printer","Label":"","SchemaId":"6a0c5776-935c-4d64-b19e-a2bdd0eb107a","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"ppmMono","FieldType":"PPM","NameInternal":"Black PPM","Label":"","SchemaId":"d510dc43-d649-493d-acd6-4ac27d72b697","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"ppmColor","FieldType":"PPM","NameInternal":"Color PPM","Label":"","SchemaId":"a2e54b07-3536-4fd2-9aa6-627aa8456b11","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"introductionDate","FieldType":"Model","NameInternal":"Introduced","Label":"","SchemaId":"7d4e248c-cfe2-4443-8ac2-af6c7f8e2bc7","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"adoptionDate","FieldType":"Common","NameInternal":"Install Date","Label":"","SchemaId":"77e23c44-abc8-412e-9d4a-7f840e00431b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"dutyCycle","FieldType":"Model","NameInternal":"Duty Cycle","Label":"","SchemaId":"4692ee99-090c-4383-ae6f-c337d1f234b5","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"operatingWattage","FieldType":"Power","NameInternal":"Watts Operating","Label":"","SchemaId":"44963a6b-0457-4b7c-8c7e-56332af10447","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"standbyWattage","FieldType":"Power","NameInternal":"Watts Idle","Label":"","SchemaId":"7e4298d0-8ce5-47c0-a336-df1514361281","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterLife","FieldType":"Meters Common","NameInternal":"Total Life Count Start Value","Label":"","SchemaId":"ecacb8c3-1696-4f54-9bd8-9eacbf5643d4","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterLife","FieldType":"Meters Common","NameInternal":"Total Life Count End Value","Label":"","SchemaId":"f4cbcac7-d17d-42a5-811a-3cdadabddac3","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterBlack","FieldType":"Meters Common","NameInternal":"Mono Life Count Start Value","Label":"","SchemaId":"3849ae2e-134b-43e0-a569-86a7a293d580","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterBlack","FieldType":"Meters Common","NameInternal":"Mono Life Count End Value","Label":"","SchemaId":"aeb3158b-b7f4-4a21-8eaa-e8c21cecce75","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterColor","FieldType":"Meters Common","NameInternal":"Color Life Count Start Value","Label":"","SchemaId":"4e3c4d6e-7a06-4bb9-ad1d-ac45f295190b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterColor","FieldType":"Meters Common","NameInternal":"Color Life Count End Value","Label":"","SchemaId":"c9c08a38-4f10-4bfb-966a-191b3137180e","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterPrintBlack","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"PRINTMONO","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterPrintBlack","FieldType":"Meters Custom","NameInternal":"End Value","Label":"PRINTMONO","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterPrintColor","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"PRINTCOLOR","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterPrintColor","FieldType":"Meters Custom","NameInternal":"End Value","Label":"PRINTCOLOR","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterCopyBlack","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"COPIERMONO","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterCopyBlack","FieldType":"Meters Custom","NameInternal":"End Value","Label":"COPIERMONO","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterCopyColor","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"COPIERCOLOR","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterCopyColor","FieldType":"Meters Custom","NameInternal":"End Value","Label":"COPIERCOLOR","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterScan","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"SCAN","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterScan","FieldType":"Meters Custom","NameInternal":"End Value","Label":"SCAN","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"startMeterFax","FieldType":"Meters Custom","NameInternal":"Start Value","Label":"FAXTOTAL","SchemaId":"63649b71-fb6d-47ce-bbb4-97bfcf8a3bf2","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"endMeterFax","FieldType":"Meters Custom","NameInternal":"End Value","Label":"FAXTOTAL","SchemaId":"50cb6376-d74b-4945-9a90-b97bb74c592b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"tonerLevelBlack","FieldType":"Supply Custom","NameInternal":"Supply Level","Label":"TONERLEVEL_BLACK","SchemaId":"4d121d32-3bb5-4ec6-989c-310e29ff5d3b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"tonerLevelCyan","FieldType":"Supply Custom","NameInternal":"Supply Level","Label":"TONERLEVEL_CYAN","SchemaId":"4d121d32-3bb5-4ec6-989c-310e29ff5d3b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"tonerLevelMagenta","FieldType":"Supply Custom","NameInternal":"Supply Level","Label":"TONERLEVEL_MAGENTA","SchemaId":"4d121d32-3bb5-4ec6-989c-310e29ff5d3b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"tonerLevelYellow","FieldType":"Supply Custom","NameInternal":"Supply Level","Label":"TONERLEVEL_YELLOW","SchemaId":"4d121d32-3bb5-4ec6-989c-310e29ff5d3b","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"pageCoverageBlack","FieldType":"Coverage","NameInternal":"Mono Coverage","Label":"","SchemaId":"ffcb46ce-f6fb-4669-a3a1-77b7afd8f9ab","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"colorCoverage","FieldType":"Coverage","NameInternal":"Color Coverage","Label":"","SchemaId":"bfb27089-7e0e-4ba5-9c68-d059f8f9d70a","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"discoveryDate","FieldType":"Common","NameInternal":"First Reported","Label":"","SchemaId":"22504d27-8b7f-4b49-8b4a-b17efabfc39e","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"monitorStartDate","FieldType":"Meters Common","NameInternal":"Total Life Count Start Value Date","Label":"","SchemaId":"a62f02c2-51e8-47bf-901f-5ae8f6570884","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"monitorEndDate","FieldType":"Meters Common","NameInternal":"Total Life Count End Value Date","Label":"","SchemaId":"948996aa-89d9-4be7-b1e5-60a7120fae61","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0},{"NameCustom":"lastSeenDate","FieldType":"Common","NameInternal":"Last Reported","Label":"","SchemaId":"b5d450b6-c2e8-447e-93ca-09f7601080c9","Aggregate":"","Summaries":"","MeterDuration":0,"MeterDurationUnit":"months","MeterDurationOffset":0}],"GroupFieldCount":"0","SortField":"rmsVendorName-PrintFleet3Standard-1.0","SortOrder":"ascending","ShowRecordCounts":false,"PfSqlNode":"","PfSqlMaster":"","CoverFrontId":"00000000-0000-0000-0000-000000000001","CoverBackId":"00000000-0000-0000-0000-000000000001","ChildReport":[]}
NEW;

$post_arr = [];
parse_str($post_str, $post_arr);
$post_json = json_decode($post_arr['jsonString'], true);

$new_arr = [];
parse_str($new_str, $new_arr);
$new_json = json_decode($new_arr['jsonString'], true);
$post_json['FieldItems'] = $new_json['FieldItems'];

$post_arr['jsonString']=json_encode($post_json);
$post_str = http_build_query($post_arr);

$arr=explode("\r\n",$headers);
$headers=[];
array_walk($arr, function($value,$key) {
    global $headers;
    $e=explode(': ',$value,2);
    if (count($e)==2) $headers[$e[0]]=$e[1];
});

$headers['Content-Length'] = ''.strlen($post_str);
array_walk($headers, function(&$value, $key) { $value="{$key}: {$value}"; });

$result = curl_post(
    'http://mycarbonsix.com/Handlers/Report.ashx',
    $post_arr,
    array_values($headers)
);

echo $result;
