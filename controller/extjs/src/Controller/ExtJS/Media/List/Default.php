<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS media list controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Media_List_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $manager = null;


	/**
	 * Initializes the media list controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Media_List' );
	}


	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties, total number of items and success property
	 */
	public function searchItems( stdClass $params )
	{
		$this->checkParams( $params, array( 'site' ) );
		$this->setLocale( $params->site );

		$totalList = 0;
		$search = $this->initCriteria( $this->getManager()->createSearch(), $params );
		$result = $this->getManager()->searchItems( $search, array(), $totalList );

		$idLists = array();
		$listItems = array();

		foreach( $result as $item )
		{
			if( ( $domain = $item->getDomain() ) != '' ) {
				$idLists[$domain][] = $item->getRefId();
			}
			$listItems[] = (object) $item->toArray();
		}

		return array(
			'items' => $listItems,
			'total' => $totalList,
			'graph' => $this->getDomainItems( $idLists ),
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = MShop_Factory::createManager( $this->getContext(), 'media/list' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'media.list';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param stdClass $entry Entry object from ExtJS
	 * @return stdClass Modified object
	 */
	protected function transformValues( stdClass $entry )
	{
		if( isset( $entry->{'media.list.datestart'} ) && $entry->{'media.list.datestart'} != '' ) {
			$entry->{'media.list.datestart'} = str_replace( 'T', ' ', $entry->{'media.list.datestart'} );
		} else {
			$entry->{'media.list.datestart'} = null;
		}

		if( isset( $entry->{'media.list.dateend'} ) && $entry->{'media.list.dateend'} != '' ) {
			$entry->{'media.list.dateend'} = str_replace( 'T', ' ', $entry->{'media.list.dateend'} );
		} else {
			$entry->{'media.list.dateend'} = null;
		}

		if( isset( $entry->{'media.list.config'} ) ) {
			$entry->{'media.list.config'} = (array) $entry->{'media.list.config'};
		}

		return $entry;
	}
}
