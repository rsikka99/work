/**
 * This file handles the mapping devices grid on the rms upload mapping step
 */
require([
    'jquery',
    'app/legacy/hardware-library/manage-devices/DeviceModal',
    'app/components/jqGrid/DeviceMapping/DeviceMappingGrid',
    'jqgrid',
    'bootstrap.modal.manager'
], function ($, DeviceModal, DeviceMappingGrid)
{
    'use strict';

    $(function ()
    {
        var $mappingGrid = $('#deviceMappingGrid');

        var deviceMappingGridInstance = new DeviceMappingGrid($mappingGrid, {
            'url'        : '/rms-uploads/mapping/list',
            'rmsUploadId': function ()
            {
                return rmsUploadId;
            }
        });

        /**
         * Bind the create master device button
         */
        $mappingGrid.on("click", ".js-create-master-device", function (event)
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

                $(createDeviceModal).on('DeviceModal.saved', function (masterDeviceId)
                {
                    deviceMappingGridInstance.mapDevice({
                        "masterDeviceId"  : masterDeviceId,
                        "deviceInstanceId": $thisElement.data('device-instance-id'),
                        "complete"        : function ()
                        {
                            deviceMappingGridInstance.reloadGrid();
                        }
                    });
                });

                createDeviceModal.show();
            });
        });

        /**
         * Bind the edit master device button
         */
        $mappingGrid.on("click", ".js-edit-master-device", function (event)
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

                $(editDeviceModal).on('DeviceModal.saved', function ()
                {
                    deviceMappingGridInstance.reloadGrid();
                });

                editDeviceModal.show();
            });
        });

    });
});