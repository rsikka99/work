define(['jquery'], function ($)
{
    var ColorInput = function (element, options)
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
             * @type {ColorInput}
             */
            var colorInputInstance = this;

            var $colorBox = $(document.createElement('div'));
            var $controlGroup = $element.parent().parent('.control-group');

            var zIndex = $element.css('z-index') || 10;

            $element.css('z-index', zIndex);

            $colorBox.addClass('color-input-box').css('z-index', zIndex + 1);
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
            console.log('You cannot attach a color input to a non input.');
        }
    };

    $.fn.colorInput = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('color-input')) return;

            // pass options to plugin constructor
            var colorInput = new ColorInput(this, options);

            // Store plugin object in this element's data
            element.data('color-input', colorInput);
        });
    };


});