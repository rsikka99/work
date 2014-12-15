define(['jquery', './OptionService', 'bootstrap.modal.manager'], function ($, OptionService)
{
    'use strict';
    var OptionForm_InstanceIdCounter = 0;

    var OptionForm = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        OptionForm_InstanceIdCounter++;
        this.id = OptionForm_InstanceIdCounter;
        var OptionFormInstance = this;


        var settings = _.extend({
            optionId: 0
        }, _.pick(options || {}, ['optionId']));

        /**
         * Create Modal
         */
        var $modal = $(document.createElement('div'));
        $modal.addClass('modal fade js-omg');
        $('body').append($modal);

        /**
         * Class Members
         */
        this.$modal = $modal;
        this.optionId = settings.optionId;
        this.isCreatingNewOption = (this.optionId === 0);

        /**
         * Option Model
         *
         * @type {{
     *     optionId    : number,
     *     cost        : number,
     *     description : string,
     *     dealerCost  : number,
     *     dealerSku   : string
     *     name        : string,
     *     oemSku      : string,
     *     }}
         */
        this.optionModel = {};

        if (!this.isCreatingNewOption)
        {
            this.optionModel.optionId = this.optionId;
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
    };

    /**
     * A list of URLs that are used throughout this object
     */
    OptionForm.prototype.urls = {
        "loadForm": "/hardware-library/options/load-form"
    };

    /**
     * Loads and initializes the loaded form
     */
    OptionForm.prototype.show = function ()
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
                optionId: that.optionId
            },
            function (responseText, textStatus, jqXHR)
            {
                $modal.find('.modal-title').text((that.isCreatingNewOption) ? 'Add New Option' : 'Edit Option');

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
                    that.save();
                });

                $modal.find('.modal-footer > .js-cancel').on('click', function ()
                {
                    $(that).trigger('option-form.cancelled');
                    $modal.modal('hide');
                });

                /**
                 * Setup two-way binding
                 */
                that.$optionForm = $modal.find('.js-option-form');

                var $optionIdElement = that.$optionForm.find('[name="id"]'),
                    $optionNameElement = that.$optionForm.find('[name="name"]'),
                    $optionDescriptionElement = that.$optionForm.find('[name="description"]'),
                    $optionCostElement = that.$optionForm.find('[name="cost"]'),
                    $optionOemSkuElement = that.$optionForm.find('[name="oemSku"]'),
                    $optionDealerSkuElement = that.$optionForm.find('[name="dealerSku"]');

                that.optionModel.optionId = $optionIdElement.val();
                that.optionModel.name = $optionNameElement.val();
                that.optionModel.description = $optionDescriptionElement.val();
                that.optionModel.cost = $optionCostElement.val();
                that.optionModel.oemSku = $optionOemSkuElement.val();
                that.optionModel.dealerSku = $optionDealerSkuElement.val();


                $optionIdElement.on('change', function ()
                {
                    that.optionModel.optionId = $(this).val();
                });

                $optionNameElement.on('change', function ()
                {
                    that.optionModel.name = $(this).val();
                });

                $optionDescriptionElement.on('change', function ()
                {
                    that.optionModel.description = $(this).val();
                });

                $optionCostElement.on('change', function ()
                {
                    that.optionModel.cost = $(this).val();
                });

                $optionOemSkuElement.on('change', function ()
                {
                    that.optionModel.oemSku = $(this).val();
                });

                $optionDealerSkuElement.on('change', function ()
                {
                    that.optionModel.dealerSku = $(this).val();
                });

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
    OptionForm.prototype.save = function ()
    {
        var that = this;
        that.clearErrors();

        return OptionService.saveOption(this.optionModel).then(function (optionId)
        {
            $(that).trigger('option-form.saved', [optionId]);
            that.$modal.modal('hide');

            return optionId;
        }).error(function (data)
        {
            that.setZendFormErrors(data.errorMessages);
        });
    };

    /**
     * Clears all the errors on the modal
     */
    OptionForm.prototype.clearErrors = function ()
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
    OptionForm.prototype.setZendFormErrors = function (zendFormErrors)
    {
        var optionFormInstance = this;
        $.each(zendFormErrors, function (key, value)
        {
            var $formControl = optionFormInstance.$optionForm.find('[name="' + key + '"]');
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

    return OptionForm;
});