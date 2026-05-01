<?php
/**
 * Plugin Name: Web Pixel Studio Image Compressor
 * Plugin URI: http://wordpress.org/plugins/web-pixel-studio-image-compressor
 * Description: Optimizes images on upload. Convert JPEG, PNG, GIF, BMP, TIFF, and HEIC/HEIF to WebP. Define max. size and select the types of images to convert and what quality the optimized image should have.
 * Author: Web Pixel Studio
 * Author URI: https://webpixelstudio.org
 * Version: 1.0.1
 * Tested up to: 6.9
 * License: GPL-2.0+
 * @category Plugin
 * @package  Web_Pixel_Studio_Image_Compressor
 * @link     https://wps.sk
 * @php      7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('IMAGOPBY_VERSION', '1.0.1');

// ========== SETTINGS PAGE ==========
function imagopby_settings_page()
{
    $options      = get_option( 'imagopby_settings' );
    $quality      = isset( $options['quality'] )   ? intval( $options['quality'] )    : 80;
    $method       = isset( $options['method'] )    ? intval( $options['method'] )     : 6;
    $maxWidth     = isset( $options['max_width'] ) ? intval( $options['max_width'] )  : 1200;
    $retainOrig   = ! empty( $options['retain_original'] );
    $setAltText   = ! empty( $options['set_alt_text'] );
    $allowedTypes = ( isset( $options['allowed_types'] ) && ! empty( $options['allowed_types'] ) )
                    ? $options['allowed_types']
                    : [ 'image/jpeg', 'image/png', 'image/gif' ];
    $allTypes = [
        'image/jpeg'    => 'JPEG',
        'image/png'     => 'PNG',
        'image/gif'     => 'GIF',
        'image/bmp'     => 'BMP',
        'image/tiff'    => 'TIFF',
        'image/svg+xml' => 'SVG',
        'image/heic'    => 'HEIC',
        'image/heif'    => 'HEIF',
    ];
    ?>
    <div class="imagopby-wrap">

        <!-- ===== HEADER ===== -->
        <header class="imagopby-header">
            <div class="imagopby-header-brand">
                <div class="imagopby-logo-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" ry="3"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                        <line x1="15" y1="3" x2="15" y2="9"/><line x1="18" y1="6" x2="12" y2="6"/>
                    </svg>
                </div>
                <div class="imagopby-header-text">
                    <h1><?php esc_html_e( 'Image Compressor', 'web-pixel-studio-image-compressor' ); ?></h1>
                    <span class="imagopby-header-by"><?php esc_html_e( 'by', 'web-pixel-studio-image-compressor' ); ?> <a href="https://wps.sk" target="_blank" rel="noopener noreferrer">Web Pixel Studio</a></span>
                </div>
            </div>
            <div class="imagopby-header-meta">
                <span class="imagopby-version-badge">v<?php echo esc_html( IMAGOPBY_VERSION ); ?></span>
                <a href="https://webpixelstudio.org" target="_blank" rel="noopener noreferrer" class="imagopby-header-link">webpixelstudio.org</a>
            </div>
        </header>

        <form action="options.php" method="post" class="imagopby-form" id="imagopby-settings-form">
            <?php settings_fields( 'imagopby_settings' ); ?>

            <div class="imagopby-layout">

                <!-- ===== MAIN SETTINGS ===== -->
                <div class="imagopby-main">

                    <!-- Card: Image Types -->
                    <div class="imagopby-card">
                        <div class="imagopby-card-head">
                            <span class="imagopby-card-icon imagopby-icon-purple">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="9" height="9" rx="2"/><rect x="13" y="2" width="9" height="9" rx="2"/>
                                    <rect x="2" y="13" width="9" height="9" rx="2"/><rect x="13" y="13" width="9" height="9" rx="2"/>
                                </svg>
                            </span>
                            <div>
                                <h2><?php esc_html_e( 'Image Types', 'web-pixel-studio-image-compressor' ); ?></h2>
                                <p><?php esc_html_e( 'Select which image formats will be converted to WebP', 'web-pixel-studio-image-compressor' ); ?></p>
                            </div>
                        </div>
                        <div class="imagopby-card-body">
                            <div class="imagopby-types-grid">
                                <?php foreach ( $allTypes as $type => $label ) : ?>
                                <label class="imagopby-type-chip<?php echo in_array( $type, $allowedTypes ) ? ' imagopby-type-chip--active' : ''; ?>">
                                    <input type="checkbox" name="imagopby_settings[allowed_types][]" value="<?php echo esc_attr( $type ); ?>" <?php checked( in_array( $type, $allowedTypes ) ); ?> class="imagopby-chip-check">
                                    <span><?php echo esc_html( $label ); ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <p class="imagopby-field-desc"><?php esc_html_e( 'HEIC/HEIF are Apple formats (iPhone/iPad). SVG files usually don\'t need optimization.', 'web-pixel-studio-image-compressor' ); ?></p>
                        </div>
                    </div>

                    <!-- Card: Quality & Dimensions -->
                    <div class="imagopby-card">
                        <div class="imagopby-card-head">
                            <span class="imagopby-card-icon imagopby-icon-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                                    <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                                    <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                                    <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
                                </svg>
                            </span>
                            <div>
                                <h2><?php esc_html_e( 'Quality & Dimensions', 'web-pixel-studio-image-compressor' ); ?></h2>
                                <p><?php esc_html_e( 'Control output quality and maximum image dimensions', 'web-pixel-studio-image-compressor' ); ?></p>
                            </div>
                        </div>
                        <div class="imagopby-card-body">
                            <div class="imagopby-field">
                                <label class="imagopby-label" for="imagopby_quality_range">
                                    <?php esc_html_e( 'Image Quality', 'web-pixel-studio-image-compressor' ); ?>
                                    <span class="imagopby-quality-badge" id="imagopby-quality-value"><?php echo esc_html( $quality ); ?></span>
                                </label>
                                <div class="imagopby-slider-wrap">
                                    <span class="imagopby-slider-label">0</span>
                                    <input type="range" id="imagopby_quality_range" min="0" max="100" step="1" value="<?php echo esc_attr( $quality ); ?>" class="imagopby-slider">
                                    <span class="imagopby-slider-label">100</span>
                                </div>
                                <input type="hidden" name="imagopby_settings[quality]" id="imagopby_quality" value="<?php echo esc_attr( $quality ); ?>">
                                <p class="imagopby-field-desc"><?php esc_html_e( 'Higher value = better quality but larger file size. Recommended: 80', 'web-pixel-studio-image-compressor' ); ?></p>
                            </div>

                            <div class="imagopby-fields-row">
                                <div class="imagopby-field">
                                    <label class="imagopby-label" for="max_width"><?php esc_html_e( 'Maximum Width', 'web-pixel-studio-image-compressor' ); ?></label>
                                    <div class="imagopby-input-group">
                                        <input type="number" name="imagopby_settings[max_width]" id="max_width" value="<?php echo esc_attr( $maxWidth ); ?>" min="0" step="1" class="imagopby-input">
                                        <span class="imagopby-input-suffix">px</span>
                                    </div>
                                    <p class="imagopby-field-desc"><?php esc_html_e( 'Images wider than this will be resized. Default: 1200. Set 0 to disable.', 'web-pixel-studio-image-compressor' ); ?></p>
                                </div>
                                <div class="imagopby-field">
                                    <label class="imagopby-label" for="method">
                                        <?php esc_html_e( 'Compression Method', 'web-pixel-studio-image-compressor' ); ?>
                                        <span class="imagopby-tooltip" title="<?php esc_attr_e( '0 = fastest processing, 6 = best compression ratio', 'web-pixel-studio-image-compressor' ); ?>">?</span>
                                    </label>
                                    <div class="imagopby-input-group">
                                        <input type="number" name="imagopby_settings[method]" id="method" value="<?php echo esc_attr( $method ); ?>" min="0" max="6" step="1" class="imagopby-input">
                                        <span class="imagopby-input-suffix">/6</span>
                                    </div>
                                    <p class="imagopby-field-desc"><?php esc_html_e( '0 = fastest, 6 = best compression. Default: 6', 'web-pixel-studio-image-compressor' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: File Options -->
                    <div class="imagopby-card">
                        <div class="imagopby-card-head">
                            <span class="imagopby-card-icon imagopby-icon-green">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            </span>
                            <div>
                                <h2><?php esc_html_e( 'File Options', 'web-pixel-studio-image-compressor' ); ?></h2>
                                <p><?php esc_html_e( 'Additional options for file handling and accessibility', 'web-pixel-studio-image-compressor' ); ?></p>
                            </div>
                        </div>
                        <div class="imagopby-card-body">
                            <div class="imagopby-toggle-row">
                                <div class="imagopby-toggle-info">
                                    <strong><?php esc_html_e( 'Keep original file', 'web-pixel-studio-image-compressor' ); ?></strong>
                                    <p><?php esc_html_e( 'Keep the original image after converting to WebP. If disabled, the original is deleted to save disk space.', 'web-pixel-studio-image-compressor' ); ?></p>
                                </div>
                                <label class="imagopby-toggle" for="retain_original">
                                    <input type="checkbox" name="imagopby_settings[retain_original]" id="retain_original" <?php checked( $retainOrig ); ?> value="1">
                                    <span class="imagopby-toggle-slider"></span>
                                </label>
                            </div>
                            <div class="imagopby-toggle-row">
                                <div class="imagopby-toggle-info">
                                    <strong><?php esc_html_e( 'Auto-set alt text', 'web-pixel-studio-image-compressor' ); ?></strong>
                                    <p><?php esc_html_e( 'Automatically generate alt text from the image filename on upload. Useful for SEO when using descriptive filenames.', 'web-pixel-studio-image-compressor' ); ?></p>
                                </div>
                                <label class="imagopby-toggle" for="set_alt_text">
                                    <input type="checkbox" name="imagopby_settings[set_alt_text]" id="set_alt_text" <?php checked( $setAltText ); ?> value="1">
                                    <span class="imagopby-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Bulk Optimization -->
                    <div class="imagopby-card imagopby-bulk-card">
                        <div class="imagopby-card-head">
                            <span class="imagopby-card-icon imagopby-icon-orange">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 4 23 10 17 10"/>
                                    <polyline points="1 20 1 14 7 14"/>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                </svg>
                            </span>
                            <div>
                                <h2><?php esc_html_e( 'Bulk Optimization', 'web-pixel-studio-image-compressor' ); ?></h2>
                                <p><?php esc_html_e( 'Convert all existing images in your media library to WebP', 'web-pixel-studio-image-compressor' ); ?></p>
                            </div>
                        </div>
                        <div class="imagopby-card-body">
                            <div class="imagopby-bulk-controls">
                                <button type="button" id="imagopby-optimize-gallery-btn" class="imagopby-btn-bulk">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php esc_html_e( 'Start Bulk Optimization', 'web-pixel-studio-image-compressor' ); ?>
                                </button>
                                <div class="imagopby-progress-wrap" id="imagopby-progress-bar-bg" style="display:none;">
                                    <div class="imagopby-progress-track">
                                        <div class="imagopby-progress-fill" id="imagopby-progress-bar"></div>
                                    </div>
                                    <span class="imagopby-progress-label" id="imagopby-progress-status"></span>
                                </div>
                            </div>
                            <p class="imagopby-field-desc imagopby-bulk-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <?php esc_html_e( 'Warning: This process may take a long time for large media libraries. Do not close this page during optimization.', 'web-pixel-studio-image-compressor' ); ?>
                            </p>
                        </div>
                    </div>

                </div><!-- /.imagopby-main -->

                <!-- ===== SIDEBAR ===== -->
                <aside class="imagopby-sidebar">

                    <div class="imagopby-save-box">
                        <button type="submit" class="imagopby-btn-save">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <?php esc_html_e( 'Save Settings', 'web-pixel-studio-image-compressor' ); ?>
                        </button>
                    </div>

                    <div class="imagopby-info-card">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <?php esc_html_e( 'About', 'web-pixel-studio-image-compressor' ); ?>
                        </h3>
                        <p><?php esc_html_e( 'Automatically converts uploaded images to WebP format for better web performance and smaller file sizes.', 'web-pixel-studio-image-compressor' ); ?></p>
                        <a href="https://webpixelstudio.org" target="_blank" rel="noopener noreferrer" class="imagopby-info-link">webpixelstudio.org &rarr;</a>
                    </div>

                    <div class="imagopby-info-card">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.001z"/>
                            </svg>
                            <?php esc_html_e( 'Tips', 'web-pixel-studio-image-compressor' ); ?>
                        </h3>
                        <ul class="imagopby-tips-list">
                            <li><?php esc_html_e( 'Quality 75–85 is optimal for most websites', 'web-pixel-studio-image-compressor' ); ?></li>
                            <li><?php esc_html_e( 'WebP is ~30% smaller than JPEG at the same quality', 'web-pixel-studio-image-compressor' ); ?></li>
                            <li><?php esc_html_e( 'Max width 1200–1600 px is ideal for most layouts', 'web-pixel-studio-image-compressor' ); ?></li>
                            <li><?php esc_html_e( 'Use Bulk Optimization to convert existing images', 'web-pixel-studio-image-compressor' ); ?></li>
                        </ul>
                    </div>

                    <div class="imagopby-info-card imagopby-social-card">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                            </svg>
                            <?php esc_html_e( 'Follow us', 'web-pixel-studio-image-compressor' ); ?>
                        </h3>
                        <div class="imagopby-social-links">
                            <a href="https://www.instagram.com/tvorbawebov/" target="_blank" rel="noopener noreferrer" class="imagopby-social-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                </svg>
                                Instagram
                            </a>
                            <a href="https://www.facebook.com/strankanakluc/" target="_blank" rel="noopener noreferrer" class="imagopby-social-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                                Facebook
                            </a>
                        </div>
                    </div>

                </aside><!-- /.imagopby-sidebar -->

            </div><!-- /.imagopby-layout -->

        </form>

        <!-- ===== FOOTER ===== -->
        <footer class="imagopby-footer">
            <p>
                <?php esc_html_e( 'Image Compressor', 'web-pixel-studio-image-compressor' ); ?> &mdash;
                <?php esc_html_e( 'by', 'web-pixel-studio-image-compressor' ); ?>
                <a href="https://wps.sk" target="_blank" rel="noopener noreferrer">Web Pixel Studio</a>
                &mdash; v<?php echo esc_html( IMAGOPBY_VERSION ); ?>
            </p>
        </footer>

    </div><!-- /.imagopby-wrap -->
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
add_action('admin_enqueue_scripts', 'imagopby_enqueue_admin_assets');
function imagopby_enqueue_admin_assets($hookSuffix)
{
    if ($hookSuffix == 'settings_page_imagopby') {
        wp_register_style(
            'imagopby-admin',
            plugin_dir_url(__FILE__) . 'web-pixel-studio-image-compressor-admin.css',
            array(),
            IMAGOPBY_VERSION
        );
        wp_enqueue_style('imagopby-admin');

        wp_register_script(
            'imagopby-admin',
            plugin_dir_url(__FILE__) . 'web-pixel-studio-image-compressor-admin.js',
            array('jquery'),
            IMAGOPBY_VERSION,
            true
        );

        wp_localize_script(
            'imagopby-admin',
            'imagopbyAdminData',
            array(
                'nonce' => wp_create_nonce('imagopby_optimize_action'),
                'initializing' => __('Initializing...', 'web-pixel-studio-image-compressor'),
                'processing' => __('Processing', 'web-pixel-studio-image-compressor'),
                'done' => __('Done! Optimized images:', 'web-pixel-studio-image-compressor'),
                'error' => __('Error:', 'web-pixel-studio-image-compressor'),
            )
        );

        wp_enqueue_script('imagopby-admin');
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

// ========== PLUGIN ACTION LINKS ==========
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'imagopby_plugin_action_links' );
function imagopby_plugin_action_links( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=imagopby' ) ) . '">' . esc_html__( 'Settings', 'web-pixel-studio-image-compressor' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}