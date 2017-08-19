<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Supplier address decorator for service providers
 *
 * @package MShop
 * @subpackage Service
 */
class Supplier
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $feConfig = array(
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'supplier.code',
			'label' => 'Pick-up address',
			'type' => 'list',
			'internaltype' => 'array',
			'default' => [],
			'required' => true
		),
	);


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider or decorator
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( \Aimeos\MShop\Service\Provider\Iface $provider,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Service\Item\Iface $serviceItem )
	{
		parent::__construct( $provider, $context, $serviceItem );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'supplier' );
		$addrManager = \Aimeos\MShop\Factory::createManager( $context, 'supplier/address' );

		$search = $manager->createSearch( true );
		$search->setSortations( [$search->sort( '+', 'supplier.label' )] );

		foreach( $manager->searchItems( $search, ['supplier/address'] ) as $item )
		{
			$addresses = $item->getAddressItems();

			if( ( $addr = reset( $addresses ) ) === false ) {
				$addr = $addrManager->createItem();
			}

			$this->feConfig['supplier.code']['default'][$item->getCode()] = preg_replace( "/\n+/m", "\n", sprintf(

			/// Supplier address format with label (%1$s), company (%2$s),
			/// address part one (%3$s, e.g street), address part two (%4$s, e.g house number), address part three (%5$s, e.g additional information),
			/// postal/zip code (%6$s), city (%7$s), state (%8$s), country ID (%9$s),
			/// e-mail (%10$s), phone (%11$s), facsimile/telefax (%12$s), web site (%13$s)
				$context->getI18n()->dt( 'mshop', '%1$s
%2$s
%3$s %4$s
%5$s
%6$s %7$s
%8$s %9$s
%10$s
%11$s
%12$s
%13$s
' 				),
				$item->getLabel(),
				$addr->getCompany(),
				$addr->getAddress1(),
				$addr->getAddress2(),
				$addr->getAddress3(),
				$addr->getPostal(),
				$addr->getCity(),
				$addr->getState(),
				$addr->getCountryId(),
				$addr->getEmail(),
				$addr->getTelephone(),
				$addr->getTelefax(),
				$addr->getWebsite()
			) );
		}
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes )
	{
		$result = $this->getProvider()->checkConfigFE( $attributes );

		return array_merge( $result, $this->checkConfig( $this->feConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$feconfig = $this->feConfig;

		try
		{
			$service = $basket->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY );

			if( ( $value = $service->getAttribute( 'supplier.code', 'delivery' ) ) != ''
				&& isset( $feconfig['supplier.code']['default'][$value] )
			) {
				// move to first position so it's selected
				$address = $feconfig['supplier.code']['default'][$value];
				unset( $feconfig['supplier.code']['default'][$value] );
				$feconfig['supplier.code']['default'] = array( $value => $address ) + $feconfig['supplier.code']['default'];
			}
		}
		catch( \Aimeos\MShop\Order\Exception $e ) {} // If service isn't available

		return array_merge( $this->getProvider()->getConfigFE( $basket ), $this->getConfigItems( $feconfig ) );
	}
}
