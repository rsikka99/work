/**
 * AssignTonersGrid component to handle it's jqgrid
 */
define([
    'jquery',
    'underscore',
    'app/Templates',
    'accounting',
    'require',
    'jqgrid',
    'bootstrap.modal.manager'
], function ($, _, Template, accounting, require)
{
    var instanceCounter = 0;

    /**
     * @param {*|HTMLElement} element
     * @param {Object} options
     * @constructor
     */
    var AssignTonersGrid = function (element, options)
    {
        var settings = $.extend({
            "deviceId"            : false,
            "filterManufacturerId": false,
            "filterTonerColorId"  : false,
            "filterTonerSku"      : false,
            "tonerColorConfigId"  : false,
            "tonersList"          : false,
            "isAllowed"           : false,
            "tonerConfigId"       : false,
            "url"                 : '#'
        }, _.pick(options || {}, [
            "deviceId",
            "filterManufacturerId",
            "filterTonerColorId",
            "filterTonerSku",
            "tonerColorConfigId",
            "tonersList",
            "isAllowed",
            "tonerConfigId",
            "url"
        ]));

        if (!_.isFunction(settings.deviceId))
        {
            throw "You must pass in a deviceId parameter and it must be a function that returns a deviceId";
        }

        var that = this;
        this.instanceId = instanceCounter++;
        this.$rootElement = $(element);

        this.isAllowed = settings.isAllowed;
        this.deviceId = settings.deviceId;

        /**
         * Setting up post data for the grid
         */
        var postData = {
            masterDeviceId: settings.deviceId
        };

        if (_.isFunction(settings.filterManufacturerId))
        {
            postData.filterManufacturerId = settings.filterManufacturerId;
        }

        if (_.isFunction(settings.filterTonerColorId))
        {
            postData.filterTonerColorId = settings.filterTonerColorId;
        }

        if (_.isFunction(settings.filterTonerColorId))
        {
            postData.filterTonerColorId = settings.filterTonerColorId;
        }

        if (_.isFunction(settings.filterTonerSku))
        {
            postData.filterTonerSku = settings.filterTonerSku;
        }

        if (_.isFunction(settings.tonerColorConfigId))
        {
            postData.tonerColorConfigId = settings.tonerColorConfigId;
        }

        if (_.isFunction(settings.tonersList))
        {
            postData.tonersList = function ()
            {
                return settings.tonersList().join(",");
            };
        }

        postData.firstLoad = true;

        /**
         * Initialize the grid elements
         */
        this.gridId = 'AssignTonersGrid' + this.instanceId;
        this.$grid = $(document.createElement('table'));
        this.$grid.attr('id', this.gridId);
        this.$rootElement.append(this.$grid);

        // Create an element for the jQGrid pager
        this.pagerId = 'AssignTonersGridPager' + this.instanceId;
        this.$pager = $(document.createElement('div'));
        this.$pager.attr('id', this.pagerId);
        this.$rootElement.append(this.$pager);


        /**
         * Setup the actual grid
         */
        this.$grid.jqGrid({
            "url"         : settings.url,
            "autowidth"   : true,
            "colModel"    : this.colModel,
            "datatype"    : 'json',
            "height"      : 'auto',
            "jsonReader"  : {repeatitems: false},
            "pager"       : this.pagerId,
            "rowNum"      : 10,
            "toppager"    : true,
            "postData"    : postData,
            "gridComplete": function ()
            {
                var grid = that.$grid.jqGrid();
                that.tonerList = [];


                var ids = that.$grid.jqGrid('getDataIDs');

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = that.$grid.jqGrid('getRowData', currentRowId);

                    currentRow.costModified = (currentRow.dealerCost == '') ? "-" : accounting.formatMoney(currentRow.dealerCost);

                    currentRow.skuModified = Template.jqGrid.tonerSku({
                        dealerSku: (currentRow.dealerSku == '') ? "-" : currentRow.dealerSku,
                        systemSku: currentRow.systemSku
                    });

                    var $assignButton = $(document.createElement('button'));
                    $assignButton
                        .addClass('btn btn-xs btn-success')
                        .attr('data-toner-id', currentRow.id)
                        .html('Assign');


                    if ($.inArray(parseInt(currentRow.id), settings.tonersList()) > -1)
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
                        "classes"  : tonerColorId>6?'':Template.data.tonerColors[tonerColorId].class,
                        "colorName": tonerColorId>6?'':Template.data.tonerColors[tonerColorId].name
                    });

                    grid.setRowData(currentRowId, currentRow);

                }
            }
        });

        this.$grid.on('click', '.js-view-device-list', function (event)
        {
            event.preventDefault();
            var $this = $(this);
            var $innerContainer = $this.parent().find('.inner-container');
            $innerContainer.toggle();
            $this.html($innerContainer.is(':visible') ? 'Collapse...' : 'View All...');
        });

        this.$grid.on('click', '.js-assign-toner', [this], function ()
        {
            var $this = $(this);
            that.$rootElement.trigger('DeviceModal.assign-toner', [$this.data('toner-id'), $this]);
        });

        this.$grid.on('click', '.js-unassign-toner', [this], function ()
        {
            var $this = $(this);
            that.$rootElement.trigger('DeviceModal.unassign-toner', [$this.data('toner-id'), $this]);
        });

return;
        $('#' + this.gridId + '_toppager_center').hide();

        /**
         * Add to the top pager the create, edit and delete buttons
         */
        this.$grid.navGrid('#' + this.gridId + '_toppager', {
            edit   : false,
            add    : false,
            del    : false,
            search : false,
            refresh: false
        });


        /**
         * Create New
         */
        this.$grid.navButtonAdd('#' + this.gridId + '_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                require([
                    'app/legacy/hardware-library/manage-devices/TonerForm',
                    'app/legacy/hardware-library/manage-devices/TonerService'
                ], function (TonerForm)
                {
                    var tonerForm = new TonerForm({
                        isAllowed    : that.isAllowed,
                        tonerConfigId: that.tonerConfigId()
                    });

                    $(tonerForm).on('toner-form.saved', function (event, tonerId)
                    {
                        assignTonersModalInstance.deviceModalInstance.deviceModalInstance.$assignedTonersGrid.addToner(tonerId);
                        that.reloadGrid();
                    });

                    tonerForm.show();
                });
            },
            position     : "last"
        });

        /**
         * Edit
         */
        this.$grid.navButtonAdd('#' + this.gridId + '_toppager', {
            caption      : "Edit",
            buttonicon   : "ui-icon-pencil",
            onClickButton: function ()
            {
                require([
                    'app/legacy/hardware-library/manage-devices/TonerForm'
                ], function (TonerForm)
                {
                    var rowData = that.$grid.jqGrid('getGridParam', 'selrow');

                    if (rowData)
                    {
                        var data = that.$grid.jqGrid('getRowData', rowData);

                        var tonerForm = new TonerForm({
                            isAllowed    : that.isAllowed,
                            tonerId      : data.id,
                            tonerConfigId: that.tonerConfigId()
                        });

                        $(tonerForm).on('toner-form.saved', function (event, tonerId)
                        {
                            that.reloadGrid();
                        });

                        tonerForm.show();
                    }
                    else
                    {
                        $("#alertMessageModal").modal().show();
                    }
                });
            },
            position     : "last"
        });
    };

    /**
     * Reloads the grid
     */
    AssignTonersGrid.prototype.reloadGrid = function ()
    {
        this.$grid.trigger('reloadGrid');

        return this;
    };

    /**
     * Gets the number of available option grids that have been instantiated
     * @returns {number}
     */
    AssignTonersGrid.prototype.getInstanceCount = function ()
    {
        return instanceCounter;
    };

    AssignTonersGrid.prototype.colModel = [
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
{ width: 100, name: 'costModified',               index: 'dealerCost',                 label: 'Your Cost',              align: 'right',  sortable: true  },
{ width: 80,  name: 'action',                     index: 'action',                     label: 'Action',                              align: 'center', sortable: false }
//@formatter:on
    ];

    return AssignTonersGrid;
});