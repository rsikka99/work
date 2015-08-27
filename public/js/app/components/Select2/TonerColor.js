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
                data         : [{id:1,text:'BLACK'},{id:2,text:'CYAN'},{id:3,text:'MAGENTA'},{id:4,text:'YELLOW'},{id:5,text:'3 COLOR'},{id:5,text:'4 COLOR'}]
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
