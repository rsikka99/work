define(['jquery', 'underscore', 'bootstrap.modal.manager'], function ($, _)
{
    'use strict';
    var ConfirmationDialog_InstanceIdCounter = 0;

    var ConfirmationDialog = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        ConfirmationDialog_InstanceIdCounter++;
        this.id = ConfirmationDialog_InstanceIdCounter;
        var ConfirmationDialogInstance = this;

        var settings = _.extend({
            title      : 'Are you sure?',
            message    : 'Are you sure you want to do that?',
            confirmText: 'Yes',
            cancelText : 'No'
        }, _.pick(options || {}, [
            'title',
            'message',
            'confirmText',
            'cancelText'
        ]));

        /**
         * Create Modal
         */
        var $modal = $(document.createElement('div'));
        $modal.addClass('modal fade js-confirmation-dialog');

        var $modalDialog = $(document.createElement('div')).addClass('modal-dialog');
        $modal.append($modalDialog);
        var $modalContent = $(document.createElement('div')).addClass('modal-content');
        $modalDialog.append($modalContent);

        var $modalHeader = $(document.createElement('div')).addClass('modal-header').append($(document.createElement('h2')).addClass('text-center').text(settings.title));
        var $modalBody = $(document.createElement('div')).addClass('modal-body').append($(document.createElement('p')).text(settings.message));
        var $modalFooter = $(document.createElement('div')).addClass('modal-footer');

        var $confirmButton = $(document.createElement('button'))
            .attr('type', 'button')
            .addClass('btn btn-primary')
            .text(settings.confirmText)
            .on('click', function ()
            {
                $(ConfirmationDialogInstance).trigger('confirmation-dialog.confirmed');
                $modal.modal('hide');
            });
        $modalFooter.append($confirmButton);

        var $cancelButton = $(document.createElement('button'))
            .attr('type', 'button')
            .addClass('btn btn-default')
            .text(settings.cancelText)
            .on('click', function ()
            {
                $(ConfirmationDialogInstance).trigger('confirmation-dialog.cancelled');
                $modal.modal('hide');
            });
        $modalFooter.append($cancelButton);

        $modalContent.append($modalHeader)
        .append($modalBody)
        .append($modalFooter);

        $modal.append($modalContent);

        $('body').append($modal);

        /**
         * Class Members
         */
        this.$modal = $modal;

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
    };

    /**
     * Loads and initializes the loaded form
     */
    ConfirmationDialog.prototype.show = function ()
    {
        var that = this;
        var $modal = that.$modal;

        var modalOptions = {
            backdrop: 'static'
        };

        $modal.modal(modalOptions);
    };

    return ConfirmationDialog;
});