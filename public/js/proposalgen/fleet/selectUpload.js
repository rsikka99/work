/**
 * show_mapped holds the visibility state of the bottom jqgrid
 */
var show_mapped = false;

$(function ()
{

    /***********************************************************************************************************************************************************
     * UNMAPPED GRID
     **********************************************************************************************************************************************************/
    $("#selectUploadGrid").jqGrid(
        {
            url       : TMTW_BASEURL + 'proposalgen/fleet/select-upload',
            datatype  : 'json',
            colModel  : [
                {
                    width   : 10,
                    name    : 'id',
                    index   : 'id',
                    hidden  : true,
                    label   : 'Upload Id',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 200,
                    name    : 'uploadDate',
                    index   : 'uploadDate',
                    label   : 'Date Uploaded',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 150,
                    name    : 'rmsProviderId',
                    index   : 'rmsProviderId',
                    label   : 'Type',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 150,
                    name    : 'fileName',
                    index   : 'fileName',
                    label   : 'File',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 80,
                    name    : 'validRowCount',
                    index   : 'validRowCount',
                    label   : 'Valid',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 80,
                    name    : 'invalidRowCount',
                    index   : 'invalidRowCount',
                    label   : 'Invalid',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 100,
                    name    : 'action',
                    index   : 'action',
                    label   : 'Select',
                    title   : false,
                    sortable: false,
                    align   : 'center'
                }
            ],
            jsonReader: {
                repeatitems: false
            },
            sortorder : 'asc',
            sortname  : 'validRowCount',

            height      : 'auto',
            rowNum      : 15,
            rowList     : [ 15, 30, 50, 100 ],
            pager       : '#selectUploadGridPager',
            gridComplete: function ()
            {

                // Get the grid object (cache in variable)
                var grid = $(this);
                var ids = grid.getDataIDs();
                var selectedUploadId = $("#selectIds").val();
                for (var i = 0; i < ids.length; i++)
                {
                    // Get the data so we can use and manipualte it.
                    var row = grid.getRowData(ids[i]);

                    if(selectedUploadId != row.id)
                    {
                        row.action = '<input title="Select Upload" type="button" style="width:100%" class="selectUpload btn btn-small btn-primary" onclick="javascript: selectValue(' + row.id + ');" upload-id="' + row.id + '" value="Select" />';
                    }
                    else
                    {
                        row.action = '<input title="Select Upload" type="button" style="width:100%" class="selectUpload btn btn-small btn-success" onclick="javascript: selectValue(' + row.id + ');" upload-id="' + row.id + '" value="Selected" />';
                    }
                    grid.setRowData(ids[i], row);
                }
            }
        }
    );


});
function selectValue($value){
//    $("#selectIds").val($(this).data("upload-id"));
    $("#selectIds").val($value);
    $("#selectUpload").submit();
};
