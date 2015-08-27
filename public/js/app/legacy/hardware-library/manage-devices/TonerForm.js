define([
    'jquery',
    './TonerService',
    'bootstrap.modal.manager',
     '../../../components/Select2/TonerColor'
], function ($, TonerService)
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
        this.tonerModel = {};

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
                $modal.find('.modal-title').text((that.isCreatingNewToner) ? 'Add New Toner' : 'Edit Toner');

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
                    $(that).trigger('toner-form.cancelled');
                    $modal.modal('hide');
                });

                var tonerColorIdOptions = {};
                if (that.tonerConfigId)
                {
                    tonerColorIdOptions.tonerConfigId = function ()
                    {
                        return that.tonerConfigId;
                    }
                }
                $modal.find('[name="tonerColorId"]').selectTonerColor(tonerColorIdOptions);

                /**
                 * Setup two-way binding
                 */
                that.$tonerForm = $modal.find('.js-toner-form');

                var $tonerIdElement = that.$tonerForm.find('[name="id"]'),
                    $tonerManufacturerElement = that.$tonerForm.find('[name="manufacturerId"]'),
                    $tonerColorElement = that.$tonerForm.find('[name="tonerColorId"]'),
                    $tonerCostElement = that.$tonerForm.find('[name="cost"]'),
                    $tonerYieldElement = that.$tonerForm.find('[name="yield"]'),
                    $tonerSkuElement = that.$tonerForm.find('[name="sku"]'),
                    $tonerNameElement = that.$tonerForm.find('[name="name"]'),
                    $tonerImageUrlElement = that.$tonerForm.find('[name="imageUrl"]');

                that.tonerModel.tonerId = $tonerIdElement.val();
                that.tonerModel.manufacturerId = $tonerManufacturerElement.val();
                that.tonerModel.tonerColorId = $tonerColorElement.val();
                that.tonerModel.cost = $tonerCostElement.val();
                that.tonerModel.sku = $tonerSkuElement.val();
                that.tonerModel.name = $tonerNameElement.val();
                that.tonerModel.yield = $tonerYieldElement.val();
                that.tonerModel.imageUrl = $tonerImageUrlElement.val();

                $tonerIdElement.on('change', function ()
                {
                    that.tonerModel.tonerId = $(this).val();
                });

                $tonerImageUrlElement.on('change', function ()
                {
                    that.tonerModel.imageUrl = $(this).val();
                });

                $tonerCostElement.on('change', function ()
                {
                    that.tonerModel.cost = $(this).val();
                });

                $tonerSkuElement.on('change', function ()
                {
                    that.tonerModel.sku = $(this).val();
                });

                $tonerNameElement.on('change', function ()
                {
                    that.tonerModel.name = $(this).val();
                });

                $tonerYieldElement.on('change', function ()
                {
                    that.tonerModel.yield = $(this).val();
                });

                $tonerManufacturerElement.on('change', function ()
                {
                    that.tonerModel.manufacturerId = $(this).val();
                });

                $tonerColorElement.on('change', function ()
                {
                    that.tonerModel.tonerColorId = $(this).val();
                });

                var $tonerDealerSkuElement = that.$tonerForm.find('[name="dealerSku"]'),
                    $tonerDealerSrpElement = that.$tonerForm.find('[name="dealerSrp"]'),
                    $tonerDealerCostElement = that.$tonerForm.find('[name="dealerCost"]');

                that.tonerModel.dealerCost = $tonerDealerCostElement.val();
                that.tonerModel.dealerSku = $tonerDealerSkuElement.val();
                that.tonerModel.dealerSrp = $tonerDealerSrpElement.val();

                $tonerDealerCostElement.on('change', function ()
                {
                    that.tonerModel.dealerCost = $(this).val();
                });

                $tonerDealerSkuElement.on('change', function ()
                {
                    that.tonerModel.dealerSku = $(this).val();
                });

                $tonerDealerSrpElement.on('change', function ()
                {
                    that.tonerModel.dealerSrp = $(this).val();
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
    TonerForm.prototype.save = function ()
    {
        var that = this;
        that.clearErrors();

        return TonerService.saveToner(this.tonerModel).then(function (tonerId)
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

function uploadDone (e, result) {
    var filename = result._response.result.filename;
    $('#imageDiv').html('<a href="/img/toners/'+filename+'" target="_blank" class="thumbnail"><img src="/img/toners/'+filename+'" style="max-width:300px;max-height:300px"></a>');
}
