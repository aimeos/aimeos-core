<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $values;

	/**
	 * Initializes the job item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'job.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the status (enabled/disabled) of the job item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['job.status'] ) ) {
			return (int) $this->values['job.status'];
		}

		return 0;
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getLabel()
	{
		if( isset( $this->values['job.label'] ) ) {
			return (string) $this->values['job.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the job item.
	 *
	 * @param string $label Type label of the job item
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['job.label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getMethod()
	{
		if( isset( $this->values['job.method'] ) ) {
			return (string) $this->values['job.method'];
		}

		return '';
	}


	/**
	 * Sets the new method for the job.
	 *
	 * @param string $method Method (object/methodname) to call
	 */
	public function setMethod( $method )
	{
		if( $method == $this->getMethod() ) { return; }

		$this->values['job.method'] = (string) $method;
		$this->setModified();
	}


	/**
	 * Returns the parameter for the job.
	 *
	 * @return array Parameter of the job
	 */
	public function getParameter()
	{
		if( isset( $this->values['job.parameter'] ) ) {
			return (array) $this->values['job.parameter'];
		}

		return [];
	}


	/**
	 * Sets the new parameter for the job.
	 *
	 * @param array $param Parameter for the job
	 */
	public function setParameter( array $param )
	{
		$this->values['job.parameter'] = $param;
		$this->setModified();
	}


	/**
	 * Returns the result of the job.
	 *
	 * @return array Associative list of result key/value pairs or list thereof
	 */
	public function getResult()
	{
		if( isset( $this->values['job.result'] ) ) {
			return (array) $this->values['job.result'];
		}

		return [];
	}


	/**
	 * Sets the new result of the job.
	 *
	 * @param array $result Associative list of result key/value pairs or list thereof
	 */
	public function setResult( array $result )
	{
		$this->values['job.result'] = $result;
		$this->setModified();
	}


	/**
	 * Sets the new status of the job item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['job.status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'job';
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'job.label': $this->setLabel( $value ); break;
				case 'job.method': $this->setMethod( $value ); break;
				case 'job.parameter': $this->setParameter( $value ); break;
				case 'job.result': $this->setResult( $value ); break;
				case 'job.status': $this->setStatus( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['job.label'] = $this->getLabel();
		$list['job.method'] = $this->getMethod();
		$list['job.parameter'] = $this->getParameter();
		$list['job.result'] = $this->getResult();
		$list['job.status'] = $this->getStatus();

		return $list;
	}

}
