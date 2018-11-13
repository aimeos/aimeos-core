<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds service performance records
 */
class ServiceAddPerfData extends \Aimeos\MW\Setup\Task\Base
{
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Context\\Item\\Iface', $additional );

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Insert product data.
	 */
	public function migrate()
	{
		$this->msg( 'Adding service performance data', 0 );


		$services = [
			'delivery' => [
				'pickup' => [
					'name' => 'Pickup',
					'short' => 'Pick-up at one of our local stores',
					'image' => 'http://demo.aimeos.org/media/service/pickup.png',
					'provider' => 'Manual',
					'costs' => '0.00',
				],
				'dhl' => [
					'name' => 'DHL',
					'short' => 'Delivery within three days by DHL',
					'image' => 'http://demo.aimeos.org/media/service/dhl.png',
					'provider' => 'Manual',
					'costs' => '3.90',
				],
				'fedex' => [
					'name' => 'Fedex',
					'short' => 'Delivery within two days by Fedex',
					'image' => 'http://demo.aimeos.org/media/service/fedex.png',
					'provider' => 'Manual',
					'costs' => '6.90',
				],
				'tnt' => [
					'name' => 'TNT',
					'short' => 'Delivery within one day by TNT',
					'image' => 'http://demo.aimeos.org/media/service/tnt.png',
					'provider' => 'Manual',
					'costs' => '9.90',
				],
			],
			'payment' => [
				'invoice' => [
					'name' => 'Invoice',
					'short' => 'Pay by invoice within 14 days',
					'image' => 'http://demo.aimeos.org/media/service/payment-in-advance.png',
					'provider' => 'PostPay',
					'costs' => '0.00',
				],
				'directdebit' => [
					'name' => 'Direct debit',
					'short' => 'Payment via your bank account',
					'image' => 'http://demo.aimeos.org/media/service/sepa.png',
					'provider' => 'PostPay',
					'costs' => '0.00',
				],
				'cash' => [
					'name' => 'Cash on delivery',
					'short' => 'Pay cash on delivery of the parcel',
					'image' => 'http://demo.aimeos.org/media/service/dhl-cod.png',
					'provider' => 'PrePay',
					'costs' => '8.00',
				],
				'prepay' => [
					'name' => 'Prepayment',
					'short' => 'Pay in advance before the parcel is shipped',
					'image' => 'http://demo.aimeos.org/media/service/payment-in-advance-alternative.png',
					'provider' => 'PrePay',
					'costs' => '0.00',
				],
			],
		];

		$numServices = $this->additional->getConfig()->get( 'setup/unitperf/max-services', 100 );

		$manager = \Aimeos\MShop\Factory::createManager( $this->additional, 'service' );
		$listManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'service/lists' );
		$mediaManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'media' );
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'price' );
		$textManager = \Aimeos\MShop\Factory::createManager( $this->additional, 'text' );

		$mListItem = $listManager->createItem( 'default', 'media' )->setStatus( 1 );
		$pListItem = $listManager->createItem( 'default', 'price' )->setStatus( 1 );
		$tListItem = $listManager->createItem( 'default', 'text' )->setStatus( 1 );

		$mediaItem = $mediaManager->createItem( 'icon', 'service' )->setStatus( 1 )->setMimeType( 'image/png' );
		$priceItem = $priceManager->createItem( 'default', 'service' )->setStatus( 1 );
		$textItem = $textManager->createItem( 'short', 'service' )->setStatus( 1 );


		$manager->begin();

		foreach( $services as $type => $list )
		{
			$pos = 0;
			$serviceItem = $manager->createItem( $type, 'service' )->setStatus( 1 );

			for( $i = 0; $i < $numServices / 4; $i++ )
			{
				foreach( $list as $code => $entry )
				{
					$code = 'perf-pay-' . $code . '-' . str_pad( $i, 3, '0', STR_PAD_LEFT );

					$item = clone $serviceItem;
					$item->setLabel( $entry['name'] )
						->setProvider( $entry['provider'] )
						->setPosition( $pos++ )
						->setCode( $code )
						->setStatus( 1 );

					$media = clone $mediaItem;
					$media->setLabel( $entry['name'] )->setPreview( $entry['image'] )->setUrl( $entry['image'] );
					$item->addListItem( 'media', clone $mListItem, $media );

					$price = clone $priceItem;
					$price->setLabel( $entry['name'] )->setCosts( $entry['costs'] );
					$item->addListItem( 'price', clone $pListItem, $price );

					$text = clone $textItem;
					$text->setLabel( $entry['name'] )->setContent( $entry['short'] );
					$item->addListItem( 'text', clone $tListItem, $text );

					$manager->saveItem( $item );
				}
			}
		}

		$manager->commit();


		$this->status( 'done' );
	}
}
