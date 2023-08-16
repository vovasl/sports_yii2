$(function() {

    $('.full-log-btn').on('click', function() {
        $('#full-log').toggle();
        $('.full-log-btn').text(function(i, text){
            return text === "Show Full Log" ? "Hide Full Log" : "Show Full Log";
        })
        return false
    });

});