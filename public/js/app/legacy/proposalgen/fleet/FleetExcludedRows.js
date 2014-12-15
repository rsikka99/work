require(['jquery', 'jqgrid'], function ($)
{
    /**
     * Handles displaying excluded rows
     */
    (function ($, rmsUploadId)
    {
        'use strict';

        $(function ()
        {
            var $excludedTable = $("#rms-upload-excluded-rows-grid");
            var $excludedTableParent = $excludedTable.parent();

            $(window).bind('resize', function ()
            {
                $excludedTable.setGridWidth($excludedTableParent.width(), true);
            }).trigger('resize');

            /***********************************************************************************************************************************************************
             * Excluded devices
             **********************************************************************************************************************************************************/
            $excludedTable.jqGrid(
                {
                    url         : '/rms-uploads/excluded-list',
                    autowidth   : true,
                    datatype    : 'json',
                    height      : "100%",
                    jsonReader  : {repeatitems: false},
                    pager       : '#rms-upload-excluded-rows-grid-pager',
                    postData    : {
                        rmsUploadId: rmsUploadId
                    },
                    rowNum      : 10,
                    rowList     : [5, 10, 20, 30, 100, 250],
                    scrollOffset: 0,
                    colModel    : [
//@formatter:off
{ width: 125, name: 'serialNumber',  index: 'serialNumber',  hidden: true },
{ width: 100, name: 'ipAddress',     index: 'ipAddress',     hidden: true },
{ width: 100, name: 'csvLineNumber', index: 'csvLineNumber', label: 'CSV Line',   title: true, align: 'center', sortable: true },
{ width: 275, name: 'model',         index: 'modelName',     label: 'Model Name', title: true, align: 'left',   sortable: true },
{ width: 500, name: 'reason',        index: 'reason',        label: 'Reason',     title: true, align: 'left',   sortable: true }
//@formatter:on
                    ]
                });
        });
    })($, rmsUploadId);
});