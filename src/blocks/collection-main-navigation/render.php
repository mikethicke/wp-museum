<?php

$encoded_attributes = wp_json_encode( $attributes );

echo (
	"<div 
		class='wpm-collection-main-navigation-front' 
		data-attributes='$encoded_attributes'
	>
	</div>"
);
