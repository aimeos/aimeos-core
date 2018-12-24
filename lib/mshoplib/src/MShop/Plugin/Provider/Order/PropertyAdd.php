<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds product properties to an order product as attributes
 *
 * Example configuration:
 * - product.property.parentid: ["length", "width", "height", "weight"]
 *
 * The product properties listed in the array are added to the order product as
 * order product attributes with key/value pairs like code: "length", value: "1.0".
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/standard/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class PropertyAdd
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'product.property.parentid' => array(
			'code' => 'product.property.parentid',
			'internalcode' => 'product.property.parentid',
			'label' => 'Property type codes',
			'type' => 'list',
			'internaltype' => 'array',
			'default' => [],
			'required' => true,
		),
	);

	private $orderAttrManager;
	private $type;


	/**
	 * Initializes the plugin instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item )
	{
		parent::__construct( $context, $item );

		$this->orderAttrManager = \Aimeos\MShop::create( $context, 'order/base/product/attribute' );
		$this->type = $context->getConfig()->get( 'plugin/provider/order/propertyadd/type', 'property' );
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE()
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'addProduct.before' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Exception in case of faulty configuration or parameters
	 * @return bool true if attributes have been added successfully
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $value );

		$productManager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$config = $this->getItemBase()->getConfig();

		foreach( $config as $key => $properties )
		{
			$keyElements = explode( '.', $key );

			if( $keyElements[0] !== 'product' || count( $keyElements ) < 3 )
			{
				$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Configuration invalid' );
				throw new \Aimeos\MShop\Plugin\Exception( $msg );
			}

			$productSubManager = $productManager->getSubManager( $keyElements[1] );

			$search = $productSubManager->createSearch( true );
			$cond = [
				$search->compare( '==', $key, $value->getProductId() ),
				$search->getConditions(),
			];
			$search->setConditions( $search->combine( '&&', $cond ) );

			$result = $productSubManager->searchItems( $search );

			foreach( $result as $item )
			{
				$attributes = $this->addAttributes( $item, $value, $properties );
				$value->setAttributeItems( $attributes );
			}
		}

		return true;
	}


	/**
	 * Adds attribute items to an array.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item containing the properties to be added as attributes
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $product Product containing attributes
	 * @param Array $properties List of item properties to be converted
	 * @return Array List of attributes
	 */
	protected function addAttributes( \Aimeos\MShop\Common\Item\Iface $item, \Aimeos\MShop\Order\Item\Base\Product\Iface $product, array $properties )
	{
		$attributeList = $product->getAttributeItems();
		$itemProperties = $item->toArray( true );

		foreach( $properties as $code )
		{
			if( array_key_exists( $code, $itemProperties )
				&& $product->getAttribute( $code, $this->type ) === null
			) {
				$new = $this->orderAttrManager->createItem();
				$new->setCode( $code );
				$new->setType( $this->type );
				$new->setValue( $itemProperties[$code] );

				$attributeList[] = $new;
			}
		}

		return $attributeList;
	}
}
