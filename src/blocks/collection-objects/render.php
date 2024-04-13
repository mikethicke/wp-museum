<?php

namespace MikeThicke\WPMuseum;

$post_id = get_the_ID();
$output  = '';
if ( $post_id ) {
	echo ( "<div class='wpm-collection-objects-block' data-post-ID='$post_id'></div>" );
}

