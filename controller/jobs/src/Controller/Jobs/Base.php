<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
{
	private $aimeos;
	private $context;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap main object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos )
	{
		$this->context = $context;
		$this->aimeos = $aimeos;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Controller\Jobs\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the \Aimeos\Bootstrap object.
	 *
	 * @return \Aimeos\Bootstrap \Aimeos\Bootstrap object
	 */
	protected function getAimeos()
	{
		return $this->aimeos;
	}


	/**
	 * Returns the attribute type item specified by the code.
	 *
	 * @param string $prefix Domain prefix for the manager, e.g. "media/type"
	 * @param string $domain Domain of the type item
	 * @param string $code Code of the type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface Type item
	 * @throws \Aimeos\Controller\Jobs\Exception If no item is found
	 */
	protected function getTypeItem( $prefix, $domain, $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $prefix );
		$prefix = str_replace( '/', '.', $prefix );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $prefix . '.domain', $domain ),
			$search->compare( '==', $prefix . '.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false )
		{
			$msg = sprintf( 'No type item for "%1$s/%2$s" in "%3$s" found', $domain, $code, $prefix );
			throw new \Aimeos\Controller\Jobs\Exception( $msg );
		}

		return $item;
	}
}
