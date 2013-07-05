function loadHardwareConfigurations()
{
    /**
     * Hardware Configurations
     */
    $('#hardwareConfigurations').jqGrid(
        {
            url         : TMTW_BASEURL + "proposalgen/managedevices/hardware-configuration-list?masterDeviceId=" + masterDeviceId,
            datatype    : 'json',
            colModel    : [
                {
                    width : 130,
                    name  : 'id',
                    index : 'id',
                    label : 'ID',
                    hidden: true
                },
                {
                    width   : 268,
                    name    : 'name',
                    index   : 'name',
                    label   : 'Configuration Name',
                    sortable: true,
                    editable: true
                },
                {
                    width   : 630,
                    name    : 'description',
                    index   : 'description',
                    label   : 'Description',
                    sortable: true,
                    editable: true
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            height      : 'auto',
            rowNum      : 15,
            pager       : '#hardwareConfigurations_Pager',
            toppager    : true,
            gridComplete: function ()
            {
            }
        });
    // Hide the top paging!
    $('#hardwareConfigurations_toppager_center').hide();
    //Add to the top pager the create, edit and delete buttons
    jQuery("#hardwareConfigurations")
        .navGrid('#hardwareConfigurations_toppager', {edit: false, add: false, del: false, search: false, refresh: false})
        //Create New
        .navButtonAdd('#hardwareConfigurations_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                $('#hardwareConfigurationsForm').load(TMTW_BASEURL + 'proposalgen/managedevices/reload-hardware-configurations-form?masterDeviceId=' + masterDeviceId, function ()
                {
                    clearForm("hardwareConfigurationsForm");
                });
                $('#hardwareConfigurationsTitle').html("Add New Configuration");
                $("#hardwareConfigurationsModal").modal('show');
            },
            position     : "last"
        })
        //Edit
        .navButtonAdd('#hardwareConfigurations_toppager', {
            caption      : "Edit",
            buttonicon   : "ui-icon-pencil",
            onClickButton: function ()
            {
                var selectedRow = jQuery("#hardwareConfigurations").jqGrid('getGridParam', 'selrow');
                if (selectedRow)
                {
                    if (selectedRow)
                    {
                        $('#hardwareConfigurationsForm').load(TMTW_BASEURL + 'proposalgen/managedevices/reload-hardware-configurations-form?id=' + selectedRow + "&masterDeviceId=" + masterDeviceId, function ()
                        {
                            $('#hardwareConfigurationsid').val(selectedRow);
                        });
                    }
                    $('#hardwareConfigurationsTitle').html("Edit Configuration");
                    $('#hardwareConfigurationsModal').modal('show');
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        })
        //Delete
        .navButtonAdd('#hardwareConfigurations_toppager', {
            caption      : "Delete",
            buttonicon   : "ui-icon-trash",
            onClickButton: function ()
            {
                var selectedRow = jQuery("#hardwareConfigurations").jqGrid('getGridParam', 'selrow');
                if (selectedRow)
                {
                    $('#deleteId').val(selectedRow);
                    $('#deleteFormName').val('hardwareConfigurations');
                    $('#deleteModal').modal('show');
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        });
};