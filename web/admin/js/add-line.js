$(function() {

    hideFields();

    if($('#addlineform-type').val() != '') {
        actions(parseInt($('#addlineform-type').val()));
    }

    $('#addlineform-type').on('change', function() {
        hideFields();
        emptyValues();
        actions(parseInt($(this).val()));
    });

    $('#addlineform-event_id').on('change', function() {
        $.ajax({
            url: '/event/players',
            type: 'POST',
            data: {id: parseInt($(this).val())},
            success: function(response) {
                var playerDropDownList = document.getElementById("addlineform-player_id");
                // remove player options
                playerDropDownList.options.length = 0;
                // add new options
                $.each(response, function(index, value) {
                    console.log(value);
                    var option = document.createElement("option");
                    option.text = value;
                    option.value = index;
                    playerDropDownList.options.add(option);
                });
            },
            error: function(){
                alert('error - players');
            }
        });

    });

    function actions(val) {
        switch (val) {
            case 1:
                spreads();
                break;
            case 2:
                totals();
                break;
            case 3:
                teamTotals();
                break;
            case 4:
                moneyline();
                break;
            case 7:
                setsSpreads();
                break;
            case 8:
                setsTotals();
                break;
            default:
                break;
        }
    }

    function hideFields() {
        $('.field-addlineform-add_type').hide();
        $('.field-addlineform-player_id').hide();
        $('.field-addlineform-value').hide();
    }

    function emptyValues() {
        $('#addlineform-add_type').val('');
        $('#addlineform-player_id').val('');
        $('#addlineform-value').val('');
    }

    function spreads() {
        $('.field-addlineform-player_id').show();
        $('.field-addlineform-value').show();
    }

    function totals() {
        $('.field-addlineform-add_type').show();
        $('.field-addlineform-value').show();
    }

    function teamTotals() {
        $('.field-addlineform-add_type').show();
        $('.field-addlineform-player_id').show();
        $('.field-addlineform-value').show();
    }

    function moneyline() {
        $('.field-addlineform-player_id').show();
    }

    function setsSpreads() {
        $('.field-addlineform-player_id').show();
        $('.field-addlineform-value').show();
    }

    function setsTotals() {
        $('.field-addlineform-add_type').show();
        $('.field-addlineform-value').show();
    }

});