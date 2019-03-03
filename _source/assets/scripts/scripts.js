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
        getBasics();
    }

    init_calculator();
});