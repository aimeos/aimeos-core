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
 * MAdmin job item Interface.
 *
 * @package MAdmin
 * @subpackage Job
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
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
	 * @return void
	 */
	public function setStatus( $status );

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
	 * @return void
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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setResult( array $result );
}
