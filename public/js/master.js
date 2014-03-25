$(document).ready(function() {
    $('.delete').bind('click', function(event) {
        event.preventDefault();
        var location = $(this).attr('href');
        $.ajax({
            type: 'DELETE',
            url: location,
            context: $(this),
            success: function() {
                $(this).parent().parent().fadeOut();
            },
            error: function(request, status, error) {
                alert('An error occurred, please try again.');
            }
        });
    });
});


