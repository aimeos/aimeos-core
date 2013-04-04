<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Common
 * @version $Id: Abstract.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MAdmin
 * @subpackage Common
 */
abstract class MAdmin_Common_Manager_Abstract extends MShop_Common_Manager_Abstract
{
	/**
	 * Returns a new manager the given extension name
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions
	 */
	protected function _getSubManager( $domain, $manager, $name, $subdomain = '' )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$config = $this->_getContext()->getConfig();


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new MAdmin_Exception( sprintf('Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$name = $config->get( 'classes/' . $domain . '/manager/' . $manager . '/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Invalid characters in manager name "%1$s".', $name ) );
		}

		$domainname = ucfirst( $domain );
		$subnames = $this->_createSubNames( $manager );

		$classname = 'MAdmin_'. $domainname . '_Manager_' . $subnames . '_' . $name;
		$interface = 'MAdmin_'. $domainname . '_Manager_' . $subnames . '_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MAdmin_Exception( sprintf('Class "%1$s" not available', $classname ) );
		}

		$subManager = new $classname( $this->_getContext() );

		if( ( $subManager instanceof $interface ) === false ) {
			throw new MAdmin_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $subManager;
	}
}
