<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Abstract class for all basket HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Client_Html_Basket_Abstract
	extends Client_Html_Abstract
{
	/**
	 * Removes all cached basket parts from the cache.
	 */
	protected function _clearCached()
	{
		$session = $this->_getContext()->getSession();

		foreach( $session->get( 'arcavias/basket/cache', array() ) as $key => $value ) {
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
	protected function _getCached( $key, $default = null )
	{
		return $this->_getContext()->getSession()->get( $key, $default );
	}


	/**
	 * Adds or overwrite a cache entry for the given key and value.
	 *
	 * @param string $key Path the cache entry should be stored in
	 * @param mixed $value Value stored in the cache for the path
	 */
	protected function _setCached( $key, $value )
	{
		$session = $this->_getContext()->getSession();

		$cached = $session->get( 'arcavias/basket/cache', array() ) + array( $key => true );
		$session->set( 'arcavias/basket/cache', $cached );
		$session->set( $key, $value );
	}
}
