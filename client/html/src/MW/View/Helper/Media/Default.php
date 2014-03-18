<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for generating media HTML tags.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Media_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	/**
	 * Returns the media HTML tag.
	 *
	 * @param MShop_Media_Item_Interface $item Media item, optional with other items media or text items attached
	 * @param string|null $baseurl Base URL that must be prepended to the relative URL
	 * @param array $boxAttributes Associative list of key/value pairs with container attributes
	 * @param array $itemAttributes Associative list of key/value pairs with item attributes.
	 * 	Item attributes can contain placeholders for the URL (%1$s) and the preview URL (%2$s).
	 * 	Title and src are automatically added.
	 * @return string HTML media tag (image, audio, video or link)
	 */
	public function transform( MShop_Media_Item_Interface $item, $baseurl = null,
		array $boxAttributes = array(), array $itemAttributes = array() )
	{
		$enc = $this->encoder();

		$boxattr = '';
		foreach( $boxAttributes as $name => $value ) {
			$boxattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
		}

		$itemattr = '';
		foreach( $itemAttributes as $name => $value ) {
			$itemattr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
		}

		$url = $item->getUrl();
		$previewUrl = $item->getPreview();
		$subItems = $item->getRefItems( 'media' );
		$parts = explode( '/', $item->getMimetype() );

		switch( $parts[0] )
		{
			case 'audio':
				$tag = "<audio ${boxattr}>";
				$tag .= "<source src=\"%1\$s\" title=\"%3\$s\" type=\"%4\$s\" ${itemattr} />";
				$tag .= $this->_createAssociatedMediaString( $subItems, $baseurl, $itemattr );
				$tag .= '%3$s';
				$tag .= '</audio>';
				break;
			case 'video':
				$tag = "<video ${boxattr}>";
				$tag .= "<source src=\"%1\$s\" title=\"%3\$s\" type=\"%4\$s\" ${itemattr} />";
				$tag .= $this->_createAssociatedMediaString( $subItems, $baseurl, $itemattr );
				$tag .= '%3$s';
				$tag .= '</video>';
				break;
			case 'image':
				$tag = "<div ${boxattr}>";
				$tag .= "<img src=\"%2\$s\" title=\"%3\$s\" ${itemattr} />";
				$tag .= $this->_createAssociatedMediaString( $subItems, $baseurl, $itemattr );
				$tag .= '</div>';
				break;
			default:
				$tag = "<a href=\"%1\$s\" ${boxattr}>";
				$tag .= "<img src=\"%2\$s\" title=\"%3\$s\" ${itemattr} />";
				$tag .= $this->_createAssociatedMediaString( $subItems, $baseurl, $itemattr );
				$tag .= '%3$s';
				$tag .= '</a>';
				break;
		}

		if( strncmp( $url, 'http', 4 ) !== 0 && strncmp( $url, 'data', 4 ) !== 0 && $baseurl !== null ) {
			$url = $baseurl . '/' . $url;
		}

		if( strncmp( $previewUrl, 'http', 4 ) !== 0 && strncmp( $previewUrl, 'data', 4 ) !== 0 && $baseurl !== null ) {
			$previewUrl = $baseurl . '/' . $previewUrl;
		}

		$mimetype = $enc->attr( $item->getMimetype() );
		$name = $enc->html( $item->getName() );

		return sprintf( $tag, $enc->attr( $url ), $enc->attr( $previewUrl ), $name, $mimetype );
	}


	/**
	 * Creates the inner tags for associated media items.
	 *
	 * @param array $mediaItems Item objects implementing MShop_Media_Item_Interface
	 * @param string|null $baseurl Base URL that must be prepended to the relative URL
	 * @param string $attributes String of name="value" pairs as item attributes.
	 * 	Item attributes can contain placeholders for the URL (%1$s) and the preview URL (%2$s)
	 * @return string Inner string for media HTML tags
	 */
	protected function _createAssociatedMediaString( array $mediaItems, $baseurl = null, $attributes )
	{
		$string = '';
		$enc = $this->encoder();

		foreach( $mediaItems as $mediaItem )
		{
			$url = $item->getUrl();
			$previewUrl = $item->getPreview();
			$parts = explode( '/', $mediaItem->getMimetype() );

			switch( $parts[0] )
			{
				case 'audio':
				case 'video':
					$tag = "<source src=\"%1\$s\" title=\"%3\$s\" type=\"%4\$s\" ${attributes} />";
					break;
				case 'image':
					$tag = "<img src=\"%2\$s\" title=\"%3\$s\" ${attributes} />";
					break;
				default:
					$tag = '';
			}

			if( strncmp( $url, 'http', 4 ) !== 0 && strncmp( $url, 'data', 4 ) !== 0 && $baseurl !== null ) {
				$url = $baseurl . '/' . $url;
			}

			if( strncmp( $previewUrl, 'http', 4 ) !== 0 && strncmp( $previewUrl, 'data', 4 ) !== 0 && $baseurl !== null ) {
				$previewUrl = $baseurl . '/' . $previewUrl;
			}

			$mimetype = $enc->attr( $item->getMimetype() );
			$name = $enc->html( $item->getName() );

			$string .= sprintf( $tag, $enc->attr( $url ), $enc->attr( $previewUrl ), $name, $mimetype );
		}

		return $string;
	}
}
