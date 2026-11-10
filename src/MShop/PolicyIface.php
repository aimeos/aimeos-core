<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Interface for authorization policies
 *
 * Policies decide if the current user is allowed to execute an action for a
 * resource. They are set by the entry points (e.g. the admin interfaces) which
 * know the permission rules and are used by the managers to protect items.
 * Without a policy in the context, no restrictions are applied.
 *
 * @package MShop
 */
interface PolicyIface
{
	/**
	 * Checks if the current user is allowed to execute the action for the resource
	 *
	 * The context is passed on each call so the policy always checks the current
	 * user, even if the context is cloned or reused for several requests.
	 *
	 * If there's no rule for the resource, each caller applies its own default, e.g.
	 * the managers allow changing the items while the admin interfaces only allow
	 * access for super users.
	 *
	 * Policies whose rules aren't defined per action (e.g. only user groups per resource)
	 * may ignore the action and return the same result for all actions of a resource.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address"
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @return bool|null TRUE if allowed, FALSE if not and NULL if there's no rule for the resource
	 */
	public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool;
}
