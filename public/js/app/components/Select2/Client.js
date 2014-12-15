define(['jquery', 'select2'], function ($)
{
    var SelectClientPlugin = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {*|HTMLElement}
         */
        var $element = $(element);

        var settings = $.extend({
            placeholder: 'Search for a client',
            url        : '/index/search-for-client'
        }, options || {});

        // This can only be attached to text inputs
        if ($element.is('input[type="text"]'))
        {
            $element.select2({
                placeholder  : settings.placeholder,
                allowClear   : true,
                ajax         : {
                    url     : settings.url,
                    dataType: 'json',
                    data    : function (term, page)
                    {
                        return {
                            page_limit: 10
                        };
                    },
                    results : function (data, page)
                    {
                        return {results: data};
                    }
                },
                initSelection: function (element, callback)
                {
                    $.ajax({
                        url      : settings.url,
                        type     : "post",
                        dataType : "json",
                        data     : {
                            clientId: element.val()
                        },
                        'success': function (data)
                        {
                            callback(data);
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
            console.log('You cannot attach a client select to a non text input element.');
        }
    };

    $.fn.selectClient = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('select-client')) return;

            // pass options to plugin constructor
            var clientSelect = new SelectClientPlugin(this, options);

            // Store plugin object in this element's data
            element.data('select-client', clientSelect);
        });
    };

    return SelectClientPlugin;
});
