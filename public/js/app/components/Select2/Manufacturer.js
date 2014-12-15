define(['jquery', 'select2'], function ($)
{
    var ManufacturerSelectPlugin = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {*|HTMLElement}
         */
        var $element = $(element);

        var settings = $.extend({
            placeholder: 'Type to search for a manufacturer',
            url        : '/hardware-library/manage-devices/search-for-manufacturer'
        }, options || {});

        // This can only be attached to text inputs
        if ($element.is('input[type="text"]'))
        {
            $element.select2({
                placeholder       : settings.placeholder,
                minimumInputLength: 1,
                allowClear        : true,
                ajax              : {
                    url     : settings.url,
                    dataType: 'json',
                    data    : function (term, page)
                    {
                        return {
                            manufacturerName: term, // search term
                            page_limit      : 10
                        };
                    },
                    results : function (data, page)
                    {
                        return {results: data};
                    }
                },
                initSelection     : function (element, callback)
                {
                    $.ajax({
                        url      : settings.url,
                        type     : "post",
                        dataType : "json",
                        data     : {
                            manufacturerId: element.val()
                        },
                        'success': function (data)
                        {
                            callback(data);
                        }
                    });
                },
                escapeMarkup      : function (m)
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
            console.log('You cannot attach a manufacturer select to a non text input element.');
        }
    };

    $.fn.selectManufacturer = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('select-manufacturer')) return;

            // pass options to plugin constructor
            var manufacturerSelect = new ManufacturerSelectPlugin(this, options);

            // Store plugin object in this element's data
            element.data('select-manufacturer', manufacturerSelect);
        });
    };

    return ManufacturerSelectPlugin;
});