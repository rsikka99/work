define([
    'jquery',
    'bluebird'
], function ($, Promise)
{
    'use strict';
    var TonerService = {};

    TonerService.urls = {
        "saveToner"        : "/hardware-library/toners/save",
        "deleteToner"      : "/hardware-library/toners/delete",
        "deviceAddToner"   : "/hardware-library/devices/toners",
        "deviceRemoveToner": "/hardware-library/devices/toners/remove",
        "findToner"        : "/hardware-library/toners/load-form"
    };

    /**
     * Saves a toner
     *
     * @param tonerModel
     * @returns {Promise}
     */
    TonerService.saveToner = function (tonerModel)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                url      : TonerService.urls.saveToner,
                type     : "post",
                dataType : "json",
                data     : tonerModel,
                'success': function (data)
                {
                    resolve(data.tonerId);
                },
                'error'  : function (xhr)
                {
                    var data = $.parseJSON(xhr.responseText);
                    error(data)
                }
            });
        });
    };

    TonerService.assignTonerToDevice = function (tonerId, deviceId)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                url      : TonerService.urls.deviceAddToner,
                type     : "post",
                dataType : "json",
                data     : {
                    tonerId       : tonerId,
                    masterDeviceId: deviceId
                },
                'success': function (data)
                {
                    resolve();
                },
                'error'  : function (xhr)
                {
                    var data = $.parseJSON(xhr.responseText);
                    error(data)
                }
            });
        });
    };

    TonerService.unassignTonerFromDevice = function (tonerId, deviceId)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                url      : TonerService.urls.deviceAddToner,
                type     : "post",
                dataType : "json",
                data     : {
                    tonerId       : tonerId,
                    masterDeviceId: deviceId
                },
                'success': function (data)
                {
                    resolve();
                },
                'error'  : function (xhr)
                {
                    var data = $.parseJSON(xhr.responseText);
                    error(data)
                }
            });
        });
    };

    TonerService.approveToner = function (tonerId)
    {

    };

    TonerService.approveTonerAssignment = function (tonerId, deviceId)
    {

    };

    return TonerService;
});