<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Service;


/**
 * Frontend service test factory.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Factorylocal
	extends \Aimeos\Controller\Frontend\Common\Factory\Base
{
	/**
	 * @param string $name
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null, $domainToTest = 'service' )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'controller/frontend/service/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false ) {
			throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
		}

		$iface = '\\Aimeos\\Controller\\Frontend\\Service\\Iface';
		$classname = '\\Aimeos\\Controller\\Frontend\\Service\\' . $name;

		$manager = self::createControllerBase( $context, $classname, $iface );
		return self::addControllerDecorators( $context, $manager, $domainToTest );
	}
}
