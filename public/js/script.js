/* 
 * Extra scripts to be run
 * Author: Lee Robert
 * 
 */

$().ready(function () {

    /*
     * Enable dropdown menus
     */
    $('.dropdown-toggle').dropdown();

    /*
     * Enable hover popovers
     */
    $('.hasPopover').popover();

});

/**
 * Prevents user from inputting anything but numbers
 * @param myfield The Input Field
 * @param e The Event
 * @param dec Whether or not to accept decimal values
 * @return {Boolean}
 */
function numbersonly(myfield, e, dec) {
    var key;
    var keychar;

    if (window.event) {
        key = window.event.keyCode;
    } else if (e) {
        key = e.which;
    } else {
        return true;
    }
    keychar = String.fromCharCode(key);

    // control keys
    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27) || (key == 46)) {
        return true;
    } else if ((("0123456789").indexOf(keychar) > -1)) {
        return true;
    } else if (dec && (keychar == ".")) {
        myfield.form.elements[dec].focus();
        return false;
    } else {
        return false;
    }
}