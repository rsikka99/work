/**
 * AssignedTonersGrid component to handle it's jqgrid
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
    var AssignedTonersGrid = function (element, options)
    {
        var settings = $.extend({
            deviceId     : false,
            isAllowed    : false,
            tonerList    : false,
            tonerConfigId: false,
            url          : '#'
        }, _.pick(options || {}, ['deviceId', 'tonerList', 'isAllowed', 'tonerConfigId', 'url']));

        var that = this;
        this.instanceId = instanceCounter++;
        this.$rootElement = $(element);

        this.isAllowed = settings.isAllowed;
        this.tonerList = [];

        // Setup post data for the grid url
        var postData = {
            'tonersList': function ()
            {
                return that.tonerList.join(',');
            }
        };

        if (_.isFunction(settings.deviceId))
        {
            postData.masterDeviceId = settings.deviceId;
            this.deviceId = settings.deviceId;
        }
        else
        {
            throw "You must pass in a deviceId parameter and it must be a function that returns a deviceId";
        }

        if (_.isArray(settings.tonerList))
        {
            that.tonerList = settings.tonerList;
        }

        if (_.isFunction(settings.tonerConfigId))
        {
            this.tonerConfigId = settings.tonerConfigId;
        }

        postData.firstLoad = true;

        /**
         * Initialize the grid
         * @type {string}
         */
        this.gridId = 'AssignedTonersGrid' + this.instanceId;
        this.$grid = $(document.createElement('table'));
        this.$grid.attr('id', this.gridId);
        this.$rootElement.append(this.$grid);

        // Create an element for the jQGrid pager
        this.pagerId = 'AssignedTonersGridPager' + this.instanceId;
        this.$pager = $(document.createElement('div'));
        this.$pager.attr('id', this.pagerId);
        this.$rootElement.append(this.$pager);

        this.$grid.jqGrid({
            "url"         : settings.url,
            "autowidth"   : true,
            "colModel"    : this.colModel,
            "datatype"    : 'json',
            "height"      : 'auto',
            "jsonReader"  : {repeatitems: false},
            "pager"       : this.pagerId,
            "rowNum"      : 50,
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

                    that.tonerList.push(parseInt(currentRow.id));

                    currentRow.costModified = Template.jqGrid.tonerCost({
                        dealerCost: (currentRow.dealerCost == '') ? "-" : accounting.formatMoney(currentRow.dealerCost),
                        systemCost: accounting.formatMoney(currentRow.systemCost)
                    });

                    currentRow.skuModified = Template.jqGrid.tonerSku({
                        dealerSku: (currentRow.dealerSku == '') ? "-" : currentRow.dealerSku,
                        systemSku: currentRow.systemSku
                    });

                    if ((that.isAllowed) || (currentRow.is_added=='1') || (currentRow.deviceTonersIsSystemDevice!='1'))
                    {
                        var $unassignButton = $(document.createElement('button'));
                        $unassignButton
                            .addClass('btn btn-xs btn-danger btn-block js-unassign-toner')
                            .attr('data-toner-id', currentRow.id)
                            .html('Unassign');
                        currentRow.action = $unassignButton.prop('outerHTML');
                    } else {
                        var $unassignButton = $(document.createElement('button'));
                        $unassignButton
                            .addClass('btn btn-xs btn-block')
                            .attr('disabled', 'disabled')
                            .html('Unassign');
                        currentRow.action = $unassignButton.prop('outerHTML');
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
                        "colorName": Template.data.tonerColors[tonerColorId].name
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
                ], function (TonerForm, TonerService)
                {
                    var tonerForm = new TonerForm({
                        isAllowed    : that.isAllowed,
                        tonerConfigId: that.tonerConfigId()
                    });

                    $(tonerForm).on('toner-form.saved', function (event, tonerId)
                    {
                        that.addToner(tonerId);
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
     * Adds a toner to the list
     *
     * @returns {Number}
     */
    AssignedTonersGrid.prototype.addToner = function (tonerId)
    {
        tonerId = parseInt(tonerId);
        var index = $.inArray(tonerId, this.tonerList);

        if (index === -1)
        {
            this.tonerList.push(tonerId);
            this.reloadGrid();
        }

        return this.tonerList.length;
    };

    /**
     * Removes a toner from the list
     *
     * @returns {Number}
     */
    AssignedTonersGrid.prototype.removeToner = function (tonerId)
    {
        tonerId = parseInt(tonerId);
        var index = $.inArray(tonerId, this.tonerList);
        if (index > -1)
        {
            this.tonerList = $.grep(this.tonerList, function (value)
            {
                return value != tonerId;
            });
        }

        return this.tonerList.length;
    };

    /**
     * Gets the toner list
     *
     * @returns {Array}
     */
    AssignedTonersGrid.prototype.getTonerList = function ()
    {
        return this.tonerList;
    };

    AssignedTonersGrid.assignOnClick = function (event)
    {

    };

    /**
     * Reloads the grid
     */
    AssignedTonersGrid.prototype.reloadGrid = function ()
    {
        this.$grid.trigger('reloadGrid');

        return this;
    };

    /**
     * Gets the number of available option grids that have been instantiated
     * @returns {number}
     */
    AssignedTonersGrid.prototype.getInstanceCount = function ()
    {
        return instanceCounter;
    };

    AssignedTonersGrid.prototype.colModel = [
//@formatter:off
{ width: 30,  name: 'id',                         index: 'id',                         label: 'Id',                                  hidden: true                     },
{ width: 30,  name: 'is_added',                   index: 'is_added',                   label: 'is_added',                            hidden: true                     },
{ width: 30,  name: 'isSystemDevice',             index: 'isSystemDevice',             label: 'isSystemDevice',                      hidden: true                     },
{ width: 30,  name: 'deviceTonersIsSystemDevice', index: 'deviceTonersIsSystemDevice', label: 'deviceTonersIsSystemDevice',          hidden: true                     },
{ width: 40,  name: 'tonerColorId',               index: 'tonerColorId',               label: 'Color',                               hidden: true                     },
{ width: 80,  name: 'dealerSku',                  index: 'dealerSku',                  label: 'dealerSku',                           hidden: true                     },
{ width: 80,  name: 'systemSku',                  index: 'systemSku',                  label: 'systemSku',                           hidden: true                     },
{ width: 55,  name: 'manufacturerId',             index: 'manufacturerId',             label: 'ManufacturerId',                      hidden: true                     },
{ width: 120, name: 'dealerCost',                 index: 'dealerCost',                 label: 'dealerCost',                          hidden: true                     },
{ width: 120, name: 'systemCost',                 index: 'systemCost',                 label: 'systemCost',                          hidden: true                     },

{ width: 70,  name: 'tonerColorIdModified',       index: 'tonerColorId',               label: 'Color',                               align: 'center', sortable: true  },
{ width: 80,  name: 'skuModified',                index: 'dealerSku',                  label: '(' + dealerSkuName + ')<br/>OEM SKU',                  sortable: true  },
{ width: 233, name: 'manufacturer',               index: 'manufacturer',               label: 'Manufacturer',                                         sortable: true  },
{ width: 250, name: 'device_list',                index: 'device_list',                label: 'Machine Compatibility'                                                 },
{ width: 60,  name: 'yield',                      index: 'yield',                      label: 'Yield',                               align: 'right',  sortable: true  },
{ width: 100, name: 'costModified',               index: 'dealerCost',                 label: 'Cost<br/>(System Cost)',              align: 'right'                   },
{ width: 80,  name: 'action',                     index: 'action',                     label: 'Action',                              align: 'center', sortable: false }
//@formatter:on
    ];

    return AssignedTonersGrid;
});