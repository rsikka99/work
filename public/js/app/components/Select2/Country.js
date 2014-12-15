define([
    'jquery',
    'select2'
], function ($)
{

    var PluginName = 'selectCountry';

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
                placeholder: 'Type to search',
                url        : '/api/v1/countries'
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
                        $.each(data.countries, function (index, value)
                        {
                            results.push({
                                id  : value.country_id,
                                text: value.name
                            });
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
                            countryId: element.val()
                        },
                        'success': function (data)
                        {
                            callback({id: data.country_id, text: data.name});
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
