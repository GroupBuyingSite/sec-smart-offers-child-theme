<?php

/**
 * This function file is loaded after the parent theme's function file. It's a great way to override functions, e.g. add_image_size sizes.
 *
 *
 */
add_filter( 'sec_modular_compact_archives', 'compact_view_for_location_archives' );
function compact_view_for_location_archives() {
	if ( is_tax( gb_get_location_tax_slug() ) ) { // only locations
		return TRUE;
	}
	return FALSE;
}