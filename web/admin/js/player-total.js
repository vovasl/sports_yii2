$(function() {
    
    $('.player-total-action').on('click', function () {

        var icon = $(this).children('svg');
        var action = $(this).data('action');

        if(action === '') return false;

        if(action === 'add') {
            add($(this), icon, 'remove');
        }
        else if(action === 'remove') {
            remove($(this), icon,  'add');
        }

        var data = {
            action: action,
            total: $(this).data('total'),
        };
        $.ajax({
            url: '/total/player-total-action',
            type: 'POST',
            data: data,
            success: function(response) {
                console.log(response);
            },
            error: function(){
                alert('error - player total');
            }
        });

        return false;
    });

    function add(el, icon, newAction) {
        el.data('action', newAction);
        icon.removeClass('fa-plus')
        icon.addClass('fa-minus');
    }

    function remove(el, icon, newAction) {
        el.data('action', newAction);
        icon.removeClass('fa-minus')
        icon.addClass('fa-plus');
    }
    
});