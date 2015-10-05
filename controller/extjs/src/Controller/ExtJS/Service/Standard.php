<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Service;


/**
 * ExtJS product controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Iface
{
	private $manager = null;


	/**
	 * Initializes the service controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Service' );
	}


	/**
	 * Creates a new service item or updates an existing one or a list thereof.
	 *
	 * @param \stdClass $params Associative array containing the service properties
	 * @return array Associative list with nodes and success value
	 */
	public function saveItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$ids = array();
		$manager = $this->getManager();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $manager->createItem();
			$item->fromArray( (array) $this->transformValues( $entry ) );
			$manager->saveItem( $item );
			$ids[] = $item->getId();
		}

		$this->clearCache( $ids );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$result = $manager->searchItems( $search );

		foreach( $result as $item ) {
			$this->checkConfig( $item );
		}

		$items = $this->toArray( $result );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Tests the configuration and throws an exception if it's invalid
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $item Service item object
	 * @throws \Aimeos\Controller\ExtJS\Exception If configuration is invalid
	 */
	protected function checkConfig( \Aimeos\MShop\Service\Item\Iface $item )
	{
		$msg = '';
		$provider = $this->manager->getProvider( $item );
		$result = $provider->checkConfigBE( $item->getConfig() );

		foreach( $result as $key => $message )
		{
			if( $message !== null ) {
				$msg .= sprintf( "- %1\$s : %2\$s\n", $key, $message );
			}
		}

		if( $msg !== '' ) {
			throw new \Aimeos\Controller\ExtJS\Exception( "Invalid configuration:\n" . $msg );
		}
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'service' );
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
		return 'service';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'service.config'} ) ) {
			$entry->{'service.config'} = (array) $entry->{'service.config'};
		}

		return $entry;
	}
}