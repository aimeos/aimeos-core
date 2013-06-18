<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Job
 */


/**
 * MAdmin job item Interface.
 *
 * @package MAdmin
 * @subpackage Job
 */
interface MAdmin_Job_Item_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the status (enabled/disabled) of the attribute item.
	 *
	 * @return integer Returns the status
	 */
	public function getStatus();

	/**
	 * Sets the new status of the attribute item.
	 *
	 * @param integer $status Status of attribute item
	 */
	public function setStatus($status);

	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel();

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 */
	public function setLabel( $label );


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getMethod();

	/**
	 * Sets the new method for the job.
	 *
	 * @param string $method Method (object/methodname) to call
	 */
	public function setMethod( $method );

	/**
	 * Returns the parameter for the job.
	 *
	 * @return array Parameter of the job
	 */
	public function getParameter();

	/**
	 * Sets the new parameter for the job.
	 *
	 * @param array $param Parameter for the job
	 */
	public function setParameter( array $param );

	/**
	 * Returns the result of the job.
	 *
	 * @return array Associative list of result key/value pairs or list thereof
	 */
	public function getResult();

	/**
	 * Sets the new result of the job.
	 *
	 * @param array $result Associative list of result key/value pairs or list thereof
	 */
	public function setResult( array $result );
}
