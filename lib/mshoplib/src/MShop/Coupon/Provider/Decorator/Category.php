<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


/**
 * Category decorator for coupon provider.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Category
	extends \Aimeos\MShop\Coupon\Provider\Decorator\Base
	implements \Aimeos\MShop\Coupon\Provider\Decorator\Iface
{
	private $beConfig = array(
		'category.code' => array(
			'code' => 'category.code',
			'internalcode' => 'category.code',
			'label' => 'Comma separated category codes',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->checkConfig( $this->beConfig, $attributes );
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
	 * Checks for requirements.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basic order of the customer
	 * @return boolean True if the requirements are met, false if not
	 */
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $base )
	{
		if( ( $value = $this->getConfigValue( 'category.code' ) ) !== null )
		{
			$prodIds = [];
			$catCodes = explode( ',', $value );

			foreach( $base->getProducts() as $product ) {
				$prodIds[] = $product->getProductId();
			}

			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'catalog' );

			$search = $manager->createSearch( true );
			$expr = [
				$search->compare( '==', 'catalog.code', $catCodes ),
				$search->compare( '==', 'catalog.lists.domain', 'product' ),
				$search->compare( '==', 'catalog.lists.refid', $prodIds ),
				$search->getConditions(),
			];
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, 1 );

			if( count( $manager->searchItems( $search ) ) === 0 ) {
				return false;
			}
		}

		return parent::isAvailable( $base );
	}
}
