$(function() {
    //default material events activate
    $(".button-collapse-custom").sideNav();
    $(".dropdown-button").dropdown({ hover: true });
    $('.modal').modal();

    // custom sub dropdown menu
    $('.subdropdown-button').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrain_width: true,
            hover: true,
            gutter: 0,
            belowOrigin: true,
            alignment: 'left'
        }
    );

    $('select').material_select();

    // flash timer to hide
    setTimeout(function(){
        $('.flash-cont').fadeOut(600);
    }, 6000);

    // Status action ajax handler
    $('.status-action').on('click', function () {
        var butt = $(this),
            action = butt.data('action'),
            service = butt.parent().prev().text();

        $.ajax({
            url: '../user-edit-ajax',
            dataType: "json",
            type: 'POST',
            data: {
                action: action,
                service: service
            },
            success: function(data) {
                if (data.status === true) {
                    butt.attr('disabled', true)
                        .siblings('.status-action')
                        .attr('disabled', false);
                }
            },
            error: function(e) {
                console.log('error, невозможно изменить статус: ', e);
            }
        });
    });

    //Service button
    $('.service-handler').on('click', function () {
        var tr = $(this).closest('tr'),
            id = +tr.children('.loop-first').text(),
            input = tr.find('.loop-last'),
            message;

        $.ajax({
            url: './service-edit-ajax',
            dataType: "json",
            type: 'POST',
            data: {
                id: id,
                value: input.val()
            },
            success: function(data) {
                var messageCont;
                if (data.status === true) {
                    message = $('<span class="service-status">'
                                + 'Новое имя для сервиса с ID: ' + data.id +
                                '<br>Установлено значение: "' + data.newValue +
                                '"</span>');
                    Materialize.toast(message, 3000);
                }
            },
            error: function(e) {
                console.log('error, невозможно изменить статус: ', e);
            }
        });
    });

});