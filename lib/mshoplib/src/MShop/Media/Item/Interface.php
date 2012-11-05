<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for all media item.
 *
 * @package MShop
 * @subpackage Media
 */
interface MShop_Media_Item_Interface
	extends MShop_Common_Item_Interface, MShop_Common_Item_ListRef_Interface, MShop_Common_Item_Typeid_Interface
{
	/**
	 * Returns the language ID of the text item.
	 *
	 * @return string|null Language ID of the text item
	 */
	public function getLanguageId();

	/**
	 *  Sets the language ID of the text item.
	 *
	 * @param string|null $langid Language ID of the text type
	 */
	public function setLanguageId( $langid );

	/**
	 * Returns the name of the media item.
	 *
	 * @return string Label of the media item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the media item.
	 *
	 * @param string $label Type label of the media item
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the media item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the new status of the media item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status );

	/**
	 * Returns the mime type of the media item.
	 *
	 * @return string Mime type of the media item
	 */
	public function getMimeType();

	/**
	 * Sets the new mime type of the media.
	 *
	 * @param string $mimetype Mime type of the media item
	 */
	public function setMimeType( $mimetype );

	/**
	 * Returns the url of the media item.
	 *
	 * @return string URL of the media file
	 */
	public function getUrl();

	/**
	 * Sets the new url of the media item.
	 *
	 * @param string $url URL of the media file
	 */
	public function setUrl( $url );

	/**
	 * Returns the preview url of the media item.
	 *
	 * @return string Preview URL of the media file
	 */
	public function getPreview();

	/**
	 * Sets the new preview url of the media item.
	 *
	 * @param string $url Preview URL of the media file
	 */
	public function setPreview( $url );
}
