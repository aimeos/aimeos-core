<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Common methods for all manager objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class MW_Common_Manager_Abstract
{
	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param array $attributes List of search attribute objects implementing
	 * 	MW_Common_Criteria_Attribute_Interface or associative arrays with 'code'
	 * 	and 'internaltype' keys
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function _getSearchTypes( array $attributes )
	{
		$types = array();
		$iface = 'MW_Common_Criteria_Attribute_Interface';

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$types[ $item->getCode() ] = $item->getInternalType();
			} else if( isset( $item['code'] ) ) {
				$types[ $item['code'] ] = $item['internaltype'];
			} else {
				throw new MW_Common_Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param array $attributes List of search attribute objects implementing
	 * 	MW_Common_Criteria_Attribute_Interface or associative arrays with 'code'
	 * 	and 'internalcode' keys
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function _getSearchTranslations( array $attributes )
	{
		$translations = array();
		$iface = 'MW_Common_Criteria_Attribute_Interface';

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$translations[ $item->getCode() ] = $item->getInternalCode();
			} else if( isset( $item['code'] ) ) {
				$translations[ $item['code'] ] = $item['internalcode'];
			} else {
				throw new MW_Common_Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $translations;
	}
}
