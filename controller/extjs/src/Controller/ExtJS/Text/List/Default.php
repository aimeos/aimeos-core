<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS text list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Text_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the text list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Text_List' );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site' ) );
		$this->_setLocale( $params->site );

		$totalList = 0;
		$search = $this->_initCriteria( $this->_getManager()->createSearch(), $params );
		$result = $this->_getManager()->searchItems( $search, array(), $totalList );

		$idLists = array();
		$listItems = array();

		foreach( $result as $item )
		{
			if( ( $domain = $item->getDomain() ) != '' ) {
				$idLists[ $domain ][] = $item->getRefId();
			}
			$listItems[] = (object) $item->toArray();
		}

		return array(
			'items' => $listItems,
			'total' => $totalList,
			'graph' => $this->_getDomainItems( $idLists ),
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _getManager()
	{
		if( $this->_manager === null ) {
			$this->_manager = MShop_Factory::createManager( $this->_getContext(), 'text/list' );
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
		return 'text.list';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function _transformValues( stdClass $entry )
	{
		if( isset( $entry->{'text.list.datestart'} ) && $entry->{'text.list.datestart'} != '' ) {
			$entry->{'text.list.datestart'} = str_replace( 'T', ' ', $entry->{'text.list.datestart'} );
		} else {
			$entry->{'text.list.datestart'} = null;
		}

		if( isset( $entry->{'text.list.dateend'} ) && $entry->{'text.list.dateend'} != '' ) {
			$entry->{'text.list.dateend'} = str_replace( 'T', ' ', $entry->{'text.list.dateend'} );
		} else {
			$entry->{'text.list.dateend'} = null;
		}

		if( isset( $entry->{'text.list.config'} ) ) {
			$entry->{'text.list.config'} = (array) $entry->{'text.list.config'};
		}

		return $entry;
	}
}
