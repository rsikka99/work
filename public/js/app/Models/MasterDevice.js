/**
 * MasterDevice model
 */
define(['riot'], function (riot)
{
    var instanceCounter = 0;

    var defaults = {
        "id"                                 : null,
        "isA3"                               : null,
        "isCapableOfReportingTonerLevels"    : null,
        "isCopier"                           : null,
        "isDuplex"                           : null,
        "isFax"                              : null,
        "isLeased"                           : null,
        "isReplacementDevice"                : null,
        "launchDate"                         : null,
        "leasedTonerYield"                   : null,
        "manufacturerId"                     : null,
        "maximumRecommendedMonthlyPageVolume": null,
        "modelName"                          : null,
        "ppmBlack"                           : null,
        "ppmColor"                           : null,
        "tonerConfigId"                      : 1,
        "wattsPowerNormal"                   : null,
        "wattsPowerIdle"                     : null,
        "dateCreated"                        : null,
        "isSystemDevice"                     : null,
        "userId"                             : null
    };

    /**
     * @param {*|HTMLElement} element
     * @param {Object} options
     * @constructor
     */
    var MasterDevice = function (options)
    {

        var settings = $.extend({
            deviceId     : false,
            isAllowed    : false,
            tonerList    : false,
            tonerConfigId: false,
            url          : '#'
        }, _.pick(options || {}, ['deviceId', 'tonerList', 'isAllowed', 'tonerConfigId', 'url']));


        this.originalData = $.extend(defaults, _.pick(settings || {}, [
            "id",
            "dateCreated",
            "isCopier",
            "isDuplex",
            "isFax",
            "isLeased",
            "isReplacementDevice",
            "launchDate",
            "manufacturerId",
            "modelName",
            "leasedTonerYield",
            "ppmBlack",
            "ppmColor",
            "tonerConfigId",
            "wattsPowerNormal",
            "wattsPowerIdle",
            "isCapableOfReportingTonerLevels",
            "userId",
            "isSystemDevice",
            "isA3",
            "maximumRecommendedMonthlyPageVolume",
            "imageUrl"
        ]));
    };

    return MasterDevice;
});