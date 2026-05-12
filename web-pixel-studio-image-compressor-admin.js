jQuery(function ($) {
    var imagopbyAjaxUrl = (typeof ajaxurl !== 'undefined') ? ajaxurl : (imagopbyAdminData && imagopbyAdminData.ajaxUrl ? imagopbyAdminData.ajaxUrl : '');

    function imagopbyRefreshAttachmentPreview(postId, payload) {
        var cacheBustedUrl = '';
        if (payload && payload.url) {
            var cleanUrl = payload.url.replace(/([?&])imagopby_ts=\d+/g, '').replace(/[?&]$/, '');
            var joiner = cleanUrl.indexOf('?') === -1 ? '?' : '&';
            cacheBustedUrl = cleanUrl + joiner + 'imagopby_ts=' + Date.now();
        }

        // Try to refresh WP media models first (grid/modal without full page reload).
        if (typeof wp !== 'undefined' && wp.media && wp.media.attachment) {
            try {
                var attachment = wp.media.attachment(postId);
                if (attachment && payload) {
                    var attrs = {};
                    if (payload.url) {
                        attrs.url = payload.url;
                    }
                    if (payload.filename) {
                        attrs.filename = payload.filename;
                    }
                    if (payload.mime) {
                        attrs.mime = payload.mime;
                        if (payload.mime.indexOf('/') > -1) {
                            var mimeParts = payload.mime.split('/');
                            attrs.type = mimeParts[0];
                            attrs.subtype = mimeParts[1];
                        }
                    }
                    if (payload.filesizeHumanReadable) {
                        attrs.filesizeHumanReadable = payload.filesizeHumanReadable;
                    }
                    if (payload.filesizeInBytes) {
                        attrs.filesizeInBytes = payload.filesizeInBytes;
                    }
                    attachment.set(attrs);
                    attachment.trigger('change');
                }
                if (attachment && attachment.fetch) {
                    attachment.fetch();
                }

                if (wp.media.frame && wp.media.frame.content && wp.media.frame.content.get) {
                    var content = wp.media.frame.content.get();
                    if (content && content.collection && content.collection.fetch) {
                        content.collection.fetch({ reset: true });
                    }
                }
            } catch (e) {
                // Non-blocking: fallback cache-bust runs below.
            }
        }

        // Update right-side attachment details text fields immediately.
        if (payload && payload.filename) {
            $('.attachment-details .filename, .attachment-info .filename').text(payload.filename);
        }
        if (payload && payload.mime) {
            $('.attachment-details .mime-type, .attachment-info .mime-type').text(payload.mime);
        }
        if (cacheBustedUrl) {
            $('.attachment-details .url, .attachment-info .url').text(payload.url);
        }
        if (payload && payload.filesizeHumanReadable) {
            $('.attachment-details .file-size, .attachment-info .file-size, .attachment-details [class*="filesize"], .attachment-info [class*="filesize"]').text(payload.filesizeHumanReadable);
            // WP media modal shows filesize in a <span> inside the details sidebar
            $('.attachment-details .details').find('span').filter(function () {
                return /^\d/.test($(this).text()) && $(this).text().match(/KB|MB|GB|B/);
            }).text(payload.filesizeHumanReadable);
        }

        // Fallback: refresh visible thumbnail URLs with cache-buster.
        $('.attachment[data-id="' + postId + '"] img, .attachment-details-preview img').each(function () {
            var $img = $(this);
            var src = cacheBustedUrl || $img.attr('src');
            if (!src) {
                return;
            }
            var clean = src.replace(/([?&])imagopby_ts=\d+/g, '').replace(/[?&]$/, '');
            var glue = clean.indexOf('?') === -1 ? '?' : '&';
            $img.attr('src', clean + glue + 'imagopby_ts=' + Date.now());
        });
    }

    // ---- Image type chips (settings page) ----
    $('.imagopby-chip-check').on('change', function () {
        $(this).closest('.imagopby-type-chip').toggleClass('imagopby-type-chip--active', this.checked);
    });

    // ---- Quality slider (settings page) ----
    var $slider      = $('#imagopby_quality_range');
    var $hiddenInput = $('#imagopby_quality');
    var $badge       = $('#imagopby-quality-value');

    $slider.on('input change', function () {
        var val = $(this).val();
        $hiddenInput.val(val);
        $badge.text(val);
    });

    // ---- Bulk optimization (settings page) ----
    var $button = $('#imagopby-optimize-gallery-btn');
    if ($button.length) {
        $button.on('click', function (event) {
            event.preventDefault();

            var $btn    = $(this);
            var $wrap   = $('#imagopby-progress-bar-bg');
            var $fill   = $('#imagopby-progress-bar').css({ width: '0%' });
            var $status = $('#imagopby-progress-status').text(imagopbyAdminData.initializing);
            var step    = 0;

            $wrap.show();
            $btn.prop('disabled', true);

            function runStep() {
                $.post(imagopbyAjaxUrl, {
                    action:   'imagopby_optimize_gallery',
                    step:     step,
                    security: imagopbyAdminData.nonce
                }).done(function (response) {
                    if (!response.success) {
                        $btn.prop('disabled', false);
                        $status.text(imagopbyAdminData.error + ' ' + response.data);
                        return;
                    }
                    var data = response.data;
                    $fill.css({ width: data.progress + '%' });
                    $status.text(data.progress + '% \u2013 ' + imagopbyAdminData.processing + ': ' + data.optimized + '/' + data.total);
                    if (data.finished) {
                        $btn.prop('disabled', false);
                        $status.text('\u2713 ' + imagopbyAdminData.done + ' ' + data.optimized);
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
    }

    // ---- Single image optimize button (attachment fields modal / edit page) ----
    $(document).on('click', '.imagopby-optimize-single', function () {
        var $btn    = $(this);
        var postId  = $btn.data('id');
        var $status = $btn.siblings('.imagopby-single-status');

        $btn.prop('disabled', true).text(imagopbyAdminData.optimizing || 'Optimizing\u2026');
        $status.text('');

        $.post(imagopbyAjaxUrl, {
            action:   'imagopby_optimize_single',
            post_id:  postId,
            security: imagopbyAdminData.nonce
        }).done(function (response) {
            if (response.success) {
                $btn.replaceWith('<span class="imagopby-media-badge imagopby-media-badge--webp">WebP \u2713</span>');
                imagopbyRefreshAttachmentPreview(postId, response.data || {});
            } else {
                $btn.prop('disabled', false).text(imagopbyAdminData.optimizeBtn || 'Optimize to WebP');
                $status.text('\u2717 ' + response.data);
            }
        }).fail(function () {
            $btn.prop('disabled', false).text(imagopbyAdminData.optimizeBtn || 'Optimize to WebP');
            $status.text('\u2717 ' + (imagopbyAdminData.error || 'Error'));
        });
    });
});
