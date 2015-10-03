<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS cache controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Admin_Cache_Default
	extends Controller_ExtJS_Base
	implements Controller_ExtJS_Common_Iface
{
	private $manager = null;


	/**
	 * Initializes the cache controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context, 'Admin_Cache' );
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		$list = parent::getServiceDescription();

		$list['Admin_Cache.flush'] = array(
			"parameters" => array(
				array( "type" => "string", "name" => "site", "optional" => false ),
			),
			"returns" => "array",
		);

		return $list;
	}


	/**
	 * Executes tasks after processing the items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function flush( stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$this->getContext()->getCache()->flush();

		return array(
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MAdmin_Cache_Manager_Factory::createManager( $this->getContext() );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MAdmin search key prefix
	 */
	protected function getPrefix()
	{
		return 'cache';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'cache.expire'} ) ) {
			$entry->{'cache.expire'} = str_replace( 'T', ' ', $entry->{'cache.expire'} );
		}

		return $entry;
	}
}
