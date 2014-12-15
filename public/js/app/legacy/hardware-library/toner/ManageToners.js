require([
    'jquery',
    'require',
    'app/Templates',
    'accounting',
    'jqgrid',
    'jquery.typewatch',
    'app/components/Select2/Manufacturer',
    'app/components/Select2/TonerColor',
    'bootstrap.modal.manager'
], function ($, require, Template, accounting)
{
    $(function ()
    {
        var $tonersGrid = $('#toners-grid');
        var $tonersGridParent = $tonersGrid.parent();

        var $filterManufacturer = $(".js-filter-manufacturer");
        var $filterTonerColor = $(".js-filter-toner-color");
        var $filterTonerSku = $(".js-filter-toner-sku");
        var $resetFilterButton = $(".js-reset-filter");

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
            placeholder: "All available colors"
        });

        $resetFilterButton.on("click", function ()
        {
            $filterManufacturer.select2("val", '');
            $filterTonerColor.select2("val", '');
            $filterTonerSku.val('');
            $tonersGrid.trigger('reloadGrid');
        });

        $filterManufacturer.on("change", function ()
        {
            $tonersGrid.trigger('reloadGrid');
        });

        $filterTonerColor.on("change", function ()
        {
            $tonersGrid.trigger('reloadGrid');
        });

        $filterTonerSku.on("change", function ()
        {
            $tonersGrid.trigger('reloadGrid');
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

        $(window).bind('resize', function ()
        {
            $tonersGrid.setGridWidth($tonersGridParent.width(), true);
        }).trigger('resize');

        /**
         * Available Toners Grid
         */
        $tonersGrid.jqGrid(
            {
                url         : '/hardware-library/all-toners-list',
                autowidth   : true,
                datatype    : 'json',
                height      : 'auto',
                jsonReader  : {repeatitems: false},
                pager       : '#toners-grid-pager',
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
                    }
                },
                colModel    : [
//@formatter:off
{ width: 30,  name: 'id',                         index: 'id',                         label: 'Id',                                 hidden: true },
{ width: 30,  name: 'isSystemDevice',             index: 'isSystemDevice',             label: 'isSystemDevice',                     hidden: true },
{ width: 30,  name: 'deviceTonersIsSystemDevice', index: 'deviceTonersIsSystemDevice', label: 'deviceTonersIsSystemDevice',         hidden: true },
{ width: 40,  name: 'tonerColorId',               index: 'tonerColorId',               label: 'Color',                              hidden: true },
{ width: 120, name: 'dealerSku',                  index: 'dealerSku',                  label: 'dealerSku',                          hidden: true },
{ width: 80,  name: 'systemSku',                  index: 'systemSku',                  label: 'systemSku',                          hidden: true },
{ width: 55,  name: 'manufacturerId',             index: 'manufacturerId',             label: 'ManufacturerId',                     hidden: true },
{ width: 120, name: 'dealerCost',                 index: 'dealerCost',                 label: 'dealerCost',                         hidden: true },
{ width: 120, name: 'systemCost',                 index: 'systemCost',                 label: 'systemCost',                         hidden: true },
{ width: 70,  name: 'tonerColorIdModified',       index: 'tonerColorId',               label: 'Color',                                                 sortable: true },
{ width: 120, name: 'skuModified',                index: 'dealerSku',                  label: '(' + dealerSkuName + ')<br/>OEM SKU' },
{ width: 213, name: 'manufacturer',               index: 'manufacturer',               label: 'Manufacturer',                                          sortable: true },
{ width: 300, name: 'device_list',                index: 'device_list',                label: 'Machine Compatibility' },
{ width: 60,  name: 'yield',                      index: 'yield',                      label: 'Yield',                                 align: 'right', sortable: true },
{ width: 100, name: 'costModified',               index: 'dealerCost',                 label: 'Cost<br/>(System Cost)',                align: 'right', sortable: true }
//@formatter:on
                ],
                gridComplete: function ()
                {
                    var grid = $(this).jqGrid();
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
        // Hide the top paging!
        $('#toners-grid_toppager_center').hide();

        $tonersGrid.navGrid('#toners-grid_toppager', {
            edit   : false,
            add    : false,
            del    : false,
            search : false,
            refresh: false
        });

        //Create New
        $tonersGrid.navButtonAdd('#toners-grid_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                require(['app/legacy/hardware-library/manage-devices/TonerForm'], function (TonerForm)
                {
                    var tonerForm = new TonerForm({
                        isAllowed: isSaveAndApproveAdmin
                    });

                    $(tonerForm).on('toner-form.saved', function (event, tonerId)
                    {
                        $tonersGrid.trigger('reloadGrid');
                    });

                    tonerForm.show();
                });
            },
            position     : "last"
        });

        //Edit
        $tonersGrid.navButtonAdd('#toners-grid_toppager', {
            caption      : "Edit",
            buttonicon   : "ui-icon-pencil",
            onClickButton: function ()
            {
                var rowData = $tonersGrid.jqGrid('getGridParam', 'selrow');

                if (rowData)
                {
                    var data = $tonersGrid.jqGrid('getRowData', rowData);

                    require(['app/legacy/hardware-library/manage-devices/TonerForm'], function (TonerForm)
                    {
                        var tonerForm = new TonerForm({
                            isAllowed: isSaveAndApproveAdmin,
                            tonerId  : data.id
                        });

                        $(tonerForm).on('toner-form.saved', function (event, tonerId)
                        {
                            $tonersGrid.trigger('reloadGrid');
                        });

                        tonerForm.show();
                    });
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        });
    });
});