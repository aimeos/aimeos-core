<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * XML delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Xml
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{
	private $num = 0;

	private $beConfig = [
		'xml.filepath' => [
			'code' => 'xml.filepath',
			'internalcode' => 'xml.filepath',
			'label' => 'Relative or absolute path and name of the XML files with strftime() placeholders',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => './order_%Y-%m-%d_%T_%%d.xml',
			'required' => true,
		],
		'xml.template' => [
			'code' => 'xml.template',
			'internalcode' => 'xml.template',
			'label' => 'Relative path of the template file name',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => 'service/provider/delivery/xml-body-standard',
			'required' => false,
		],
	];


	/**
	 * Checks the backend configuration attributes for validity
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
	 * Creates the XML files and updates the delivery status
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order instance
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order )
	{
		$baseItem = $this->getOrderBase( $order->getBaseId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->createFile( $this->createXml( [$order], [$baseItem->getId() => $baseItem] ) );

		return $order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
	 */
	public function processBatch( array $orders )
	{
		$baseItems = $this->getOrderBaseItems( $orders );
		$this->createFile( $this->createXml( $orders, $baseItems ) );

		foreach( $orders as $key => $order ) {
			$orders[$key] = $order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
		}

		return $orders;
	}


	/**
	 * Stores the content into the file
	 *
	 * @param string $content XML content
	 */
	protected function createFile( $content )
	{
		$filepath = $this->getConfigValue( 'filepath', './order_%Y-%m-%d_%T_%%d.xml' );
		$filepath = sprintf( strftime( $filepath ), $this->num++ );

		if( file_put_contents( $filepath, $content ) === false )
		{
			$msg = sprintf( 'Unable to create order XML file "%1$s"', $filepath );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}
	}


	/**
	 * Creates the XML file for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @param \Aimeos\MShop\Order\Item\Base\Iface[] $baseItems Associative list of order base items to export
	 * @return string Generated XML
	 */
	protected function createXml( array $orderItems, array $baseItems )
	{
		$view = $this->getContext()->getView();
		$template = $this->getConfigValue( 'template', 'service/provider/delivery/xml-body-standard' );

		return $view->assign( ['orderItems' => $orderItems, 'baseItems' => $baseItems] )->render( $template );
	}


	/**
	 * Returns the order base items for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items
	 * @return \Aimeos\MShop\Order\Item\Base\Iface[] Associative list of IDs as keys and order base items as values
	 */
	protected function getOrderBaseItems( array $orderItems )
	{
		$ids = [];
		$ref = ['order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];

		foreach( $orderItems as $item ) {
			$ids[$item->getBaseId()] = null;
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/base' );
		$search = $manager->createSearch()->setSlice( 0, count( $ids ) );
		$search->setConditions( $search->compare( '==', 'order.base.id', array_keys( $ids ) ) );

		return $manager->searchItems( $search, $ref );
	}
}
