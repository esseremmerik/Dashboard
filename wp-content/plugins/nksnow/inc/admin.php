<?php

/**
 * Install hook
 *
 * @todo use one option... somewhen
 */
function nksnow_activate() {
	if ( !get_option( 'nksnow_snowflakes' ) ) {
	    update_option( 'nksnow_snowflakes', '10' );
	    update_option( 'nksnow_timeout', '80' );
	    update_option( 'nksnow_maxstepx', '10' );
	    update_option( 'nksnow_maxstepy', '10' );
	    update_option( 'nksnow_selected', 'flake2.gif,flake3.gif');
	    update_option( 'nksnow_maxtime', '20' );
	    update_option( 'nksnow_uri', '' );
	    update_option( 'nksnow_precise', '' );
	    update_option( 'nksnow_flakesize', '40' );
	    update_option( 'nksnow_invert', 'No' );
	}
}

/**
 * Uninstall hook
 */
function nksnow_uninstall() {
    delete_option( 'nksnow_snowflakes' );
    delete_option( 'nksnow_timeout' );
    delete_option( 'nksnow_maxstepx' );
    delete_option( 'nksnow_maxstepy' );
    delete_option( 'nksnow_selected' );
    delete_option( 'nksnow_maxtime' );
    delete_option( 'nksnow_uri' );
    delete_option( 'nksnow_precise' );
    delete_option( 'nksnow_flakesize' );
    delete_option( 'nksnow_invert' );
}

/**
 * Add options page
 */
function nksnow_add_pages() {
	$page = add_options_page( __( 'Snow and more', 'nksnow' ), __( 'Snow and more', 'nksnow' ), 10, 'nksnow', 'nksnow_options_page' );
	add_action( 'admin_head-' . $page, 'nksnow_css_admin' );

	// Add icon
	add_filter( 'ozh_adminmenu_icon_nksnow', 'nksnow_icon' );
}

/**
 * Return admin menu icon
 *
 * @return string path to icon
 *
 * @since 0.9.1.1
 */
function nksnow_icon() {
	global $nksnow;
	return $nksnow['url'] . 'pic/weather_snow.png';
}

/**
 * Load admin CSS style
 *
 * @since 0.9.0
 *
 */
function nksnow_css_admin() {
	global $nksnow; ?>
    <link rel="stylesheet" href="<?php echo $nksnow['url'] . 'css/admin.css?v=0.2.4' ?>" type="text/css" media="all" /> <?php
}


/**
 * The options page
 *
 * @todo Makes this, erm, better. Or not. It was my first plugin.
 */
function nksnow_options_page() {
	if ( current_user_can( 'manage_options' ) ) { ?>
		<div class="wrap" >  <?php
			if ( $_POST['nksnow'] ) {
				#function_exists( 'check_admin_referer' ) ? check_admin_referer( 'nksnow' ) : null;
				$nonce = $_POST['_wpnonce'];
				if ( !wp_verify_nonce( $nonce, 'nksnow') ) die( 'Security check' );

				update_option('nksnow_snowflakes', $_POST['nksnow_snowflakes']);
				update_option('nksnow_uri', $_POST['nksnow_uri']);
				update_option('nksnow_precise', $_POST['nksnow_precise']);
				update_option('nksnow_timeout', $_POST['nksnow_timeout']);
				update_option('nksnow_maxstepx', $_POST['nksnow_maxstepx']);
				update_option('nksnow_homelink', $_POST['nksnow_homelink']);
				update_option('nksnow_maxstepy', $_POST['nksnow_maxstepy']);
				update_option('nksnow_maxtime', $_POST['nksnow_maxtime']);
				update_option('nksnow_invert', $_POST['nksnow_invert']);

				// todo why array? single flake?
				if ( is_array( $_POST['nksnow_selected'] ) ) {
					update_option( 'nksnow_selected', $_POST['nksnow_selected'] );
				}
				else {
					update_option( 'nksnow_selected', array( 'flake2.gif','flake3.gif' ) );
				}
				update_option( 'nksnow_flakesize', $_POST['nksnow_flakesize'] );
			}
		} ?>

		<h2><?php _e( 'Snow and more', 'nksnow' ) ?></h2> <?php

		require_once( 'nkuttler.php' );
		nkuttler0_2_4_links(
			'nksnow',
			'http://www.nkuttler.de/wordpress-plugin/snow-balloons-and-more/'
		); ?>

		<h3><?php _e( 'Settings', 'nksnow' ) ?></h3>
		<form action="" method="post">
			<?php if ( function_exists('wp_nonce_field') ) wp_nonce_field( 'nksnow' )  ?>
			<input type="hidden" name="nksnow" value="hello!" />
			<table class="form-table" id="clearnone" >
				<tr>
					<th>
						<label>
							<?php _e( 'Show how many snowflakes (or other objects)?', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_snowflakes" > <?php
							$select = get_option('nksnow_snowflakes');
							for ($i = 20 ; $i >= 0; $i--) {
								if ( $i == $select ) {
									echo "<option selected>$i</option>\n";
								}
								else {
									echo "<option>$i</option>\n";
								}
							} ?>
						</select>
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php _e( 'Which of these flakes, drops, leaves and balloons do you want? ', 'nksnow' ) ?>
						</label>
					</th>
					<td> <?php
						nksnow_listpics( '<b>GIF</b>', '/\.gif$/' );
						nksnow_listpics( '<b>PNG</b> Don\'t use them if you care about <a href="http://www.stoplivinginthepast.com/" target="_blank" >IE6</a> support.', '/\.png$/' );
						nksnow_listpics( '<b>Rest</b>', '/!(\.(gif|png)$)/' ); ?>
					</td>
				</tr>

				<tr>
					<th>
						&nbsp;<!-- yes -->
					<td>
						<?php _e( 'By the way if you have nice snowflakes, drops, leaves etc. feel free to submit them to me if you made them yourself.', 'nksnow' ) ?>
					</td>
				</tr>

				<tr>
					<th>
						<label>
							<?php _e( 'Use the balloon mode? This will make all images float upwards.', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_invert">
							<option value="Yes" <?php
								if (get_option('nksnow_invert') === 'Yes') {
									echo "selected";
								}?>><?php _e( 'Yes', 'nksnow' ) ?></option>
							<option <?php
								if (get_option('nksnow_invert') !== 'Yes') {
									echo "selected";
								}?>><?php _e( 'No', 'nksnow' ) ?></option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit" >
				<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'nksnow' ) ?>" />
			</p>

			<h3><?php _e( 'Custom images', 'nksnow' ) ?></h3>
			<p>
				<?php _e( 'If you add your own images to the <tt>pics</tt> directory they will appear in the table above. To have them disappear properly when they are leaving the visible part of the browser window you may have to change the <tt>flakesize</tt> value.', 'nksnow' ) ?>
				<br />
				<?php _e( "Make sure the value is bigger than your highest image's height and broadest image's width.", 'nksnow' ) ?>
			</p>

			<table class="form-table">
				<tr>
					<th>
						<label>
							<?php _e( 'Flakesize?', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_flakesize" > <?php
						$select = get_option('nksnow_flakesize');
						for ($i = 20 ; $i <= 500; $i = $i + 10) {
							if ( $i == $select ) {
								echo "<option selected>$i</option>\n";
							}
							else {
								echo "<option>$i</option>\n";
							}
						} ?>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit" >
				<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'nksnow' ) ?>" />
			</p>

			<h3><?php _e( 'Pro settings', 'nksnow' ) ?></h3>

			<table class="form-table">
				<tr>
					<th>
						<label>
							<?php _e( 'Stop snow after how many seconds?', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<input type="text" name="nksnow_maxtime" value="<?php echo get_option('nksnow_maxtime'); ?>" size="3">
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( 'Overall speed (timeout in milliseconds between moves)? ', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_timeout" > <?php
							$select = get_option('nksnow_timeout');
							for ($i = 40 ; $i <= 500; $i = $i + 40) {
								if ( $i == $select ) {
									echo "<option selected>$i</option>\n";
								}
								else {
									echo "<option>$i</option>\n";
								}
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( 'Maximum Wind strength ', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_maxstepx" > <?php
							$select = get_option('nksnow_maxstepx');
							for ($i = 1 ; $i <= 20; $i++) {
								if ( $i == $select ) {
									echo "<option selected>$i</option>\n";
								}
								else {
									echo "<option>$i</option>\n";
								}
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( 'Maximum Falling speed', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_maxstepy" > <?php
							$select = get_option('nksnow_maxstepy');
							for ($i = 3 ; $i <= 20; $i++) {
								if ( $i == $select ) {
									echo "<option selected>$i</option>\n";
								}
								else {
									echo "<option>$i</option>\n";
								}
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( 'Show snowflakes only on pages whose URI contains', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<input type="text" value="<?php echo get_option('nksnow_uri'); ?>" name="nksnow_uri" />
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( "Show snowflakes only if the URI given above and the URI are equal (\$_SERVER['REQUEST_URI'] == URI string)?", 'nksnow' ) ?>
						</label>
					</th>
					<td>
		   				<input type="checkbox" name="nksnow_precise" <?php
							if (get_option('nksnow_precise') === 'on') {
								echo "checked";
							}?>>
					</td>
				</tr>
				<tr>
					<th>
						<label>
							<?php _e( 'Hide the &quot;Powered by&quot; message in the footer?', 'nksnow' ) ?>
						</label>
					</th>
					<td>
						<select name="nksnow_homelink">
							<option value="Yes" <?php
								if (get_option('nksnow_homelink') === 'Yes') {
									echo "selected";
								}?>><?php _e( 'Yes', 'nksnow' ) ?></option>
							<option value="No" <?php
								if (get_option('nksnow_homelink') !== 'Yes') {
									echo "selected";
								}?>><?php _e( 'No', 'nksnow' ) ?></option>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit" >
				<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'nksnow' ) ?>" />
			</p>


		</form>
	</div> <?php
}

/**
 * List pictures with proper markup
 *
 * @since 0.9.2
 */
function nksnow_listpics( $header, $pattern = null ) {
	global $nksnow;
	$dirArray = nksnow_dirArray( $pattern );
	if ( !isset( $dirArray ) )
		return;
	$selected_array = get_option('nksnow_selected');

	// 0.7.3 had some incompatible changes, check
	if (!is_array($selected_array)) {
		$selected_array = array('flake2.gif', 'flake3.gif');
	} ?>

	<?php echo $header ?>

	<div class="nksnow_select_wrap"> <?php

		for ($i = 0 ; $i < count($dirArray); $i++) { ?>
			<div class="nksnow_select" > <?php
				if ( is_integer(array_search($dirArray[$i], $selected_array)) ) {
					echo "<input type=\"checkbox\" name=\"nksnow_selected[]\" value=\"$dirArray[$i]\" checked />";
				}
				else {
					echo "<input type=\"checkbox\" name=\"nksnow_selected[]\" value=\"$dirArray[$i]\" />";
				}
				echo '<br>';
				echo '<img src="' . $nksnow['url'] . "pics/" . $dirArray[$i] . "\" />"; ?>
			</div >
			<?php
		} ?>
		<div style="clear:both;">&nbsp;</div>
	</div> <?php
}
