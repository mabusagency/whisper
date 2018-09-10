$(function () {
    $('.collapse').collapse();

    $( "#search" ).keyup(function() {
        var current_search = $(this).val().toLowerCase();

        $( ".panel-title" ).each(function( ) {
            if(!$( this ).text().trim().toLowerCase().includes(current_search)) {
                $('#'+($(this).attr('state'))).hide();
            } else {
                $('#'+($(this).attr('state'))).show();
            }
        });
    });

    $( ".stateCheckbox" ).click(function() {
        if($(this).is(':checked')) {
            $('.'+$(this).attr('name')).prop('checked', true);
        } else {
            $('.'+$(this).attr('name')).prop('checked', false);
        }
    });

    $(document).on('change', '#trigger', function () {
        $('.option_checkbox').prop('checked', false);
        var selected_trigger = $('#trigger option:selected').text();
        console.log(selected_trigger);
        if (selected_trigger == 'County') {
            $('#counties_input').show();
            $('#field_inputs').hide();
            $('.field_options').hide();
        } else {
            $('#field_inputs').show();
            $('.field_options').hide();
            $('#'+selected_trigger+'_options').show();
            $('#counties_input').hide();
        }
    });

    $(document).on('change', '#role', function () {
        var selected_role = $('#role option:selected').text();

        if (selected_role == 'Recruiter') {
            $('#password_section').show();
        } else {
            $('#password_section').hide();
        }
    });
});