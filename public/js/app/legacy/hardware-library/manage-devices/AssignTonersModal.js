define([
    'jquery',
    'underscore',
    'accounting',
    'app/Templates',
    'app/legacy/hardware-library/manage-devices/TonerService',
    'app/components/jqGrid/DeviceModal/AssignTonersGrid',
    'bootstrap.modal.manager',
    'jquery.typewatch',
    '../../../components/Select2/Manufacturer',
    '../../../components/Select2/TonerColor'
], function ($, _, accounting, Template, TonerService, AssignTonersGrid)
{
    'use strict';
    var AssignTonersModal_InstanceIdCounter = 0;

    /**
     *
     * @param options
     * @constructor
     */
    var AssignTonersModal = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        AssignTonersModal_InstanceIdCounter++;
        this.id = AssignTonersModal_InstanceIdCounter;
        var assignTonersModalInstance = this;


        var settings = _.extend({
            "assignTonersModal"  : {},
            "deviceModalInstance": {},
            "onModalClose"       : false
        }, _.pick((options || {}), ['deviceModalInstance', 'assignTonersModal', 'onModalClose']) || {});

        /**
         * Class Members
         */
        this.$modal = settings.assignTonersModal;
        this.deviceId = settings.deviceId;
        this.deviceModalInstance = settings.deviceModalInstance;
        this.modalOptions = {
            show: false
        };

        this.init();
    };

    /**
     * A list of urls that the modal uses
     */
    AssignTonersModal.prototype.urls = {
        "tonerList": "/hardware-library/devices/available-toners-list"
    };

    /**
     * Shows the modal
     */
    AssignTonersModal.prototype.show = function ()
    {
        if ($(window).width() > 960)
        {
            this.modalOptions.width = 960;
        }
        else
        {
            this.modalOptions.width = undefined;
        }

        this.$modal.modal(this.modalOptions);
        this.$modal.modal('show');
    };

    /**
     * Hides the modal
     */
    AssignTonersModal.prototype.hide = function ()
    {
        this.$modal.modal('hide');
    };

    /**
     * Initializes the modal
     */
    AssignTonersModal.prototype.init = function ()
    {
        if ($(window).width() > 960)
        {
            this.modalOptions.width = 960;
        }
        else
        {
            this.modalOptions.width = undefined;
        }

        this.$modal.modal(this.modalOptions);
        this.initAssignTonersGrid();
    };

    /**
     * Initializes the grid
     */
    AssignTonersModal.prototype.initAssignTonersGrid = function ()
    {
        var assignTonersModalInstance = this;
        assignTonersModalInstance.$assignTonersGrid = $('.js-assign-toners-grid');
        var $assignTonersGridParent = assignTonersModalInstance.$assignTonersGrid.parent();
        var $availableTonersModal = $('#availableTonersModal');

        var $filterManufacturer = this.$modal.find(".js-filter-manufacturer");
        var $filterTonerColor = this.$modal.find(".js-filter-toner-color");
        var $filterTonerSku = this.$modal.find(".js-filter-toner-sku");
        var $resetFilterButton = this.$modal.find(".js-reset-filter");

        /**
         * Available Toners Manufacturer search
         */
        $filterManufacturer.selectManufacturer({
            placeholder: "Any manufacturer. Type to search."
        });

        /**
         * Toner Color Filter
         */
        $filterTonerColor.selectTonerColor({
            placeholder  : "All available colors",
            tonerConfigId: function ()
            {
                return assignTonersModalInstance.deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val();
            }
        });

        $resetFilterButton.on("click", {
            "$assignTonersGrid": assignTonersModalInstance.$assignTonersGrid
        }, function (e)
        {
            $filterManufacturer.select2("val", '');
            $filterTonerColor.select2("val", '');
            $filterTonerSku.val('');
            e.data.$assignTonersGrid.trigger('reloadGrid');
        });

        $filterManufacturer.on("change", {
            "$assignTonersGrid": assignTonersModalInstance.$assignTonersGrid
        }, function (e)
        {
            e.data.$assignTonersGrid.trigger('reloadGrid');
        });

        $filterTonerColor.on("change", {
            "$assignTonersGrid": assignTonersModalInstance.$assignTonersGrid
        }, function (e)
        {
            e.data.$assignTonersGrid.trigger('reloadGrid');
        });

        $filterTonerSku.on("change", {
            "$assignTonersGrid": assignTonersModalInstance.$assignTonersGrid
        }, function (e)
        {
            e.data.$assignTonersGrid.trigger('reloadGrid');
        });

        /**
         * Setup type watch on toner sku so that we automatically
         * filter when someone types in a sku.
         */
        $filterTonerSku.typeWatch({
            callback     : function (value)
            {
                $filterTonerSku.trigger('change');
            },
            wait         : 750,
            highlight    : true,
            captureLength: 1
        });

        $(window).on('hide.bs.modal', function (e)
        {
            if ($(e.target).hasClass('js-assign-toners-modal'))
            {
                $filterManufacturer.select2("val", '');
                $filterTonerColor.select2("val", '');
                $filterTonerSku.val('');
            }
        });

        $(window).on('show.bs.modal', {
            "$assignTonersGrid": assignTonersModalInstance.$assignTonersGrid
        }, function (e)
        {
            if ($(e.target).hasClass('js-assign-toners-modal'))
            {
                e.data.$assignTonersGrid.trigger('reloadGrid');
            }
        });

        assignTonersModalInstance.$assignTonersGrid.on(
            'click',
            '.js-assign-toner',
            {
                "deviceModalInstance": assignTonersModalInstance.deviceModalInstance
            },
            assignTonersModalInstance.deviceModalInstance.assignTonerButtonHandler
        );

        assignTonersModalInstance.$assignTonersGridObject = new AssignTonersGrid(assignTonersModalInstance.$assignTonersGrid, {
            "url"                 : assignTonersModalInstance.urls.tonerList,
            "filterManufacturerId": function ()
            {
                return $filterManufacturer.val();
            },
            "filterTonerColorId"  : function ()
            {
                return $filterTonerColor.val();
            },
            "filterTonerSku"      : function ()
            {
                return $filterTonerSku.val();
            },
            "tonerColorConfigId"  : function ()
            {
                return assignTonersModalInstance.deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val();
            },
            "tonersList"          : function ()
            {
                return assignTonersModalInstance.deviceModalInstance.$assignedTonersGrid.getTonerList();
            },
            "deviceId"            : function ()
            {
                return assignTonersModalInstance.deviceModalInstance.deviceId;
            }
        });

        return;

        /**
         * Available Toners Grid
         */
        assignTonersModalInstance.$assignTonersGrid.jqGrid(
            {
                url         : assignTonersModalInstance.urls.tonerList,
                autowidth   : true,
                datatype    : 'json',
                height      : 'auto',
                jsonReader  : {repeatitems: false},
                pager       : '#assign-toners-grid-pager',
                rowNum      : 10,
                rowList     : [10, 20, 30, 50],
                toppager    : true,
                postData    : {
                    filterManufacturerId: function ()
                    {
                        return $filterManufacturer.val();
                    },
                    filterTonerColorId  : function ()
                    {
                        return ($filterTonerColor.val() );
                    },
                    filterTonerSku      : function ()
                    {
                        return $filterTonerSku.val();
                    },
                    tonerColorConfigId  : function ()
                    {
                        return assignTonersModalInstance.deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val();
                    },
                    tonersList          : function ()
                    {
                        return assignTonersModalInstance.deviceModalInstance.$assignedTonersGrid.getTonerList().join(",");
                    },
                    masterDeviceId      : function ()
                    {
                        return assignTonersModalInstance.deviceModalInstance.deviceId;
                    }
                },
                colModel    : [
//@formatter:off
{ width: 30,  name: 'id',                         index: 'id',                          hidden: true },
{ width: 30,  name: 'isSystemDevice',             index: 'isSystemDevice',              hidden: true },
{ width: 30,  name: 'deviceTonersIsSystemDevice', index: 'deviceTonersIsSystemDevice',  hidden: true },
{ width: 40,  name: 'tonerColorId',               index: 'tonerColorId',                hidden: true },
{ width: 80,  name: 'dealerSku',                  index: 'dealerSku',                   hidden: true },
{ width: 80,  name: 'systemSku',                  index: 'systemSku',                   hidden: true },
{ width: 55,  name: 'manufacturerId',             index: 'manufacturerId',              hidden: true },
{ width: 120, name: 'dealerCost',                 index: 'dealerCost',                  hidden: true },
{ width: 120, name: 'systemCost',                 index: 'systemCost',                  hidden: true },

{ width: 70,  name: 'tonerColorIdModified',       index: 'tonerColorId',               label: 'Color',                               align: 'center', sortable: true  },
{ width: 80,  name: 'skuModified',                index: 'dealerSku',                  label: '(' + dealerSkuName + ')<br/>OEM SKU'                                   },
{ width: 213, name: 'manufacturer',               index: 'manufacturer',               label: 'Manufacturer',                                         sortable: true  },
{ width: 250, name: 'device_list',                index: 'device_list',                label: 'Machine Compatibility'                                                 },
{ width: 60,  name: 'yield',                      index: 'yield',                      label: 'Yield',                               align: 'right',  sortable: true  },
{ width: 100, name: 'costModified',               index: 'dealerCost',                 label: 'Cost<br/>(System Cost)',              align: 'right',  sortable: true  },
{ width: 80,  name: 'action',                     index: 'action',                     label: 'Action',                              align: 'center', sortable: false }
//@formatter:on
                ],
                gridComplete: function ()
                {
                    var grid = assignTonersModalInstance.$assignTonersGrid.jqGrid();
                    var ids = grid.getDataIDs();

                    for (var i = 0; i < ids.length; i++)
                    {
                        var currentRowId = ids[i];
                        var currentRow = grid.getRowData(currentRowId);

                        currentRow.costModified = Template.jqGrid.tonerCost({
                            dealerCost: (currentRow.dealerCost == '') ? "-" : accounting.formatMoney(currentRow.dealerCost),
                            systemCost: accounting.formatMoney(currentRow.systemCost)
                        });

                        currentRow.skuModified = Template.jqGrid.tonerSku({
                            dealerSku: (currentRow.dealerSku == '') ? "-" : currentRow.dealerSku,
                            systemSku: currentRow.systemSku
                        });

                        var $assignButton = $(document.createElement('button'));
                        $assignButton
                            .addClass('btn btn-xs btn-success')
                            .attr('data-toner-id', currentRow.id)
                            .html('Assign');


                        if ($.inArray(currentRow.id.toString(), assignTonersModalInstance.deviceModalInstance.$assignedTonersGrid.getTonerList()) > -1)
                        {
                            $assignButton
                                .attr('disabled', 'disabled')
                                .html('Assigned');

                            currentRow.action = $assignButton.prop('outerHTML');
                        }
                        else
                        {
                            $assignButton
                                .addClass('js-assign-toner')
                                .html('Assign');
                            currentRow.action = $assignButton.prop('outerHTML');
                        }

                        /**
                         * This is the number of Devices to list before the view all toggle takes into effect
                         * @type {number}
                         */
                        var max = 1;

                        /**
                         * This is the final container that everything will be in
                         * @type {string}
                         */
                        var deviceListCollapsibleContainer = '<div class="collapsible-container" style="text-align: left; width: 200px;">';
                        var compatibleDevices = currentRow.device_list.split(";,");
                        for (var j = 0; j < compatibleDevices.length; j++)
                        {
                            var device = compatibleDevices[j];
                            if (j == max)
                            {
                                deviceListCollapsibleContainer += '<div class="inner-container" style="display: none;">';
                            }
                            deviceListCollapsibleContainer += device + '<br />';
                        }
                        if (compatibleDevices.length > max)
                        {
                            deviceListCollapsibleContainer += '</div><a class="js-view-device-list">View All...</a>';
                        }
                        currentRow.device_list = deviceListCollapsibleContainer;

                        /**
                         * Toner Color Image
                         */
                        var tonerColorId = parseInt(currentRow.tonerColorId);
                        currentRow.tonerColorIdModified = Template.jqGrid.tonerColor({
                            "classes"  : Template.data.tonerColors[tonerColorId].class,
                            "source"   : Template.data.tonerColors[tonerColorId].image,
                            "colorName": Template.data.tonerColors[tonerColorId].name
                        });
                        grid.setRowData(currentRowId, currentRow);
                    }
                }
            }
        );


        $('#assign-toners-grid_toppager_center').hide();
        assignTonersModalInstance.$assignTonersGrid.navGrid('#assign-toners-grid_toppager', {
            edit   : false,
            add    : false,
            del    : false,
            search : false,
            refresh: false
        });

        /**
         * Create New
         */
        assignTonersModalInstance.$assignTonersGrid.navButtonAdd('#assign-toners-grid_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                var tonerForm = new TonerForm({
                    isAllowed    : this.isAllowed,
                    tonerConfigId: assignTonersModalInstance.deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val()
                });

                $(tonerForm).on('toner-form.saved', function (event, tonerId)
                {
                    TonerService.assignTonerToDevice(tonerId, assignTonersModalInstance.deviceModalInstance.deviceId).then(function ()
                    {
                        assignTonersModalInstance.deviceModalInstance.deviceModalInstance.$assignedTonersGrid.addToner(tonerId);
                    }, function ()
                    {
                        alert("There was an error automatically assigning the toner to your device. Please find and assign it manually.")
                    }).finally(function ()
                    {
                        assignTonersModalInstance.deviceModalInstance.reloadTonersGrids();
                    });

                });

                tonerForm.show();
            },
            position     : "last"
        });

        /**
         * Edit
         */
        assignTonersModalInstance.$assignTonersGrid.navButtonAdd('#assign-toners-grid_toppager', {
            caption      : "Edit",
            buttonicon   : "ui-icon-pencil",
            onClickButton: function ()
            {
                var rowData = assignTonersModalInstance.$assignTonersGrid.jqGrid('getGridParam', 'selrow');

                if (rowData)
                {
                    var data = assignTonersModalInstance.$assignTonersGrid.jqGrid('getRowData', rowData);

                    var tonerForm = new TonerForm({
                        isAllowed    : this.isAllowed,
                        tonerId      : data.id,
                        tonerConfigId: assignTonersModalInstance.deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val()
                    });

                    $(tonerForm).on('toner-form.saved', function (event, tonerId)
                    {
                        assignTonersModalInstance.$assignTonersGrid.trigger('reloadGrid');
                    });

                    tonerForm.show();
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        });
    };

    return AssignTonersModal;
});