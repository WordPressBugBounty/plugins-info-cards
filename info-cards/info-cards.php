<?php
/**
 * Plugin Name:       Info Cards
 * Description:       Create beautiful cards with text and image.
 * Requires at least: 5.8
 * Requires PHP:      7.1
 * Version:           1.0.10
 * Author:            bPlugins
 * Author URI:        http://bplugins.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       info-cards
 */

if ( !defined( 'ABSPATH' ) ) { exit; }

class BPICB_Info_Cards{
    private static $instance;

    private function __construct()
    {
        $this->constants_define();
        add_action( "enqueue_block_assets", array( $this, "icbCardsEnqueueBlockAssets" ) );
        add_action( "init", array( $this, "icbCardsInit" ) );
        
    }

    public static function get_instance(){
        if( self::$instance ){
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }

    private function constants_define() {
        // Constant
        define( 'ICB_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.10' );
        define( 'ICB_DIR', plugin_dir_url( __FILE__ ) );
    }

    public function icbCardsEnqueueBlockAssets(){
        wp_register_style( 'icb-cards-style', ICB_DIR . 'dist/style.css', [], ICB_VERSION );
    }

    public function icbCardsInit() {
        wp_register_style( 'icb-cards-editor-style', ICB_DIR . 'dist/editor.css', [ 'icb-cards-style' ], ICB_VERSION ); // Backend Style
    
        register_block_type( __DIR__, [
            'editor_style'      => 'icb-cards-editor-style',
            "render_callback"   => function ( $attributes ) {
                extract( $attributes );
    
                wp_enqueue_style( 'icb-cards-style' );
                wp_enqueue_script( 'icb-cards-script', ICB_DIR . 'dist/script.js', [ 'react', 'react-dom' ], ICB_VERSION );
    
                $blockClassName = 'wp-block-icb-cards ' . $className . ' align' . $align;
            
                ob_start(); ?>
                <div class="<?php echo esc_attr( $blockClassName ); ?>" id='icbCards-<?php echo esc_attr( $clientId ); ?>' data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'></div>
    
                <?php return ob_get_clean();
            }
        ] );
    
        wp_set_script_translations( 'icb-cards-editor-script', 'info-cards', plugin_dir_path( __FILE__ ) . 'languages' ); // Translate
    }
}
BPICB_Info_Cards::get_instance();


