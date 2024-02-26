$(function() {
    
    $('.player-total-action').on('click', function () {

        let $this = $(this);

        var icon = $this.children('svg');
        var action = $this.attr('data-action');

        if(action === '') return false;

        var data = {
            action: action,
            total: $(this).data('total'),
        };
        $.ajax({
            url: '/statistic/total/player-total-action',
            type: 'POST',
            data: data,
            success: function(response) {
                if(action === 'add') {
                    iconAction($this, 'remove', icon,'fa-plus', 'fa-minus');
                }
                else if(action === 'remove') {
                    iconAction($this, 'add', icon, 'fa-minus', 'fa-plus');
                }
                //console.log(response);
            },
            error: function(){
                alert('Error: Player Total');
            }
        });

        return false;
    });

    function iconAction($this, action, icon, oldClass, newClass) {
        $this.attr('data-action', action);
        $this.attr('title', action)
        icon.removeClass(oldClass)
        icon.addClass(newClass);
    }
    
});