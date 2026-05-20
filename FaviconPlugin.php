<?php
/*
 * Plugin Name:       Page Specific Favicons
 * Plugin URI:        https://github.com/jcjason12108-alt/Page-Specific-Favicons/
 * Description:       Add a different favicon to each post or page.
 * Version:           1.0.8
 * Author:            Jason Cox
 * Requires at least: 6.0
 * Tested up to:      7.0
 * Requires PHP:      7.4
 * License:           Proprietary
 * Text Domain:       page-specific-favicons
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

add_action( 'plugins_loaded', 'page_specific_favicons_init_update_checker' );

function page_specific_favicons_init_update_checker() {
    if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
        return;
    }

    $update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/jcjason12108-alt/Page-Specific-Favicons/',
        __FILE__,
        'page-specific-favicons'
    );
    $update_checker->setBranch( 'main' );

    $github_token = defined( 'PLUGIN_UPDATE_GITHUB_TOKEN' )
        ? PLUGIN_UPDATE_GITHUB_TOKEN
        : getenv( 'PLUGIN_UPDATE_GITHUB_TOKEN' );

    if ( ! empty( $github_token ) ) {
        $update_checker->setAuthentication( $github_token );
    }

    add_filter(
        $update_checker->getUniqueName( 'vcs_update_detection_strategies' ),
        static function ( array $strategies ): array {
            return isset( $strategies['branch'] ) ? array( 'branch' => $strategies['branch'] ) : $strategies;
        }
    );
}

if ( ! class_exists( 'Multi_Favicons' ) ) {
class Multi_Favicons {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_favicon_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_favicon_meta' ] );
        add_action( 'wp_head', [ $this, 'output_favicon' ], 1 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }

    public function add_favicon_meta_box() {
        add_meta_box(
            'multi_favicon_meta_box',
            'Custom Favicon',
            [ $this, 'render_meta_box' ],
            [ 'post', 'page' ],
            'side'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'multi_favicon_nonce', 'multi_favicon_nonce_field' );
        $favicon_url = get_post_meta( $post->ID, '_custom_favicon', true );
        ?>
        <div>
            <input type="text" name="custom_favicon_url" id="custom_favicon_url" value="<?php echo esc_attr( $favicon_url ); ?>" style="width:100%; margin-bottom: 5px;" />
            <button type="button" class="button" id="upload_favicon_button">Upload</button>
            <button type="button" class="button button-secondary" id="remove_favicon_button">Remove</button>
            <div id="favicon_preview" style="margin-top:10px;">
                <?php if ( $favicon_url ) : ?>
                    <img src="<?php echo esc_url( $favicon_url ); ?>" alt="Favicon Preview" style="max-width:100%; height:auto;" />
                <?php endif; ?>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($){
            $('#upload_favicon_button').on('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: 'Select or Upload Favicon',
                    button: { text: 'Use this icon' },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#custom_favicon_url').val(attachment.url);
                    var $img = $('<img/>', {
                        src: attachment.url,
                        alt: 'Favicon Preview'
                    }).css({'max-width':'100%','height':'auto'});
                    $('#favicon_preview').empty().append($img);
                });
                frame.open();
            });

            $('#remove_favicon_button').on('click', function(e) {
                e.preventDefault();
                $('#custom_favicon_url').val('');
                $('#favicon_preview').empty();
            });
        });
        </script>
        <?php
    }

    public function save_favicon_meta( $post_id ) {
        if (
            ! isset( $_POST['multi_favicon_nonce_field'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['multi_favicon_nonce_field'] ) ), 'multi_favicon_nonce' )
        ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $post_type = get_post_type( $post_id );
        if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
            return;
        }

        if ( isset( $_POST['custom_favicon_url'] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST['custom_favicon_url'] ) );

            if ( ! empty( $url ) ) {
                $scheme = parse_url( $url, PHP_URL_SCHEME );
                if ( $scheme && ! in_array( $scheme, array( 'http', 'https', 'data' ), true ) ) {
                    return;
                }
            }

            if ( empty( $url ) ) {
                delete_post_meta( $post_id, '_custom_favicon' );
            } else {
                update_post_meta( $post_id, '_custom_favicon', $url );
            }
        }
    }

    public function output_favicon() {
        if ( is_singular() ) {
            $favicon_url = get_post_meta( get_the_ID(), '_custom_favicon', true );

            if ( $favicon_url ) {
                echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '" type="image/x-icon" />' . PHP_EOL;
                echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '" type="image/x-icon" />' . PHP_EOL;
                echo '<link rel="apple-touch-icon" href="' . esc_url( $favicon_url ) . '" />' . PHP_EOL;
            } else {
                // Fall back to site-wide favicon
                $site_icon_id = get_option( 'site_icon' );
                if ( $site_icon_id ) {
                    $site_icon_url = wp_get_attachment_url( $site_icon_id );
                    if ( $site_icon_url ) {
                        echo '<link rel="icon" href="' . esc_url( $site_icon_url ) . '" type="image/x-icon" />' . PHP_EOL;
                        echo '<link rel="shortcut icon" href="' . esc_url( $site_icon_url ) . '" type="image/x-icon" />' . PHP_EOL;
                        echo '<link rel="apple-touch-icon" href="' . esc_url( $site_icon_url ) . '" />' . PHP_EOL;
                    }
                }
            }
        }
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
            wp_enqueue_media();
        }
    }
}

new Multi_Favicons();
}
