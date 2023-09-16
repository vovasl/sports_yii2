$(function() {

    var fields = hideFields();
    showAndHideFields(fields);

    if($('#addlineform-type').val() != '') {
        actions(parseInt($('#addlineform-type').val()));
    }

    $('#addlineform-type').on('change', function() {
        showAndHideFields(fields);
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
        var fields = {};
        switch (val) {
            case 1:
                fields = spreads();
                break;
            case 2:
                fields = totals();
                break;
            case 4:
                fields = moneyline();
                break;
            case 7:
                fields = setsSpreads();
                break;
            case 8:
                fields = setsTotals();
                break;
            default:
                break;
        }
        showAndHideFields(fields);
    }

    function spreads() {
        return {
            show: [
                '.field-addlineform-value',
                '.field-addlineform-odd_home',
                '.field-addlineform-odd_away'
            ],
            hide: [
            ]
        };
    }

    function totals() {
        return {
            show: [
                '.field-addlineform-value',
                '.field-addlineform-odd_over',
                '.field-addlineform-odd_under'
            ],
            hide: [
            ]
        };
    }

    function moneyline() {
        return {
            show: [
                '.field-addlineform-odd_home',
                '.field-addlineform-odd_away'
            ],
            hide: [
            ]
        };
    }

    function setsSpreads() {
        return {
            show: [
                '.field-addlineform-value',
                '.field-addlineform-odd_home',
                '.field-addlineform-odd_away'
            ],
            hide: [
            ]
        };
    }

    function setsTotals() {
        return {
            show: [
                '.field-addlineform-value',
                '.field-addlineform-odd_over',
                '.field-addlineform-odd_under'
            ],
            hide: [
            ]
        };
    }

    function hideFields() {
        return {
            show: [

            ],
            hide: [
                '.field-addlineform-add_type',
                '.field-addlineform-player_id',
                '.field-addlineform-value',
                '.field-addlineform-odd_home',
                '.field-addlineform-odd_away',
                '.field-addlineform-odd_over',
                '.field-addlineform-odd_under'
            ]
        };
    }

    function emptyValues() {
        var selectors = [
            '#addlineform-add_type',
            '#addlineform-player_id',
            '#addlineform-value',
            '#addlineform-odd_home',
            '#addlineform-odd_away',
            '#addlineform-odd_over',
            '#addlineform-odd_under'
        ];

        selectors.forEach(function (item) {
            $(item).val('');
        })
    }

    function showAndHideFields(fields) {
        // show fields
        fields.show.forEach( function (field) {
            $(field).show();
        });

        // hide fields
        fields.hide.forEach( function (field) {
            $(field).hide();
        });
    }

});