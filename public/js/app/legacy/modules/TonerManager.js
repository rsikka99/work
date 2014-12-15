require(function ()
{
    /**
     * The goal of the toner manager is to provide a single interface for interacting with the API.
     * All other JavaScript should be using this object instead of making it's own API calls
     *
     * @constructor
     */
    var TonerManager = function ()
    {
        // Empty constructor
    };

    /**
     * A list of URLs that are used for API calls
     */
    TonerManager.prototype.urls = {
        "list"  : "/toners",
        "view"  : "/toners/{tonerId}",
        "create": "/toners/create",
        "delete": "/toners/{tonerId}/delete",
        "save"  : "/toners/{tonerId}/save"
    };

    /**
     * View Toner List API Handler
     *
     * @param options
     */
    TonerManager.prototype.fetchList = function (options)
    {
        var ajaxOptions = _.extend({
            url     : this.urls.list,
            type    : "GET",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * View Toner API Handler
     *
     * @param options
     */
    TonerManager.prototype.fetch = function (options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.view, {"tonerId": tonerId}),
            type    : "GET",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * Create Toner API Handler
     *
     * @param data
     * @param options
     */
    TonerManager.prototype.doCreate = function (data, options)
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
     * Save Toner API Handler
     *
     * @param tonerId
     * @param formData
     * @param options
     */
    TonerManager.prototype.doSave = function (tonerId, formData, options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.save, {"tonerId": tonerId}),
            data    : formData,
            type    : "POST",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };

    /**
     * Delete Toner API Handler
     *
     * @param tonerId
     * @param options
     */
    TonerManager.prototype.doDelete = function (tonerId, options)
    {
        var ajaxOptions = _.extend({
            url     : URI.expand(this.urls.delete, {"tonerId": tonerId}),
            type    : "POST",
            dataType: "JSON"
        }, _.pick(options, 'error', 'success', 'completed'));

        jQuery.ajax(ajaxOptions);
    };
});