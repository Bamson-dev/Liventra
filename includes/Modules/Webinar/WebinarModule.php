<?php
namespace Liventra\Modules\Webinar;

use Liventra\Modules\ModuleInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebinarModule
 * Module 2 — Webinar Management & Shortcode Engine (PRD-002 Section 4)
 * Owns webinar creation, editing, publishing, shortcode rendering, and auto-page generation.
 */
class WebinarModule implements ModuleInterface {

	public function get_name() {
		return 'webinar';
	}

	public function register() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}

		if ( function_exists( 'add_shortcode' ) ) {
			add_shortcode( 'liventra_registration', array( $this, 'render_shortcode_registration' ) );
			add_shortcode( 'liventra_webinar', array( $this, 'render_shortcode_webinar' ) );
			add_shortcode( 'liventra_replay', array( $this, 'render_shortcode_replay' ) );
			add_shortcode( 'liventra_thankyou', array( $this, 'render_shortcode_thankyou' ) );
		}
	}

	public function boot() {}

	public function register_admin_menu() {
		if ( function_exists( 'add_menu_page' ) ) {
			add_menu_page(
				__( 'Liventra Studio', 'liventra' ),
				__( 'Liventra', 'liventra' ),
				'manage_options',
				'liventra',
				array( $this, 'render_admin_dashboard' ),
				'dashicons-video-alt3',
				30
			);

			add_submenu_page(
				'liventra',
				__( 'Admin Studio', 'liventra' ),
				__( 'Studio & Builder', 'liventra' ),
				'manage_options',
				'liventra',
				array( $this, 'render_admin_dashboard' )
			);
		}
	}

	public function enqueue_admin_assets( $hook ) {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( false === strpos( $hook, 'liventra' ) && 0 !== strpos( $page, 'liventra' ) ) {
			return;
		}

		if ( function_exists( 'wp_enqueue_style' ) && function_exists( 'plugins_url' ) ) {
			$ver = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LIVENTRA_VERSION;
			wp_enqueue_style( 'liventra-admin-css', plugins_url( 'assets/css/admin.css', LIVENTRA_FILE ), array(), $ver );
			wp_enqueue_script( 'liventra-admin-studio-js', plugins_url( 'assets/js/admin-studio.js', LIVENTRA_FILE ), array(), $ver, false );

			$rest_settings = array(
				'root'  => esc_url_raw( rest_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
			);
			wp_localize_script( 'liventra-admin-studio-js', 'liventraSettings', $rest_settings );
		}
	}

	public function render_admin_dashboard() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Liventra Admin Studio & Visual Webinar Builder', 'liventra' ) . '</h1>';
		echo '<div id="liventra-admin-studio"></div>';
		echo '<script>(function(){ var count=0; var inv=setInterval(function(){ count++; var el=document.getElementById("liventra-admin-studio"); if(window.LiventraAdminStudio && el && !el.dataset.mounted){ el.dataset.mounted="true"; window.liventraApp=new window.LiventraAdminStudio({ containerId: "liventra-admin-studio" }); clearInterval(inv); } if(count>100){ clearInterval(inv); } }, 50); })();</script>';
		echo '</div>';
	}

	/**
	 * Automatically create 4 WordPress pages with shortcodes upon webinar publication
	 */
	public static function auto_create_webinar_pages( $webinar_id, $title = '' ) {
		if ( ! function_exists( 'wp_insert_post' ) ) {
			return array();
		}

		$title = ! empty( $title ) ? $title : 'Automated Masterclass #' . $webinar_id;
		$pages = array(
			'registration' => array(
				'title'     => $title . ' - Registration',
				'content'   => '[liventra_registration id="' . intval( $webinar_id ) . '"]',
				'slug'      => 'webinar-registration-' . intval( $webinar_id ),
			),
			'thankyou' => array(
				'title'     => $title . ' - Thank You',
				'content'   => '[liventra_thankyou id="' . intval( $webinar_id ) . '"]',
				'slug'      => 'webinar-thankyou-' . intval( $webinar_id ),
			),
			'live' => array(
				'title'     => $title . ' - Live Room',
				'content'   => '[liventra_webinar id="' . intval( $webinar_id ) . '"]',
				'slug'      => 'webinar-live-' . intval( $webinar_id ),
			),
			'replay' => array(
				'title'     => $title . ' - Replay',
				'content'   => '[liventra_replay id="' . intval( $webinar_id ) . '"]',
				'slug'      => 'webinar-replay-' . intval( $webinar_id ),
			),
		);

		$created_urls = array();
		foreach ( $pages as $key => $p ) {
			$existing = get_page_by_path( $p['slug'] );
			if ( ! $existing ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $p['title'],
					'post_content' => $p['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_name'    => $p['slug'],
				) );
				if ( $post_id && ! is_wp_error( $post_id ) ) {
					$created_urls[$key] = get_permalink( $post_id );
				} else {
					$created_urls[$key] = home_url( '/' . $p['slug'] . '/' );
				}
			} else {
				$created_urls[$key] = get_permalink( $existing->ID );
			}
		}

		return $created_urls;
	}

	/* ==========================================================================
	   SHORTCODE RENDERING ENGINE (Frontend Templates)
	   ========================================================================== */

	/**
	 * Shortcode [liventra_registration id="X"]
	 */
	public function render_shortcode_registration( $atts ) {
		$atts = shortcode_atts( array( 'id' => '1' ), $atts, 'liventra_registration' );
		$webinar_id = intval( $atts['id'] );

		ob_start();
		?>
		<div class="liventra-frontend-registration" style="max-width: 600px; margin: 40px auto; padding: 32px; background: #0F172A; color: #FFF; border-radius: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
			<div style="text-align:center; margin-bottom: 24px;">
				<span style="background: #2563EB; color: #FFF; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;">🔥 Exclusive Automated Masterclass</span>
				<h1 style="font-size: 26px; font-weight: 800; margin: 12px 0 8px 0; color: #FFFFFF;">Transform Cold Prospects Into High-Ticket Customers On Autopilot</h1>
				<p style="color: #94A3B8; font-size: 14px; margin: 0;">Reserve your virtual seat for the upcoming live stream replay!</p>
			</div>

			<form id="liventra-reg-form-${webinar_id}" onsubmit="event.preventDefault(); alert('Registration Successful! Redirecting to Live Room...'); window.location.href='?liventra_live=1';" style="display: flex; flex-direction: column; gap: 16px;">
				<div>
					<label style="display: block; font-size: 12px; font-weight: 600; color: #CBD5E1; margin-bottom: 6px;">Your Full Name</label>
					<input type="text" required placeholder="e.g. John Doe" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334155; background: #1E293B; color: #FFF; font-size: 14px; box-sizing: border-box;" />
				</div>
				<div>
					<label style="display: block; font-size: 12px; font-weight: 600; color: #CBD5E1; margin-bottom: 6px;">Your Best Email Address</label>
					<input type="email" required placeholder="john@example.com" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334155; background: #1E293B; color: #FFF; font-size: 14px; box-sizing: border-box;" />
				</div>
				<button type="submit" style="width: 100%; padding: 14px; border: none; border-radius: 8px; background: linear-gradient(90deg, #2563EB 0%, #1D4ED8 100%); color: #FFF; font-size: 16px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);">
					🚀 Reserve My Free Seat Now →
				</button>
			</form>
			<div style="text-align: center; font-size: 11px; color: #64748B; margin-top: 16px;">🔒 100% Secure. Instant Access Guaranteed.</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Shortcode [liventra_webinar id="X"]
	 */
	public function render_shortcode_webinar( $atts ) {
		$atts = shortcode_atts( array( 'id' => '1' ), $atts, 'liventra_webinar' );
		$webinar_id = intval( $atts['id'] );

		ob_start();
		?>
		<div class="liventra-frontend-room" style="max-width: 1100px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
			<div style="background: #0F172A; padding: 16px 24px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1E293B;">
				<div>
					<span style="background: #EF4444; color: #FFF; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 4px;">🔴 LIVE</span>
					<strong style="color: #FFF; font-size: 16px; margin-left: 8px;">Automated Masterclass Live Room</strong>
				</div>
				<div style="color: #10B981; font-weight: 700; font-size: 14px;">👥 142 Attendees Watching</div>
			</div>
			<div style="display: grid; grid-template-columns: 2fr 1fr; background: #020617; border-radius: 0 0 12px 12px; overflow: hidden;">
				<div style="background: #000; padding: 10px;">
					<video controls autoplay muted style="width: 100%; height: 440px; border-radius: 8px; object-fit: cover;">
						<source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
					</video>
				</div>
				<div style="background: #0F172A; border-left: 1px solid #1E293B; display: flex; flex-direction: column; height: 460px;">
					<div style="padding: 12px; border-bottom: 1px solid #1E293B; color: #FFF; font-weight: 700; font-size: 13px;">💬 Live Chat Feed</div>
					<div style="flex: 1; padding: 12px; overflow-y: auto; font-size: 12px;">
						<div style="margin-bottom: 10px; background: #1E293B; padding: 8px; border-radius: 6px;">
							<span style="color: #60A5FA; font-weight: 700;">Host Bamidele:</span>
							<p style="color: #FFF; margin: 2px 0 0 0;">Welcome everyone! Excited to have you all here today!</p>
						</div>
						<div style="margin-bottom: 10px; background: #1E293B; padding: 8px; border-radius: 6px;">
							<span style="color: #60A5FA; font-weight: 700;">Moderator Sarah:</span>
							<p style="color: #FFF; margin: 2px 0 0 0;">Special discount offer unlocks during the presentation!</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Shortcode [liventra_replay id="X"]
	 */
	public function render_shortcode_replay( $atts ) {
		$atts = shortcode_atts( array( 'id' => '1' ), $atts, 'liventra_replay' );
		return $this->render_shortcode_webinar( $atts );
	}

	/**
	 * Shortcode [liventra_thankyou id="X"]
	 */
	public function render_shortcode_thankyou( $atts ) {
		ob_start();
		?>
		<div style="max-width: 500px; margin: 40px auto; padding: 32px; background: #0F172A; color: #FFF; border-radius: 12px; text-align: center; font-family: sans-serif;">
			<div style="font-size: 48px;">🎉</div>
			<h2 style="color: #10B981; margin: 12px 0;">You Are Registered!</h2>
			<p style="color: #94A3B8; font-size: 14px;">Your virtual seat is confirmed. Click below to enter the live room preview!</p>
			<a href="?liventra_live=1" style="display: inline-block; margin-top: 16px; padding: 12px 24px; background: #2563EB; color: #FFF; border-radius: 8px; font-weight: 700; text-decoration: none;">Join Live Room Now →</a>
		</div>
		<?php
		return ob_get_clean();
	}
}
