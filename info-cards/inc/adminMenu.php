<?php


if ( !defined( 'ABSPATH' ) ) { exit; } 

class BPICBMenu {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
		add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
	}

	
	       public function adminMenu(){
					$icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 576 512">
    				<path fill="currentColor" d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 256h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zm256-32H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
				</svg>';

					add_menu_page(
			__('Info Cards - bPlugins', 'info-cards'),
			__('Info Cards', 'info-cards'),
			'manage_options',
			'info-cards-dashboard',
			'',
			'data:image/svg+xml;base64,' . base64_encode( $icon ),
			20
		);
        
        
			add_submenu_page(
			'info-cards-dashboard',
			__('Help - bPlugins', 'info-cards'),
			__('Help', 'info-cards'),
			'manage_options',
			'info-cards-dashboard',
			[$this, 'renderDashboardPage'],
			2
			
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
new BPICBMenu();
