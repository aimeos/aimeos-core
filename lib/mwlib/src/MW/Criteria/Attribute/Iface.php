<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Attribute;


/**
 * Interface for search attribute classes.
 *
 * @package MW
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Returns the type of the attribute.
	 *
	 * Can be used in the frontend to create a speacial form for this type
	 *
	 * @return string Available types are "string", "integer", "float", "boolean", "date", "time", "datetime"
	 */
	public function getType();

	/**
	 * Returns the type internally used by the manager.
	 *
	 * @return mixed Type used by the manager
	 */
	public function getInternalType();

	/**
	 * Returns the public code for the search attribute.
	 *
	 * @return string Public code of the search attribute
	 */
	public function getCode();

	/**
	 * Returns the internal code for the search attribute.
	 *
	 * @return string Internal code of the search attribute
	 */
	public function getInternalCode();

	/**
	 * Returns the list of internal dependencies.
	 *
	 * @return array List of dependency strings
	 */
	public function getInternalDeps();

	/**
	 * Returns the human readable label for the search attribute.
	 *
	 * @return string Name of the search attribute
	 */
	public function getLabel();

	/**
	 * Returns the default value of the search attribute.
	 *
	 * @return string Default value of the search attribute
	 */
	public function getDefault();

	/**
	 * Returns true if the attribute is for public use.
	 *
	 * @return boolean True if the attribute is public, false if not
	 */
	public function isPublic();

	/**
	 * Returns true if the attribute is required.
	 *
	 * @return boolean True if the attribute is required, false if not
	 */
	public function isRequired();
}
