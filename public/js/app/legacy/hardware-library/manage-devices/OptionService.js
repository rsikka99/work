define(['jquery'], function ($)
{
    'use strict';
    var OptionService = function ()
    {
    };

    OptionService.urls = {
        "saveOption"        : "/hardware-library/options/save",
        "deleteOption"      : "/hardware-library/options/delete",
        "deviceAddOption"   : "/hardware-library/devices/options",
        "assignOption"      : "/hardware-library/manage-devices/assign-available-option",
        "unassignOption"    : "/hardware-library/manage-devices/assign-available-option",
        "deviceRemoveOption": "/hardware-library/devices/options/remove",
        "findOption"        : "/hardware-library/options/load-form"
    };

    /**
     * Saves an option
     *
     * @param optionModel
     * @returns {Window.Promise}
     */
    OptionService.saveOption = function (optionModel)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                "url"     : OptionService.urls.saveOption,
                "type"    : "post",
                "dataType": "json",
                "data"    : optionModel,
                "success" : function (data)
                {
                    resolve(data.optionId);
                },
                "error"   : function (xhr)
                {
                    var data = $.parseJSON(xhr.responseText);
                    error(data)
                }
            });
        });
    };

    /**
     * Deletes an option
     *
     * @param {Number} optionId
     * @returns {Window.Promise}
     */
    OptionService.deleteOption = function (optionId)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                url      : OptionService.urls.deleteOption,
                type     : "post",
                dataType : "json",
                data     : {optionId: optionId},
                'success': function (data)
                {
                    resolve(data.optionId);
                },
                'error'  : function (xhr)
                {
                    var data = $.parseJSON(xhr.responseText);
                    error(data)
                }
            });
        });
    };

    OptionService.assignOptionToDevice = function (optionId, deviceId)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                    "url"     : OptionService.urls.assignOption,
                    "type"    : "post",
                    "dataType": "json",
                    "data"    : {
                        "optionId"      : optionId,
                        "masterDeviceId": deviceId
                    },
                    'success' : function (data)
                    {
                        resolve();
                    },
                    'error'   : function (xhr)
                    {
                        var data = $.parseJSON(xhr.responseText);
                        error(data)
                    }
                }
            );

            //$.ajax({
            //    url      : OptionService.urls.deviceAddOption,
            //    type     : "post",
            //    dataType : "json",
            //    data     : {
            //        optionId      : optionId,
            //        masterDeviceId: deviceId
            //    },
            //    'success': function (data)
            //    {
            //        resolve();
            //    },
            //    'error'  : function (xhr)
            //    {
            //        var data = $.parseJSON(xhr.responseText);
            //        error(data)
            //    }
            //});
        });
    };

    OptionService.unassignOptionFromDevice = function (optionId, deviceId)
    {
        return new Promise(function (resolve, error)
        {
            $.ajax({
                    "url"     : OptionService.urls.assignOption,
                    "type"    : "post",
                    "dataType": "json",
                    "data"    : {
                        "optionId"      : optionId,
                        "masterDeviceId": deviceId
                    },
                    'success' : function (data)
                    {
                        resolve();
                    },
                    'error'   : function (xhr)
                    {
                        var data = $.parseJSON(xhr.responseText);
                        error(data)
                    }
                }
            );


            //$.ajax({
            //    url      : OptionService.urls.deviceAddOption,
            //    type     : "post",
            //    dataType : "json",
            //    data     : {
            //        optionId      : optionId,
            //        masterDeviceId: deviceId
            //    },
            //    'success': function (data)
            //    {
            //        resolve();
            //    },
            //    'error'  : function (xhr)
            //    {
            //        var data = $.parseJSON(xhr.responseText);
            //        error(data)
            //    }
            //});
        });
    };

    OptionService.approveOption = function (optionId)
    {

    };

    OptionService.approveOptionAssignment = function (optionId, deviceId)
    {

    };

    return OptionService;
});