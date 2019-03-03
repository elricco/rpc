require ('nouislider');

$(function () {
    function getBasics() {
        $.get(
            'index.php?rex-api-call=PrintConfiguratorGetData',
            function (data) {
                console.log(data);
            }
        );
    }

    function init_calculator() {
        console.log('Initial');
        getBasics();
    }

    init_calculator();
});