<?php
/**
 * Plugin name: Rss Feed Add Eyecatch
 * Description: WordPressから出力されるRSSフィードにアイキャッチURLを新しいitemとして追加して出力
 * Version: 0.1.0
 *
 * @package rss_feed_add_eyecatch
 * @author kutsu
 * @license GPL-2.0+
 */

/* 
プログラム参考：https://digipress.info/wordpress/tips/how-to-add-post-thumbnail-to-rss-feed/
rss2_item hook document：https://developer.wordpress.org/reference/hooks/rss2_item/
 */

function rfae_add_item_eyecatch( $size = 'thumbnail' ) {
	//変数
	global $post; //投稿のオブジェクト

	// アイキャッチが存在しない場合の例外処理
	if ( !has_post_thumbnail($post->ID) ) {
		return;
	}

	//アイキャッチURL出力用のHTMLを生成
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
	if ( isset($thumbnail) ) {
		$output  = '<media:content xmlns:media="http://search.yahoo.com/mrss/" medium="image" type="image/jpeg"';
		$output .= ' url="'. $thumbnail[0] .'"';
		$output .= ' width="'. $thumbnail[1] .'"';
		$output .= ' height="'. $thumbnail[2] .'"';
		$output .= ' />';
	}
	echo $output;
}

add_action('rss2_item', 'rfae_add_item_eyecatch');