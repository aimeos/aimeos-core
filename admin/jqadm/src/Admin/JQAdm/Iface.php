<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm;


/**
 * Common interface for all JQAdm client classes.
 *
 * @package Admin
 * @subpackage JQAdm
 */
interface Iface
{
	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null );

	/**
	 * Returns the view object that will generate the admin output.
	 *
	 * @return \Aimeos\MW\View\Iface $view The view object which generates the admin output
	 */
	public function getView();

	/**
	 * Sets the view object that will generate the admin output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the admin output
	 * @return \Aimeos\Admin\Html\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view );

	/**
	 * Copies a resource
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function copy();

	/**
	 * Creates a new resource
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function create();

	/**
	 * Deletes a resource
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function delete();

	/**
	 * Returns a single resource
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function get();

	/**
	 * Saves the data
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function save();

	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string admin output to display
	 */
	public function search();
}
