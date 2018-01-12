$(document).ready(function(){

    $(document).mousedown(function(e){
        if(!focusQtyElem)
            return true;
        var test = UnderElement(focusQtyElem,e);
        if(test){
            e.stopImmediatePropagation();
            return false;
        }
        return true;
    });

    $(".item-qty input[type=submit]").click(function(e){
        //$(this).attr('disabled','disabled');
        //$(this).parents('form:first').submit();
        e.stopImmediatePropagation();
    });

    $(".item-qty").focusin(function () {
        focusQtyElem = $(this).children('input[type=submit]');
        $(this).children('input[type=submit]').removeClass('hidden');
        $(this).next().fadeTo('fast', 0.3);
    }).focusout(function() {
        $(this).children('input[type=submit]').addClass('hidden');
        $(this).next().fadeTo('fast', 1);
    });

});


var focusQtyElem = null;
/**
 *
 * @param elem
 * @param e
 * @returns {boolean}
 * @constructor
 */
function UnderElement(elem,e) {
    var elemWidth = elem.outerWidth();
    var elemHeight = elem.outerHeight();
    var elemPosition = elem.offset();
    var elemPosition2 = new Object;
    elemPosition2.top = elemPosition.top + elemHeight;
    elemPosition2.left = elemPosition.left + elemWidth;

    return ((e.pageX > elemPosition.left && e.pageX < elemPosition2.left) && (e.pageY > elemPosition.top && e.pageY < elemPosition2.top));
}