<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
interface Iface extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MAdmin\Job\Item\Iface;


	/**
	 * Returns the name of the job item.
	 *
	 * @return string Label of the job item
	 */
	public function getMethod() : string;

	/**
	 * Sets the new method for the job.
	 *
	 * @param string $method Method (object/methodname) to call
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setMethod( string $method ) : \Aimeos\MAdmin\Job\Item\Iface;

	/**
	 * Returns the parameter for the job.
	 *
	 * @return array Parameter of the job
	 */
	public function getParameter() : array;

	/**
	 * Sets the new parameter for the job.
	 *
	 * @param array $param Parameter for the job
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setParameter( array $param ) : \Aimeos\MAdmin\Job\Item\Iface;

	/**
	 * Returns the result of the job.
	 *
	 * @return array Associative list of result key/value pairs or list thereof
	 */
	public function getResult() : array;

	/**
	 * Sets the new result of the job.
	 *
	 * @param array $result Associative list of result key/value pairs or list thereof
	 * @return \Aimeos\MAdmin\Job\Item\Iface Job item for chaining method calls
	 */
	public function setResult( array $result ) : \Aimeos\MAdmin\Job\Item\Iface;
}
