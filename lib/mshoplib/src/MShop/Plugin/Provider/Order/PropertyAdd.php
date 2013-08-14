<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Adds attributes to a product in an order
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_PropertyAdd
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Interface
{
	private $_orderAttrManager;
	private $_type;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		parent::__construct($context, $item);

		$this->_orderAttrManager = MShop_Order_Manager_Factory::createManager( $this->_getContext() )
			->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$this->_type = $context->getConfig()->get( 'plugin/provider/order/propertyadd/type', 'property' );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.before' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Exception in case of faulty configuration or parameters
	 * @return bool true if attributes have been added successfully
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$context = $this->_getContext();

		$context->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$class = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $value instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$config = $this->_getItem()->getConfig();

		foreach( $config as $key => $properties )
		{
			$keyElements = explode( '.', $key );

			if( $keyElements[0] !== 'product' || count( $keyElements ) < 3 ) {
				throw new MShop_Plugin_Exception( sprintf( 'Configuration invalid' ) );
			}

			$productSubManager = $productManager->getSubManager( $keyElements[1] );

			$search = $productSubManager->createSearch( true );

			$cond = array();
			$cond[] = $search->compare( '==', $key, $value->getProductId() );
			$cond[] = $search->getConditions();

			$search->setConditions( $search->combine( '&&', $cond ) );

			$result = $productSubManager->searchItems( $search );

			foreach( $result as $item )
			{
				$attributes = $this->_addAttributes( $item, $value, $properties );
				$value->setAttributes( $attributes );
			}
		}

		return true;
	}


	/**
	* Adds attribute items to an array.
	*
	* @param MShop_Common_Item_Interface $item Item containing the properties to be added as attributes
	* @param MShop_Order_Item_Base_Product_Interface $product Product containing attributes
	* @param Array $properties List of item properties to be converted
	* @return Array List of attributes
	*/
	protected function _addAttributes( MShop_Common_Item_Interface $item, MShop_Order_Item_Base_Product_Interface $product , array $properties )
	{
		$attributeList = $product->getAttributes();
		$config = $this->_getItem()->getConfig();
		$itemProperties = $item->toArray();

		foreach( $properties as $current )
		{
			if( array_key_exists( $current, $itemProperties )
				&& ( $new = $this->_createAttribute( $product, $current, $itemProperties[$current] ) ) !== null
			) {
				$attributeList[] = $new;
			}
		}

		return $attributeList;
	}


	/**
	* Creates an attribute with given values for code, type, name and value
	*
	* @param String $code Value for attribute code
	* @param String $value Value for attribute value
	* @param String $name Optional value for attribute name
	* @return MShop_Order_Item_Base_Product_Attribute_Interface Newly created attribte item
	*/
	protected function _createAttribute( MShop_Order_Item_Base_Product_Interface $product, $code, $value, $name = null )
	{
		if( $product->getAttribute( $code ) !== null ) {
			return null;
		}

		$new = $this->_orderAttrManager->createItem();
		$new->setCode( $code );
		$new->setType( $this->_type );
		$new->setName( $name );
		$new->setValue( $value );

		return $new;
	}

}
