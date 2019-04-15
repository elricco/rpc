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

    function init_calculator() {
        getBasics();
        replaceTextWithHtml($('[name*="paper_options"]').next());
        calculate_price();
    }

    init_calculator();
});