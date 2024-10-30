<?php
/**
 * Plugin Name: Logo Page Redirect
 * Version: 1.0.1
 * Author: Elmar Abdurayimov
 * Author URI: mailto:e.abdurayimov@gmail.com
 * Description: Plugin for redirection to the logo page using cookies
 *
 *
 * Copyright 2014 Elmar Abdurayimov (email: e.abdurayimov@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*
 * ACTIVATE PLUGIN
 */
register_activation_hook( __FILE__, 'mc_redirect_install' );
function mc_redirect_install() {
	$option_name = 'mc_redirect';
	$value       = array(
		'enable' => 1,
		'url'    => 'http://',
		'hours'  => 0
	);

	if ( ! get_option( $option_name ) ) {
		$deprecated = ' ';
		$autoload   = 'no';
		add_option( $option_name, $value, $deprecated, $autoload );
	}
}

/**
 * DEACTIVATE PLUGIN
 */
register_deactivation_hook( __FILE__, 'mc_redirect_deactivate' );
function mc_redirect_deactivate() {
	delete_option( "mc_redirect" );
}

/**
 * ASSETS
 */
add_action( 'admin_init', 'mc_redirect_scripts' );
function mc_redirect_scripts() {
	wp_register_style( 'mc_redirect_style', plugin_dir_url( __FILE__ ) . 'style.css' );
	wp_enqueue_style( 'mc_redirect_style' );
}

/**
 * PLUGIN PAGE
 */
add_action( 'admin_menu', 'mc_redirect_menu' );
function mc_redirect_menu() {
	add_submenu_page( 'options-general.php', 'Logo Page Redirect', 'Logo Page Redirect', 'manage_options', 'logo-page-redirect', 'mc_redirect_render' );
}

function mc_redirect_render() {
	?>

	<div class="wrap">
		<div id="icon-mc-redirect" class="icon32"><br></div>
		<h2><?php echo __( 'Logo Page Redirect', 'Nilesadvertising' ); ?></h2>

		<?php if ( false !== $_REQUEST['submit'] && $_REQUEST['submit'] == "Save Changes" ) : ?>
			<div id="message" class="updated">
				<p><strong><?php _e( 'Redirect is saved', 'Nilesadvertising' ); ?></strong></p>
			</div>
		<?php endif; ?>

		<form method="post">

			<?php $redirect = get_option( 'mc_redirect' ); ?>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="mc_redirect[enable]">Enable</label>
					</th>
					<td>
						<input type="checkbox" name="mc_redirect[enable]" id="mc_redirect[enable]" value="1" <?php echo ( $redirect['enable'] ) ? "checked" : "" ?> />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="mc_redirect[url]">Redirect URL</label>
					</th>
					<td>
						<input type="text" name="mc_redirect[url]" id="mc_redirect[url]" value="<?php echo $redirect['url']; ?>" class="regular-text" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="mc_redirect[hours]">Cookie Hours</label>
					</th>
					<td>
						<input type="number" name="mc_redirect[hours]" id="mc_redirect[hours]" value="<?php echo $redirect['hours']; ?>" class="regular-text" />
					</td>
				</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			</p>
		</form>
	</div>

<?php
}


/**
 * HOOKS
 */
add_action( 'wp', 'mc_redirect_action' );
function mc_redirect_action() {
	if ( ( is_home() || is_front_page() ) && ! isset( $_COOKIE['mc-redirect-hours'] ) ) {

		$redirect = get_option( 'mc_redirect' );
		$url      = $redirect['url'];
		$hours    = $redirect['hours'];
		$enabled  = $redirect['enable'];

		if ( $redirect && $enabled ) {
			if ( false === strpos( $url, '://' ) ) {
				$url = 'http://' . $url;
			}

			setcookie( 'mc-redirect-hours', 1, ( ! $hours ) ? 0 : ( time() + 60 * 60 * $hours ), '/' );

			wp_redirect( $url );
			exit;
		}
	}
}

add_action( 'init', 'mc_redirect_init_action' );
function mc_redirect_init_action() {

	if ( isset ( $_POST['mc_redirect'] ) ) {
		$option_name = 'mc_redirect';
		$newvalue    = stripslashes_deep( $_POST['mc_redirect'] );

		if ( get_option( $option_name ) ) {
			update_option( $option_name, $newvalue );
		}
		else {
			$deprecated = ' ';
			$autoload   = 'no';
			add_option( $option_name, $newvalue, $deprecated, $autoload );
		}
	}

}