<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks the value of a property defined in the configuration
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_PropertyMatch implements MShop_Plugin_Provider_Interface
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
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$class = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $value instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->_item->getConfig();

		if( $config === array() ) {
			return true;
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$criteria = $productManager->createSearch( true );

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.id', $value->getProductId() );
		$expr[] = $criteria->getConditions();

		foreach ( $config as $property => $value) {
			$expr[] = $criteria->compare( '==', $property, $value );
		}

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$result = $productManager->searchItems( $criteria );

		if( reset( $result ) === false )
		{
			$code = array( 'product' => array_keys( $config ) );
			throw new MShop_Plugin_Provider_Exception( sprintf( 'An error occured in a search. Product matching given properties not found.' ), -1, null, $code );
		}

		return true;
	}
}
