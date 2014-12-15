require(['jquery', 'jqgrid'], function ($)
{
    $(function ()
    {
        /**
         * Select Upload Grid
         */
        $("#selectRmsUploadGrid").jqGrid({
            url       : TMTW_BASEURL + 'proposalgen/fleet/rms-upload-list',
            datatype  : 'json',
            height    : 'auto',
            jsonReader: {repeatitems: false},
            pager     : '#selectRmsUploadGridPager',
            rowList   : [15, 30, 50, 100],
            rowNum    : 15,
            sortorder : 'asc',
            sortname  : 'validRowCount',
            colModel  : [
                //@formatter:off
                { width: 10,  name: 'id',              index: 'id',              hidden: true                                                           },
                { width: 190, name: 'uploadDate',      index: 'uploadDate',      label: 'Date Uploaded', title: false, sortable: true                   },
                { width: 80,  name: 'providerName',    index: 'rmsProviderId',   label: 'Type',          title: false, sortable: true                   },
                { width: 350, name: 'fileName',        index: 'fileName',        label: 'File',          title: false, sortable: true                   },
                { width: 75,  name: 'validRowCount',   index: 'validRowCount',   label: 'Valid',         title: false, sortable: true,  align: 'right'  },
                { width: 75,  name: 'invalidRowCount', index: 'invalidRowCount', label: 'Invalid',       title: false, sortable: true,  align: 'right'  },
                { width: 100, name: 'action',          index: 'action',          label: 'Select',        title: false, sortable: false, align: 'center' }
                //@formatter:on
            ],

            gridComplete: function ()
            {
                // Get the grid object (cache in variable)
                var grid = $(this);
                var ids = grid.getDataIDs();
                var selectedUploadId = $("#selectedRmsUploadId").val();
                for (var i = 0; i < ids.length; i++)
                {
                    // Get the data so we can use and manipulate it.
                    var row = grid.getRowData(ids[i]);

                    if (selectedUploadId != row.id)
                    {
                        row.action = '<button title="Select Upload" type="submit" name="selectRmsUploadId" class="btn btn-sm btn-block btn-primary" value="' + row.id + '">Select</button>';
                    }
                    else
                    {
                        row.action = '<button title="Selected" type="button" disabled="disabled" class="btn btn-sm btn-block btn-success" value="-1">Selected</button>';
                    }
                    grid.setRowData(ids[i], row);
                }
            }
        });


    });
});