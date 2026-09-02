<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * XML delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class Xml
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{
	private array $beConfig = [
		'xml.backupdir' => [
			'code' => 'xml.backupdir',
			'internalcode' => 'xml.backupdir',
			'label' => 'Relative backup path and file name in the import filesystem (with date() placeholders)',
			'default' => '',
			'required' => false,
		],
		'xml.exportpath' => [
			'code' => 'xml.exportpath',
			'internalcode' => 'xml.exportpath',
			'label' => 'Relative path and name of the XML files in the export filesystem (with date() placeholders)',
			'default' => 'order_%Y-%m-%d_%H:%i:%s_%v.xml',
			'required' => true,
		],
		'xml.template' => [
			'code' => 'xml.template',
			'internalcode' => 'xml.template',
			'label' => 'Relative path of the template file name',
			'default' => 'service/provider/delivery/xml-body',
			'required' => false,
		],
		'xml.updatedir' => [
			'code' => 'xml.updatedir',
			'internalcode' => 'xml.updatedir',
			'label' => 'Relative path and name of the order update XML files in the import filesystem',
			'default' => '',
			'required' => false,
		],
	];


	/**
	 * Checks the backend configuration attributes for validity
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\Map Updated order items
	 */
	public function push( iterable $orders ) : \Aimeos\Map
	{
		$this->createFile( $this->createXml( $orders ) );

		foreach( $orders as $key => $order ) {
			$orders[$key] = $order->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
		}

		return map( $orders );
	}


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return bool True if the update was successful, false if async updates are not supported
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateAsync() : bool
	{
		$context = $this->context();
		$fs = $context->fs( 'fs-import' );
		$logger = $context->logger();
		$location = (string) $this->require( 'xml.updatedir' );

		if( !$fs->has( $location ) )
		{
			$msg = sprintf( 'File or directory "%1$s" doesn\'t exist', $location );
			throw new \Aimeos\Controller\Jobs\Exception( $msg );
		}

		$msg = sprintf( 'Started order status import from "%1$s"', $location );
		$logger->info( $msg, 'core/service' );

		$files = [];

		if( $fs instanceof \Aimeos\Base\Filesystem\DirIface && $fs->isDir( $location ) )
		{
			foreach( $fs->scan( $location ) as $entry )
			{
				$filename = (string) $entry;

				if( str_starts_with( $filename, 'order' ) && str_ends_with( $filename, '.xml' ) ) {
					$files[] = rtrim( $location, '/' ) . '/' . $filename;
				}
			}
		}
		else
		{
			$files[] = $location;
		}

		sort( $files );

		foreach( $files as $filepath ) {
			$this->importFile( $filepath );
		}

		$msg = sprintf( 'Finished order status import from "%1$s"', $location );
		$logger->info( $msg, 'core/service' );

		return true;
	}


	/**
	 * Stores the content into the file
	 *
	 * @param string $content XML content
	 * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
	 */
	protected function createFile( string $content ) : \Aimeos\MShop\Service\Provider\Delivery\Iface
	{
		$filepath = (string) $this->getConfigValue( 'xml.exportpath', 'order_%Y-%m-%d_%H:%i:%s_%v.xml' );
		$filepath = \Aimeos\Base\Str::strtime( $filepath );

		try {
			$this->context()->fs( 'fs-export' )->write( $filepath, $content );
		} catch( \Exception $e )
		{
			$msg = sprintf( 'Unable to create order XML file "%1$s"', $filepath );
			throw new \Aimeos\MShop\Service\Exception( $msg, 0, $e );
		}

		return $this;
	}


	/**
	 * Creates the XML file for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @return string Generated XML
	 */
	protected function createXml( iterable $orderItems ) : string
	{
		$view = $this->context()->view();
		$template = $this->getConfigValue( 'xml.template', 'service/provider/delivery/xml-body' );

		return $view->assign( ['orderItems' => $orderItems] )->render( $template );
	}


	/**
	 * Imports all orders from the given XML file name
	 *
	 * @param string $filename Relative path to the XML file in the import filesystem
	 * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
	 */
	protected function importFile( string $filename ) : \Aimeos\MShop\Service\Provider\Delivery\Iface
	{
		$nodes = [];
		$xml = new \XMLReader();
		$context = $this->context();
		$fs = $context->fs( 'fs-import' );
		$logger = $context->logger();
		$backup = \Aimeos\Base\Str::strtime( (string) $this->getConfigValue( 'xml.backupdir', '' ) );
		$tmpfile = $fs->readf( $filename );

		try
		{
			if( $xml->open( $tmpfile, null, LIBXML_COMPACT | LIBXML_PARSEHUGE ) === false )
			{
				$msg = $context->translate( 'mshop', 'No XML file "%1$s" found' );
				throw new \Aimeos\Controller\Jobs\Exception( sprintf( $msg, $filename ) );
			}

			$msg = sprintf( 'Started order status import from file "%1$s"', $filename );
			$logger->info( $msg, 'core/service' );

			while( $xml->read() === true )
			{
				if( $xml->depth === 1 && $xml->nodeType === \XMLReader::ELEMENT && $xml->name === 'orderitem' )
				{
					if( ( $dom = $xml->expand() ) === false )
					{
						$msg = sprintf( 'Expanding "%1$s" node failed', 'orderitem' );
						throw new \Aimeos\Controller\Jobs\Exception( $msg );
					}

					if( ( $attr = $dom->attributes->getNamedItem( 'ref' ) ) !== null ) {
						$nodes[$attr->nodeValue] = $dom;
					}
				}
			}

			// @phpstan-ignore argument.type
			$this->importNodes( $nodes );
		}
		finally
		{
			$xml->close();
			@unlink( $tmpfile );
		}

		$msg = sprintf( 'Finished order status import from file "%1$s"', $filename );
		$logger->info( $msg, 'core/service' );

		if( $backup !== '' )
		{
			try {
				$fs->move( $filename, $backup );
			} catch( \Exception $e ) {
				$msg = sprintf( 'Unable to move imported file "%1$s" to "%2$s"', $filename, $backup );
				throw new \Aimeos\Controller\Jobs\Exception( $msg, 0, $e );
			}
		}

		return $this;
	}


	/**
	 * Imports the orders from the given XML nodes
	 *
	 * @param array<string, \DOMNode> $nodes List of order DOM nodes
	 * @return \Aimeos\MShop\Service\Provider\Delivery\Iface Same object for fluent interface
	 */
	protected function importNodes( array $nodes ) : \Aimeos\MShop\Service\Provider\Delivery\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'order' );
		$search = $manager->filter()->slice( 0, count( $nodes ) );
		$search->setConditions( $search->compare( '==', 'order.id', array_keys( $nodes ) ) );
		$items = $manager->search( $search );

		foreach( $nodes as $node )
		{
			$list = [];

			foreach( $node->childNodes as $childNode ) {
				$list[$childNode->nodeName] = $childNode->nodeValue;
			}

			// @phpstan-ignore argument.type
			if( ( $attr = $node->attributes->getNamedItem( 'ref' ) ) !== null
				&& ( $item = $items->get( (string) $attr->nodeValue ) ) !== null
			) {
				$item->fromArray( $list, true );
			}
		}

		// @phpstan-ignore argument.type
		$manager->save( $items->toArray() );
		return $this;
	}
}
