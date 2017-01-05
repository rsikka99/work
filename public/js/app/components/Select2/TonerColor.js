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


        $element.select2({
            placeholder  : settings.placeholder,
            allowClear   : true
        });

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
