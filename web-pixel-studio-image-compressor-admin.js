jQuery(function ($) {
    var $button = $('#imagopby-optimize-gallery-btn');

    if (!$button.length) {
        return;
    }

    $button.on('click', function (event) {
        event.preventDefault();

        var $btn = $(this);
        var $bar = $('#imagopby-progress-bar').css({ width: '0%' }).show();
        $('#imagopby-progress-bar-bg').show();

        var $status = $('#imagopby-progress-status').text(imagopbyAdminData.initializing);
        var step = 0;

        $btn.prop('disabled', true);

        function runStep() {
            $.post(ajaxurl, {
                action: 'imagopby_optimize_gallery',
                step: step,
                mode: 'delete',
                security: imagopbyAdminData.nonce
            }).done(function (response) {
                if (!response.success) {
                    $btn.prop('disabled', false);
                    $status.text(imagopbyAdminData.error + ' ' + response.data);
                    return;
                }

                var data = response.data;
                $bar.css({ width: data.progress + '%' });
                $status.text(data.progress + '% - ' + imagopbyAdminData.processing + ': ' + data.optimized + '/' + data.total);

                if (data.finished) {
                    $btn.prop('disabled', false);
                    $status.text('✓ ' + imagopbyAdminData.done + ' ' + data.optimized);
                    return;
                }

                step = data.step;
                setTimeout(runStep, 400);
            }).fail(function (xhr, textStatus) {
                $btn.prop('disabled', false);
                $status.text(imagopbyAdminData.error + ' ' + textStatus);
            });
        }

        runStep();
    });
});
