<?php
/**
 * Functions for upgrading database.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Upgrades database from version 0.13 to 0.15. Version 0.13 is the live
 * verion on UTSIC in 2018/2019.
 */
function upgrade_0_13_to_0_15() {
	global $wpdb;

	$old_table_name = $wpdb->prefix . WPM_PREFIX . 'object_types';
	$new_table_name = $wpdb->prefix . WPM_PREFIX . 'mobject_kinds';

	$result = $wpdb->query( "ALTER TABLE $old_table_name RENAME TO `$new_table_name`;" );
	$result = $wpdb->query( "ALTER TABLE `$new_table_name` CHANGE `object_id` `kind_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT;" );

	$object_posts = get_posts(
		[
			'numberposts' => -1,
			'post_type'   => get_object_type_names(),
			'post_status' => 'any',
		]
	);
	foreach ( $object_posts as $object_post ) {
		$custom      = get_post_custom( $object_post->ID );
		if ( ! empty( $custom['description'] ) && empty( $object_post->post_content ) ) {
			$posts_table = $wpdb->prefix . 'posts';
			$query       = "UPDATE $posts_table SET `post_content` = %s WHERE `ID` = %s;";
			$result = $wpdb->query( $wpdb->prepare( $query, $custom['description'][0], $object_post->ID ) );
		}
	}

	$postmeta_table = $wpdb->prefix . 'postmeta';
	$wpdb->query( "DELETE FROM $postmeta_table WHERE meta_key = 'description'" );

	$old_fields_table = $wpdb->prefix . WPM_PREFIX . 'object_fields';
	$new_fields_table = $wpdb->prefix . WPM_PREFIX . 'mobject_fields';
	$wpdb->query( "ALTER TABLE $old_fields_table RENAME TO $new_fields_table ;" );
	$wpdb->query( "ALTER TABLE $new_fields_table CHANGE `object_id` `kind_id` MEDIUMINT(9);");
	$wpdb->query( "DELETE FROM $new_fields_table WHERE `slug` = 'description'" );
}

/**
 * Replace HTML entities in a string while leaving tags in place.
 *
 * @see https://www.php.net/manual/en/function.htmlspecialchars.php#101592
 * @param string $text Text containing potential HTML entities for encoding.
 */
function fixtags( $text ) {
	$text = htmlspecialchars( $text );
	$text = preg_replace( '/=/', '=""', $text );
	$text = preg_replace( '/&quot;/', '&quot;"', $text );

	$tags = '/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(")"&quot;"|)(?|(.*)?&quot;(")|)([\ ]?)(\/|)&gt;/i';

	$replacement = '<$1$2$3$4$5$6$7$8$9$10>';

	$text = preg_replace( $tags, $replacement, $text );
	$text = preg_replace( '/=""/', '=', $text );
	$text = str_replace( '""', '"', $text );

	return $text;
}

/**
 * Encode HTML entities in meta fields that aren't part of proper tags. Otherwise
 * they can break the RichText component.
 */
function fix_meta_html_entities() {
	$posts = get_posts(
		[
			'numberposts' => -1,
			'post_type'   => get_object_type_names(),
			'post_status' => 'any',
		]
	);
	foreach ( $posts as $object_post ) {
		$custom = get_post_custom( $object_post->ID );
		foreach ( $custom as $meta_key => $meta_value ) {
			$updated_value = $meta_value[0];
			$updated_value = htmlspecialchars( $updated_value );
			$updated_value = str_replace( '&quot;', '"', $updated_value );
			$updated_value = str_replace( '&apos;', "'", $updated_value );
			update_post_meta( $object_post->ID, $meta_key, $updated_value );
		}
	}
}
