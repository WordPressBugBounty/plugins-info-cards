<?php


if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists('\icb_PromoBanner' ) ) {

    class icb_PromoBanner {
        const DISMISSED_KEY   = 'icb_promo_banner_dismissed_permanent';
        const EXPIRATION_DATE = '2026-12-05 23:59:59';

        /** Target landing URL */
        const COUPON_URL      = 'https://bplugins.com/products/info-cards/pricing/?utm_source=admin&utm_medium=dashboard&utm_campaign=bfcm2026';

        public function __construct() {
            add_action( 'admin_notices',                     [ $this, 'render' ] );
            add_action( 'admin_head',                        [ $this, 'enqueue_styles' ] );
            add_action( 'admin_footer',                      [ $this, 'enqueue_scripts' ] );
        }

        /* ------------------------------------------------------------------ */
        /*  Screen, Expiration, Dismissal & Pro Helpers                       */
        /* ------------------------------------------------------------------ */

        /**
         * Check if the current admin screen belongs to Info Cards
         */
     private function is_plugin_screen(): bool {
    $screen = get_current_screen();

    if ( $screen ) {
        $id        = $screen->id ?? '';
        $base      = $screen->base ?? '';
        $post_type = $screen->post_type ?? '';

        if (
            strpos( $id, 'info-cards' ) !== false ||
            strpos( $base, 'info-cards' ) !== false ||
            $post_type === 'icb'
        ) {
            return true;
        }
    }

    if ( isset( $_GET['page'] ) ) {
        $page = sanitize_key( $_GET['page'] );

        if ( strpos( $page, 'info-cards' ) !== false ) {
            return true;
        }
    }

    return false;
}

        /**
         * Check if current date is past December 5, 2026
         */
        private function is_expired(): bool {
            $expiry_timestamp = strtotime( self::EXPIRATION_DATE );
            $current_time     = time();
            return ( $current_time > $expiry_timestamp );
        }


        /**
         * Determine if the banner should be omitted
         */
        private function should_skip(): bool {
            // 1. Only show inside Info Cards admin menu / screens
            if ( ! $this->is_plugin_screen() ) {
                return true;
            }

            // 2. Automatically remove after Dec 5, 2026
            if ( $this->is_expired() ) {
                return true;
            }

            // 3. Premium check: hardcoded false for the Free version.
            $is_premium = false;

            if ( $is_premium ) {
                return true;
            }

            return false;
        }

        /* ------------------------------------------------------------------ */
        /*  CSS Styles                                                        */
        /* ------------------------------------------------------------------ */

        public function enqueue_styles(): void {
            if ( $this->should_skip() ) { return; }
            ?>
            <style id="icb-promo-banner-css">
            #icb-promo-banner {
                --bpl-primary:        #146ef5;
                --bpl-primary-hover:  #0d5ecc;
                --bpl-secondary:      #ff7a00;
                --bpl-dark:           #070127;
                --bpl-text:           #1b2e4b;
                --bpl-muted:          #475569;

                background: #ffffff;
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                box-shadow: 0 3px 12px rgba(7, 1, 39, 0.05);
                display: flex !important;
                align-items: stretch;
                min-height: 84px;
                overflow: hidden;
                position: relative;
                margin: 14px 0 16px 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                box-sizing: border-box;
                animation: icbBannerSlide .3s ease both;
            }
            @keyframes icbBannerSlide {
                from { opacity: 0; transform: translateY(-6px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            /* Left Brand Ribbon Block */
            #icb-promo-banner .icb-brand-ribbon {
                width: 140px;
                min-width: 140px;
                background: linear-gradient(135deg, var(--bpl-primary) 0%, var(--bpl-dark) 100%);
                color: #ffffff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 12px 10px;
                gap: 6px;
                flex-shrink: 0;
            }
            #icb-promo-banner .icb-brand-ribbon img {
                width: 38px;
                height: 38px;
                object-fit: contain;
                filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.35));
            }
            #icb-promo-banner .icb-brand-ribbon .icb-ribbon-title {
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .7px;
                text-transform: uppercase;
                color: #ffffff;
                text-align: center;
                line-height: 1.2;
            }

            /* Main Content Zone */
            #icb-promo-banner .icb-content-zone {
                flex: 1;
                padding: 14px 22px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                flex-wrap: wrap;
                padding-right: 38px;
            }

            /* Left Text Stack */
            #icb-promo-banner .icb-text-stack {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            #icb-promo-banner .icb-headline {
                font-size: 14px;
                font-weight: 700;
                color: var(--bpl-dark);
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 0;
                line-height: 1.3;
            }
            #icb-promo-banner .icb-discount-chip {
                background: #ffedd5;
                color: #c2410c;
                border: 1px solid #fed7aa;
                font-size: 11px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 12px;
                letter-spacing: .2px;
            }

            /* Feature Checklist */
            #icb-promo-banner .icb-feature-chips {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 12px;
                color: var(--bpl-muted);
                flex-wrap: wrap;
            }
            #icb-promo-banner .icb-feature-chips span {
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            #icb-promo-banner .icb-feature-chips .check-icon {
                color: #10b981;
                font-weight: 800;
            }

            /* CTA Action Button */
            #icb-promo-banner .icb-btn-cta {
                background: var(--bpl-primary);
                color: #ffffff !important;
                padding: 9px 20px;
                border-radius: 5px;
                font-size: 12.5px;
                font-weight: 600;
                text-decoration: none !important;
                box-shadow: 0 2px 8px rgba(20, 110, 245, 0.32);
                transition: background-color .15s ease, transform .15s ease, box-shadow .15s ease;
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            #icb-promo-banner .icb-btn-cta:hover {
                background: var(--bpl-primary-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(20, 110, 245, 0.45);
            }

            /* Dismiss Button (×) */
            #icb-promo-banner .icb-banner-dismiss-btn {
                position: absolute;
                top: 8px;
                right: 8px;
                background: none;
                border: none;
                cursor: pointer;
                color: #94a3b8;
                font-size: 20px;
                line-height: 1;
                padding: 6px;
                transition: color .15s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }
            #icb-promo-banner .icb-banner-dismiss-btn:hover {
                color: var(--bpl-dark);
            }

            /* Responsive */
            @media (max-width: 960px) {
                #icb-promo-banner .icb-brand-ribbon {
                    width: 110px;
                    min-width: 110px;
                }
            }
            @media (max-width: 782px) {
                #icb-promo-banner {
                    flex-direction: column;
                    align-items: stretch;
                }
                #icb-promo-banner .icb-brand-ribbon {
                    width: 100%;
                    flex-direction: row;
                    justify-content: flex-start;
                    padding: 8px 16px;
                }
                #icb-promo-banner .icb-content-zone {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 14px 16px;
                }
            }
            </style>
            <?php
        }

        /* ------------------------------------------------------------------ */
        /*  Permanent Dismiss Script (AJAX + Animation)                       */
        /* ------------------------------------------------------------------ */

        public function enqueue_scripts(): void {
            if ( $this->should_skip() ) { return; }
            ?>
            <script id="icb-promo-banner-js">
            (function () {
                var storageKey = <?php echo wp_json_encode( self::DISMISSED_KEY ); ?>;
                var banner = document.getElementById('icb-promo-banner');
                var btn = document.getElementById('icb-banner-close');

                if (!banner) return;

                // Hide the banner if it was dismissed in this browser.
                try {
                    if (window.localStorage.getItem(storageKey) === '1') {
                        banner.remove();
                        return;
                    }
                } catch (error) {
                    // If localStorage is unavailable, keep the banner functional.
                }

                if (!btn) return;

                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Save dismissal only in this browser's localStorage.
                    try {
                        window.localStorage.setItem(storageKey, '1');
                    } catch (error) {
                        // The banner will still close for the current page.
                    }

                    banner.style.transition = 'opacity .25s ease, transform .25s ease';
                    banner.style.opacity = '0';
                    banner.style.transform = 'translateY(-6px)';

                    setTimeout(function() {
                        banner.remove();
                    }, 260);
                });
            })();
            </script>
            <?php
        }

        /* ------------------------------------------------------------------ */
        /*  Render Single Banner                                              */
        /* ------------------------------------------------------------------ */

        public function render(): void {
            if ( $this->should_skip() ) { return; }

            $coupon_url = esc_url( self::COUPON_URL );
            $icon_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 576 512"><path fill="#ffffff" d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 256h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zm256-32H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>';
            $icon_url   = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg );
            ?>
            <div id="icb-promo-banner">

                <!-- Left Asymmetric Brand Ribbon -->
                <div class="icb-brand-ribbon">
                    <img src="<?php echo $icon_url; ?>" alt="Info Cards" />
                    <span class="icb-ribbon-title">Info Cards</span>
                </div>

                <!-- Main Content Zone -->
                <div class="icb-content-zone">
                    <div class="icb-text-stack">
                        <div class="icb-headline">
                            <span><?php esc_html_e( 'Upgrade to Info Cards Pro', 'info-cards' ); ?></span>
                            <span class="icb-discount-chip"><?php esc_html_e( 'Save up to 45%', 'info-cards' ); ?></span>
                        </div>
                        <div class="icb-feature-chips">
                            <span><strong class="check-icon">✓</strong> <?php esc_html_e( 'Pro Card Styles', 'info-cards' ); ?></span>
                            <span><strong class="check-icon">✓</strong> <?php esc_html_e( 'Animation Effects', 'info-cards' ); ?></span>
                            <span><strong class="check-icon">✓</strong> <?php esc_html_e( 'Advanced Analytics', 'info-cards' ); ?></span>
                            <span><strong class="check-icon">✓</strong> <?php esc_html_e( 'Priority Support', 'info-cards' ); ?></span>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <a href="<?php echo $coupon_url; ?>" target="_blank" rel="noopener noreferrer" class="icb-btn-cta">
                        <?php esc_html_e( 'Unlock PRO Features →', 'info-cards' ); ?>
                    </a>
                </div>

                <!-- Dismiss Button -->
                <button id="icb-banner-close"
                        class="icb-banner-dismiss-btn"
                        title="<?php esc_attr_e( 'Dismiss', 'info-cards' ); ?>"
                        aria-label="<?php esc_attr_e( 'Dismiss banner', 'info-cards' ); ?>">
                    &times;
                </button>

            </div>
            <?php
        }


    }

    new icb_PromoBanner();
}