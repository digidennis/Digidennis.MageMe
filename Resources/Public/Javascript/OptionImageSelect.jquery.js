(function($) {
    $.OptionImageSelect = function(element, options) {
        var plugin = this;
        plugin.settings = {};
        var $element = $(element), element = element;    // reference to the actual DOM element
        plugin.init = function() {
            var data = {};
            plugin.settings = $.extend({}, data, options);
            $element.find('input').change(function(event){
                $(".col-sm-6.col-md-7 > .slick-node ").slick('slickGoTo',$(this).attr('data-slick-index'));
                $element.trigger('change');
            });
            $(".neos-nodetypes-image", $element).each(function(){
                var index = _getSlickIndex($(this).attr('data-node-identifier'));
                $(this).parent().siblings("input").first().attr('data-slick-index',index);
            })
        };
        var _getSlickIndex = function(nodeid){
            return $(".col-sm-6.col-md-7 > .slick-node  .slick-track img[data-node-identifier=" + nodeid + "]").parent().attr('data-slick-index');
        };
        plugin.init();
    };
    $.fn.OptionImageSelect = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('OptionImageSelect')) {
                var plugin = new $.OptionImageSelect(this, options);
                $(this).data('OptionImageSelect', plugin);
            }
        });
    };
})(jQuery);