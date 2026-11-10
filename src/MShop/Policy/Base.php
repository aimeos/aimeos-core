<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Policy
 */


namespace Aimeos\MShop\Policy;


/**
 * Base class for authorization policies checking the user groups configured for the resources
 *
 * @package MShop
 * @subpackage Policy
 */
abstract class Base implements \Aimeos\MShop\PolicyIface
{
	private string $prefix;


	/**
	 * Initializes the policy
	 *
	 * @param string $prefix Configuration key prefix of the resources, e.g. "admin/jqadm/resource"
	 */
	public function __construct( string $prefix )
	{
		$this->prefix = rtrim( $prefix, '/' );
	}


	/**
	 * Checks if the configuration value is a list of user group names or a single group name
	 *
	 * Empty lists are lists of user groups which allow access for super users only.
	 * Single group names as strings have been accepted by the interfaces before but
	 * empty strings aren't group names.
	 *
	 * @param mixed $value Configuration value
	 * @return bool TRUE if the value contains user group names, FALSE if not
	 */
	public static function isGroups( $value ) : bool
	{
		if( is_string( $value ) ) {
			return $value !== '';
		}

		return is_array( $value ) && array_filter( $value, 'is_array' ) === [];
	}


	/**
	 * Checks if the configuration value is a resource
	 *
	 * Resources contain only actions, user group lists or sub-resources, so they are
	 * never empty because empty lists are lists of user groups. Actions and user group
	 * lists can be single group names as strings (e.g. "groups" => "admin") but lists
	 * of group names (e.g. ["admin", "editor"]) have no names as keys and aren't resources.
	 *
	 * @param mixed $value Configuration value
	 * @return bool TRUE if the value is a resource, FALSE if not
	 * @see isGroups()
	 */
	public static function isResource( $value ) : bool
	{
		if( !is_array( $value ) || $value === [] ) {
			return false;
		}

		foreach( $value as $key => $entry )
		{
			if( !is_array( $entry ) && !( is_string( $key ) && static::isGroups( $entry ) ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if the current user is member of one of the user groups
	 *
	 * The user groups are checked by the "access" view helper of the host application.
	 * The helpers grant access for an empty group list to super users only, so the list
	 * of allowed groups can be empty to restrict access to super users.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the view of the current user
	 * @param array $groups List of user group names, empty for super users only
	 * @return bool TRUE if the user is member of one of the groups or a super user, FALSE if not
	 * @throws \Aimeos\MShop\Exception If the context contains no view for checking the user groups
	 */
	protected static function member( \Aimeos\MShop\ContextIface $context, array $groups ) : bool
	{
		return $context->view()->access( $groups ) === true;
	}


	/**
	 * Returns the configuration key for the resource
	 *
	 * @param string $resource Resource name, e.g. "product" or "order/address", empty for the root resource
	 * @return string Configuration key, e.g. "admin/jqadm/resource/order/address"
	 */
	protected function key( string $resource ) : string
	{
		return $resource !== '' ? $this->prefix . '/' . $resource : $this->prefix;
	}
}
