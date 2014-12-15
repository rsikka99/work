define(['jquery', 'underscore'], function ($, _)
{
    'use strict';
    var MasterDeviceModel_InstanceIdCounter = 0;

    var MasterDeviceModel = function (options)
    {
        var settings = _.extend({
            id                                 : 0,
            dateCreated                        : '1900-01-01',
            isCopier                           : false,
            isDuplex                           : false,
            isFax                              : false,
            isLeased                           : false,
            isReplacementDevice                : false,
            launchDate                         : '1900-01-01',
            manufacturerId                     : false,
            modelName                          : false,
            leasedTonerYield                   : false,
            ppmBlack                           : false,
            ppmColor                           : false,
            tonerConfigId                      : false,
            wattsPowerNormal                   : false,
            wattsPowerIdle                     : false,
            isCapableOfReportingTonerLevels    : false,
            userId                             : false,
            isSystemDevice                     : false,
            isA3                               : false,
            maximumRecommendedMonthlyPageVolume: false
        }, _.pick(options, ['rmsUploadRowId', 'deviceId', 'isAllowed', 'onModalClose']) || {});


        /**
         * Class Members
         */
        this.$modal = $modal;
        this.rmsUploadRowId = settings.rmsUploadRowId;
        this.deviceId = settings.deviceId;
        this.isAllowed = !(settings.isAllowed == 'undefined' || settings.isAllowed == 'false');
        this.isCreatingNewDevice = (this.masterDeviceId === 0);
    };

    return MasterDeviceModel;
});