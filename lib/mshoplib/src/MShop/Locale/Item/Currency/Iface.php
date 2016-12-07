<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Currency;


/**
 * Common interface for all currency items.
 *
 * @package MShop
 * @subpackage Locale
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface
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
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
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
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setStatus( $status );

}
