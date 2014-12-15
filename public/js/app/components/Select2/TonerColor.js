define(['jquery', 'select2'], function ($)
{
    var SelectTonerColorPlugin = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {*|HTMLElement}
         */
        var $element = $(element);

        var settings = $.extend({
            placeholder  : 'Type to search for a toner color',
            url          : '/hardware-library/toners/colors-for-configuration',
            tonerConfigId: function ()
            {
                return '';
            }
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
                            tonerConfigId: settings.tonerConfigId(),
                            page_limit   : 10
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
                            tonerColorId: element.val()
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
            console.log('You cannot attach a tonerColor select to a non text input element.');
        }
    };

    $.fn.selectTonerColor = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('select-toner-color')) return;

            // pass options to plugin constructor
            var tonerColorSelect = new SelectTonerColorPlugin(this, options);

            // Store plugin object in this element's data
            element.data('select-toner-color', tonerColorSelect);
        });
    };

    return SelectTonerColorPlugin;
});
