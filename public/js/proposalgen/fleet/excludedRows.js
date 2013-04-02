$(function ()
{
    /***********************************************************************************************************************************************************
     * Excluded devices
     **********************************************************************************************************************************************************/
    $("#excludedTable").jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/fleet/excluded-list',
            datatype    : 'json',
            jsonReader  : { repeatitems: false },
            colModel    : [
                {
                    width   : 200,
                    name    : 'manufacturerName',
                    index   : 'manufacturerName',
                    label   : 'Manufacturer Name',
                    title   : true,
                    sortable: true,
                    align   : 'center'
                },
                {
                    width   : 200,
                    name    : 'modelName',
                    index   : 'modelName',
                    label   : 'Model Name',
                    title   : true,
                    sortable: true,
                    align   : 'center'
                },
                {
                    width   : 350,
                    name    : 'reason',
                    index   : 'reason',
                    label   : 'Reason',
                    title   : true,
                    sortable: false,
                    align   : 'center'
                },
                {
                    width   : 125,
                    name    : 'serialNumber',
                    index   : 'serialNumber',
                    label   : 'Serial Number',
                    title   : true,
                    sortable: false,
                    align   : 'center'
                },
                {
                    width   : 100,
                    name    : 'ipAddress',
                    index   : 'ipAddress',
                    label   : 'IP Address',
                    title   : true,
                    sortable: false,
                    align   : 'center'
                },
                {
                    width: 50,
                    name: 'csvLineNumber',
                    index: 'csvLineNumber',
                    label: 'CSV Line Number',
                    title: true,
                    sortable: false,
                    align: 'center'
                }
            ],
            scrollOffset: 0,
            height      : "100%",
            rowNum      : 10,
            rowList     : [10, 20, 30],
            autowidth   : true,
            pager       : '#excludedPager'
        });
});