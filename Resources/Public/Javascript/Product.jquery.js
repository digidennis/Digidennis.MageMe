(function($) {
    $.Product = function(element, options) {

        var plugin = this;
        plugin.settings = {};
        var $element = $(element), element = element;    // reference to the actual DOM element

        plugin.init = function() {
            var data = {};
            plugin.settings = $.extend({}, data, options);
            $($element).find("[data-component='SumoSelect']").each(function(){
                $(this).SumoSelect({
                    placeholder: $(this).attr('data-placeholder'),
                    locale: ['OK', 'Annuller', 'Vælg alt'],
                    captionFormat: '{0} Valgt',
                    captionFormatAllSelected: '{0} Valgt',
                    floatWidth: 50
                });
            });
            $("[data-component='SumoSelect']").on('change', _update);
            $("[data-component='Dimension']").stepper().on('change', _update);
            _update();
            _handleRules();
        };


        var _update = function (){

            var baseprice = $element.attr('data-baseprice'),
                totalprice = parseFloat(baseprice);

            // product baseprice calc
            $element.children("[data-component='Slot']").each(function(){
                var formular = _getValueFormular($(this).attr('data-priceform')),
                    min = $(this).attr('data-pricemin'),
                    max = $(this).attr('data-pricemax');

                if(formular){
                    var calcedbasedprice = Math.floor(eval(formular)*baseprice);
                    if(min && calcedbasedprice < min )
                        calcedbasedprice = min;
                    if(max && calcedbasedprice > max )
                        calcedbasedprice = max;
                    totalprice = calcedbasedprice;
                }
            });

            //foreach option
            $("[data-component='Option']").each(function(){

                var optiontotalprice = 0,
                    optionid = $(this).attr('data-optionid'),
                    disabled = $(this).hasClass('option-disabled');

                if(!disabled){

                    //foreach item in option
                    $(this).find("[data-component='Item']").each(function(){

                        var hasselection = false;
                        //if we have selected values ( which isn't -1 )
                        $(this).find(":selected[value!='-1']",":checked[value!='-1']").each(function(){

                            var optionprice = parseFloat($(this).attr('data-baseprice')),
                                optionpricetype = $(this).attr('data-pricetype'),
                                calcedprice  = 0;

                            hasselection = true;

                            if($(this).attr('data-component')==='Slot' && $(this).attr('data-priceform') !== ''){

                                var formular = _getValueFormular($(this).attr('data-priceform')),
                                    min = parseFloat($(this).attr('data-pricemin')),
                                    max = parseFloat($(this).attr('data-pricemax'));

                                calcedprice = Math.floor(eval(formular)*optionprice);

                                if(min && calcedprice < min )
                                    calcedprice = min;
                                if(max && calcedprice > max )
                                    calcedprice = max;

                            } else { // normal option calculation

                                if(optionpricetype === 'fixed')
                                    calcedprice = optionprice;
                                else if(optionpricetype === 'percent')
                                    calcedprice = (parseFloat(baseprice)/100)*optionprice;

                            }
                            optiontotalprice += calcedprice;
                        });

                        if(hasselection){
                            $(this).addClass('item-is-selected');
                            $(this).find("[data-component='Dimension']").each(function(){
                                $(this).prop('disabled', false);
                                $(this).parent().siblings("input[type='hidden']").each(function () {
                                    $(this).prop('disabled', false);
                                })
                            });
                        } else {
                            $(this).removeClass('item-is-selected');
                            $(this).find("[data-component='Dimension']").each(function(){
                                $(this).prop('disabled', true);
                                $(this).parent().siblings("input[type='hidden']").each(function () {
                                    $(this).prop('disabled', true);
                                })
                            });
                        }
                    });
                    totalprice += optiontotalprice;
                } else {
                    $(this).find("[data-component='Item']").each(function(){
                        $(this).removeClass('item-is-selected')
                    });
                }
                _updateOptionPrice(optionid,optiontotalprice);
            });
            _updateTotalPrice(totalprice);
            _updateSummary();
        };
        var _updateSummary = function(){
            var labels = Array();
            $element.find(".item-is-selected [data-component='Dimension'][data-output='1']").each(function () {
                var label = $(this).attr('data-label'),
                    unit =  $(this).attr('data-unit'),
                    val = parseFloat($(this).val());
                if(!(label in labels)){
                    labels[label] = [val,unit];
                } else {
                    labels[label][0] += val;
                }
            });
            var buf = '';
            for(property in labels){
                buf += property + ': ' + labels[property][0] + labels[property][1] + '<br>';
            }
            $element.find("[data-component='DimensionSummary']").html(buf);
        };
        var _updateOptionPrice = function( option, price ){
            $("[data-component='Option'][data-optionid='" + option  + "']").each(function(){
                var priceelm = $(this).find("[data-component='OptionPrice']").first(),
                    prefix = priceelm.attr('data-prefix') ? priceelm.attr('data-prefix'): '',
                    postfix = priceelm.attr('data-postfix') ? priceelm.attr('data-postfix'): '',
                    symbol = priceelm.attr('data-currency-symbol') ? priceelm.attr('data-currency-symbol') : '';
                priceelm.html(symbol + prefix + price.toLocaleString() + postfix);
            });
        };
        var _updateTotalPrice = function(  price ){
            $("[data-component='TotalPrice']").each(function(){
                var prefix = $(this).attr('data-prefix') ? $(this).attr('data-prefix'): '',
                    postfix = $(this).attr('data-postfix') ? $(this).attr('data-postfix'): '',
                    symbol = $(this).attr('data-currency-symbol') ? $(this).attr('data-currency-symbol') : '';
                $(this).html(symbol + prefix + price.toLocaleString() + postfix);
            });
        };
        var _getValueFormular = function( formular ){
            var regExp = /{([^}]+)}/g,
                found = [],
                curMatch;
            while( curMatch = regExp.exec( formular ) ) {
                found.push( curMatch[1] );
            }
            for( var i = 0; i < found.length; i++){
                var val = $element.find("[data-component='Dimension'][data-id='" + found[i] + "']").first().val();
                formular = formular.replace( '{' + found[i] + '}', val );
            }
            return formular;
        };
        var _handleRules = function () {
            $element.find("[data-component='Rule']").each(function(){
               var type = $(this).attr('data-rule-type'),
                   truewhen = $(this).attr('data-rule-truewhen'),
                   targetvalue = $(this).attr('data-rule-targetvalue'),
                   target = $(this).attr('data-rule-target-identifier'),
                   notice = $(this).attr('data-rule-notice'),
                   option = $(this).closest("[data-component='Option']").attr("data-optionid");
               if(type === 'dimension'){
                   $("[data-component='Dimension'][data-id='" + target +  "']").on('change', function(){
                       if( truewhen === "grtthan" && parseFloat($(this).val()) > targetvalue){
                           _setAbleState(false,option,notice);
                       }else if ( truewhen === "lssthan" && parseFloat($(this).val()) < targetvalue){
                           _setAbleState(false,option,notice);
                       } else {
                           _setAbleState(true,option);
                       }
                   })
               } else {
                   $("[data-component='SumoSelect'][data-optionid='" + target +  "']").on('change', function(){
                       var sel = false;
                       $("[data-component='Option'][data-optionid='" + target +  "']").find("[data-component='SumoSelect'] :selected").each(function(){
                           if( $(this).val() !== '-1' )
                               sel = true;
                       });
                       if( truewhen === 'hasselection' && sel)
                           _setAbleState(false,option,notice);
                       else if( truewhen  === 'noselection' && !sel)
                           _setAbleState(false,option,notice);
                       else
                           _setAbleState(true,option);

                   });
               }
            });
        };
        var _setAbleState = function(flag,option,notice){
            if(flag){
                $("[data-component='Option'][data-optionid='" + option + "']").removeClass('option-disabled').find("[data-component='SumoSelect']").each(function(){
                    this.sumo.enable();
                });
                $("[data-component='Option'][data-optionid='" + option + "']").find(".digidennis-mageme-option-notice").html('');
            } else {
                $("[data-component='Option'][data-optionid='" + option + "']").addClass('option-disabled').find("[data-component='SumoSelect']").each(function(){
                    this.sumo.disable();
                });
                $("[data-component='Option'][data-optionid='" + option + "']").find(".digidennis-mageme-option-notice").html(notice);
            }
            _update();
        };
        plugin.init();
    };

    $.fn.Product = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('Product')) {
                var plugin = new $.Product(this, options);
                $(this).data('Product', plugin);
            }

        });
    };
})(jQuery);