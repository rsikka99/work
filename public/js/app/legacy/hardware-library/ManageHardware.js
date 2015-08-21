if (!window.hardware_type) {
    window.hardware_type = 'computers';
}

require(['jquery', 'jqgrid', 'bootstrap.modal.manager'], function ($)
{
    'use strict';

    var $hardwareListGrid = $("#hardwareGrid");
    var $hardwareListGridParent = $hardwareListGrid.parent();
    var $canSellCheckbox = $("#can-sell");
    var $unapprovedCheckbox = $("#unapproved");
    var $searchColumnDropdown = $("#filter-index");
    var $searchTextInput = $("#filter-value");

    $(window).bind('resize', function ()
    {
        $hardwareListGrid.setGridWidth($hardwareListGridParent.width(), true);
    }).trigger('resize');

    /**
     * Bind the create new button
     */
    $(document).on("click", ".js-create-new", function (event)
    {
        var $thisElement = $(this);
        $(".js-create-new").prop('disabled', true);
        var hardwareId = null;
        var isAllowed = $thisElement.attr('data-is-allowed');

        require(['app/legacy/hardware-library/HardwareModal'], function (HardwareModal)
        {
            var createModal = new HardwareModal({
                "hardwareId"      : hardwareId,
                "isAllowed"     : isAllowed,
                "onModalClose"  : function ()
                {
                    $(".js-create-new").prop('disabled', false);
                }
            });

            $(createModal).on('HardwareModal.saved', function ()
            {
                $hardwareListGrid.trigger("reloadGrid");
            });

            createModal.show();
        });
    });

    /**
     * Bind the edit button
     */
    $(document).on("click", ".js-edit", function (event)
    {
        var $thisElement = $(this);
        $(".js-edit").prop('disabled', true);
        var hardwareId = $thisElement.attr('data-id');
        var isAllowed = $thisElement.attr('data-is-allowed');
        require(['app/legacy/hardware-library/HardwareModal'], function (HardwareModal)
        {
            var editModal = new HardwareModal({
                "hardwareId"      : hardwareId,
                "isAllowed"     : isAllowed,
                "onModalClose"  : function ()
                {
                    $(".js-edit").prop('disabled', false);
                }
            });

            $(window).on('hardwareSaveSuccess', function ()
            {
                $hardwareListGrid.trigger("reloadGrid");
            });

            editModal.show();
        });
    });

    $(document).on("click", ".js-delete", function (event)
    {
        var hardwareId = $(this).data('id');
        require(['app/components/ConfirmationModal'], function (ConfirmationModal) {
            var confirmationModal = new ConfirmationModal({
                "title": 'Delete',
                "message": 'Are you sure you want to delete this hardware?',
                "cancel": false,
                "confirm": function () {
                    $.ajax({
                        url: '/hardware-library/' + window.hardware_type + '/delete',
                        type    : "post",
                        dataType: "json",
                        data: {
                            "hardwareId": hardwareId
                        },
                        success: function (data) {
                            $hardwareListGrid.trigger("reloadGrid");
                        }
                    });
                }
            });
        });
    });

    $hardwareListGrid.jqGrid({
        url       : '/api/v1/' + window.hardware_type + '/grid-list',
        datatype  : 'json',
        autowidth : true,
        sortname  : 'name',
        colModel  : [

{ width: 0,  name: 'id',             index: 'id',             label: 'Ids', hidden: true,  sortable: false                       },

{ width: 80, name: 'category',       index: 'category',       label: 'Category',  hidden: false, sortable: true, firstsortorder: 'asc' },
{ width: 380, name: 'name',           index: 'name',           label: 'Name',  hidden: false, sortable: true, firstsortorder: 'asc' },
{ width: 150, name: 'oemSku',         index: 'oemSku',         label: 'OEM SKU',           hidden: false, sortable: false                       },
{ width: 150, name: 'dealerSku',      index: 'dealerSku',      label: 'Dealer SKU Name',   hidden: false, sortable: false                       },
{ width: 50,  name: 'online',         index: 'online',         label: 'Online',            hidden: false, sortable: false                       },
{ width: 99,  name: 'action',         index: 'action',         label: 'Action',            hidden: false, sortable: false                       }

        ],
        jsonReader: {
            repeatitems: false
        },

        height      : 'auto',
        mtype       : "POST",
        postData    : {
            filterSearchIndex: function ()
            {
                return ($searchColumnDropdown.length > 0 && $searchTextInput.length > 0 && $searchColumnDropdown.val() && $searchTextInput.val()) ? $searchColumnDropdown.val() : '';

            },
            filterSearchValue: function ()
            {
                return ($searchColumnDropdown.length > 0 && $searchTextInput.length > 0 && $searchColumnDropdown.val() && $searchTextInput.val()) ? $searchTextInput.val() : '';

            }
        },
        "rowList"   : [10, 15, 25, 50, 100],
        rowNum      : 50,
        pager       : '#hardwareGridPager',
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
                        .attr('title', 'Edit')
                        .addClass('btn btn-warning js-edit')
                        .html('Edit')
                        .attr('data-id', row.id)
                        .attr('data-can-edit', canEdit)
                );

                if (canDelete)
                {
                    $buttonGroup.append(
                        $(document.createElement('a'))
                            .attr('title', 'Delete')
                            .addClass('btn btn-danger js-delete')
                            .html('Delete')
                            .attr('data-id', row.id)
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
        $hardwareListGrid.trigger("reloadGrid");
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

        $hardwareListGrid.trigger("reloadGrid");
    });

    $("#hardwareManagement")
        .bind("saveSuccess", function (e, myName, myValue)
        {
            $("#devicesGrid").trigger("reloadGrid");
        });
});

function online_click(that, id, dealerId) {
    $(that).parent().css('background-color','#00ae5a');
    $.get('/api/'+window.hardware_type+'/online', {id:id, online:that.checked}, function () {
        $(that).parent().animate({backgroundColor:'#ffffff'}, function() {
            sync_shopify(id, dealerId);
        });
    });
}

function sync_shopify(id, dealerId) {
    $('body').append('' +
        '<img ' +
        'style="display:none" ' +
        'src="http://proxy.mpstoolbox.com/shopify/sync_'+window.hardware_type+'.php?origin='+window.location.hostname+'&id='+id+'&dealerId='+dealerId+'&_='+$.now()+'">'
    );
}
