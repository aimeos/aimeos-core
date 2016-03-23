<?php

/** admin/jsonadm/url/target
 * Destination of the URL where the client specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the client that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2016.01
 * @category Developer
 * @see admin/jsonadm/url/controller
 * @see admin/jsonadm/url/action
 * @see admin/jsonadm/url/config
 */
$target = $this->config( 'admin/jsonadm/url/target' );

/** admin/jsonadm/url/controller
 * Name of the client whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the client contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the client
 * @since 2016.01
 * @category Developer
 * @see admin/jsonadm/url/target
 * @see admin/jsonadm/url/action
 * @see admin/jsonadm/url/config
 */
$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );

/** admin/jsonadm/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * client that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2016.01
 * @category Developer
 * @see admin/jsonadm/url/target
 * @see admin/jsonadm/url/controller
 * @see admin/jsonadm/url/config
 */
$action = $this->config( 'admin/jsonadm/url/action', 'get' );

/** admin/jsonadm/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  admin/jsonadm/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2016.01
 * @category Developer
 * @see admin/jsonadm/url/target
 * @see admin/jsonadm/url/controller
 * @see admin/jsonadm/url/action
 */
$config = $this->config( 'admin/jsonadm/url/config', array() );


/** admin/jsonadm/partials/template-errors
 * Relative path to the error partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The error
 * partial creates the "error" part for the JSON API response.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "partials/error-standard.php".
 *
 * @param string Relative path to the template file
 * @since 2016.01
 * @category Developer
 */

/** admin/jsonadm/partials/template-data
 * Relative path to the data partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The data
 * partial creates the "data" part for the JSON API response.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "partials/data-standard.php".
 *
 * @param string Relative path to the template file
 * @since 2016.01
 * @category Developer
 */

/** admin/jsonadm/partials/template-included
 * Relative path to the "included" partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The "included"
 * partial creates the "included" part for the JSON API response.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "partials/included-standard.php".
 *
 * @param string Relative path to the template file
 * @since 2016.01
 * @category Developer
 */


$ref = array( 'id', 'resource', 'filter', 'page', 'sort', 'include', 'fields' );
$params = array_intersect_key( $this->param(), array_flip( $ref ) );

if( !isset( $params['id'] ) ) {
	$params['id'] = '';
}


$total = $this->get( 'total', 0 );
$offset = max( $this->param( 'page/offset', 0 ), 0 );
$limit = max( $this->param( 'page/limit', 25 ), 1 );

$first = ( $offset > 0 ? 0 : null );
$prev = ( $offset - $limit >= 0 ? $offset - $limit : null );
$next = ( $offset + $limit < $total ? $offset + $limit : null );
$last = ( ((int) ($total / $limit)) * $limit > $offset ? ((int) ($total / $limit)) * $limit : null );

?>
{
	"meta": {
		"total": <?php echo $total; ?>

	},
	"links": {
<?php if( is_array( $this->get( 'data' ) ) ) : ?>
<?php	if( $first !== null ) : ?>
		"first": "<?php $params['page']['offset'] = $first; echo $this->url( $target, $cntl, $action, $params, array(), $config ); ?>",
<?php	endif; ?>
<?php	if( $prev !== null ) : ?>
		"prev": "<?php $params['page']['offset'] = $prev; echo $this->url( $target, $cntl, $action, $params, array(), $config ); ?>",
<?php	endif; ?>
<?php	if( $next !== null ) : ?>
		"next": "<?php $params['page']['offset'] = $next; echo $this->url( $target, $cntl, $action, $params, array(), $config ); ?>",
<?php	endif; ?>
<?php	if( $last !== null ) : ?>
		"last": "<?php $params['page']['offset'] = $last; echo $this->url( $target, $cntl, $action, $params, array(), $config ); ?>",
<?php	endif; ?>
<?php endif; ?>
		"self": "<?php $params['page']['offset'] = $offset; echo $this->url( $target, $cntl, $action, $params, array(), $config ); ?>"
	},
<?php if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>
<?php elseif( isset( $this->data ) ) : ?>
	"data": <?php echo $this->partial( $this->config( $this->get( 'partial-data', 'admin/jsonadm/partials/template-data' ), 'partials/data-standard.php' ), array( 'data' => $this->get( 'data' ), 'childItems' => $this->get( 'childItems', array() ), 'listItems' => $this->get( 'listItems', array() ) ) ); ?>,
	"included": <?php echo $this->partial( $this->config( $this->get( 'partial-included', 'admin/jsonadm/partials/template-included' ), 'partials/included-standard.php' ), array( 'childItems' => $this->get( 'childItems', array() ), 'refItems' => $this->get( 'refItems', array() ) ) ); ?>
<?php endif; ?>

}
