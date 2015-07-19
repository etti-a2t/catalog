$(document).ready(function () {
    $('#form').submit(function (event) {

        var formData = {
            'action': $('input[name=action]').val(),
            'id': $('input[name=id]').val(),
            'name': $('input[name=name]').val(),
            'description': $('textarea[name=description]').val(),
            'price': $('input[name=price]').val(),
            'url': $('input[name=url]').val()
        };

        $.ajax({
            type: 'POST',
            url: 'save.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
            .done(function (data) {

                if (data.errors instanceof Object) {
                    errors = data.errors;
                    $.each( errors, function( key, value ) {
                        $('div[id='+key+']').addClass('has-error has-feedback')
                        alert(value);
                    });

                }else{
                    alert('Сохранено!');
                }
            });

        event.preventDefault();
    });
    $('.link').click(function (event) {
        var elem = $(this);
        $.ajax({
            type: "GET",
            url: "remove.php",
            data: "id="+elem.attr('data-artid'),
            dataType:"json",
            success: function(data) {
                if(data.success){
                    $('#'+elem.attr('data-artid')).hide();
                }
                   alert(data.message);
            }
        });
        return false;
        event.preventDefault();
    });

});
