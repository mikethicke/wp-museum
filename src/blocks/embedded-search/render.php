<?php
/**
 * Embedded search block that redirects to search page on submit.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

$encoded_attributes = wp_json_encode( $attributes );

echo (
	"<div 
		class='wpm-embedded-search-block-frontend' 
		data-attributes='$encoded_attributes'
	>
	</div>"
);
