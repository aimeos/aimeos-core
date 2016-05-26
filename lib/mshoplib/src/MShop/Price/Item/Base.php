<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Item;


/**
 * Basic methods for all price implementations
 *
 * @package MShop
 * @subpackage Price
 */
abstract class Base
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Price\Item\Iface
{
	/**
	 * Compares the properties of the given price item with its own one.
	 *
	 * This method compare only the essential price properties:
	 * * Value
	 * * Costs
	 * * Rebate
	 * * Tax rate
	 * * Tax flag
	 * * Quantity
	 * * Currency ID
	 *
	 * All other item properties are not compared.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item to compare with
	 * @return boolean True if equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Price\Item\Iface $price )
	{
		if( $this->getValue() === $price->getValue()
			&& $this->getCosts() === $price->getCosts()
			&& $this->getRebate() === $price->getRebate()
			&& $this->getTaxRate() === $price->getTaxRate()
			&& $this->getTaxFlag() === $price->getTaxFlag()
			&& $this->getQuantity() === $price->getQuantity()
			&& $this->getCurrencyId() === $price->getCurrencyId()
		) {
			return true;
		}

		return false;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'price';
	}


	/**
	 * Tests if the price is within the requirements.
	 *
	 * @param integer|double $value Monetary value
	 * @param integer $precision Number of decimal places
	 * @return string Sanitized monetary value
	 */
	protected function checkPrice( $value, $precision = 2 )
	{
		if( $value !== '' && !is_numeric( $value ) ) {
			throw new \Aimeos\MShop\Price\Exception( sprintf( 'Invalid characters in price "%1$s"', $value ) );
		}

		return $this->formatNumber( $value, $precision );
	}


	/**
	 * Formats the money value.
	 *
	 * @param string|double $number Money value
	 * @param integer $precision Number of decimal places
	 * @return string Formatted money value
	 */
	protected function formatNumber( $number, $precision = 2 )
	{
		return number_format( (double) $number, $precision, '.', '' );
	}
}
