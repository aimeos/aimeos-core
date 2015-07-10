<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Default implementation for order item base service.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Item_Base_Service_Default
	extends MShop_Order_Item_Base_Service_Abstract
	implements MShop_Order_Item_Base_Service_Interface
{
	private $_price;
	private $_attributes;
	private $_attributesMap;
	private $_values;

	/**
	 * Initializes the order base service item
	 *
	 * @param MShop_Price_Item_Interface $price
	 * @param array $values Values to be set on initialisation
	 * @param array $attributes Attributes to be set on initialisation
	 */
	public function __construct( MShop_Price_Item_Interface $price, array $values=array(), array $attributes=array() )
	{
		parent::__construct('order.base.service.', $values);

		$this->_values = $values;
		$this->_price = $price;

		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Service_Attribute_Interface', $attributes );
		$this->_attributes = $attributes;
	}


	/**
	 * Clones internal objects of the order base service item.
	 */
	public function __clone()
	{
		$this->_price = clone $this->_price;
	}


	/**
	 * Returns the order base ID of the order service if available.
	 *
	 * @return integer|null Base ID of the item.
	 */
	public function getBaseId()
	{
		return ( isset( $this->_values['baseid'] ) ? (int) $this->_values['baseid'] : null );
	}


	/**
	 * Sets the order service base ID of the order service item.
	 *
	 * @param integer|null $value Order service base ID
	 */
	public function setBaseId( $value )
	{
		if ( $value == $this->getBaseId() ) { return; }

		$this->_values['baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the original ID of the service item used for the order.
	 *
	 * @return string Original service ID
	 */
	public function getServiceId()
	{
		return( isset( $this->_values['servid'] ) ? (string) $this->_values['servid'] : '' );
	}


	/**
	 * Sets a new ID of the service item used for the order.
	 *
	 * @param string $servid ID of the service item used for the order
	 */
	public function setServiceId( $servid )
	{
		if( $servid == $this->getServiceId() ) { return; }

		$this->_values['servid'] = (string) $servid;
		$this->setModified();
	}


	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the name of the service item.
	 *
	 * @return string Service item name
	 */
	public function getName()
	{
		return ( isset( $this->_values['name'] ) ? (string) $this->_values['name'] : '' );
	}


	/**
	 * Sets a new name for the service item.
	 *
	 * @param string $name service item name
	 */
	public function setName( $name )
	{
		if ( $name == $this->getName() ) { return; }

		$this->_values['name'] = (string) $name;
		$this->setModified();
	}


	/**
	 * Returns the type of the service item.
	 *
	 * @return string service item type
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Sets a new type for the service item.
	 *
	 * @param string $type Type of the service item
	 */
	public function setType( $type )
	{
		if ( $type == $this->getType() ) { return; }

		$this->_values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Returns the location of the media.
	 *
	 * @return string Location of the media
	 */
	public function getMediaUrl()
	{
		return ( isset( $this->_values['mediaurl'] ) ? (string) $this->_values['mediaurl'] : '' );
	}


	/**
	 * Sets the media url of the service item.
	 *
	 * @param string $value Location of the media/picture
	 */
	public function setMediaUrl( $value )
	{
		if ( $value == $this->getMediaUrl() ) {
			return;
		}

		$this->_values['mediaurl'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the price object which belongs to the service item.
	 *
	 * @return MShop_Price_Item_Interface Price item
	 */
	public function getPrice()
	{
		return $this->_price;
	}


	/**
	 * Sets a new price object for the service item.
	 *
	 * @param MShop_Price_Item_Interface $price Price item
	 */
	public function setPrice( MShop_Price_Item_Interface $price )
	{
		if ( $price === $this->_price ) { return; }

		$this->_price = $price;
		$this->setModified();
	}


	/**
	 * Returns the value of the attribute item for the service with the given code.
	 *
	 * @param string $code code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return string|null value of the attribute item for the service and the given code
	 */
	public function getAttribute( $code, $type = '' )
	{
		$map = $this->_getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code]->getValue();
		}

		return null;
	}

	/**
	 * Returns the attribute item for the service with the given code.
	 *
	 * @param string $code Code of the service attribute item
	 * @param string $type Type of the service attribute item
	 * @return MShop_Order_Item_Base_Service_Attribute_Interface|null Attribute item for the service and the given code
	 */
	public function getAttributeItem( $code, $type = '' )
	{
		$map = $this->_getAttributeMap();

		if( isset( $map[$type][$code] ) ) {
			return $map[$type][$code];
		}

		return null;
	}


	/**
	 * Adds or replaces the attribute item in the list of service attributes.
	 *
	 * @param MShop_Order_Item_Base_Service_Attribute_Interface $item Service attribute item
	 */
	public function setAttributeItem( MShop_Order_Item_Base_Service_Attribute_Interface $item )
	{
		$this->_getAttributeMap();

		$type = $item->getType();
		$code = $item->getCode();

		if( !isset( $this->_attributesMap[$type][$code] ) )
		{
			$this->_attributesMap[$type][$code] = $item;
			$this->_attributes[] = $item;
		}

		$this->_attributesMap[$type][$code]->setValue( $item->getValue() );
		$this->setModified();
	}


	/**
	 * Returns the list of attribute items for the service.
	 *
	 * @param string|null $type Filters returned attributes by the given type or null for no filtering
	 * @return array List of attribute items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function getAttributes( $type = null )
	{
		if( $type === null ) {
			return $this->_attributes;
		}

		$map = $this->_getAttributeMap();

		if( isset( $map[$type] ) ) {
			return $map[$type];
		}

		return array();
	}


	/**
	 * Sets the new list of attribute items for the service.
	 *
	 * @param array $attributes List of attribute items implementing MShop_Order_Item_Base_Service_Attribute_Interface
	 */
	public function setAttributes( array $attributes )
	{
		MW_Common_Abstract::checkClassList( 'MShop_Order_Item_Base_Service_Attribute_Interface', $attributes );

		$this->_attributes = $attributes;
		$this->_attributesMap = null;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'order.base.service.baseid': $this->setBaseId( $value ); break;
				case 'order.base.service.code': $this->setCode( $value ); break;
				case 'order.base.service.serviceid': $this->setServiceId( $value ); break;
				case 'order.base.service.name': $this->setName( $value ); break;
				case 'order.base.service.mediaurl': $this->setMediaUrl( $value ); break;
				case 'order.base.service.type': $this->setType( $value ); break;
				case 'order.base.service.price': $this->setValue( $value ); break;
				case 'order.base.service.costs': $this->setCosts( $value ); break;
				case 'order.base.service.rebate': $this->setRebate( $value ); break;
				case 'order.base.service.taxrate': $this->setTaxRate( $value ); break;
				case 'order.base.service.price': $this->_price->setValue( $value ); break;
				case 'order.base.service.costs': $this->_price->setCosts( $value ); break;
				case 'order.base.service.rebate': $this->_price->setRebate( $value ); break;
				case 'order.base.service.taxrate': $this->_price->setTaxRate( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values.
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$price = $this->_price;

		$list['order.base.service.baseid'] = $this->getBaseId();
		$list['order.base.service.code'] = $this->getCode();
		$list['order.base.service.serviceid'] = $this->getServiceId();
		$list['order.base.service.name'] = $this->getName();
		$list['order.base.service.mediaurl'] = $this->getMediaUrl();
		$list['order.base.service.type'] = $this->getType();
		$list['order.base.service.price'] = $price->getValue();
		$list['order.base.service.costs'] = $price->getCosts();
		$list['order.base.service.rebate'] = $price->getRebate();
		$list['order.base.service.taxrate'] = $price->getTaxRate();

		return $list;
	}


	/**
	 * Copys all data from a given service item.
	 *
	 * @param MShop_Service_Item_Interface $service New service item
	 */
	public function copyFrom( MShop_Service_Item_Interface $service )
	{
		$this->setCode( $service->getCode() );
		$this->setName( $service->getName() );
		$this->setType( $service->getType() );
		$this->setServiceId( $service->getId() );

		$items = $service->getRefItems( 'media', 'default', 'default' );
		if( ( $item = reset( $items ) ) !== false ) {
			$this->setMediaUrl( $item->getUrl() );
		}

		$this->setModified();
	}


	/**
	 * Returns the attribute map for the service.
	 *
	 * @return array Associative list of type and code as key and an MShop_Order_Item_Base_Service_Attribute_Interface as value
	 */
	protected function _getAttributeMap()
	{
		if( !isset( $this->_attributesMap ) )
		{
			$this->_attributesMap = array();

			foreach( $this->_attributes as $attribute ) {
				$this->_attributesMap[ $attribute->getType() ][ $attribute->getCode() ] = $attribute;
			}
		}

		return $this->_attributesMap;
	}
}