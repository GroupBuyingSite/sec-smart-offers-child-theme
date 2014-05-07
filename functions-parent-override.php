<?php

/**
 * This function file is loaded after the parent theme's function file. It's a great way to override functions, e.g. add_image_size sizes.
 *
 *
 */


/**
 * Location preference function
 * @return  
 */
function gb_has_location_preference() {
	if ( isset( $_COOKIE[ 'gb_subscription_process_complete' ] ) && $_COOKIE[ 'gb_subscription_process_complete' ] ) {
		return TRUE;
	}
	return FALSE;
}

/**
 * Don't redirect from homepage
 * @return
 */
function delayed_init_to_remove_redirect() {
	remove_action( 'pre_gbs_head', array( 'Group_Buying_Subscription_Lightbox', 'redirect' ) );
}
add_action( 'init', 'delayed_init_to_remove_redirect' );


/**
 * Set cookie based on signup
 * @return  
 */
function gb_set_signup_cookie() {
	// for those special redirects with a query var
	if ( !headers_sent() && isset( $_GET['signup-success'] ) && $_GET['signup-success'] ) {
		$cookie_time = (time() + 24 * 60 * 60 * 30);
		setcookie( 'gb_subscription_process_complete', '1', $cookie_time, '/' );
		return TRUE;
	}
	return FALSE;
}
add_action( 'gb_deal_view', 'gb_set_signup_cookie' );
add_action( 'gb_deals_view', 'gb_set_signup_cookie' );

/**
 * Don't show lightbox/modal when a signup attempt is successful
 * @param  bool $bool 
 * @return bool       
 */
function lb_show_lightbox( $bool ) {
	gb_set_signup_cookie();
	if ( isset( $_GET['signup-success'] ) ) {
		return FALSE;
	}
	return $bool;
}
add_filter( 'gb_lb_show_lightbox', 'lb_show_lightbox' );

/**
 * Remove js and CSS so smart offers theme can use bootstrap
 */
add_filter( 'gb_lightbox_footer_script', '__return_null', 10 );
add_filter( 'gb_lightbox_custom_css', '__return_null', 10 );

/**
 * Modal using bootstrap
 * @param  string $view                 
 * @param  string $show_locations       
 * @param  string $select_location_text 
 * @param  string $button_text          
 * @return string                       
 */
function lightbox_subscription_form( $view, $show_locations, $select_location_text, $button_text ) {
	ob_start();
	?>
		<script type="text/javascript" charset="utf-8">
			/* When the page is ready */
			jQuery(document).ready(function($){
				$('#subscription_modal').modal('toggle');
			});
		</script>
		<!-- Modal -->
		<div  id="subscription_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="subscription_modal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php gb_e('Join today to start getting awesome daily deals!') ?></h4>
					</div>
					<div class="modal-body">
					  	<form action="" id="gb_lightbox_subscription_form" method="post" class="clearfix">
							
							<div class="form-group">
								<label class="sr-only control-label" for="email_address"><?php gb_e('E-mail'); ?></label>
								<input type="email" class="form-control" id="email_address" name="email_address" placeholder="<?php gb_e('Enter your email'); ?>" required>
							</div>

							<div class="form-group">
								<?php 
									$locations = gb_get_locations( false );
									$no_city_text = get_option(Group_Buying_List_Services::SIGNUP_CITYNAME_OPTION);
									if ( ( !empty($locations) || !empty($no_city_text) ) && $show_locations ) {
											
										echo '<label class="sr-only control-label" for="locations">'.gb__( $select_location_text ).'</label>';

										$current_location = null;
										if ( isset($_COOKIE[ 'gb_location_preference' ]) && $_COOKIE[ 'gb_location_preference' ] != '') {
											$current_location = $_COOKIE[ 'gb_location_preference' ];
										} elseif ( is_tax() ) {
											global $wp_query;
											$query_slug = $wp_query->get_queried_object()->slug;
											if ( isset($query_slug) && !empty( $query_slug ) ) {
												$current_location = $query_slug;
											}
										}
										echo '<select name="deal_location" id="deal_location"  class="form-control">';
											foreach ($locations as $location) {
												echo '<option value="'.$location->slug.'" '.selected($current_location,$location->slug).'>'.$location->name.'</option>';
											}
											if ( !empty($no_city_text) ) {
												echo '<option value="notfound">'.esc_attr( $no_city_text ).'</option>';
											}
										echo '</select>';
									} ?>
							</div>



							<div class="row">
								<div class="form-group">
									<span class="submit_wrap col-sm-5">
										<?php wp_nonce_field( 'gb_subscription' );?>
										<input type="submit" name="gb_subscription" id="gb_subscription" value="<?php gb_e($button_text); ?>" class="form_submit btn btn-primary">
									</span>
									<span class="col-sm-7">
										<a type="button" class="btn btn-default pull-right" href="<?php echo wp_login_url(); ?>"><?php sec_e( 'Login' ); ?></a>
									</span>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	$view = ob_get_clean();
	return $view;
}
add_filter( 'gb_lightbox_subscription_form', 'lightbox_subscription_form', 10, 4 );