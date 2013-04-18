$(function ()
{
    /***********************************************************************************************************************************************************
     * Excluded devices
     **********************************************************************************************************************************************************/
    $("#excludedTable").jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/fleet/excluded-list',
            postData    : {
                rmsUploadId: rmsUploadId
            },
            datatype    : 'json',
            jsonReader  : { repeatitems: false },
            colModel    : [
                {
                    width   : 100,
                    name    : 'csvLineNumber',
                    index   : 'csvLineNumber',
                    label   : 'CSV Line',
                    title   : true,
                    sortable: true,
                    align   : 'center'
                },
                {
                    width   : 275,
                    name    : 'model',
                    index   : 'model',
                    label   : 'Model Name',
                    title   : true,
                    sortable: false,
                    align   : 'left'
                },
                {
                    width   : 500,
                    name    : 'reason',
                    index   : 'reason',
                    label   : 'Reason',
                    title   : true,
                    sortable: true,
                    align   : 'left'
                },
                {
                    width   : 125,
                    name    : 'serialNumber',
                    index   : 'serialNumber',
                    label   : 'Serial Number',
                    title   : true,
                    sortable: true,
                    align   : 'center',
                    hidden  : true
                },
                {
                    width   : 100,
                    name    : 'ipAddress',
                    index   : 'ipAddress',
                    label   : 'IP Address',
                    title   : true,
                    sortable: true,
                    align   : 'center',
                    hidden  : true
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