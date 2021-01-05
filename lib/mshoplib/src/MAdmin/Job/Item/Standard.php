<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Item;


/**
 * Default job item implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MAdmin\Job\Item\Iface
{
	/**
	 * Initializes the job item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'job.', $values );
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getLabel() : string
	{
		return $this->get( 'job.label', '' );
	}


	/**
	 * Sets the new label of the job item.
	 *
	 * @param string|null $label Type label of the job item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MAdmin\Job\Item\Iface
	{
		return $this->set( 'job.label', (string) $label );
	}


	/**
	 * Returns the generated file path of the job.
	 *
	 * @return string Relative filesystem path to the generated file
	 */
	public function getPath() : string
	{
		return $this->get( 'job.path', '' );
	}


	/**
	 * Sets the new generated file path of the job.
	 *
	 * @param string|null $path Relative filesystem path to the generated file
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setPath( ?string $path ) : \Aimeos\MAdmin\Job\Item\Iface
	{
		return $this->set( 'job.path', (string) $path );
	}


	/**
	 * Returns the status (enabled/disabled) of the job item.
	 *
	 * @return int Returns the status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( 'job.status', 1 );
	}


	/**
	 * Sets the new status of the job item.
	 *
	 * @param int $status Status of the item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'job.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'job';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'job.path': $item = $item->setPath( (string) $value ); break;
				case 'job.label': $item = $item->setLabel( (string) $value ); break;
				case 'job.status': $item = $item->setStatus( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['job.path'] = $this->getPath();
		$list['job.label'] = $this->getLabel();
		$list['job.status'] = $this->getStatus();

		return $list;
	}

}
