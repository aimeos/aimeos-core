<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2024
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
	extends \Aimeos\MShop\Common\Item\Base
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
	 * @return bool True if equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Price\Item\Iface $price ) : bool
	{
		if( $this->getValue() === $price->getValue()
			&& $this->getCosts() === $price->getCosts()
			&& $this->getRebate() === $price->getRebate()
			&& $this->getTaxFlag() === $price->getTaxFlag()
			&& $this->getTaxRate() === $price->getTaxRate()
			&& $this->getTaxRates() === $price->getTaxRates()
			&& $this->getQuantity() === $price->getQuantity()
			&& $this->getCurrencyId() === $price->getCurrencyId()
		) {
			return true;
		}

		return false;
	}


	/**
	 * Tests if the price is within the requirements.
	 *
	 * @param string|int|float|null $value Monetary value
	 * @param int|null $precision Number of decimal digits, null for default value
	 * @return string|null Sanitized monetary value
	 */
	protected function checkPrice( $value, ?int $precision = null ) : ?string
	{
		if( $value != '' && !is_numeric( $value ) ) {
			throw new \Aimeos\MShop\Price\Exception( sprintf( 'Invalid characters in price "%1$s"', $value ) );
		}

		return $this->formatNumber( $value !== '' ? $value : null, $precision );
	}


	/**
	 * Formats the money value.
	 *
	 * @param string|int|float|null $number Money value
	 * @param int|null $precision Number of decimal digits, null for default value
	 * @return string|null Formatted money value
	 */
	protected function formatNumber( $number, ?int $precision = null ) : ?string
	{
		return $number !== null ? number_format( $number, $precision ?: $this->getPrecision(), '.', '' ) : null;
	}
}
