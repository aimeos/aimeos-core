<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Item;


/**
 * Generic interface for all media item.
 *
 * @package MShop
 * @subpackage Media
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\PropertyRef\Iface,
		\Aimeos\MShop\Common\Item\Status\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the ISO language code.
	 *
	 * @return string|null ISO language code (e.g. de or de_DE)
	 */
	public function getLanguageId() : ?string;

	/**
	 * Sets the ISO language code.
	 *
	 * @param string|null $langid ISO language code (e.g. de or de_DE)
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLanguageId( ?string $langid ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Returns the name of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Type label of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType() : string;

	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setMimeType( string $mimetype ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Returns the preview url of the media item.
	 *
	 * @param bool|int $size TRUE for the largest image, FALSE for the smallest or a concrete image width
	 * @return string Preview URL of the media file
	 */
	public function getPreview( $width = false ) : string;

	/**
	 * Returns all preview urls of the media item
	 *
	 * @return array Associative list of widths in pixels as keys and urls as values
	 */
	public function getPreviews() : array;

	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreview( string $url ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param array $url Preview URL or list of URLs with widths of the media file in pixels as keys
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setPreviews( array $urls ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl() : string;

	/**
	 * Sets the new url of the media item.
	 *
	 * @param string|null $url URL of the media file
	 * @return \Aimeos\MShop\Media\Item\Iface Media item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Media\Item\Iface;
}
