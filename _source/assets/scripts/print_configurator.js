const noUiSlider = require ('nouislider');

$(function () {
    function getBasics() {
        $.get(
            'index.php?rex-api-call=PrintConfiguratorGetData',
            function (data) {
                console.log(data);
            }
        );
    }

    function calculate_price() {

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
    // set update listener
    slider_baw.noUiSlider.on('update', function( values, handle ) {
        input_baw.value = Number.parseFloat(values[handle]).toFixed(0); // convert number to no decimals
        calculate_price();
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
    slider_clr.noUiSlider.on('update', function( values, handle ) {
        input_clr.value = Number.parseFloat(values[handle]).toFixed(0); // convert number to no decimals
        calculate_price();
    });

    function init_calculator() {
        getBasics();
    }

    init_calculator();
});