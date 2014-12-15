/**
 * Handles binding edit-master-device
 */
require(['jquery', 'jqgrid', 'bootstrap.modal.manager'], function ($)
{
    'use strict';

    var $deviceListGrid = $("#devicesGrid");
    var $deviceListGridParent = $deviceListGrid.parent();
    var $canSellCheckbox = $("#can-sell");
    var $unapprovedCheckbox = $("#unapproved");
    var $searchColumnDropdown = $("#filter-index");
    var $searchTextInput = $("#filter-value");

    $(window).bind('resize', function ()
    {
        $deviceListGrid.setGridWidth($deviceListGridParent.width(), true);
    }).trigger('resize');

    /**
     * Bind the create master device button
     */
    $(document).on("click", ".js-create-master-device", function (event)
    {
        var $thisElement = $(this);
        $(".js-create-master-device").prop('disabled', true);
        var deviceId = null;
        var rmsUploadRowId = $thisElement.attr('data-rms-upload-row-id');
        var isAllowed = $thisElement.attr('data-is-allowed');

        require(['app/legacy/hardware-library/manage-devices/DeviceModal'], function (DeviceModal)
        {
            var createDeviceModal = new DeviceModal({
                "deviceId"      : deviceId,
                "rmsUploadRowId": rmsUploadRowId,
                "isAllowed"     : isAllowed,
                "onModalClose"  : function ()
                {
                    $(".js-create-master-device").prop('disabled', false);
                }
            });

            $(createDeviceModal).on('DeviceModal.saved', function() {
                $deviceListGrid.trigger("reloadGrid");
            });

            createDeviceModal.show();
        });
    });

    /**
     * Bind the edit master device button
     */
    $(document).on("click", ".js-edit-master-device", function (event)
    {
        var $thisElement = $(this);
        $(".js-edit-master-device").prop('disabled', true);
        var deviceId = $thisElement.attr('data-device-id');
        var rmsUploadRowId = $thisElement.attr('data-rms-upload-row-id');
        var isAllowed = $thisElement.attr('data-is-allowed');
        require(['app/legacy/hardware-library/manage-devices/DeviceModal'], function (DeviceModal)
        {
            var editDeviceModal = new DeviceModal({
                "deviceId"      : deviceId,
                "rmsUploadRowId": rmsUploadRowId,
                "isAllowed"     : isAllowed,
                "onModalClose"  : function ()
                {
                    $(".js-edit-master-device").prop('disabled', false);
                }
            });

            $(editDeviceModal).on('DeviceModal.saved', function() {
                $deviceListGrid.trigger("reloadGrid");
            });

            editDeviceModal.show();
        });
    });

    /**
     * Bind the delete master device button
     */
    $(document).on("click", ".js-delete-master-device", function (event)
    {
        var masterDeviceId = $(this).data('device-id');
        var confirmationModal = new ConfirmationModal(
            {
                "title"  : 'Delete Master Device',
                "message": 'Are you sure you want to delete this master device?',
                "cancel" : false,
                "confirm": function ()
                {
                    $.ajax({
                        url     : '/hardware-library/devices/delete',
                        dataType: "json",
                        data    : {
                            "masterDeviceId": masterDeviceId
                        },
                        success : function (data)
                        {
                            $deviceListGrid.trigger("reloadGrid");
                        }
                    });
                }
            }
        );
    });

    $(window).bind('resize', function ()
    {
        $deviceListGrid.setGridWidth($deviceListGrid.parent().width(), true);
    }).trigger('resize');

    $deviceListGrid.jqGrid({
        url       : '/api/v1/devices/grid-list',
        datatype  : 'json',
        autowidth : true,
        sortname  : 'deviceName',
        colModel  : [
//@formatter:off
{ width: 50,  name: 'id',             index: 'id',             label: 'Master Device Ids', hidden: true,  sortable: false                       },
{ width: 150, name: 'isSystemDevice', index: 'isSystemDevice', label: 'isSystemDevice',    hidden: true                                         },
{ width: 100, name: 'displayname',    index: 'displayname',    label: 'displayname',       hidden: true,  sortable: false                       },

{ width: 460, name: 'modelName',      index: 'deviceName',     label: 'Full Device Name',  hidden: false, sortable: true, firstsortorder: 'asc' },
{ width: 150, name: 'oemSku',         index: 'oemSku',         label: 'OEM SKU',           hidden: false, sortable: false                       },
{ width: 150, name: 'dealerSku',      index: 'dealerSku',      label: 'Dealer SKU Name',   hidden: false, sortable: false                       },
{ width: 99,  name: 'action',         index: 'action',         label: 'Action',            hidden: false, sortable: false                       }
//@formatter:on
        ],
        jsonReader: {
            repeatitems: false
        },

        height      : 'auto',
        rowNum      : 15,
        mtype       : "POST",
        postData    : {
            filterCanSell    : function ()
            {
                return ($canSellCheckbox.length > 0 && $canSellCheckbox[0].checked) ? 'true' : 'false';

            },
            filterUnapproved : function ()
            {
                return ($unapprovedCheckbox.length > 0 && $unapprovedCheckbox[0].checked) ? 'true' : 'false';

            },
            filterSearchIndex: function ()
            {
                return ($searchColumnDropdown.length > 0 && $searchTextInput.length > 0 && $searchColumnDropdown.val() && $searchTextInput.val()) ? $searchColumnDropdown.val() : '';

            },
            filterSearchValue: function ()
            {
                return ($searchColumnDropdown.length > 0 && $searchTextInput.length > 0 && $searchColumnDropdown.val() && $searchTextInput.val()) ? $searchTextInput.val() : '';

            }
        },
        rowList     : [15, 30, 50, 100],
        pager       : '#devicesGridPager',
        gridComplete: function ()
        {
            // Get the grid object (cache in variable)
            var grid = $(this);
            var ids = grid.getDataIDs();
            for (var i = 0; i < ids.length; i++)
            {
                var row = grid.getRowData(ids[i]);
                var canEdit = (row.isSystemDevice == 0 || isSaveAndApproveAdmin) ? 'true' : 'false';

                row.modelName = row.displayname + ' ' + row.modelName;

                var $buttonGroup = $(document.createElement('div'))
                    .addClass('btn-group btn-group-sm btn-group-justified');

                $buttonGroup.append(
                    $(document.createElement('a'))
                        .attr('title', 'Edit Printer')
                        .addClass('btn btn-warning js-edit-master-device')
                        .html('Edit')
                        .attr('data-device-id', row.id)
                        .attr('data-can-edit', canEdit)
                );

                if (canDelete)
                {
                    $buttonGroup.append(
                        $(document.createElement('a'))
                            .attr('title', 'Delete Printer')
                            .addClass('btn btn-danger js-delete-master-device')
                            .html('Delete')
                            .attr('data-device-id', row.id)
                    );
                }

                row.action = $buttonGroup.prop('outerHTML');

                grid.setRowData(ids[i], row);
            }
        }
    });

    /**
     * Search Button
     */
    $('.js-perform-search').on('click', function (e)
    {
        $deviceListGrid.trigger("reloadGrid");
    });

    /**
     * Clear Button
     */
    $('.js-clear-search').on('click', function (e)
    {
        var $closestFormToClearButton = $(this).closest('form');
        if ($closestFormToClearButton.length > 0)
        {
            $closestFormToClearButton[0].reset();
        }

        $deviceListGrid.trigger("reloadGrid");
    });

    /**
     * Master Device Management Events
     */
    $("#masterDeviceManagement")
        .bind("saveSuccess", function (e, myName, myValue)
        {
            $("#devicesGrid").trigger("reloadGrid");
        })
        .bind("approveSuccess", function (e, masterDeviceId)
        {
            $("#manageMasterDeviceModal").modal("hide");
        });
});