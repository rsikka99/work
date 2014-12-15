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
    'bootstrap.modal.manager',
    'app/components/Select2/MasterDevice'
], function ($, _, Template, accounting, require)
{
    var instanceCounter = 0;

    /**
     * @param {*|HTMLElement} element
     * @param {Object} options
     * @constructor
     */
    var DeviceMappingGrid = function (element, options)
    {
        var settings = $.extend({
            url        : '#',
            rmsUploadId: false
        }, _.pick(options || {}, ['url', 'rmsUploadId']));

        var that = this;
        this.instanceId = instanceCounter++;
        this.$rootElement = $(element);

        // Setup post data for the grid url
        var postData = {};

        if (_.isFunction(settings.rmsUploadId))
        {
            postData.rmsUploadId = settings.rmsUploadId;
            this.rmsUploadId = settings.rmsUploadId;
        }
        else
        {
            throw "You must pass in an rmsUploadId parameter and it must be a function that returns an rmsUploadId";
        }

        /**
         * Initialize the grid
         * @type {string}
         */
        this.gridId = 'DeviceMappingGrid' + this.instanceId;
        this.$grid = $(document.createElement('table'));
        this.$grid.attr('id', this.gridId);
        this.$rootElement.append(this.$grid);

        // Create an element for the jQGrid pager
        this.pagerId = 'DeviceMappingGridPager' + this.instanceId;
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
            "rowNum"      : 15,
            "rowList"     : [15, 30, 50, 100, 250],
            "sortorder"   : 'desc',
            "sortname"    : 'deviceCount',
            "postData"    : postData,
            "gridComplete": function ()
            {
                // Get the grid object (cache in variable)
                var $grid = $(this);
                var ids = $grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var row = $grid.getRowData(ids[i]);

                    if (row.mappedManufacturer != undefined)
                    {
                        row.mappedManufacturer = row.mappedManufacturer.replace('"', '&quot;');
                    }
                    row.manufacturer = row.manufacturer.replace('"', '&quot;');

                    row.deviceName = '<strong>RMS</strong> Name: ' + row.manufacturer + ' ' + row.modelName + '<br/><em><strong>Raw</strong> Name: ';
                    if (row.rawDeviceName.length > 0)
                    {
                        row.deviceName = row.deviceName + row.rawDeviceName;
                    }
                    else
                    {
                        row.deviceName = row.deviceName + 'N/A';
                    }
                    row.deviceName = row.deviceName + '</em>';

                    /**
                     * This is what toggles the 'master printer
                     * name' field between the auto complete text
                     * box and the 'Click to Remove' text
                     */
                    if (row.useUserData == 1)
                    {
                        /**
                         * Display Message instead of a dropdown
                         */
                        var $removeUnknownDeviceLink = $(document.createElement('a'));
                        $removeUnknownDeviceLink
                            .addClass('btn btn-xs btn-danger btn-block js-remove-unknown-device-button')
                            .html('Click to remove')
                            .attr('data-device-instance-id', row.deviceInstanceId)
                            .on('click', function (event)
                            {
                                event.preventDefault();
                            });

                        row.mapToMasterDevice = 'New Printer Added (' + $removeUnknownDeviceLink.prop('outerHTML') + ')';

                        var $editUnknownMasterDeviceButton = $(document.createElement('button'));
                        $editUnknownMasterDeviceButton
                            .addClass('btn btn-xs btn-success btn-block js-edit-master-device')
                            .html('Edit')
                            .attr('data-device-instance-id', row.deviceInstanceId);

                        row.action = $editUnknownMasterDeviceButton.prop('outerHTML');
                    }
                    else
                    {
                        var $masterDeviceSelect = $(document.createElement('input'))
                            .attr('type', 'text')
                            .attr('value', row.masterDeviceId)
                            .attr('data-device-instance-id', row.deviceInstanceId)
                            .attr('data-device-id', row.masterDeviceId)
                            .addClass('form-control js-select2-master-device');

                        row.mapToMasterDevice = $masterDeviceSelect.prop('outerHTML');

                        // Should we have access?
                        var hasAccess = (isSaveAndApproveAdmin || row.isSystemDevice == 0 || row.isMapped == 0) ? 'true' : 'false';

                        if (row.isMapped == 1)
                        {
                            var $editMasterDeviceButton = $(document.createElement('button'));
                            $editMasterDeviceButton
                                .addClass('btn btn-xs btn-warning btn-block js-edit-master-device')
                                .html('Edit Device')
                                .attr('data-is-allowed', hasAccess)
                                .attr('data-device-id', row.masterDeviceId)
                                .attr('data-rms-upload-row-id', row.rmsUploadRowId)
                                .attr('data-device-instance-id', row.deviceInstanceId);

                            row.action = $editMasterDeviceButton.prop('outerHTML');
                        }
                        else
                        {
                            var $createMasterDeviceButton = $(document.createElement('button'));
                            $createMasterDeviceButton
                                .addClass('btn btn-xs btn-success btn-block js-create-master-device')
                                .html('Create')
                                .attr('data-is-allowed', hasAccess)
                                .attr('data-rms-upload-row-id', row.rmsUploadRowId)
                                .attr('data-device-id', row.masterDeviceId)
                                .attr('data-device-instance-id', row.deviceInstanceId);

                            row.action = $createMasterDeviceButton.prop('outerHTML');
                        }
                    }

                    // Put our new data back into the grid
                    $grid.setRowData(ids[i], row);

                }


                $(".js-select2-master-device").on('change', function ()
                {
                    var $this = $(this);
                    // Only update if there's an actual change
                    if ($this.data('device-id') != $this.val())
                    {
                        that.mapDevice({
                            "masterDeviceId"  : $this.val(),
                            "deviceInstanceId": $this.data('device-instance-id'),
                            "complete"        : function ()
                            {
                                that.reloadGrid();
                            }
                        });
                    }
                }).selectMasterDevice();
            }
        });

        $(window).bind('resize', function ()
        {
            that.$grid.setGridWidth(that.$rootElement.width(), true);
        }).trigger('resize');
    };

    DeviceMappingGrid.prototype.mapDevice = function (options)
    {
        var settings = $.extend({
            "deviceInstanceId": '#',
            "masterDeviceId"  : false,
            "complete"        : false,
            "success"         : false,
            "error"           : false
        }, _.pick(options || {}, ['deviceInstanceId', 'masterDeviceId', 'complete', 'success', 'error']));

        var postData = {
            "deviceInstanceId": settings.deviceInstanceId,
            "masterDeviceId"  : 0
        };

        if (settings.masterDeviceId > 0)
        {
            postData.masterDeviceId = settings.masterDeviceId;
        }

        var ajaxOptions = {
            "url"     : '/rms-uploads/mapping/set-mapped-to',
            "type"    : 'POST',
            "dataType": 'json',
            "data"    : postData
        };

        if (_.isFunction(settings.complete))
        {
            ajaxOptions.complete = settings.complete;
        }

        if (_.isFunction(settings.complete))
        {
            ajaxOptions.success = settings.success;
        }

        if (_.isFunction(settings.complete))
        {
            ajaxOptions.error = settings.error;
        }

        $.ajax(ajaxOptions);
    };


    /**
     * Reloads the grid
     */
    DeviceMappingGrid.prototype.reloadGrid = function ()
    {
        this.$grid.trigger('reloadGrid');

        return this;
    };

    /**
     * Gets the number of available option grids that have been instantiated
     * @returns {number}
     */
    DeviceMappingGrid.prototype.getInstanceCount = function ()
    {
        return instanceCounter;
    };

    DeviceMappingGrid.prototype.colModel = [
//@formatter:off
{ width: 10,  name: 'deviceInstanceId',   index: 'deviceInstanceId',   hidden: true                                                                                              },
{ width: 50,  name: 'isMapped',           index: 'isMapped',           hidden: true                                                                                              },
{ width: 50,  name: 'isSystemDevice',     index: 'isSystemDevice',     hidden: true                                                                                              },
{ width: 10,  name: 'rmsModelId',         index: 'rmsModelId',         hidden: true                                                                                              },
{ width: 10,  name: 'rmsProviderId',      index: 'rmsProviderId',      hidden: true                                                                                              },
{ width: 10,  name: 'rmsUploadRowId',     index: 'rmsUploadRowId',     hidden: true                                                                                              },
{ width: 220, name: 'rawDeviceName',      index: 'rawDeviceName',      hidden: true                                                                                              },
{ width: 160, name: 'manufacturer',       index: 'manufacturer',       hidden: true                                                                                              },
{ width: 160, name: 'modelName',          index: 'modleName',          hidden: true                                                                                              },
{ width: 50,  name: 'masterDeviceId',     index: 'masterDeviceId',     hidden: true                                                                                              },
{ width: 10,  name: 'mappedModelName',    index: 'mappedModelName',    hidden: true                                                                                              },
{ width: 10,  name: 'mappedManufacturer', index: 'mappedManufacturer', hidden: true                                                                                              },
{ width: 10,  name: 'useUserData',        index: 'useUserData',        hidden: true                                                                                              },

{ width: 70,  name: 'deviceCount',        index: 'deviceCount',        label: 'Count',                sortable: true,  align: 'center', sorttype: 'int', firstsortorder: 'desc'  },
{ width: 350, name: 'deviceName',         index: 'manufacturer',       label: 'Device Name',          sortable: true                                                             },
{ width: 240, name: 'mapToMasterDevice',  index: 'mapToMasterDevice',  label: 'Select Master Device', sortable: false                                                            },
{ width: 65,  name: 'action',             index: 'action',             label: 'Action',               sortable: false, align: 'center'                                           }
//@formatter:on
    ];

    return DeviceMappingGrid;
});