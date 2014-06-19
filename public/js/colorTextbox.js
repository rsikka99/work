/**
 * jQuery Plugin for showing a hex color in an input
 *
 * Requires jQuery
 *
 * @param $ {jQuery}
 */
(function ($, window)
{
    var HexColorInput = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {*|HTMLElement}
         */
        var $element = $(element);

        /**
         * Validation
         */
        var validateCssHex = /^#(?:[0-9a-f]{3}){1,2}$/i;

        // This can only be attached to anchors at the moment
        if ($element.is('input'))
        {
            /**
             * What do people normally name this?
             *
             * @type {HexColorInput}
             */
            var hexColorInputInstance = this;

            var $colorBox = $(document.createElement('div'));
            var $controlGroup = $element.parent().parent('.control-group');

            $colorBox.addClass('hex-color-input-box');
            $element.after($colorBox);

            var setBackground = function (color)
            {
                $colorBox.css('background-color', color);
                $controlGroup.removeClass('error');
            };

            /**
             * Handles the textbox change
             * @param e The event
             */
            var onChange = function (e)
            {
                var color = $element.val().trim();

                if (validateCssHex.test(color))
                {
                    setBackground(color);
                }
                else
                {
                    console.log($controlGroup.hasClass('error'));
                    if (!$controlGroup.hasClass('error'))
                        $controlGroup.addClass('error');
                }
            };

            // Bind the element
            $element.on('keyup', onChange);

            $element.trigger('keyup');
        }
        else
        {
            console.log('You cannot attach a hex color input to a non input.');
        }
    };

    $.fn.hexColorInput = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('hex-color-input')) return;

            // pass options to plugin constructor
            var hexColorInput = new HexColorInput(this, options);

            // Store plugin object in this element's data
            element.data('hex-color-input', hexColorInput);
        });
    };
})(jQuery, window);

jQuery(function ()
{
    $('.hex-color-input').hexColorInput();
});