define(['jquery', 'underscore', 'bootstrap', 'bootstrap-modal'], function ($, _)
{
    'use strict';
    var ConfirmationModal_InstanceIdCounter = 0;

    var ConfirmationModal = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        ConfirmationModal_InstanceIdCounter++;
        this.id = ConfirmationModal_InstanceIdCounter;
        var ConfirmationModalInstance = this;


        var settings = _.extend({
            "title"     : 'Are you sure?',
            "message"   : 'Are you sure you wish to proceed?',
            "cancel"    : false,
            "confirm"   : false,
            onModalClose: false
        }, _.pick(options || {}, ['title', 'message', 'cancel', 'confirm', 'onModalClose']));

        /**
         * Create Modal
         */
        var $title = $(document.createElement('h1'));
        $title.html(settings.title);

        var $closeButton = $(document.createElement('h1'));
        $closeButton
            .addClass('close')
            .attr('type', 'button')
            .attr('data-dismiss', 'modal')
            .attr('aria-hidden', 'true')
            .html('×');

        var $deleteModalHeader = $(document.createElement('div'));
        $deleteModalHeader
            .addClass('modal-header')
            .append($closeButton)
            .append($title);

        var $message = $(document.createElement('p'));
        $message
            .html(settings.message);

        var $deleteModalBody = $(document.createElement('div'));
        $deleteModalBody
            .addClass('modal-body')
            .append($message);

        var $confirmButton = $(document.createElement('button'));
        $confirmButton
            .addClass('btn btn-primary')
            .html('Confirm');

        if (_.isFunction(settings.confirm))
        {
            $confirmButton.on('click', settings.confirm);
        }

        var $cancelButton = $(document.createElement('button'));
        $cancelButton
            .addClass('btn btn-default')
            .html('Cancel');

        if (_.isFunction(settings.cancel))
        {
            $cancelButton.on('click', settings.cancel);
        }


        var $deleteModalFooter = $(document.createElement('div'));
        $deleteModalFooter
            .addClass('modal-footer')
            .append($confirmButton)
            .append($cancelButton);


        var $deleteModalContent = $(document.createElement('div'));
        $deleteModalContent
            .addClass('modal-content')
            .append($deleteModalHeader)
            .append($deleteModalBody)
            .append($deleteModalFooter);

        var $deleteModalDialog = $(document.createElement('div'));
        $deleteModalDialog
            .addClass('modal-dialog')
            .append($deleteModalContent);

        var $modal = $(document.createElement('div'));
        $modal
            .addClass('modal')
            .append($deleteModalDialog);

        $('body').append($modal);

        this.$modal = $modal;

        $confirmButton.on('click', function ()
        {
            $modal.modal('hide');
        });

        $cancelButton.on('click', function ()
        {
            $modal.modal('hide');
        });


        /**
         * Cleanup time!
         */
        if (_.isFunction(settings.onModalClose))
        {
            $modal.on('hide.bs.modal', settings.onModalClose);
        }

        // We want to destroy the modal once we're finished with it.
        $modal.on('hide.bs.modal', function ()
        {
            $modal.removeClass('fade');

            // Timeout was needed as the modal wasn't being destroyed
            window.setTimeout(function ()
            {
                $modal.removeData('bs.modal');
                $modal.remove();
            }, 1000);
        });

        this.show();
    };


    ConfirmationModal.prototype.show = function ()
    {
        this.$modal.modal('show');
    };

    ConfirmationModal.prototype.hide = function ()
    {
        this.$modal.modal('hide');
    };

    return ConfirmationModal;
});