define([
    'jquery',
    'select2'
], function ($)
{

    var PluginName = 'selectMasterDevice';

    var Plugin = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {jQuery}
         */
        var $element = $(element);

        // This can only be attached to text inputs
        if ($element.is('input[type="text"]'))
        {
            var settings = $.extend({
                placeholder: 'Search for device',
                url        : '/api/v1/devices'
            }, options || {});


            /**
             * Initialize the select2
             */
            $element.select2({
                placeholder  : settings.placeholder,
                allowClear   : true,
                ajax         : {
                    url     : settings.url,
                    dataType: 'json',
                    data    : function (term, page)
                    {
                        return {
                            q         : term,
                            page_limit: 10,
                            page      : page
                        };
                    },
                    results : function (data, page)
                    {
                        var more = (page * 10) < data.total;

                        var results = [];

                        results = $.map(data.masterDevices, function (item)
                        {
                            return {
                                id  : item.id,
                                text: item.manufacturer.displayname + ' ' + item.modelName
                            };
                        });

                        return {
                            results: results,
                            more   : more
                        };
                    }
                },
                initSelection: function (element, callback)
                {
                    $.ajax({
                        url      : settings.url,
                        type     : "post",
                        dataType : "json",
                        data     : {
                            masterDeviceId: element.val()
                        },
                        'success': function (data)
                        {
                            callback({
                                id  : data.id,
                                text: data.manufacturer.displayname + ' ' + data.modelName
                            });
                        }
                    });
                },
                escapeMarkup : function (m)
                {
                    /**
                     * We do not want to escape markup since we are
                     * displaying html in results
                     */
                    return m;
                }
            });
        }
        else
        {
            console.log('This plugin can only be used on input[type="text"] elements.');
        }
    };

    $.fn[PluginName] = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data(PluginName)) return;

            // pass options to plugin constructor
            var pluginInstance = new Plugin(this, options);

            // Store plugin object in this element's data
            element.data(PluginName, pluginInstance);
        });
    };

    return Plugin;
});
