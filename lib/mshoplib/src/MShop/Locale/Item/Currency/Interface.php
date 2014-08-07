<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Common interface for all currency items.
 * 
 * @package MShop
 * @subpackage Locale
 */
interface MShop_Locale_Item_Currency_Interface
	extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode();


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $key Code of the currency
	 * @return void
	 */
	public function setCode( $key );


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel();


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 * @return void
	 */
	public function setLabel( $label );


	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();


	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 * @return void
	 */
	public function setStatus( $status );

}
