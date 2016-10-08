$(function() {

});

function template_changed(template_id) {
    var src = 'alias_column_grid.php?template_id=' + template_id;
    $("#template_columns iframe").attr("src",'');
    $("#template_columns").attr('data-src',src);

}

function set_template_for_upload(template_id) {
    var my_object = {};
    my_object.action = 'set_default';
    my_object.template_id = template_id;
    $.ajax({
        type: "POST",
        url: 'ajax_templates.php',
        data: my_object,
        async: true,
        success: function(data) {
            if (data.valid) {
                //ok
                return;
            }
            if (data.message) {
                $(".main-header").noty({
                    text: data.message,
                    type: 'error',
                    dismissQueue: true,
                    layout: 'top',
                    theme: 'defaultTheme',
                    timeout: 20000
                });
            }
        }
    })
        .fail(function() {
            $(".main-header").noty({
                text: 'could not connect with server',
                type: 'error',
                dismissQueue: true,
                layout: 'top',
                theme: 'defaultTheme',
                timeout: 20000
            });
        })
    ;
}