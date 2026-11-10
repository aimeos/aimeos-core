<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Authorization policy handling for the entry points like the admin interfaces
 *
 * The classes using the trait must implement the static policy() method which
 * returns their default authorization policy and the static prefix() method
 * which returns the configuration key of the resources they protect.
 *
 * The methods check the permissions on different levels:
 * - allowed(): if the user can execute an action for one resource
 * - check(): same as allowed() but throws the exception returned by denial() if not allowed
 * - denial(): the exception for an action which isn't allowed, e.g. if checked by allowed() before
 * - forbidden(): the exception for other actions which aren't allowed, e.g. converted policy exceptions
 * - permitted(): which resources of a list the user can execute an action for
 * - readable(): if the user can retrieve a single shared item
 * - restrict(): limits a filter to the shared items the user can retrieve
 *
 * @package MShop
 */
trait PolicyTrait
{
	/**
	 * Returns the default authorization policy of the entry point
	 *
	 * @return \Aimeos\MShop\PolicyIface Authorization policy
	 */
	abstract public static function policy() : \Aimeos\MShop\PolicyIface;


	/**
	 * Returns the configuration key prefix of the resources protected by the entry point
	 *
	 * @return string Configuration key prefix, e.g. "admin/jqadm/resource"
	 */
	abstract protected static function prefix() : string;


	/**
	 * Checks if the current user is allowed to execute the action for the resource
	 *
	 * Uses the policy from the context or the default policy if none is set.
	 * Resources without rules in the policy use the result of fallback().
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address"
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @param bool $inherit TRUE if sub-resources without own rules (e.g. "order/address") use the rules of their parent resource
	 * @return bool TRUE if allowed, FALSE if not
	 */
	public static function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action, bool $inherit = false ) : bool
	{
		$policy = $context->policy() ?? static::policy();
		$name = $resource;

		while( ( $result = $policy->allowed( $context, $name, $action ) ) === null
			&& $inherit && ( $pos = strrpos( $name, '/' ) ) !== false
		) {
			$name = substr( $name, 0, $pos );
		}

		return $result ?? static::fallback( $context, $resource, $action );
	}


	/**
	 * Ensures that the current user is allowed to execute the action for the resource
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address"
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @throws \Exception If the action isn't allowed, the exception returned by denial()
	 * @see allowed()
	 */
	public static function check( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : void
	{
		if( !static::allowed( $context, $resource, $action ) ) {
			throw static::denial( $context, $resource, $action );
		}
	}


	/**
	 * Returns the exception of the entry point for an action which isn't allowed for the resource
	 *
	 * The resource and action names are configuration keys and therefore not translated.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object for translating the message
	 * @param string $resource Resource name, e.g. "product" or "order/address", empty for the root resource
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @return \Exception Exception with code 403
	 * @see forbidden()
	 */
	public static function denial( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : \Exception
	{
		$msg = $resource !== ''
			? sprintf( $context->translate( 'mshop', 'Action "%1$s" is not allowed for "%2$s"' ), $action, $resource )
			: sprintf( $context->translate( 'mshop', 'Action "%1$s" is not allowed' ), $action );

		return static::forbidden( $msg );
	}


	/**
	 * Returns the exception of the entry point for actions which aren't allowed
	 *
	 * Used by denial() and for converting the policy exceptions thrown by the managers,
	 * e.g. if shared items of domains the user isn't allowed to change are saved.
	 *
	 * @param string $msg Translated error message
	 * @param \Throwable|null $previous Previous exception, e.g. the policy exception thrown by a manager
	 * @return \Exception Exception with code 403
	 * @see exception()
	 */
	final public static function forbidden( string $msg, ?\Throwable $previous = null ) : \Exception
	{
		return static::exception( $msg, 403, $previous );
	}


	/**
	 * Returns the resources the current user is allowed to execute the action for
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string[] $resources List of resource names, e.g. ["product", "order/address"]
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @param bool $inherit TRUE if sub-resources without own rules (e.g. "order/address") use the rules of their parent resource
	 * @return string[] List of allowed resource names
	 */
	public static function permitted( \Aimeos\MShop\ContextIface $context, array $resources, string $action, bool $inherit = false ) : array
	{
		return array_values( array_filter( $resources, fn( $resource ) => static::allowed( $context, $resource, $action, inherit: $inherit ) ) );
	}


	/**
	 * Returns the item if the current user is allowed to retrieve it
	 *
	 * Shared items (e.g. prices, texts and media) can contain sensitive data of the
	 * domain owning them like service provider credentials, so they require the read
	 * permission of the owning domain. Items without owning domain can be retrieved.
	 *
	 * Items the user isn't allowed to retrieve are reported like missing items, so
	 * their IDs don't reveal which items exist in the domains the user can't access.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item to check
	 * @return \Aimeos\MShop\Common\Item\Iface Passed item
	 * @throws \Aimeos\MShop\Exception If the user isn't allowed to retrieve the item, same as for missing items
	 */
	public static function readable( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Common\Item\Iface $item ) : \Aimeos\MShop\Common\Item\Iface
	{
		$type = $item->getResourceType();
		$domain = (string) $item->get( \Aimeos\MShop\Common\Manager\Decorator\Owner::key( $type ) );

		// only the owning domain is checked instead of computing all readable domains
		if( $domain !== '' && ( !static::protects( $context, $domain ) || !static::allowed( $context, $domain, 'get' ) ) ) {
			// same exception as for missing items, so the IDs don't reveal which items exist
			$msg = $context->translate( 'mshop', 'Item with ID "%2$s" in "%1$s" not found' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, str_replace( '/', '.', $type ) . '.id', (string) $item->getId() ), 404 );
		}

		return $item;
	}


	/**
	 * Restricts the filter to the items the current user is allowed to retrieve
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager the filter is used for
	 * @param \Aimeos\Base\Criteria\Iface $filter Filter for searching the items which is restricted in place
	 * @see readable()
	 */
	public static function restrict( \Aimeos\MShop\ContextIface $context, \Aimeos\MShop\Common\Manager\Iface $manager,
		\Aimeos\Base\Criteria\Iface $filter ) : void
	{
		$key = \Aimeos\MShop\Common\Manager\Decorator\Owner::key( join( '/', $manager->type() ) );

		if( isset( $manager->getSearchAttributes( false )[$key] ) ) {
			$filter->add( $key, '==', array_merge( [''], static::domains( $context ) ) );
		}
	}


	/**
	 * Returns if the action is allowed for resources without rules in the policy
	 *
	 * Only super users are allowed by default because the "access" view helpers of the
	 * host applications grant access for an empty list of user groups to super users only.
	 * Entry points can overwrite this method if they don't use these view helpers.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @param string $resource Resource name, e.g. "product" or "order/address"
	 * @param string $action Action name, e.g. "get", "save" or "delete"
	 * @return bool TRUE if allowed, FALSE if not
	 * @throws \Aimeos\MShop\Exception If the context contains no view for checking the user
	 */
	protected static function fallback( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : bool
	{
		return $context->view()->access( [] ) === true;
	}


	/**
	 * Creates the exception of the entry point
	 *
	 * Entry points overwrite this method to return their own exceptions. The code is
	 * passed by forbidden(), which is final so subclasses of the entry points can't change it.
	 *
	 * @param string $msg Translated error message
	 * @param int $code Error code, e.g. 403 for actions which aren't allowed
	 * @param \Throwable|null $previous Previous exception
	 * @return \Exception Exception with the passed code
	 */
	protected static function exception( string $msg, int $code, ?\Throwable $previous = null ) : \Exception
	{
		return new \Aimeos\MShop\PolicyException( $msg, $code, $previous );
	}


	/**
	 * Returns the owning domains whose shared items the current user is allowed to retrieve
	 *
	 * Owning domains which aren't resources of the entry point are never returned,
	 * so only shared items of known domains can be retrieved.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the current user
	 * @return string[] List of domain names, e.g. ["product", "service"]
	 */
	protected static function domains( \Aimeos\MShop\ContextIface $context ) : array
	{
		return static::permitted( $context, static::resources( $context ), 'get' );
	}


	/**
	 * Checks if the entry point protects the top-level resource
	 *
	 * Same as in_array( $name, resources() ) but reads only the configuration of the resource.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the configuration
	 * @param string $name Resource name, e.g. "product"
	 * @return bool TRUE if the name is a top-level resource, FALSE if not
	 * @see resources()
	 */
	protected static function protects( \Aimeos\MShop\ContextIface $context, string $name ) : bool
	{
		return !str_contains( $name, '/' )
			&& \Aimeos\MShop\Policy\Base::isResource( $context->config()->get( static::prefix() . '/' . $name ) );
	}


	/**
	 * Returns the names of the top-level resources protected by the entry point
	 *
	 * Resources contain lists of user groups and sub-resources, so lists of user group
	 * names like the ones of the root resource (e.g. "admin/jsonadm/resource/groups")
	 * and empty lists aren't resources.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with the configuration
	 * @return string[] List of resource names, e.g. ["product", "service"]
	 */
	protected static function resources( \Aimeos\MShop\ContextIface $context ) : array
	{
		$config = (array) $context->config()->get( static::prefix(), [] );
		$config = array_filter( $config, fn( $value ) => \Aimeos\MShop\Policy\Base::isResource( $value ) );

		return array_map( 'strval', array_keys( $config ) );
	}


	/**
	 * Returns a context containing an authorization policy
	 *
	 * The policy is set in a copy because the passed context may be shared with other requests.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Passed context if it contains a policy, otherwise a copy with the default policy
	 */
	protected static function withPolicy( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		return $context->policy() ? $context : ( clone $context )->setPolicy( static::policy() );
	}
}
