<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item;

use \Aimeos\MShop\Common\Item\Config;
use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;
use \Aimeos\MShop\Common\Item\TypeRef;


/**
 * Default impelementation of a product item.
 *
 * @package MShop
 * @subpackage Product
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Product\Item\Iface
{
	use Stock, Config\Traits, ListsRef\Traits, PropertyRef\Traits, TypeRef\Traits {
		PropertyRef\Traits::__clone as __cloneProperty;
		ListsRef\Traits::__clone as __cloneList;
		ListsRef\Traits::getName as getNameList;
		Stock::__clone as __cloneStock;
	}


	/**
	 * Initializes the item object.
	 *
	 * @param string $prefix Domain specific prefix string
	 * @param array $values Parameter for initializing the basic properties
	 */
	public function __construct( string $prefix, array $values = [] )
	{
		parent::__construct( $prefix, $values );

		$this->initPropertyItems( $values['.propitems'] ?? [] );
		$this->initListItems( $values['.listitems'] ?? [] );
		$this->initStockItems( $values['.stock'] ?? [] );
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();
		$this->__cloneList();
		$this->__cloneStock();
		$this->__cloneProperty();
	}


	/**
	 * Returns the parent product items referencing the product
	 *
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Product\Item\Iface
	 */
	public function getParentItems() : \Aimeos\Map
	{
		return map( $this->get( '.parent' ) );
	}


	/**
	 * Returns the supplier items referencing the product
	 *
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Supplier\Item\Iface
	 */
	public function getSiteItem() : ?\Aimeos\MShop\Locale\Item\Site\Iface
	{
		return $this->get( '.locale/site' );
	}


	/**
	 * Returns the status of the product item.
	 *
	 * @return int Status of the product item
	 */
	public function getStatus() : int
	{
		return $this->get( 'product.status', 1 );
	}


	/**
	 * Sets the new status of the product item.
	 *
	 * @param int $status New status of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'product.status', $status );
	}


	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product item
	 */
	public function getCode() : string
	{
		return $this->get( 'product.code', '' );
	}


	/**
	 * Sets the new code of the product item.
	 *
	 * @param string $code New code of product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.code', \Aimeos\Utils::code( $code ) );
	}


	/**
	 * Returns the data set name assigned to the product item.
	 *
	 * @return string Data set name
	 */
	public function getDataset() : string
	{
		return $this->get( 'product.dataset', '' );
	}


	/**
	 * Sets a new data set name assignd to the product item.
	 *
	 * @param string $name New data set name
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDataset( ?string $name ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.dataset', \Aimeos\Utils::code( (string) $name ) );
	}


	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel() : string
	{
		return $this->get( 'product.label', '' );
	}


	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.label', $label );
	}


	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @param string|null $langId Two letter ISO Language code of the text
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name', ?string $langId = null ) : string
	{
		$name = $this->getNameList( $type, $langId );

		if( $type === 'url' && $name === $this->getLabel() ) {
			return $this->getUrl();
		}

		return $name;
	}


	/**
	 * Returns the URL segment for the product item.
	 *
	 * @return string URL segment of the product item
	 */
	public function getUrl() : string
	{
		return (string) $this->get( 'product.url' ) ?: \Aimeos\Base\Str::slug( $this->getLabel() );
	}


	/**
	 * Sets a new URL segment for the product.
	 *
	 * @param string|null $url New URL segment of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setUrl( ?string $url ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.url', \Aimeos\Base\Str::slug( $url ) );
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig() : array
	{
		return $this->get( 'product.config', [] );
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setConfig( array $config ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'product.config', $config );
	}


	/**
	 * Returns the starting point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart() : ?string
	{
		$value = $this->get( 'product.datestart' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new starting point of time, in which the product is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateStart( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'product.datestart', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the ending point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd() : ?string
	{
		$value = $this->get( 'product.dateend' );
		return $value ? substr( $value, 0, 19 ) : null;
	}


	/**
	 * Sets a new ending point of time, in which the product is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateEnd( ?string $date ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'product.dateend', \Aimeos\Utils::datetime( $date ) );
	}


	/**
	 * Returns the rating of the item
	 *
	 * @return string Decimal value of the item rating
	 */
	public function getRating() : string
	{
		return (string) $this->get( 'product.rating', 0 );
	}


	/**
	 * Returns the total number of ratings for the item
	 *
	 * @return int Total number of ratings for the item
	 */
	public function getRatings() : int
	{
		return (int) $this->get( 'product.ratings', 0 );
	}


	/**
	 * Returns the quantity scale of the product item.
	 *
	 * @return float Quantity scale
	 */
	public function getScale() : float
	{
		return (float) $this->get( 'product.scale', 1 ) ?: 1;
	}


	/**
	 * Sets a new quantity scale of the product item.
	 *
	 * @param float $value New quantity scale
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setScale( float $value ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.scale', $value > 0 ? $value : 1 );
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget() : string
	{
		return $this->get( 'product.target', '' );
	}


	/**
	 * Sets a new URL target specific for that product
	 *
	 * @param string $value New URL target specific for that product
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTarget( ?string $value ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.target', (string) $value );
	}


	/**
	 * Returns the create date of the item
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated() : ?string
	{
		return $this->get( 'product.ctime' );
	}


	/**
	 * Sets the create date of the item
	 *
	 * @param string|null $value ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTimeCreated( ?string $value ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.ctime', \Aimeos\Utils::datetime( $value ) );
	}


	/**
	 * Returns the type of the product item.
	 * Overwritten for different default value.
	 *
	 * @return string Type of the product item
	 */
	public function getType() : string
	{
		return $this->get( 'product.type', 'default' );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		$date = $this->get( '.date' ) ?: date( 'Y-m-d H:i:s' );

		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $date )
			&& ( $this->getDateStart() === null || $this->getDateStart() < $date || $this->getType() === 'event' );
	}


	/**
	 * Returns the flag if stock is available for that product.
	 *
	 * @return int "1" if product is in stock, "0" if product is out of stock
	 */
	public function inStock() : int
	{
		return (int) $this->get( 'product.instock', 0 );
	}


	/**
	 * Sets the flag if stock is available for that product.
	 *
	 * @param int $value "1" if product is in stock, "0" if product is out of stock
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setInStock( int $value ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.instock', $value );
	}

	/**
	 * Returns the boost factor for that product.
	 *
	 * @return float Boost factor
	 */
	public function boost() : float
	{
		return (float) $this->get( 'product.boost', 1 );
	}

	/**
	 * Sets the boost factor for that product.
	 *
	 * @param float $value Boost factor
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setBoost( float $value ) : \Aimeos\MShop\Product\Item\Iface
	{
		return $this->set( 'product.boost', $value );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'product.url': $item->setUrl( $value ); break;
				case 'product.type': $item->setType( $value ); break;
				case 'product.code': $item->setCode( $value ); break;
				case 'product.label': $item->setLabel( $value ); break;
				case 'product.dataset': $item->setDataset( $value ); break;
				case 'product.scale': $item->setScale( (float) $value ); break;
				case 'product.status': $item->setStatus( (int) $value ); break;
				case 'product.datestart': $item->setDateStart( $value ); break;
				case 'product.dateend': $item->setDateEnd( $value ); break;
				case 'product.config': $item->setConfig( $value ); break;
				case 'product.target': $item->setTarget( $value ); break;
				case 'product.ctime': $item->setTimeCreated( $value ); break;
				case 'product.instock': $item->setInStock( (bool) $value ); break;
				case 'product.boost': $item->setBoost( (float) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['product.url'] = $this->getUrl();
		$list['product.type'] = $this->getType();
		$list['product.code'] = $this->getCode();
		$list['product.label'] = $this->getLabel();
		$list['product.status'] = $this->getStatus();
		$list['product.dataset'] = $this->getDataset();
		$list['product.datestart'] = $this->getDateStart();
		$list['product.dateend'] = $this->getDateEnd();
		$list['product.config'] = $this->getConfig();
		$list['product.scale'] = $this->getScale();
		$list['product.target'] = $this->getTarget();
		$list['product.ctime'] = $this->getTimeCreated();
		$list['product.ratings'] = $this->getRatings();
		$list['product.rating'] = $this->getRating();
		$list['product.instock'] = $this->inStock();
		$list['product.boost'] = $this->boost();

		return $list;
	}
}
