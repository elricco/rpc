const noUiSlider = require ('nouislider');

$(function () {
    function replaceTextWithHtml(selector) {
        $.each(selector, function(index, value) {
            let txtToHtml = $(value).text();
            $(this).html(txtToHtml);
        });
    }

    function getBasics() {
        $.get(
            'index.php?rex-api-call=PrintConfiguratorGetData',
            function (data) {
                console.log(data);
            }
        );
    }

    function calculate_price() {
        let data;

        //serializeArray and convert to object
        //https://stackoverflow.com/questions/2276463/how-can-i-get-form-data-with-javascript-jquery
        data = $('#config_form').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        $.get(
            'index.php?rex-api-call=PrintConfiguratorCalculatePrice', {
                data: data
            },
            function (data) {
                console.log(data);
            }
        ).done(function (data) {
            console.log(data);
            // send paper and print
            $('.order-data_check').html(data['dom_elements']['order-data_check']);
            $('.order-paper').html(data['dom_elements']['order-paper']);
            $('.order-subtotal').html(data['dom_elements']['order-subtotal']);
            $('.order-fixations').html(data['dom_elements']['order-fixations']);
        }).fail(function () {
            
        });
    }
    function init_calculator() {
        getBasics();

        // set range slider for black and white pages
        let slider_baw = document.getElementById('page_baw');
        let $slider_baw = $('#page_baw');
        let input_baw_id = $slider_baw.next('div').find('input').prop('id');
        let input_baw = document.getElementById(input_baw_id);
        noUiSlider.create(slider_baw, {
            start: [ $slider_baw.next('div').find('input').data('start') ],
            step: 1,
            connect: [ true,false ],
            range: {
                'min': [ $slider_baw.next('div').find('input').data('min') ],
                'max': [ $slider_baw.next('div').find('input').data('max') ]
            },
        });

        // set range slider for coloured pages
        let slider_clr = document.getElementById('page_clr');
        let $slider_clr = $('#page_clr');
        let input_clr_id = $slider_clr.next('div').find('input').prop('id');
        let input_clr = document.getElementById(input_clr_id);
        noUiSlider.create(slider_clr, {
            start: [ $slider_clr.next('div').find('input').data('start') ],
            step: 1,
            connect: [ true,false ],
            range: {
                'min': [ $slider_clr.next('div').find('input').data('min') ],
                'max': [ $slider_clr.next('div').find('input').data('max') ]
            }
        });

        // set update listener
        slider_baw.noUiSlider.on('set', function( values, handle ) {
            input_baw.value = Number.parseFloat(values[handle]).toFixed(0); // convert number to no decimals
            slider_clr.noUiSlider.updateOptions({
                range: {
                    'min': [ $slider_clr.next('div').find('input').data('min') ],
                    'max': [ Number.parseInt(input_baw.value) ]
                }
            });
            calculate_price();
        });
        input_baw.addEventListener('keyup', function () {
            slider_baw.noUiSlider.set(this.value);
            slider_clr.noUiSlider.updateOptions({
                range: {
                    'min': [ $slider_clr.next('div').find('input').data('min') ],
                    'max': [ Number.parseInt(input_baw.value) ]
                }
            });
            calculate_price();
        });

        // set update listener
        slider_clr.noUiSlider.on('set', function( values, handle ) {
            input_clr.value = Number.parseFloat(values[handle]).toFixed(0); // convert number to no decimals
            calculate_price();
        });
        input_clr.addEventListener('keyup', function () {
            slider_clr.noUiSlider.set(this.value);
            calculate_price();
        });

        // set update listener
        $('input:radio').on('click',function () {
            $.each($('input:radio:checked'),function (index, value) {
                    calculate_price();
            });
        });

        // set update listener
        $('input[name*="fixation"]').on('keyup', function () {
            calculate_price();
        });

        replaceTextWithHtml($('[name*="paper_options"]').next());
        calculate_price();
    }
    if($('#config_form').length) {
        init_calculator();
    }

    if($('#options_form').length) {

        if($('#yform-options_form-spine_data').length) {
            $('[name="firstname"]').on('keypress keyup blur', function() {
                $('[name="spine_firstname"]').val($(this).val());
            });
            $('[name="lastname"]').on('keypress keyup blur', function() {
                $('[name="spine_lastname"]').val($(this).val());
            });
            $('[name="type_of_work"]').on('keypress keyup blur', function() {
                $('[name="spine_type_of_work"]').val($(this).val());
            });
            $('[name="title"]').on('keypress keyup blur', function() {
                $('[name="spine_title"]').val($(this).val());
            });
        }

        $('select[name*="_template"]').on('change', function () {
            let $templateColorId = $('#' + $(this).val());
            let $fixationTemplate = $(this).attr('name') + '_color';
            let fxTemp = $('select[name="' + $fixationTemplate + '"]');
            let c = {};
            let colors = $templateColorId.data('template-colors').split(',');
            $.each(colors, function (index, value) {
                c[value] = value;
            });

            fxTemp.empty(); // remove old options
            $.each(c, function(key,value) {
                fxTemp.append($("<option></option>")
                    .attr("value", value).text(key));
            });
        });

    }

    if($('#billing_form').length) {
        $('[name*="delivery_address_same_as_billing_address"]').on('click', function () {
            if($(this).prop('checked')) {
                $('#yform-billing_form-billing_address').hide();
                $('[name="billing_firstname"]').val($('[name="firstname"]').val());
                $('[name="billing_lastname"]').val($('[name="lastname"]').val());
                $('[name="billing_street"]').val($('[name="street"]').val());
                $('[name="billing_street_no"]').val($('[name="street_no"]').val());
                $('[name="billing_additional_info"]').val($('[name="additional_info"]').val());
                $('[name="billing_zip"]').val($('[name="zip"]').val());
                $('[name="billing_city"]').val($('[name="city"]').val());
                let id = $('[name="salutation"]:checked').attr('id');
                let split_id = id.split('-');
                let last_string = split_id.slice(-1);
                $.each($('[id$="-' + last_string[0] + '"]'), function (index, value) {
                    $(this).prop('checked', true);
                });

                $('[name="salutation"]').on('click', function () {
                    let id = $(this).attr('id');
                    let split_id = id.split('-');
                    let last_string = split_id.slice(-1);
                    $.each($('[id$="-' + last_string[0] + '"]'), function (index, value) {
                        $(this).prop('checked', true);
                    });
                });
                $('[name="firstname"]').on('keypress keyup blur', function () {
                    $('[name="billing_firstname"]').val($(this).val());
                });
                $('[name="lastname"]').on('keypress keyup blur', function () {
                    $('[name="billing_lastname"]').val($(this).val());
                });
                $('[name="street"]').on('keypress keyup blur', function () {
                    $('[name="billing_street"]').val($(this).val());
                });
                $('[name="street_no"]').on('keypress keyup blur', function () {
                    $('[name="billing_street_no"]').val($(this).val());
                });
                $('[name="additional_info"]').on('keypress keyup blur', function () {
                    $('[name="billing_additional_info"]').val($(this).val());
                });
                $('[name="zip"]').on('keypress keyup blur', function () {
                    $('[name="billing_zip"]').val($(this).val());
                });
                $('[name="city"]').on('keypress keyup blur', function () {
                    $('[name="billing_city"]').val($(this).val());
                });
            } else {
                $('#yform-billing_form-billing_address').show();
                $('[name="salutation"]').off();
                $('[name="firstname"]').off();
                $('[name="lastname"]').off();
                $('[name="street"]').off();
                $('[name="street_no"]').off();
                $('[name="additional_info"]').off();
                $('[name="zip"]').off();
                $('[name="city"]').off();
            }
        })
    }
});