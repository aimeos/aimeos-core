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
	 * @return string HTML media tag (image, audio, video or link)
	 */
	public function transform( MShop_Media_Item_Interface $item, $baseurl = null, array $attributes = array() )
	{
		$enc = $this->encoder();

		$attr = '';
		foreach( $attributes as $name => $value ) {
			$attr .= $name . ( $value != null ? '="' . $enc->attr( $value ) . '"' : '' ) . ' ';
		}

		$url = $item->getUrl();
		$subItems = $item->getRefItems( 'media' );
		$parts = explode( '/', $item->getMimetype() );

		switch( $parts[0] )
		{
			case 'audio':
				$tag = '<audio %2$s>';
				$tag .= '<source src="%1$s" type="%3$s" />';
				$tag .= $this->_createAssociatedMediaString( $subItems );
				$tag .= '%4$s';
				$tag .= '</audio>';
				break;
			case 'video':
				$tag = '<video %2$s>';
				$tag .= '<source src="%1$s" type="%3$s" />';
				$tag .= $this->_createAssociatedMediaString( $subItems );
				$tag .= '%4$s';
				$tag .= '</video>';
				break;
			case 'image':
				$tag = '<div %2$s>';
				$tag .= '<img src="%1$s" />';
				$tag .= $this->_createAssociatedMediaString( $subItems );
				$tag .= '</div>';
				break;
			default:
				$tag = '<a href="%1$s" %2$s>';
				$tag .= $this->_createAssociatedMediaString( $subItems );
				$tag .= '%4$s';
				$tag .= '</a>';
				break;
		}

		if( strncmp( $url, 'data', 4 ) !== 0 && $baseurl !== null ) {
			$url = $baseurl . '/' . $url;
		}

		return sprintf( $tag, $enc->attr( $url ), $attr, $item->getMimetype(), $item->getName() );
	}


	/**
	 * Creates the inner tags for associated media items.
	 *
	 * @param array $mediaItems Item objects implementing MShop_Media_Item_Interface
	 * @return string Inner string for media HTML tags
	 */
	protected function _createAssociatedMediaString( array $mediaItems )
	{
		$string = '';

		foreach( $mediaItems as $mediaItem )
		{
			$mimetype = $mediaItem->getMimetype();
			$parts = explode( '/', $mimetype );

			switch( $parts[0] )
			{
				case 'audio':
				case 'video':
					$tag = '<source src="%1$s" type="%2$s" />';
					break;
				case 'image':
					$tag = '<img src="%1$s" />';
					break;
				default:
					$tag = '';
			}

			$string .= sprintf( $tag, $mediaItem->getUrl(), $mimetype );
		}

		return $string;
	}
}
