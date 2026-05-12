=== Web Pixel Studio Image Compressor ===
Contributors: duddi, wpssk, webpixelstudio
Tags: images, optimization, webp, compression, resize
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.2
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Optimizes images on upload. Define max. size and select the types of images to convert to webp and what quality the optimized image should have.

== Description ==

Speed up your website and improve performance without compromising quality. This powerful yet lightweight plugin gives you full control over image optimization directly in WordPress — from compression settings to automatic alt text generation.

= ✨ Features =
* Convert images to modern WebP format for faster loading times
* Option to keep original images for backup and flexibility
* Adjust image quality (1–100) — full control over output quality
* Set compression method (0–6) — from fastest to maximum compression
* Define maximum image width — larger images are automatically resized
* Automatically generate alt text based on the image filename (SEO-friendly)
* Support for multiple formats: JPEG, PNG, GIF, BMP, TIFF, SVG, HEIF, HEIC

= 🌐 Why WebP? =
WebP is a modern image format developed by Google that delivers significantly smaller file sizes compared to JPEG and PNG — often by 25–50%, while maintaining high visual quality.

Using WebP helps you:
* 🚀 Improve page load speed
* 📉 Reduce bandwidth usage
* 🔍 Boost SEO and Core Web Vitals
* 📱 Enhance user experience on mobile devices

= ⚡ Bulk Optimization =

Optimize all existing images in your media library with a single click.

⚠️ Important: Test your settings on a few images first. Bulk optimization is irreversible.

= ⚙️ Settings =
Go to Settings → Web Pixel Studio Image Compressor in your WordPress dashboard.
Here you can configure image quality, compression method, WebP conversion, and other optimization options.

== Changelog ==

= 1.0.2 =
* Added "Optimize to WebP" button in individual attachment detail view
* Added per-image "Exclude from optimization" option in attachment detail view
* Added auto-optimize on upload toggle in plugin settings
* File size, filename and thumbnail refresh immediately after single-image optimization
* Fixed upload breakage caused by missing max_width guard and unhandled Imagick exceptions
* Removed direct database queries in favour of WordPress API calls

= 1.0.1 =
* Complete redesign of the admin settings page
* New card-based layout with sidebar, toggle switches, and image type chips
* Quality setting now uses a visual range slider
* Added Settings link to the plugin list page
* Improved responsive design for mobile screens

== Upgrade Notice ==

= 1.0.2 =
Adds per-image optimize & exclude controls in the media library, auto-optimize toggle, and instant UI refresh after optimization. Recommended update for all users.

= 1.0.0 =
* Initial release
* Modern admin interface with gradient design
* Image optimization and WebP conversion
* Support for JPEG, PNG, GIF, BMP, TIFF, HEIC, and HEIF formats
* Bulk image optimization
* Automatic alt text generation
* Customizable image quality and compression settings


