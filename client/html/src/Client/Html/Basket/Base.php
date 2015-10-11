<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Basket;


/**
 * Abstract class for all basket HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
{
	/**
	 * Removes all cached basket parts from the cache.
	 */
	protected function clearCached()
	{
		$session = $this->getContext()->getSession();

		foreach( $session->get( 'aimeos/basket/cache', array() ) as $key => $value ) {
			$session->set( $key, null );
		}
	}


	/**
	 * Returns the basket cache entry from the cache if available.
	 *
	 * @param string $key Path to the requested cache entry
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 *	key is found in the cache, the given default value is returned
	 */
	protected function getBasketCached( $key, $default = null )
	{
		return $this->getContext()->getSession()->get( $key, $default );
	}


	/**
	 * Adds or overwrite a cache entry for the given key and value.
	 *
	 * @param string $key Path the cache entry should be stored in
	 * @param mixed $value Value stored in the cache for the path
	 */
	protected function setBasketCached( $key, $value )
	{
		$context = $this->getContext();

		/** client/html/basket/cache/enable
		 * Enables or disables caching of the basket content
		 *
		 * For performance reasons, the content of the small baskets is cached
		 * in the session of the customer. The cache is updated each time the
		 * basket content changes either by adding, deleting or editing products.
		 *
		 * To ease development, the caching can be disabled but you shouldn't
		 * disable it in your production environment!
		 *
		 * @param boolean True to enable, false to disable basket content caching
		 * @category Developer
		 * @since 2014.11
		 */
		if( $context->getConfig()->get( 'client/html/basket/cache/enable', true ) != false )
		{
			$session = $context->getSession();

			$cached = $session->get( 'aimeos/basket/cache', array() ) + array( $key => true );
			$session->set( 'aimeos/basket/cache', $cached );
			$session->set( $key, $value );
		}
	}
}
