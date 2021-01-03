<?php
function wsv_update_option( array $options ) {
	foreach ( $options as $key => $value ) {
		update_option( $key, $value );
	}
}
