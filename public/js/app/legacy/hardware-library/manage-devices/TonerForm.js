define([
    'jquery',
    'underscore',
    './TonerService',
    'bootstrap.modal.manager',
     '../../../components/Select2/TonerColor'
], function ($, _, TonerService)
{
    'use strict';
    var TonerForm_InstanceIdCounter = 0;

    var TonerForm = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        TonerForm_InstanceIdCounter++;
        this.id = TonerForm_InstanceIdCounter;
        var TonerFormInstance = this;


        var settings = _.extend({
            tonerId: 0
        }, _.pick(options, ['tonerId', 'tonerConfigId']) || {});

        /**
         * Create Modal
         */
        var $modal = $(document.createElement('div'));
        $modal.addClass('modal fade');
        $('body').append($modal);

        /**
         * Class Members
         */
        this.$modal = $modal;
        this.tonerId = settings.tonerId;
        this.tonerConfigId = settings.tonerConfigId;
        this.isCreatingNewToner = (this.tonerId === 0);

        /**
         * Toner Model
         */
        //this.tonerModel = {};

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
     * A list of URLs that are used throughout this object
     */
    TonerForm.prototype.urls = {
        "loadForm": "/hardware-library/toners/load-form"
    };

    /**
     * Loads and initializes the loaded form
     */
    TonerForm.prototype.show = function ()
    {
        var that = this;
        var $modal = that.$modal;

        var modalOptions = {
            backdrop: 'static'
        };

        if ($(window).width() > 960)
        {
            modalOptions.width = 960;
        }

        $modal.load(that.urls.loadForm,
            {
                tonerId: that.tonerId
            },
            function ()
            {
                $modal.find('.modal-title').text((that.isCreatingNewToner) ? 'Add New Supply' : 'Edit Supply');

                /**
                 * Button on-click handlers
                 */
                $modal.find('.modal-footer > .js-save').on('click', function ()
                {
                    that.save();
                });

                /**
                 * Button on-click handlers
                 */
                $modal.find('.modal-footer > .js-save-approve').on('click', function ()
                {
                    $('#saveAndApproveHdn').val(1);
                    that.save();
                });

                $modal.find('.modal-footer > .js-cancel').on('click', function ()
                {
                    $(that).trigger('toner-form.cancelled');
                    $modal.modal('hide');
                });

                /**
                 * Setup two-way binding
                 */
                that.$tonerForm = $modal.find('.js-toner-form');

                /**
                 * Show the modal
                 */
                $modal.modal(modalOptions);
            }
        );
    };

    /**
     * Save handler
     * @returns {*}
     */
    TonerForm.prototype.save = function ()
    {
        var that = this;
        that.clearErrors();

        var data = $('#availableTonersForm').serialize();

        //this.tonerModel
        return TonerService.saveToner(data).then(function (tonerId)
        {
            $(that).trigger('toner-form.saved', [tonerId]);
            that.$modal.modal('hide');

            return tonerId;
        }, function (data)
        {
            that.setZendFormErrors(data.errorMessages);
        });
    };

    /**
     * Clears all the errors on the modal
     */
    TonerForm.prototype.clearErrors = function ()
    {
        this.$modal.find(".has-error").removeClass("has-error");
        this.$modal.find(".has-feedback").removeClass("has-feedback");
        this.$modal.find('.form-error-element').remove();
    };

    /**
     * Handles taking errors from Zend Form and applying them to the form so users
     * can see the issues with validation
     *
     * @param zendFormErrors
     */
    TonerForm.prototype.setZendFormErrors = function (zendFormErrors)
    {
        var tonerFormInstance = this;
        $.each(zendFormErrors, function (key, value)
        {
            var $formControl = tonerFormInstance.$tonerForm.find('[name="' + key + '"]');
            var $formControlParent = $formControl.parents('.control-group');
            var $formGroup = $formControl.parents('.form-group');

            if ($formControlParent.length < 1)
            {
                $formControlParent = $formGroup;
            }

            if (!$formGroup.hasClass('has-feedback'))
            {
                $formGroup.addClass('has-feedback has-error');
                var $messages = $(document.createElement('span')).addClass('help-block form-error-element');
                $.each(value, function (errorKey, errorMessage)
                {
                    $messages.append(errorMessage + '<br>');
                });

                $formControlParent.append($messages);
            }
        });
    };

    return TonerForm;
});

