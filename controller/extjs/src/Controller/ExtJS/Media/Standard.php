<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Media;


/**
 * ExtJs media controller for admin interfaces.
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
	 * Initializes the media controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Media' );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$idList = array();
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'media' );
		$cntl = \Aimeos\Controller\Common\Media\Factory::createController( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.id', (array) $params->items ) );
		$search->setSlice( 0, count( (array) $params->items ) );

		foreach( $manager->searchItems( $search ) as $id => $item )
		{
			$idList[$item->getDomain()][] = $id;
			$cntl->delete( $item );
		}

		$manager->deleteItems( (array) $params->items );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain . '.lists.refid', $domainIds ),
				$search->compare( '==', $domain . '.lists.domain', 'media' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain . '.lists.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}


		$this->clearCache( (array) $params->items );

		return array(
			'success' => true,
		);
	}


	/**
	 * Stores an uploaded file
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return \stdClass Object with success value
	 * @throws \Aimeos\Controller\ExtJS\Exception If an error occurs
	 */
	public function uploadItem( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'domain' ) );
		$this->setLocale( $params->site );

		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'media' );
		$typeManager = \Aimeos\MShop\Factory::createManager( $context, 'media/type' );

		$item = $manager->createItem();
		$item->setTypeId( $typeManager->findItem( 'default', array(), 'product' )->getId() );
		$item->setDomain( 'product' );
		$item->setStatus( 1 );

		$file = $this->getUploadedFile();

		\Aimeos\Controller\Common\Media\Factory::createController( $context )->add( $item, $file );
		$manager->saveItem( $item );

		return (object) $item->toArray();
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		$smd = parent::getServiceDescription();

		$smd['Media.uploadItem'] = array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "string", "name" => "domain", "optional" => false ),
				),
				"returns" => "array",
		);

		return $smd;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'media' );
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
		return 'media';
	}


	/**
	 * Returns the PHP file information of the uploaded file
	 *
	 * @return Psr\Http\Message\UploadedFileInterface Uploaded file
	 * @throws \Aimeos\Controller\ExtJS\Exception If no file upload is available
	 */
	protected function getUploadedFile()
	{
		$files = $this->getContext()->getView()->request()->getUploadedFiles();

		if( ( $file = reset( $files ) ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded' );
		}

		return $file;
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'media.languageid'} ) && $entry->{'media.languageid'} === '' ) {
			$entry->{'media.languageid'} = null;
		}

		return $entry;
	}
}
