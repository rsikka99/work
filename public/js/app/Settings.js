define(function ()
{
    var Settings = {};

    /**
     *
     * @type {{pageVolume: string, number: string, marginPercent: string, number2: string, costPerPage: string, currency: string}}
     */
    Settings.format = {
        pageVolume   : '0,0',
        number       : '0,0',
        marginPercent: '0.00%',
        number2      : '0,0.00',
        costPerPage  : '$0.0000',
        currency     : '$,0.00'
    };

    return Settings;
});