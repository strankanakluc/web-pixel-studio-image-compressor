jQuery(function ($) {
    var $button = $('#imagopby-optimize-gallery-btn');

    if (!$button.length) {
        return;
    }

    // Toggle active class on image-type chips
    $('.imagopby-chip-check').on('change', function () {
        $(this).closest('.imagopby-type-chip').toggleClass('imagopby-type-chip--active', this.checked);
    });

    // Quality slider — sync hidden input and badge
    var $slider = $('#imagopby_quality_range');
    var $hiddenInput = $('#imagopby_quality');
    var $badge = $('#imagopby-quality-value');

    $slider.on('input change', function () {
        var val = $(this).val();
        $hiddenInput.val(val);
        $badge.text(val);
    });

    // Bulk optimization
    $button.on('click', function (event) {
        event.preventDefault();

        var $btn     = $(this);
        var $wrap    = $('#imagopby-progress-bar-bg');
        var $fill    = $('#imagopby-progress-bar').css({ width: '0%' });
        var $status  = $('#imagopby-progress-status').text(imagopbyAdminData.initializing);
        var step     = 0;

        $wrap.show();
        $btn.prop('disabled', true);

        function runStep() {
            $.post(ajaxurl, {
                action:   'imagopby_optimize_gallery',
                step:     step,
                mode:     'delete',
                security: imagopbyAdminData.nonce
            }).done(function (response) {
                if (!response.success) {
                    $btn.prop('disabled', false);
                    $status.text(imagopbyAdminData.error + ' ' + response.data);
                    return;
                }

                var data = response.data;
                $fill.css({ width: data.progress + '%' });
                $status.text(data.progress + '% – ' + imagopbyAdminData.processing + ': ' + data.optimized + '/' + data.total);

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
