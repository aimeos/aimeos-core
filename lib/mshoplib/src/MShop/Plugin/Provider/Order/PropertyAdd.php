<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds product properties to an order product as attributes
 *
 * Example configuration:
 * - types: ["package-length", "package-width", "package-height", "package-weight"]
 *
 * The product properties listed in the array are added to the order product as
 * order product attributes with key/value pairs like code: "package-length", value: "10".
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class PropertyAdd
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'types' => array(
			'code' => 'types',
			'internalcode' => 'types',
			'label' => 'Property type codes',
			'type' => 'list',
			'internaltype' => 'array',
			'default' => [],
			'required' => true,
		),
	);

	private $orderAttrManager;


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
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
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
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addProduct.before' );
		$p->attach( $plugin, 'setProducts.before' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( ( $types = (array) $this->getItemBase()->getConfigValue( 'types', [] ) ) === [] ) {
			return $value;
		}

		if( !is_array( $value ) )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $value );
			return $this->addAttributes( $value, $this->getProductItems( [$value->getProductId()] ), $types );
		}

		$list = [];

		foreach( $value as $orderProduct )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $orderProduct );
			$list[] = $orderProduct->getProductId();
		}

		$products = $this->getProductItems( $list );

		foreach( $value as $key => $orderProduct ) {
			$value[$key] = $this->addAttributes( $orderProduct, $products, $types );
		}

		return $value;
	}


	/**
	 * Adds the product properties as attribute items to the order product item
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct Order product containing attributes
	 * @param \Aimeos\Map $products List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys and properties
	 * @param string[] $types List of property types to add
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Modified order product item
	 */
	protected function addAttributes( \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct,
	\Aimeos\Map $products, array $types ) : \Aimeos\MShop\Order\Item\Base\Product\Iface
	{
		if( ( $product = $products->get( $orderProduct->getProductId() ) ) === null ) {
			return $orderProduct;
		}

		foreach( $types as $type )
		{
			$list = $product->getProperties( $type );

			if( !$list->isEmpty() )
			{
				if( ( $attrItem = $orderProduct->getAttributeItem( $type, 'product/property' ) ) === null ) {
					$attrItem = $this->orderAttrManager->create();
				}

				$attrItem = $attrItem->setType( 'product/property' )->setCode( $type )
					->setValue( count( $list ) > 1 ? $list->toArray() : $list->first() );

				$orderProduct = $orderProduct->setAttributeItem( $attrItem );
			}
		}

		return $orderProduct;
	}


	/**
	 * Returns the product items for the given product IDs limited by the map of properties
	 *
	 * @param string[] $productIds List of product IDs
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with IDs as keys
	 */
	protected function getProductItems( array $productIds ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$search = $manager->filter( true );
		$expr = [
			$search->compare( '==', 'product.id', array_unique( $productIds ) ),
			$search->getConditions(),
		];
		$search->setConditions( $search->and( $expr ) );

		return $manager->search( $search, ['product/property'] );
	}
}
