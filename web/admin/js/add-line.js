$(function() {

    hideFields();

    $('#addlineform-type').on('change', function() {
        hideFields();
        switch (parseInt($(this).val())) {
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
    });

    function hideFields() {
        $('.field-addlineform-add_type').hide();
        $('.field-addlineform-player_id').hide();
        $('.field-addlineform-value').hide();
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