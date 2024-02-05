<?php
/**
 * Plugin name: Rss Feed Add Eyecatch
 * Description: WordPressから出力されるRSSフィードにRSSフィード（XML）の各itemにアイキャッチ画像取得用のMIMEを追加します
 * Version: 0.1.0
 *
 * @package rss_feed_add_eyecatch
 * @author kutsu
 * @license GPL-2.0+
 */

/**
 * プログラム参考：https://digipress.info/wordpress/tips/how-to-add-post-thumbnail-to-rss-feed/
 * rss2_item hook document：https://developer.wordpress.org/reference/hooks/rss2_item/
 * https://blog.ver001.com/mime_content_type/
 * https://www.php.net/manual/ja/function.image-type-to-mime-type.php
 */

/**
 * RSS フィードにアイキャッチ画像を追加
 *
 * @param String $size 取得する画像のサイズ.
 */
function rfae_add_item_eyecatch( $size = 'thumbnail' ) {
	// 投稿のオブジェクトを宣言.
	global $post;

	// アイキャッチ画像が存在しない場合の例外処理.
	if ( ! has_post_thumbnail( $post->ID ) ) {
		return;
	}

	// アイキャッチ画像 URL 出力用の HTML を生成.
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
	if ( isset( $thumbnail ) ) {
		// wp_get_attachment_image_src で取得できる各データを変数に代入.
		$image_url    = $thumbnail[0];
		$image_width  = $thumbnail[1];
		$image_height = $thumbnail[2];

		// アイキャッチ画像の名前を取得.
		$image_file_name = basename( $image_url );

		// 許可する画像のタイプを配列で宣言.
		$mime_type_array = array( 'image/gif', 'image/jpeg', 'image/png', 'image/webp' );

		// 取得したアイキャッチ画像の MIME タイプを取得.
		$mime_type = mime_content_type_image( $image_file_name );

		// 許可する MIME タイプに合致した場合のみ出力する.
		if ( in_array( $mime_type, $mime_type_array, true ) ) {
			// 出力する内容を作成.
			$output  = '<media:content xmlns:media="http://search.yahoo.com/mrss/" medium="image" type="' . $mime_type . '"';
			$output .= ' url="' . $image_url . '"';
			$output .= ' width="' . $image_width . '"';
			$output .= ' height="' . $image_height . '"';
			$output .= ' />';
		} else {
			return;
		}
	}
	echo $output; // phpcs:ignore
}
add_action( 'rss2_item', 'rfae_add_item_eyecatch' );

/**
 * 画像ファイルの MIME タイプを返す関数
 *
 * @param String $filename 画像の名前.
 */
function mime_content_type_image( $filename ) {
	// PHP の関数 getimagesize で画像の情報を取得.
	list( $w, $h, $type ) = getimagesize( $filename );

	// $type に画像の MIME タイプが入っていなければカラを返す（画像以外はこちら）.
	if ( ! $type ) {
		return '';
	} else {
		// PHP の関数 getimagesize から返される画像形式の MIME タイプを取得し返す.
		return image_type_to_mime_type( $type );
	}
}
