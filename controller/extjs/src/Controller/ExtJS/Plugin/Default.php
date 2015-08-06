<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS plugin controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Plugin_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the plugin controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Plugin' );
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'plugin' );
		}

		return $this->_manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function _getPrefix()
	{
		return 'plugin';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'plugin.config'} ) ) {
			$entry->{'plugin.config'} = (array) $entry->{'plugin.config'};
		}

		return $entry;
	}
}