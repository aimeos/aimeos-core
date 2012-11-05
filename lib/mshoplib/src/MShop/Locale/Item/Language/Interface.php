<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common interface for all language items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface MShop_Locale_Item_Language_Interface
	extends MShop_Common_Item_Interface
{
	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode();


	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $key two letter ISO language code
	 */
	public function setCode( $key );


	/**
	 * Returns the label property of the language.
	 *
	 * @return string Label or symbol of the language
	 */
	public function getLabel();


	/**
	 * Sets the label property of the language.
	 *
	 * @param string $label Label or symbol of the language
	 */
	public function setLabel( $label );


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the language
	 */
	public function setStatus( $status );

}
