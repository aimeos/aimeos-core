<?php

$target = $this->config( 'controller/jsonadm/url/target' );
$cntl = $this->config( 'controller/jsonadm/url/controller', 'jsonadm' );
$action = $this->config( 'controller/jsonadm/url/action', 'get' );
$config = $this->config( 'controller/jsonadm/url/config', array() );


$ref = array( 'id', 'resource', 'filter', 'page', 'sort', 'include', 'fields' );
$params = array_intersect_key( $this->param(), array_flip( $ref ) );


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
	"errors": <?php echo $this->partial( $this->get( 'partial-errors', 'controller/jsonadm/partials/template-errors' ), 'partials/errors-standard.php', array( 'errors' => $this->errors ) ); ?>
<?php elseif( isset( $this->data ) ) : ?>
	"data": <?php echo $this->partial( $this->get( 'partial-data', 'controller/jsonadm/partials/template-data' ), 'partials/data-standard.php', array( 'data' => $this->get( 'data' ), 'childItems' => $this->get( 'childItems', array() ), 'listItems' => $this->get( 'listItems', array() ) ) ); ?>,
	"included": <?php echo $this->partial( $this->get( 'partial-included', 'controller/jsonadm/partials/template-included' ), 'partials/included-standard.php', array( 'childItems' => $this->get( 'childItems', array() ), 'refItems' => $this->get( 'refItems', array() ) ) ); ?>
<?php endif; ?>

}
