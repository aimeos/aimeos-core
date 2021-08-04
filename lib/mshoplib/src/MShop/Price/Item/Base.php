<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;


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
	use ListsRef\Traits, PropertyRef\Traits {
		ListsRef\Traits::__clone insteadof PropertyRef\Traits;
		ListsRef\Traits::__clone as __cloneList;
		PropertyRef\Traits::__clone as __cloneProperty;
	}


	private $precision;


	/**
	 * Initalizes the object with the given values
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( string $prefix, array $values = [], array $listItems = [], array $refItems = [], array $propItems = [] )
	{
		parent::__construct( $prefix, $values );

		$this->precision = (int) ( isset( $values['.precision'] ) ? $values['.precision'] : 2 );
		$this->initListItems( $listItems, $refItems );
		$this->initPropertyItems( $propItems );
	}


	/**
	 * Creates a deep clone of all objects
	 */
	 public function __clone()
	 {
		 parent::__clone();
		 $this->__cloneList();
		 $this->__cloneProperty();
	 }


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
	 * Returns the decimal precision of the price
	 *
	 * @return int Number of decimal digits
	 */
	public function getPrecision() : int
	{
		return $this->precision;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'price';
	}


	/**
	 * Tests if the price is within the requirements.
	 *
	 * @param string|int|float|null $value Monetary value
	 * @param int|null $precision Number of decimal digits, null for default value
	 * @return string|null Sanitized monetary value
	 */
	protected function checkPrice( $value, int $precision = null ) : ?string
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
	protected function formatNumber( $number, int $precision = null ) : ?string
	{
		return $number !== null ? number_format( $number, $precision ?: $this->precision, '.', '' ) : null;
	}
}
