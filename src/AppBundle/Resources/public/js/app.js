$(document).ready(function() {
    //bindAuthentificationForm();

    $('[data-toggle="confirmation"]').confirmation();
    $('select').select2();

    $(document).on('click', '.env-delete', function(e) {
        var self = $(this);
        $.ajax({
            method: 'POST',
            url: $(this).data('url'),
            success: function() {
                self.parents('tr').remove();
            }
        });
    });

    $(document).on('click', '.webhook-delete', function(e) {
        var self = $(this);
        $.ajax({
            method: 'POST',
            url: $(this).data('url'),
            success: function() {
                self.parents('tr').remove();
            }
        });
    });

    $(document).on('click', '.slack-delete', function(e) {
        var self = $(this);
        $.ajax({
            method: 'POST',
            url: $(this).data('url'),
            success: function() {
                self.parents('tr').remove();
            }
        });
    });

    $(document).on('click', '.collaborator-delete', function(e) {
        var self = $(this);
        $.ajax({
           method: 'POST',
           url: $(this).data('url'),
           success: function() {
               self.parents('tr').remove();
           }
        });
    });

    $(document).on('click', '.check-permissions button', function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).data('href'),
            method: 'POST',
            success: function() {
                if (!repositoryOptions.currentlyChecking) {
                    checkStatePermissions();
                }
            }
        });
    });
});

function checkStatePermissions()
{
    repositoryOptions.currentlyChecking = true;

    $.ajax({
        url: repositoryOptions.stateUrl,
        method: 'GET',
        success: function(data) {
            if (!data) {
                setTimeout(function() {
                    checkStatePermissions();
                }, 2000);
            } else {
                repositoryOptions.currentlyChecking = false;
                $('.check-permissions').hide();
                $('.permissions-success').show();
            }
        }
    });
}

function bindAuthentificationForm()
{
    $(document).on('submit', '.project-configure-authentification form', function(e) {
        processAuthentificationForm($(this), e);
    });
}

function processAuthentificationForm(target, e) {
    e.preventDefault();

    var submit = target.find('.btn-primary');
    submit.hide();

    var container = target.parents('.project-configure-authentification');

    $.ajax({
        method: 'POST',
        url: target.attr('action'),
        data:   target.serialize(),
        error: function(data) {
            container.html(data.responseText);
            submit.show();
        },
        success: function() {
            container.empty();
            $(container.data('success')).show();

            if ($('.project-configure-authentification form').length === 0) {
                $('#project-configure-continue').show();
            }
        }
    });
}
