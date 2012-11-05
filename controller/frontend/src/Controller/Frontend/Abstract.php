<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Abstract.php 896 2012-07-04 12:25:26Z nsendetzky $
 */


/**
 * Common methods for frontend controller classes.
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Controller_Frontend_Abstract
{
	private $_context = null;
	private $_domainMangers = array();


	/**
	 * Common initialization for controller classes.
	 *
	 * @param MShop_Context_Item_Interface $context Common MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop context object implementing MShop_Context_Item_Interface
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the manager for the given domain and sub-domains.
	 *
	 * @param string $domain String of domain and sub-domains, e.g. "product" or "order/base/service"
	 * @throws Controller_Frontend_Exception If domain string is invalid or no manager can be instantiated
	 */
	protected function _getDomainManager( $domain )
	{
		$domain = strtolower( trim( $domain, "/ \n\t\r\0\x0B" ) );

		if( strlen( $domain ) === 0 ) {
			throw new Controller_Frontend_Exception( 'An empty domain is invalid' );
		}

		if( !isset( $this->_domainManagers[$domain] ) )
		{
			$parts = explode( '/', $domain );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new Controller_Frontend_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
				}
			}

			if( ( $domainname = array_shift( $parts ) ) === null ) {
				throw new Controller_Frontend_Exception( 'An empty domain is invalid' );
			}


			if( !isset( $this->_domainManagers[$domainname] ) )
			{
				$iface = 'MShop_Common_Manager_Interface';
				$factory = 'MShop_' . ucwords( $domainname ) . '_Manager_Factory';
				$manager = call_user_func_array( $factory . '::createManager', array( $this->_getContext() ) );

				if( !( $manager instanceof $iface ) ) {
					throw new Controller_Frontend_Exception( sprintf( 'No factory "%1$s" found', $factory ) );
				}

				$this->_domainManagers[$domainname] = $manager;
			}


			foreach( $parts as $part )
			{
				$tmpname = $domainname .  '/' . $part;

				if( !isset( $this->_domainManagers[$tmpname] ) ) {
					$this->_domainManagers[$tmpname] = $this->_domainManagers[$domainname]->getSubManager( $part );
				}

				$domainname = $tmpname;
			}
		}

		return $this->_domainManagers[$domain];
	}
}
