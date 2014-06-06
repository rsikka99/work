/**
 * jQuery Plugin for downloads
 *
 * Requires jQuery and jQuery UI
 */
(function ($, window)
{
    var DownloadManagerPlugin = function (element, options)
    {
        /**
         * jQuery version of the element
         *
         * @type {*|HTMLElement}
         */
        var $element = $(element);

        // This can only be attached to anchors at the moment
        if ($element.is('a'))
        {
            /**
             * What do people normally name this?
             *
             * @type {DownloadManagerPlugin}
             */
            var downloadManagerInstance = this;

            var settings = $.extend({
                defaultMessage: 'Please be patient while we generate your document. This normally takes a minute or two.'
            }, options || {});

            /**
             * The file extension to use
             * @type {string}
             */
            var extension = "." + $element.data('ext');

            /**
             * The loading message to show when fetching the document
             * @type {string}
             */
            var loadingMessage = $element.data('loadingmessage') || settings.defaultMessage;

            /**
             * Additional data to send to the server when requesting a download
             * @type {object}
             */
            var extraData = {};

            var extraDataId = $element.data('extra-parameter');
            if (typeof extraDataId != 'undefined' && extraDataId.length > 0)
            {
                extraData[extraDataId] = $('#' + extraDataId.toString()).val();
            }

            var cancelled = false;
            var xhr;

            /**
             * On Click handler function
             * @param e The event
             */
            var onClick = function (e)
            {
                e.preventDefault();

                /**
                 * Popup Window
                 */
                $("<div id='loading' style='text-align: center'></div>")
                    .append("<p>" + loadingMessage + "</p>")
                    .append("<div class='AjaxLoadingIcon'></div>")
                    .dialog({
                        height       : 200,
                        width        : 450,
                        modal        : true,
                        draggable    : false,
                        resizable    : false,
                        title        : "Generating Document...",
                        closeOnEscape: false,
                        close        : function (event, ui)
                        {
                            if (typeof xhr == 'jqXHR')
                            {
                                cancelled = true;
                                xhr.abort();
                            }
                        }
                    });

                xhr = $.ajax({
                    url     : element.href,
                    data    : extraData,
                    success : function (data)
                    {
                        if (data.indexOf(extension) < 0 || data.length > 100)
                        {
                            $("<div id='errorMessage'></div>").append(data).dialog({
                                height    : 300,
                                width     : 900,
                                modal     : true,
                                draggable : true,
                                resizeable: true,
                                title     : "Error",
                                buttons   : {
                                    Ok: function ()
                                    {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                        }
                        else
                        {
                            window.location.href = data;
                        }
                    },
                    error   : function (data)
                    {
                        if (!cancelled)
                        {
                            var errorText = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : data.responseText;

                            $("<div id='errorMessage'></div>").html(errorText).dialog({
                                height    : 500,
                                width     : 900,
                                modal     : true,
                                draggable : true,
                                resizeable: true,
                                title     : "Error"
                            });
                        }
                    },
                    complete: function (data)
                    {
                        xhr = null;
                        cancelled = false;
                        $("#loading").remove();
                    }
                });
            };

            // Bind the element
            $element.on('click', onClick);
        }
        else
        {
            console.log('You cannot attach a download manager to a non anchor element.');
        }
    };

    $.fn.downloadManager = function (options)
    {
        return this.each(function ()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('download-manager')) return;

            // pass options to plugin constructor
            var downloadManager = new DownloadManagerPlugin(this, options);

            // Store plugin object in this element's data
            element.data('download-manager', downloadManager);
        });
    };
})(jQuery, window);
