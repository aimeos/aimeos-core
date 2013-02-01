<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: PropertyAdd.php 1390 2013-01-23 13:36:13Z jevers $
 */


/**
 * Adds attributes to a product in an order
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_PropertyAdd implements MShop_Plugin_Provider_Interface
{

	protected $_item;
	protected $_context;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		$this->_item = $item;
		$this->_context = $context;

		$config = $context->getConfig();
		$this->_type = $config->get( 'plugin/provider/order/propertyadd/type', 'property' );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			$str = 'Received notification from "%1$s" which doesn\'t implement "%2$s"';
			throw new MShop_Plugin_Exception( sprintf( $str, get_class( $order ), $class ) );
		}

		$class = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $value instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Given object isn\'t of type "%1$s"', $class ) );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$config = $this->_item->getConfig();

		foreach( $config as $key => $properties )
		{
			$keyElements = explode( '.', $key );

			if( $keyElements[0] !== 'product' ) {
				throw new MShop_Plugin_Exception( 'Error in configuration.' );
			}

			$productSubManager = $productManager->getSubManager( $keyElements[1] );

			$search = $productSubManager->createSearch( true );

			$cond = array();
			$cond[] = $search->getConditions();
			$cond[] = $search->compare( '==', $key, $value->getProductId() );

			$search->setConditions( $search->combine( '&&', $cond ) );

			$result = $productSubManager->searchItems( $search );

			foreach( $result as $item )
			{
				$attributes = $value->getAttributes();
				$attributes = $this->_addAttributes( $item, $attributes, $properties );
				$value->setAttributes( $attributes );
			}
		}

		return true;
	}


	protected function _addAttributes( MShop_Common_Item_Interface $item, array $attributeList, $properties )
	{
		$config = $this->_item->getConfig();

		$itemProperties = $item->toArray();

		$attributes = array();
		foreach( $properties as $current )
		{
			if( array_key_exists( $current, $itemProperties ) )
			{
				$parts = explode( '.', $current );
				$attributes[] = $this->_createAttribute( $parts[2], $itemProperties[ $current ] );
			}
		}

		foreach( $attributes as $attr )
		{
			if( $this->_similarAttribute( $attr, $attributeList ) === false ) {
				$attributeList[] = $attr;
			}
		}

		return $attributeList;
	}


	protected function _createAttribute( $code, $value, $name = null )
	{
		$attributeManager = MShop_Order_Manager_Factory::createManager( $this->_context )->getSubManager('base')->getSubManager('product')->getSubManager('attribute');

		if( $name === null ) {
			$name = $code;
		}

		$new = $attributeManager->createItem();
		$new->setCode( $code );
		$new->setType( $this->_type );
		$new->setName( $name );
		$new->setValue( $value );

		return $new;
	}


	protected function _similarAttribute( MShop_Order_Item_Base_Product_Attribute_Interface $item, array $list )
	{
		foreach( $list as $element )
		{
			if( $item->getType() === $element->getType() && $item->getCode() === $element->getCode() ) {
				return true;
			}
		}

		return false;
	}
}
