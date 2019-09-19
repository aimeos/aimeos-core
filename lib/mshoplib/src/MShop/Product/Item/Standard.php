<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item;


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
	use \Aimeos\MShop\Common\Item\Config\Traits;
	use \Aimeos\MShop\Common\Item\ListRef\Traits {
		__clone as __cloneList;
	}
	use \Aimeos\MShop\Common\Item\PropertyRef\Traits {
		__clone as __cloneProperty;
	}


	private $values;


	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 */
	public function __construct( array $values = [], array $listItems = [],
		array $refItems = [], array $propItems = [] )
	{
		parent::__construct( 'product.', $values );

		$this->initListItems( $listItems, $refItems );
		$this->initPropertyItems( $propItems );
		$this->values = $values;
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
	 * Returns the type of the product item.
	 *
	 * @return string|null Type of the product item
	 */
	public function getType()
	{
		if( isset( $this->values['product.type'] ) ) {
			return (string) $this->values['product.type'];
		}
	}


	/**
	 * Sets the new type of the product item.
	 *
	 * @param string $type New type of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['product.type'] = $this->checkCode( $type );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the status of the product item.
	 *
	 * @return integer Status of the product item
	 */
	public function getStatus()
	{
		if( isset( $this->values['product.status'] ) ) {
			return (int) $this->values['product.status'];
		}

		return 1;
	}


	/**
	 * Sets the new status of the product item.
	 *
	 * @param integer $status New status of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['product.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product item
	 */
	public function getCode()
	{
		if( isset( $this->values['product.code'] ) ) {
			return (string) $this->values['product.code'];
		}

		return '';
	}


	/**
	 * Sets the new code of the product item.
	 *
	 * @param string $code New code of product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( (string) $code !== $this->getCode() )
		{
			$this->values['product.code'] = $this->checkCode( $code );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the data set name assigned to the product item.
	 *
	 * @return string Data set name
	 */
	public function getDataset()
	{
		if( isset( $this->values['product.dataset'] ) ) {
			return (string) $this->values['product.dataset'];
		}

		return '';
	}


	/**
	 * Sets a new data set name assignd to the product item.
	 *
	 * @param string $name New data set name
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDataset( $name )
	{
		if( (string) $name !== $this->getDataset() )
		{
			$this->values['product.dataset'] = $name;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel()
	{
		if( isset( $this->values['product.label'] ) ) {
			return (string) $this->values['product.label'];
		}

		return '';
	}


	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values['product.label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the starting point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		if( isset( $this->values['product.datestart'] ) ) {
			return (string) $this->values['product.datestart'];
		}
	}


	/**
	 * Sets a new starting point of time, in which the product is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateStart( $date )
	{
		if( $date !== $this->getDateStart() )
		{
			$this->values['product.datestart'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the ending point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		if( isset( $this->values['product.dateend'] ) ) {
			return (string) $this->values['product.dateend'];
		}
	}


	/**
	 * Sets a new ending point of time, in which the product is available.
	 *
	 * @param string|null $date New ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setDateEnd( $date )
	{
		if( $date !== $this->getDateEnd() )
		{
			$this->values['product.dateend'] = $this->checkDateFormat( $date );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the configuration values of the item
	 *
	 * @return array Configuration values
	 */
	public function getConfig()
	{
		if( isset( $this->values['product.config'] ) ) {
			return (array) $this->values['product.config'];
		}

		return [];
	}


	/**
	 * Sets the configuration values of the item.
	 *
	 * @param array $config Configuration values
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setConfig( array $config )
	{
		$this->values['product.config'] = $config;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the URL target specific for that product
	 *
	 * @return string URL target specific for that product
	 */
	public function getTarget()
	{
		if( isset( $this->values['product.target'] ) ) {
			return (string) $this->values['product.target'];
		}

		return '';
	}


	/**
	 * Sets a new URL target specific for that product
	 *
	 * @param string $value New URL target specific for that product
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTarget( $value )
	{
		if( (string) $value !== $this->getTarget() )
		{
			$this->values['product.target'] = (string) $value;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the create date of the item
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimeCreated()
	{
		if( isset( $this->values['product.ctime'] ) ) {
			return (string) $this->values['product.ctime'];
		}

		return date( 'Y-m-d H:i:s' );
	}


	/**
	 * Sets the create date of the item
	 *
	 * @param string|null $value ISO date in YYYY-MM-DD hh:mm:ss format
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setTimeCreated( $value )
	{
		if( (string) $value !== $this->getTimeCreated() )
		{
			$this->values['product.ctime'] = $this->checkDateFormat( $value );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'product';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->getDateStart() === null || $this->getDateStart() < $this->values['.date'] )
			&& ( $this->getDateEnd() === null || $this->getDateEnd() > $this->values['.date'] );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'product.type': $item = $item->setType( $value ); break;
				case 'product.code': $item = $item->setCode( $value ); break;
				case 'product.label': $item = $item->setLabel( $value ); break;
				case 'product.status': $item = $item->setStatus( $value ); break;
				case 'product.dataset': $item = $item->setDataset( $value ); break;
				case 'product.datestart': $item = $item->setDateStart( $value ); break;
				case 'product.dateend': $item = $item->setDateEnd( $value ); break;
				case 'product.config': $item = $item->setConfig( $value ); break;
				case 'product.target': $item = $item->setTarget( $value ); break;
				case 'product.ctime': $item = $item->setTimeCreated( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['product.type'] = $this->getType();
		$list['product.code'] = $this->getCode();
		$list['product.label'] = $this->getLabel();
		$list['product.status'] = $this->getStatus();
		$list['product.dataset'] = $this->getDataset();
		$list['product.datestart'] = $this->getDateStart();
		$list['product.dateend'] = $this->getDateEnd();
		$list['product.config'] = $this->getConfig();
		$list['product.target'] = $this->getTarget();
		$list['product.ctime'] = $this->getTimeCreated();

		return $list;
	}
}
