<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Job
 */


/**
 * Default job item implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class MAdmin_Job_Item_Default
	extends MShop_Common_Item_Abstract
	implements MAdmin_Job_Item_Interface
{
	private $_values;

	/**
	 * Initializes the job item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'job.', $values );

		$this->_values = $values;
	}


	/**
	 * Returns the status (enabled/disabled) of the job item.
	 *
	 * @return integer Returns the status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the job item.
	 *
	 * @param string $label Type label of the job item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getMethod()
	{
		return ( isset( $this->_values['method'] ) ? (string) $this->_values['method'] : '' );
	}


	/**
	 * Sets the new method for the job.
	 *
	 * @param string $method Method (object/methodname) to call
	 */
	public function setMethod( $method )
	{
		if ( $method == $this->getMethod() ) { return; }

		$this->_values['method'] = (string) $method;
		$this->setModified();
	}


	/**
	 * Returns the parameter for the job.
	 *
	 * @return array Parameter of the job
	 */
	public function getParameter()
	{
		return ( isset( $this->_values['parameter'] ) ? $this->_values['parameter'] : array() );
	}


	/**
	 * Sets the new parameter for the job.
	 *
	 * @param array $param Parameter for the job
	 */
	public function setParameter( array $param )
	{
		$this->_values['parameter'] = $param;
		$this->setModified();
	}


	/**
	 * Returns the result of the job.
	 *
	 * @return array Associative list of result key/value pairs or list thereof
	 */
	public function getResult()
	{
		return ( isset( $this->_values['result'] ) ? $this->_values['result'] : array() );
	}


	/**
	 * Sets the new result of the job.
	 *
	 * @param array $result Associative list of result key/value pairs or list thereof
	 */
	public function setResult( array $result )
	{
		$this->_values['result'] = $result;
		$this->setModified();
	}


	/**
	 * Sets the new status of the job item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['job.label'] = $this->getLabel();
		$list['job.method'] = $this->getMethod();
		$list['job.parameter'] = $this->getParameter();
		$list['job.result'] = $this->getResult();
		$list['job.status'] = $this->getStatus();

		return $list;
	}

}
