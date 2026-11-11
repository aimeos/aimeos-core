<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Policy
 */


namespace Aimeos\MShop\Policy;


/**
 * Authorization policy based on the user groups configured per resource
 *
 * The user groups are configured in "<prefix>/<resource>/groups" and allow all actions.
 * Actions can have their own user groups in "<prefix>/<resource>/<action>" which are used
 * instead, e.g. for restricting file imports. Values which aren't user groups (e.g. empty
 * strings or nested arrays) are handled like resources without configured user groups,
 * so only super users are allowed by default.
 *
 * @package MShop
 * @subpackage Policy
 */
class Groups extends Base
{
	/**
	 * Checks if the current user is allowed to execute the action for the resource
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address", empty for the root resource
	 * @param string $action Action name, e.g. "get", "save", "delete" or "import"
	 * @return bool|null TRUE if allowed, FALSE if not and NULL if no user groups are configured for the resource
	 */
	public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool
	{
		$key = $this->key( $resource );
		$groups = $context->config()->get( $key . '/' . $action );

		// sub-resources and other values which aren't user groups don't overwrite the resource groups
		if( !static::isGroups( $groups ) ) {
			$groups = $context->config()->get( $key . '/groups' );
		}

		return static::isGroups( $groups ) ? static::member( $context, (array) $groups ) : null;
	}
}
