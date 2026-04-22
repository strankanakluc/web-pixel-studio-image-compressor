<?php
/**
 * Plugin Name: Web Pixel Studio Image Compressor
 * Plugin URI: http://wordpress.org/plugins/web-pixel-studio-image-compressor
 * Description: Optimizes images on upload. Convert JPEG, PNG, GIF, BMP, TIFF, and HEIC/HEIF to WebP. Define max. size and select the types of images to convert and what quality the optimized image should have.
 * Author: Web Pixel Studio
 * Author URI: https://wps.sk
 * Version: 1.0.0
 * Tested up to: 6.9
 * License: GPL-2.0+
 * @category Plugin
 * @package  Web_Pixel_Studio_Image_Compressor
 * @link     https://wps.sk
 * @php      7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ========== SETTINGS PAGE ==========
function imagopby_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Image Compressor', 'web-pixel-studio-image-compressor'); ?></h1>
        
        <form action='options.php' method='post'>
            <div class="imagopby-section-header">
                <h2><?php esc_html_e('Plugin Settings', 'web-pixel-studio-image-compressor'); ?></h2>
            </div>
            <div class="imagopby-section-description">
                <p><?php esc_html_e('Optimizes images when uploading. Convert JPEG, PNG, GIF, BMP, TIFF, and HEIC/HEIF files to WebP format for better performance. Define the maximum image size and choose which file types should be converted and what quality the optimized image should have.', 'web-pixel-studio-image-compressor'); ?></p>
            </div>
            <table class="form-table imagopby-form-section">
                <?php
                settings_fields('imagopby_settings');
                do_settings_sections('imagopby_settings');
                ?>
            </table>
            <div class="submit">
                <button type="button" class="button imagopby-bulk-btn" id="imagopby-optimize-gallery-btn">
                    <?php esc_html_e('Start Bulk Optimization', 'web-pixel-studio-image-compressor'); ?>
                </button>
                <button type="submit" class="button imagopby-save-btn">
                    <?php esc_html_e('Save Settings', 'web-pixel-studio-image-compressor'); ?>
                </button>
            </div>
        </form>

        <div class="imagopby-footer">
            <h2><?php esc_html_e('Thank you for using Image Compressor!', 'web-pixel-studio-image-compressor'); ?></h2>
            <h3><?php esc_html_e('We\'re committed to helping your website perform better.', 'web-pixel-studio-image-compressor'); ?></h3>
            
            <div class="imagopby-social-links">
                <?php esc_html_e('Follow us:', 'web-pixel-studio-image-compressor'); ?>
                <a href="https://www.instagram.com/tvorbawebov/" target="_blank" rel="noopener noreferrer">Instagram</a>
                <a href="https://www.facebook.com/strankanakluc/" target="_blank" rel="noopener noreferrer">Facebook</a>
            </div>
            
            <h3><?php esc_html_e('Visit our website to learn more:', 'web-pixel-studio-image-compressor'); ?></h3>
            <p class="imagopby-website">
                <a href="https://wps.sk" target="_blank" rel="noopener noreferrer">wps.sk</a>
            </p>
            
            <h3 style="margin-top: 20px; font-size: 0.9em; color: #999;">
                <?php esc_html_e('Image Compressor by', 'web-pixel-studio-image-compressor'); ?> <strong><?php esc_html_e('Web Pixel Studio', 'web-pixel-studio-image-compressor'); ?></strong>
            </h3>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($){
        $('#imagopby-optimize-gallery-btn').on('click', function(e){
            e.preventDefault();
            let $btn = $(this);
            let $barbg = $('#imagopby-progress-bar-bg').show();
            let $bar = $('#imagopby-progress-bar').css({width:'0%'}).show();
            let $status = $('#imagopby-progress-status').text('<?php esc_html_e("Initializing...", "web-pixel-studio-image-compressor"); ?>');
            $btn.prop('disabled', true);
            let step = 0;
            function runStep() {
                $.post(ajaxurl, {
                    action:'imagopby_optimize_gallery', 
                    step:step, 
                    mode:'delete',
                    security:'<?php echo esc_js(wp_create_nonce('imagopby_optimize_action')); ?>'
                }, function(resp){
                    if(resp.success) {
                        $bar.css({width:resp.data.progress+'%'});
                        $status.text(resp.data.progress + '% - <?php esc_html_e("Processing", "web-pixel-studio-image-compressor"); ?>: ' + resp.data.optimized + '/' + resp.data.total);
                        if(resp.data.finished) {
                            $btn.prop('disabled', false);
                            $status.text('<?php esc_html_e("✓ Done! Optimized images:", "web-pixel-studio-image-compressor"); ?> ' + resp.data.optimized);
                        } else {
                            step = resp.data.step;
                            setTimeout(runStep, 400);
                        }
                    } else {
                        $btn.prop('disabled', false);
                        $status.text('<?php esc_html_e("✗ Error:", "web-pixel-studio-image-compressor"); ?> ' + resp.data);
                    }
                });
            }
            runStep();
        });
    });
    </script>
    <?php
}

// Menu a settings section
add_action('admin_menu', 'imagopby_add_menu');
function imagopby_add_menu()
{
    add_options_page(
        'Image Compressor Settings',
        'Image Compressor',
        'manage_options',
        'imagopby',
        'imagopby_settings_page'
    );
}

// Enqueue admin styles
add_action('admin_enqueue_scripts', 'imagopby_enqueue_admin_styles');
function imagopby_enqueue_admin_styles($hookSuffix)
{
    if ($hookSuffix == 'settings_page_imagopby') {
        wp_enqueue_style('imagopby-admin', plugin_dir_url(__FILE__) . 'web-pixel-studio-image-compressor-admin.css', array(), '1.0.0');
    }
}

// Register settings
add_action('admin_init', 'imagopby_register_settings');
function imagopby_register_settings()
{
    register_setting('imagopby_settings', 'imagopby_settings', 'imagopby_sanitize_settings');
    add_settings_section(
        'imagopby_main_settings',
        __('Settings', 'web-pixel-studio-image-compressor'),
        'imagopby_section_callback',
        'imagopby_settings'
    );
    add_settings_field(
        'retain_original',
        __('Also keep the original image', 'web-pixel-studio-image-compressor'),
        'imagopby_render_retain_original',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    add_settings_field(
        'quality',
        __('Image Quality', 'web-pixel-studio-image-compressor'),
        'imagopby_render_quality',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    add_settings_field(
        'method',
        __('Compression Method', 'web-pixel-studio-image-compressor'),
        'imagopby_render_method',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    add_settings_field(
        'allowed_types',
        __('Allowed Image Types', 'web-pixel-studio-image-compressor'),
        'imagopby_render_allowed_types',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    add_settings_field(
        'set_alt_text',
        __('Copy file name to alt text', 'web-pixel-studio-image-compressor'),
        'imagopby_render_set_alt_text',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    add_settings_field(
        'max_width',
        __('Maximum Image Width', 'web-pixel-studio-image-compressor'),
        'imagopby_render_max_width',
        'imagopby_settings',
        'imagopby_main_settings'
    );
    // Bulk optimization button – as a settings field (so it appears in the same block)
    add_settings_field(
        'bulk_optimization',
        __('Bulk image optimization', 'web-pixel-studio-image-compressor'),
        'imagopby_render_bulk_optimization_btn',
        'imagopby_settings',
        'imagopby_main_settings'
    );
}

function imagopby_sanitize_settings($input)
{
    $sanitized = array();
    if (isset($input['retain_original'])) {
        $sanitized['retain_original'] = intval($input['retain_original']);
    }
    if (isset($input['quality'])) {
        $sanitized['quality'] = intval($input['quality']);
    }
    if (isset($input['method'])) {
        $sanitized['method'] = intval($input['method']);
    }
    if (isset($input['allowed_types']) && is_array($input['allowed_types'])) {
        $sanitized['allowed_types'] = array_map('sanitize_text_field', $input['allowed_types']);
    }
    if (isset($input['set_alt_text'])) {
        $sanitized['set_alt_text'] = intval($input['set_alt_text']);
    }
    if (isset($input['max_width'])) {
        $sanitized['max_width'] = intval($input['max_width']);
    }
    return $sanitized;
}

function imagopby_section_callback()
{
    echo '<p>' . esc_html__('Optimizes images when uploading. Convert JP, PNG, GIF, BMP, TIFF, and HEIC/HEIF files to WebP format. Define the maximum image size and choose which file types should be converted and what quality the optimized image should have.', 'web-pixel-studio-image-compressor') . '</p>';
}

function imagopby_render_retain_original()
{
    $options = get_option('imagopby_settings');
    ?>
    <label for="retain_original" style="display: flex; align-items: center; margin: 0;">
        <input type='checkbox' name='imagopby_settings[retain_original]' id="retain_original" <?php checked(isset($options['retain_original'])); ?> value='1'>
        <span style="margin-left: 8px;"><?php esc_html_e('Keep the original file without optimization', 'web-pixel-studio-image-compressor'); ?></span>
    </label>
    <p class="description"><?php esc_html_e('If unchecked, the original image will be deleted after successful conversion to WebP, saving disk space.', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_quality()
{
    $options = get_option('imagopby_settings');
    $quality = isset($options['quality']) ? intval($options['quality']) : 80;
    ?>
    <label for="quality">
        <?php esc_html_e('Image Quality', 'web-pixel-studio-image-compressor'); ?>
    </label>
    <input type='number' name='imagopby_settings[quality]' id="quality" value='<?php echo esc_attr($quality); ?>' min='0' max='100' step='1'>
    <p class="description"><?php esc_html_e('Quality after optimization (0-100). Higher values = better quality but larger file size. Default: 80', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_method()
{
    $options = get_option('imagopby_settings');
    $method = isset($options['method']) ? intval($options['method']) : 6;
    ?>
    <label for="method">
        <?php esc_html_e('Compression Method', 'web-pixel-studio-image-compressor'); ?>
    </label>
    <input type='number' name='imagopby_settings[method]' id="method" value='<?php echo esc_attr($method); ?>' min='0' max='6' step='1'>
    <p class="description"><?php esc_html_e('Compression strength (0-6). Higher values = better compression but longer processing time. Default: 6', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_allowed_types()
{
    $options = get_option('imagopby_settings');
    $allowedTypes = isset($options['allowed_types']) ? $options['allowed_types'] : ['image/jpeg', 'image/png', 'image/gif'];
    $allTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/svg+xml', 'image/heic', 'image/heif'];
    ?>
    <label><?php esc_html_e('Image Types to Optimize', 'web-pixel-studio-image-compressor'); ?></label>
    <div style="margin-top: 8px;">
        <?php
        foreach ($allTypes as $type) {
            $label = ucfirst(str_replace(['image/', '+xml'], '', $type));
            ?>
            <label style="display: flex; align-items: center; margin-bottom: 8px;">
                <input type='checkbox' name='imagopby_settings[allowed_types][]' <?php checked(in_array($type, $allowedTypes)); ?> value='<?php echo esc_attr($type); ?>'>
                <span style="margin-left: 8px;"><?php echo esc_html($label); ?></span>
            </label>
            <?php
        }
        ?>
    </div>
    <p class="description"><?php esc_html_e('Select which image types should be optimized. HEIC/HEIF are Apple formats (iPhone/iPad). SVG files usually don\'t need optimization.', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_set_alt_text()
{
    $options = get_option('imagopby_settings');
    ?>
    <label for="set_alt_text" style="display: flex; align-items: center; margin: 0;">
        <input type='checkbox' name='imagopby_settings[set_alt_text]' id="set_alt_text" <?php checked(isset($options['set_alt_text'])); ?> value='1'>
        <span style="margin-left: 8px;"><?php esc_html_e('Auto-set alt text from filename', 'web-pixel-studio-image-compressor'); ?></span>
    </label>
    <p class="description"><?php esc_html_e('Automatically generate alt text based on the image filename. Useful for SEO if you use descriptive filenames.', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_max_width()
{
    $options = get_option('imagopby_settings');
    $maxWidth = isset($options['max_width']) ? intval($options['max_width']) : 1200;
    ?>
    <label for="max_width">
        <?php esc_html_e('Maximum Image Width', 'web-pixel-studio-image-compressor'); ?>
    </label>
    <input type='number' name='imagopby_settings[max_width]' id="max_width" value='<?php echo esc_attr($maxWidth); ?>' min='0' step='1'>
    <span style="margin-left: 8px; color: #666;">px</span>
    <p class="description"><?php esc_html_e('Images wider than this will be resized before optimization. Default: 1200 pixels. Set to 0 to disable resizing.', 'web-pixel-studio-image-compressor'); ?></p>
    <?php
}

function imagopby_render_bulk_optimization_btn()
{
    ?>
    <span class="imagopby-bulk-label"><?php esc_html_e('Bulk optimization progress', 'web-pixel-studio-image-compressor'); ?></span>
    <div style="margin-top: 12px;">
        <span id="imagopby-progress-bar-bg" style="display:none;">
            <span id="imagopby-progress-bar"></span>
        </span>
        <span id="imagopby-progress-status"></span>
    </div>
    <?php
}

// ============ BATCH OPTIMIZATION ==============
add_action('wp_ajax_imagopby_optimize_gallery', 'imagopby_ajax_optimize_gallery');
function imagopby_ajax_optimize_gallery() {
    check_ajax_referer('imagopby_optimize_action', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
    $step = isset($_POST['step']) ? intval($_POST['step']) : 0;
    $batch = 10;
    $mode = 'delete'; // always only delete mode

    if ($step === 0) {
        $upload_dir = wp_get_upload_dir();
        $base_dir = trailingslashit($upload_dir['basedir']);
        $allowed_ext = ['jpg','jpeg','png','gif','bmp','tiff','heic','heif'];
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
        $files = [];
        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_ext)) {
                $files[] = $file->getPathname();
            }
        }
        set_transient('imagopby_gallery_files', $files, 30*MINUTE_IN_SECONDS);
    } else {
        $files = get_transient('imagopby_gallery_files');
        if (!$files) wp_send_json_error('Session expired');
    }

    $total = count($files);
    $from = $step * $batch;
    $to = min($from + $batch, $total);

    $optimized = 0;
    for ($i = $from; $i < $to; $i++) {
        if (isset($files[$i])) {
            imagopby_optimize_and_replace_with_webp($files[$i], false);
            $optimized++;
        }
    }

    $progress = $to >= $total ? 100 : intval(($to / $total) * 100);

    if ($to >= $total) {
        delete_transient('imagopby_gallery_files');
        wp_send_json_success([
            'finished' => true,
            'progress' => 100,
            'optimized' => $total,
            'total' => $total,
        ]);
    } else {
        wp_send_json_success([
            'finished' => false,
            'progress' => $progress,
            'optimized' => $to,
            'total' => $total,
            'step' => $step + 1,
        ]);
    }
    wp_die();
}

function imagopby_optimize_and_replace_with_webp($filePath, $keep_original = false) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'heic', 'heif']) || $ext === 'webp') {
        return false;
    }

    $newWebpPath = preg_replace('/\.(jpe?g|png|gif|bmp|tiff|heic|heif)$/i', '.webp', $filePath);
    if (file_exists($newWebpPath)) {
        wp_delete_file($newWebpPath);
    }

    $options = get_option('imagopby_settings');
    $quality = isset($options['quality']) ? intval($options['quality']) : 80;
    $maxWidth = isset($options['max_width']) ? intval($options['max_width']) : 1200;

    $imageEditor = wp_get_image_editor($filePath);
    if (is_wp_error($imageEditor)) return false;

    $imageSize = $imageEditor->get_size();
    if ($imageSize && isset($imageSize['width']) && $imageSize['width'] > $maxWidth) {
        $imageEditor->resize($maxWidth, null);
    }

    $imageEditor->save($newWebpPath, 'image/webp', array('quality' => $quality));

    $upload_dir = wp_get_upload_dir();
    $relative_path = str_replace(trailingslashit($upload_dir['basedir']), '', $filePath);
    $attachment_url = $upload_dir['url'] . '/' . $relative_path;
    $attachment_id = attachment_url_to_postid($attachment_url);

    if ($attachment_id && file_exists($newWebpPath)) {
        $upload_dir = wp_upload_dir();
        $new_url = $upload_dir['url'] . '/' . basename($newWebpPath);
        $relative_path = str_replace(trailingslashit($upload_dir['basedir']), '', $newWebpPath);
        update_post_meta($attachment_id, '_wp_attached_file', $relative_path);
        wp_update_post(array(
            'ID' => $attachment_id,
            'post_mime_type' => 'image/webp',
            'guid' => $new_url
        ));

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $newWebpPath);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Always delete original
        if (file_exists($filePath)) {
            wp_delete_file($filePath);
        }
        return true;
    }
    return false;
}

// === DISABLE UNWANTED IMAGE SIZES ===
add_filter('intermediate_image_sizes_advanced', 'imagopby_disable_default_image_sizes');
function imagopby_disable_default_image_sizes($sizes)
{
    unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['medium_large']);
    unset($sizes['large']);
    return $sizes;
}
add_action('init', 'imagopby_disable_additional_image_sizes');
function imagopby_disable_additional_image_sizes()
{
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
}
add_filter('big_image_size_threshold', '__return_false');
if (!isset($content_width)) {
    $content_width = 1920;
}

// === OPTIMALIZATION ON UPLOAD (STILL TO WEBP) ===
add_filter('wp_handle_upload', 'imagopby_handle_upload');
function imagopby_handle_upload($upload)
{
    $options = get_option('imagopby_settings');
    $retainOriginal = isset($options['retain_original']) ? $options['retain_original'] : false;
    $quality = isset($options['quality']) ? intval($options['quality']) : 80;
    $method = isset($options['method']) ? intval($options['method']) : 6;
    $maxWidth = isset($options['max_width']) ? intval($options['max_width']) : 1200;
    $allowedTypes = isset($options['allowed_types']) && !empty($options['allowed_types']) ? $options['allowed_types'] : ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($upload['type'], $allowedTypes, true)) {
        return $upload;
    }

    $filePath = $upload['file'];
    $fileInfo = pathinfo($filePath);

    if (strpos($filePath, '-scaled') !== false || preg_match('/-\d+x\d+\./', $filePath)) {
        return $upload;
    }

    $imageEditor = wp_get_image_editor($filePath);
    if (!is_wp_error($imageEditor)) {
        $imageSize = $imageEditor->get_size();
        if ($imageSize['width'] > $maxWidth) {
            $imageEditor->resize($maxWidth, null);
            $imageEditor->save($filePath);
        }
    }

    $newFilePath = $fileInfo['dirname'] . '/' . wp_unique_filename($fileInfo['dirname'], $fileInfo['filename'] . '.webp');
    if (extension_loaded('imagick')) {
        $image = new Imagick($filePath);
        $image->setImageFormat('webp');
        $image->setOption('webp:method', $method);
        $image->setImageCompressionQuality($quality);
        $image->stripImage();
        $image->writeImage($newFilePath);
        $image->clear();
        $image->destroy();
    } elseif (extension_loaded('gd')) {
        if (!is_wp_error($imageEditor)) {
            $imageEditor->save($newFilePath, 'image/webp', array('quality' => $quality));
        }
    } else {
        // No suitable image library found
        return $upload;
    }

    if (file_exists($newFilePath)) {
        $upload['file'] = $newFilePath;
        $upload['url'] = str_replace(basename($upload['url']), basename($newFilePath), $upload['url']);
        $upload['type'] = 'image/webp';

        // Always delete original (for upload)
        if (file_exists($filePath)) {
            wp_delete_file($filePath);
        }
    } else {
        // Image optimization failed
    }

    return $upload;
}

// === ALT TEXT ===
add_action('add_attachment', 'imagopby_set_image_alt_text_on_upload');
function imagopby_set_image_alt_text_on_upload($postId)
{
    $options = get_option('imagopby_settings');
    $setAltText = isset($options['set_alt_text']) ? $options['set_alt_text'] : false;
    if (!$setAltText) return;
    $attachment = get_post($postId);
    if (!wp_attachment_is_image($postId)) return;
    $title = $attachment->post_title;
    $title = str_replace('-', ' ', $title);
    $altText = ucfirst(strtolower($title));
    update_post_meta($postId, '_wp_attachment_image_alt', $altText);
}