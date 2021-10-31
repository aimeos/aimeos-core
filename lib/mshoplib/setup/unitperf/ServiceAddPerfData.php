<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds service performance records
 */
class ServiceAddPerfData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddTypeDataUnitperf', 'LocaleAddPerfData', 'MShopSetLocale'];
	}


	/**
	 * Insert product data.
	 */
	public function up()
	{
		$this->info( 'Adding service performance data', 'v' );


		$services = [
			'delivery' => [
				'pickup' => [
					'name' => 'Pickup',
					'short' => 'Pick-up at one of our local stores',
					'image' => 'https://demo.aimeos.org/media/service/pickup.png',
					'provider' => 'Standard',
					'costs' => '0.00',
				],
				'dhl' => [
					'name' => 'DHL',
					'short' => 'Delivery within three days by DHL',
					'image' => 'https://demo.aimeos.org/media/service/dhl.png',
					'provider' => 'Standard',
					'costs' => '3.90',
				],
				'fedex' => [
					'name' => 'Fedex',
					'short' => 'Delivery within two days by Fedex',
					'image' => 'https://demo.aimeos.org/media/service/fedex.png',
					'provider' => 'Standard',
					'costs' => '6.90',
				],
				'tnt' => [
					'name' => 'TNT',
					'short' => 'Delivery within one day by TNT',
					'image' => 'https://demo.aimeos.org/media/service/tnt.png',
					'provider' => 'Standard',
					'costs' => '9.90',
				],
			],
			'payment' => [
				'invoice' => [
					'name' => 'Invoice',
					'short' => 'Pay by invoice within 14 days',
					'image' => 'https://demo.aimeos.org/media/service/payment-in-advance.png',
					'provider' => 'PostPay',
					'costs' => '0.00',
				],
				'directdebit' => [
					'name' => 'Direct debit',
					'short' => 'Payment via your bank account',
					'image' => 'https://demo.aimeos.org/media/service/sepa.png',
					'provider' => 'PostPay',
					'costs' => '0.00',
				],
				'cash' => [
					'name' => 'Cash on delivery',
					'short' => 'Pay cash on delivery of the parcel',
					'image' => 'https://demo.aimeos.org/media/service/dhl-cod.png',
					'provider' => 'PrePay',
					'costs' => '8.00',
				],
				'prepay' => [
					'name' => 'Prepayment',
					'short' => 'Pay in advance before the parcel is shipped',
					'image' => 'https://demo.aimeos.org/media/service/payment-in-advance-alternative.png',
					'provider' => 'PrePay',
					'costs' => '0.00',
				],
			],
		];

		$numServices = $this->context()->getConfig()->get( 'setup/unitperf/max-services', 100 );

		$manager = \Aimeos\MShop::create( $this->context(), 'service' );
		$listManager = \Aimeos\MShop::create( $this->context(), 'service/lists' );
		$mediaManager = \Aimeos\MShop::create( $this->context(), 'media' );
		$priceManager = \Aimeos\MShop::create( $this->context(), 'price' );
		$textManager = \Aimeos\MShop::create( $this->context(), 'text' );

		$mListItem = $listManager->create()->setType( 'default' );
		$pListItem = $listManager->create()->setType( 'default' );
		$tListItem = $listManager->create()->setType( 'default' );

		$mediaItem = $mediaManager->create()->setType( 'icon' )->setMimeType( 'image/png' );
		$priceItem = $priceManager->create()->setType( 'default' )->setCurrencyId( 'EUR' );
		$textItem = $textManager->create()->setType( 'short' );


		$manager->begin();

		foreach( $services as $type => $list )
		{
			$pos = 0;
			$serviceItem = $manager->create()->setType( $type );

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

					$manager->save( $item );
				}
			}
		}

		$manager->commit();
	}
}
