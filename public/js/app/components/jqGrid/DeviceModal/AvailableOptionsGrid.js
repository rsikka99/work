/**
 * jQuery Plugin to initiate an available-option grid
 *
 * Requires jQuery, jQGrid
 */
define([
    'jquery',
    'underscore',
    'require',
    'require',
    'jqgrid',
    'bootstrap.modal.manager'
], function ($, _, require)
{
    var instanceCounter = 0;
    var pluginName = 'availableOptionGrid';

    /**
     * @param {*|HTMLElement} element
     * @param {Object} options
     * @constructor
     */
    var Plugin = function (element, options)
    {
        var settings = $.extend({
            deviceId        : false,
            filterOptionSku : false,
            filterOptionText: false,
            url             : '#'
        }, _.pick(options || {}, ['deviceId', 'filterOptionSku', 'filterOptionText', 'url']));

        var that = this;
        this.instanceId = instanceCounter++;
        this.$rootElement = $(element);

        // Setup post data for the grid url
        var postData = {};
        if (_.isFunction(settings.deviceId))
        {
            postData.masterDeviceId = settings.deviceId;
        }
        else
        {
            throw "You must pass in a deviceId parameter and it must be a function that returns a deviceId";
        }

        if (_.isFunction(settings.filterOptionSku))
        {
            postData.filterOptionSku = settings.filterOptionSku;
        }

        if (_.isFunction(settings.filterOptionText))
        {
            postData.filterOptionText = settings.filterOptionText;
        }

        /**
         * Initialize the grid
         * @type {string}
         */
        this.gridId = 'AvailableOptionGrid' + this.instanceId;
        this.$grid = $(document.createElement('table'));
        this.$grid.attr('id', this.gridId);
        this.$rootElement.append(this.$grid);

        // Create an element for the jQGrid pager
        this.pagerId = 'AvailableOptionGridPager' + this.instanceId;
        this.$pager = $(document.createElement('div'));
        this.$pager.attr('id', this.pagerId);
        this.$rootElement.append(this.$pager);

        this.$grid.jqGrid({
            "url"         : settings.url,
            "datatype"    : 'json',
            "colModel"    : this.colModel,
            "jsonReader"  : {repeatitems: false},
            "height"      : 'auto',
            "rowNum"      : 15,
            "rowList"     : [15, 30, 50, 100],
            "pager"       : this.pagerId,
            "toppager"    : true,
            "postData"    : postData,
            "gridComplete": function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);

                    /**
                     * Add the assign/unassign button
                     */
                    var $button = $(document.createElement('button'));

                    $button.addClass('btn btn-block');
                    $button.attr('data-option-id', currentRow.id);

                    if (currentRow.assigned == 1)
                    {
                        $button.addClass('btn-danger js-unassign-option').html('Unassign');
                    }
                    else
                    {
                        $button.addClass('btn-success js-assign-option').html('Assign');
                    }

                    currentRow.action = $button.prop('outerHTML');

                    // Set grid data
                    grid.setRowData(currentRowId, currentRow);
                }
            }
        });

        this.$grid.on('click', '.js-assign-option', [this], function ()
        {
            var $this = $(this);
            that.$rootElement.trigger('assign-option', [$this.data('option-id'), $this]);
        });

        this.$grid.on('click', '.js-unassign-option', [this], function ()
        {
            var $this = $(this);
            that.$rootElement.trigger('unassign-option', [$this.data('option-id'), $this]);
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
         * Create New button
         */
        this.$grid.navButtonAdd('#' + this.gridId + '_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                require(['app/legacy/hardware-library/manage-devices/OptionForm'], function (OptionForm)
                {
                    var optionForm = new OptionForm();

                    $(optionForm).on('option-form.saved', function (event, optionId)
                    {
                        that.reloadGrid();
                        that.$rootElement.trigger('available-options-form.option-form.saved', [optionId]);
                    });

                    optionForm.show();
                });
            },
            position     : "last"
        });

        /**
         * Edit button
         */
        this.$grid.navButtonAdd('#' + this.gridId + '_toppager', {
                caption      : "Edit",
                buttonicon   : "ui-icon-pencil",
                onClickButton: function ()
                {
                    require(['app/legacy/hardware-library/manage-devices/OptionForm'], function (OptionForm)
                    {
                        var rowId = that.$grid.jqGrid('getGridParam', 'selrow');
                        if (rowId)
                        {
                            var rowData = that.$grid.jqGrid('getRowData', rowId);
                            var optionId = rowData.id;
                            var optionForm = new OptionForm({
                                    optionId: function ()
                                    {
                                        return optionId;
                                    }
                                }
                            );

                            $(optionForm).on('option-form.saved', function (event, optionId)
                            {
                                that.reloadGrid();

                                that.$rootElement.trigger('available-options-form.option-form.saved', [optionId]);
                            });

                            optionForm.show();
                        }
                        else
                        {
                            $("#alertMessageModal").modal().show()
                        }
                    });
                },
                position     : "last"
            }
        );

        /**
         * Delete button
         */
        this.$grid.navButtonAdd('#' + this.gridId + '_toppager', {
            caption      : "Delete",
            buttonicon   : "ui-icon-trash",
            onClickButton: function ()
            {
                require([
                    'app/legacy/hardware-library/manage-devices/OptionService',
                    'app/legacy/hardware-library/manage-devices/ConfirmationDialog'
                ], function (OptionService, ConfirmationDialog)
                {
                    var rowId = that.$grid.jqGrid('getGridParam', 'selrow');
                    if (rowId)
                    {
                        var rowData = that.$grid.jqGrid('getRowData', rowId);

                        var confirmationDialog = new ConfirmationDialog({
                            title  : 'Delete Option?',
                            message: 'Are you sure you want to delete the option?'
                        });

                        $(confirmationDialog).on('confirmation-dialog.confirmed', function ()
                        {
                            OptionService.deleteOption(rowData.id).then(function ()
                            {
                                that.reloadGrid();
                            }, function ()
                            {
                                // TODO lrobert: need better message here
                                alert('Error deleting option');
                            });
                        });

                        confirmationDialog.show();
                    }
                    else
                    {
                        $("#alertMessageModal").modal().show()
                    }
                });
            },
            position     : "last"
        });
    };

    Plugin.assignOnClick = function (event)
    {

    };

    /**
     * Reloads the grid
     */
    Plugin.prototype.reloadGrid = function ()
    {
        this.$grid.trigger('reloadGrid');

        return this;
    };

    /**
     * Gets the number of available option grids that have been instantiated
     * @returns {number}
     */
    Plugin.prototype.getInstanceCount = function ()
    {
        return instanceCounter;
    };

    Plugin.prototype.colModel = [
//@formatter:off
{ width: 30,  name: 'id',          index: 'id',          label: 'Id',          hidden: true                   },
{ width: 50,  name: 'assigned',    index: 'assigned',    label: 'isAssigned',  hidden: true                   },

{ width: 80,  name: 'oemSku',      index: 'oemSku',      label: 'OEM Sku',     sortable: true, editable: true },
{ width: 100, name: 'dealerSku',   index: 'dealerSku',   label: dealerSkuName, sortable: true, editable: true },
{ width: 250, name: 'name',        index: 'name',        label: 'Option',                      editable: true },
{ width: 280, name: 'description', index: 'description', label: 'Description',                 editable: true },
{ width: 90,  name: 'cost',        index: 'cost',        label: 'Price',                       editable: true, align: 'right', formatter: 'currency', formatoptions: {decimalPlaces: 2, defaultValue: '-'}},
{ width: 80,  name: 'action',      index: 'action',      label: 'Action',      align: 'center'                }
//@formatter:on
    ];


    $.fn[pluginName] = function (options)
    {
        return this.each(function ()
        {
            if (!$.data(this, 'plugin_' + pluginName))
            {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
            else if ($.isFunction(Plugin.prototype[options]))
            {
                $.data(this, 'plugin_' + pluginName)[options]();
            }
        });
    };
});