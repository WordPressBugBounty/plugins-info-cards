<?php


if ( !defined( 'ABSPATH' ) ) { exit; } 

class BPICBAdminMenu {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
		add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
	}

	
	       public function adminMenu(){
			add_submenu_page(
			'tools.php',
			__('Info Cards - bPlugins', 'info-cards'),
			__('Info Cards', 'info-cards'),
			'manage_options',
			'info-cards-dashboard',
			[$this, 'renderDashboardPage'],
		);
        } 

	public function renderDashboardPage(){ ?>
		<div
			id='bpInfoCardsBlock'
			data-info='<?php echo esc_attr( wp_json_encode( [
				'version' => ICB_VERSION,
				'isPremium' => bpicbIsPremium(),
				'hasPro' => INFO_CARDS_PRO
			] ) ); ?>'
		></div>
	<?php }

	function adminEnqueueScripts( $hook ) {
		if( strpos( $hook, 'info-cards-dashboard' ) ){
			wp_enqueue_style( 'icb-admin-dashboard', ICB_DIR . 'build/admin-dashboard.css', [], ICB_VERSION );
			wp_enqueue_script( 'icb-admin-dashboard', ICB_DIR . 'build/admin-dashboard.js', [ 'react', 'react-dom' ], ICB_VERSION, true );
			wp_set_script_translations( 'icb-admin-dashboard', 'click-to-copy', ICB_DIR_PATH . 'languages' );
		}
	}
}
new BPICBAdminMenu();
