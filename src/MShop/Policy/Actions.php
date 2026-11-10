<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Policy
 */


namespace Aimeos\MShop\Policy;


/**
 * Authorization policy based on the user groups configured per resource and action
 *
 * The user groups are configured in "<prefix>/<resource>/<action>". Sub-resources share
 * the configuration of their parent resource, e.g. "<prefix>/order/address/<action>",
 * so action names must not be used as resource names and vice versa. Empty lists are
 * actions allowed for super users only and never sub-resources, so sub-resources must
 * contain at least one action and can't be added as empty placeholders.
 *
 * @package MShop
 * @subpackage Policy
 */
class Actions extends Base
{
	/**
	 * Checks if the current user is allowed to execute the action for the resource
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address"
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @return bool|null TRUE if allowed, FALSE if not and NULL if the resource isn't configured
	 */
	public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool
	{
		$key = $this->key( $resource );
		$config = $context->config()->get( $key );

		// Resources are only configured if they contain at least one action with a list of
		// user groups, resources containing only sub-resources have no rules on their own.
		if( !static::isResource( $config ) || !map( $config )->some( fn( $value ) => static::isGroups( $value ) ) ) {
			return null;
		}

		// Actions of configured resources without user groups are allowed for super users only.
		// The groups are read by their own key and not from the resource array because the
		// config merges changed lists into the default ones instead of replacing them.
		return static::member( $context, (array) $context->config()->get( $key . '/' . $action, [] ) );
	}
}
