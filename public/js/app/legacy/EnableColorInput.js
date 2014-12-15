require(['jquery', 'app/plugins/ColorInput'], function ($)
{
    $(function ()
    {
        $('.hex-color-input').colorInput();
        $('.color-input').colorInput();
    });
});