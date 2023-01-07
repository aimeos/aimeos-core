<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;

use \Aimeos\MShop\Locale\Manager\Base as Locale;


/**
 * Site trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait Site
{
	private static $siteInactive = [];


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Returns a filter object.
	 *
	 * @return \Aimeos\Base\Criteria\Iface Filter object
	 */
	abstract public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface;


	/**
	 * Returns the site expression for the given name
	 *
	 * @param string $name Name of the site condition
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return \Aimeos\Base\Criteria\Expression\Iface Site search condition
	 * @since 2022.04
	 */
	protected function siteCondition( string $name, int $sitelevel ) : \Aimeos\Base\Criteria\Expression\Iface
	{
		$sites = $this->context()->locale()->getSites();
		$current = $sites[Locale::SITE_ONE] ?? null;
		$values = [''];

		if( isset( $sites[Locale::SITE_PATH] ) && $sitelevel & Locale::SITE_PATH ) {
			$values = array_merge( $values, $sites[Locale::SITE_PATH] );
		} elseif( $current ) {
			$values[] = $current;
		}

		$filter = $this->filter();
		$cond = [$filter->compare( '==', $name, $values )];

		if( isset( $sites[Locale::SITE_SUBTREE] ) && $sitelevel & Locale::SITE_SUBTREE ) {
			$cond[] = $filter->compare( '=~', $name, $sites[Locale::SITE_SUBTREE] );
		}

		if( $current && !( $inactive = $this->siteInactive( $current ) )->isEmpty() )
		{
			return $filter->and( [
				$filter->is( $name, '!=', $inactive ),
				$filter->or( $cond )
			] );
		}

		return $filter->or( $cond );
	}


	/**
	 * Returns the site IDs that are inactive
	 *
	 * @param string $current Current site ID
	 * @return \Aimeos\Map List of inactive site IDs
	 */
	protected function siteInactive( string $current ) : \Aimeos\Map
	{
		// Required for fetching customer item below
		if( !strncmp( current( $this->getResourceType( false ) ), 'customer', 8 ) ) {
			return map();
		}

		if( !isset( self::$siteInactive[$current] ) )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'locale/site' );
			$search = $manager->filter()->add( 'locale.site.siteid', '=~', $current )->add( 'locale.site.status', '<', 1 );
			$sites = $manager->search( $search )->getSiteId();

			if( ( $userId = $this->context()->user() ) )
			{
				$manager = \Aimeos\MShop::create( $this->context(), 'customer' );
				$custItems = $manager->search( $manager->filter()->add( ['customer.id' => $userId] ) );

				if( $siteId = $custItems->getSiteId()->first() )
				{
					$sites = $sites->filter( function( $item ) use ( $siteId ) {
						return strncmp( $item, $siteId, strlen( $siteId ) );
					} );
				}
			}

			self::$siteInactive[$current] = $sites;
		}

		return self::$siteInactive[$current];
	}


	/**
	 * Returns the site ID that should be used based on the site level
	 *
	 * @param string $siteId Site ID to check
	 * @param int $sitelevel Site level to check against
	 * @return string Site ID that should be use based on the site level
	 * @since 2022.04
	 */
	protected function siteId( string $siteId, int $sitelevel ) : string
	{
		$sites = $this->context()->locale()->getSites();

		if( ( $sitelevel & Locale::SITE_ONE ) && isset( $sites[Locale::SITE_ONE] )
			&& $siteId === $sites[Locale::SITE_ONE]
		) {
			return $siteId;
		}

		if( ( $sitelevel & Locale::SITE_PATH ) && isset( $sites[Locale::SITE_PATH] )
			&& in_array( $siteId, $sites[Locale::SITE_PATH] )
		) {
			return $siteId;
		}

		if( ( $sitelevel & Locale::SITE_SUBTREE ) && isset( $sites[Locale::SITE_SUBTREE] )
			&& !strncmp( $sites[Locale::SITE_SUBTREE], $siteId, strlen( $sites[Locale::SITE_SUBTREE] ) )
		) {
			return $siteId;
		}

		return $this->context()->locale()->getSiteId();
	}


	/**
	 * Returns the site expression for the given name
	 *
	 * @param string $name SQL name for the site condition
	 * @param int $sitelevel Site level constant from \Aimeos\MShop\Locale\Manager\Base
	 * @return string Site search condition
	 * @since 2022.04
	 */
	protected function siteString( string $name, int $sitelevel ) : string
	{
		$translation = ['marker' => $name];
		$types = ['marker' => \Aimeos\Base\DB\Statement\Base::PARAM_STR];

		return $this->siteCondition( 'marker', $sitelevel )->toSource( $types, $translation );
	}
}
