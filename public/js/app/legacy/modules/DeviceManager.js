require(function ()
{
    /**
     * The goal of the device manager is to provide a single interface for interacting with the API.
     * All other JavaScript should be using this object instead of making it's own API calls
     *
     * @constructor
     */
    var DeviceManager = function ()
    {
        // Empty constructor
    };

    /**
     * A list of URLs that are used for API calls
     */
    DeviceManager.prototype.urls = {
        "list"  : "/devices",
        "view"  : "/devices/{deviceId}",
        "create": "/devices/create",
        "delete": "/devices/{deviceId}/delete",
        "save"  : "/devices/{deviceId}/save"
    };

    /**
     * View Device List API Handler
     *
     * @param options
     */
    DeviceManager.prototype.fetchList = function (options)
    {
        var ajaxOptions = _.extend({
            url     : this.urls.list,
            type    : "GET",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * View Device API Handler
     *
     * @param options
     */
    DeviceManager.prototype.fetch = function (options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.view, {"deviceId": deviceId}),
            type    : "GET",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * Create Device API Handler
     *
     * @param data
     * @param options
     */
    DeviceManager.prototype.doCreate = function (data, options)
    {
        var ajaxOptions = _.extend({
            url     : this.urls.create,
            data    : {
                "data": data
            },
            type    : "POST",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * Save Device API Handler
     *
     * @param deviceId
     * @param formData
     * @param options
     */
    DeviceManager.prototype.doSave = function (deviceId, formData, options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.save, {"deviceId": deviceId}),
            data    : formData,
            type    : "POST",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * Delete Device API Handler
     *
     * @param deviceId
     * @param options
     */
    DeviceManager.prototype.doDelete = function (deviceId, options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.delete, {"deviceId": deviceId}),
            type    : "POST",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    return DeviceManager;
});