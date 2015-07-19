$(document).ready(function () {
    var formData = {
        'page': $('input[name=page]').val(),
        'sort': $('input[name=sort]').val(),
        'total': $('input[name=total]').val()
    };
    $.ajax({
        type: 'POST',
        url: 'cache.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function (data) {

        });
});