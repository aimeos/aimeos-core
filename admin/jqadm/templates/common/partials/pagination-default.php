<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$target = $this->config( 'admin/jqadm/url/search/target' );
$controller = $this->config( 'admin/jqadm/url/search/controller', 'jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', array() );

$params = $this->param();

$total = $this->get( 'total', 0 );
$offset = max( $this->param( 'page/offset', 0 ), 0 );
$limit = max( $this->param( 'page/limit', 100 ), 1 );

$first = ( $offset > 0 ? 0 : null );
$prev = ( $offset - $limit >= 0 ? $offset - $limit : null );
$next = ( $offset + $limit < $total ? $offset + $limit : null );
$last = ( ((int) ($total / $limit)) * $limit > $offset ? ((int) ($total / $limit)) * $limit : null );

$pageCurrent = floor( $offset / $limit ) + 1;
$pageTotal = ( $total != 0 ? ceil( $total / $limit ) : 1 );

?>
<nav class="list-page">
	<ul class="page-offset pagination">
		<li class="page-item">
			<a class="page-link" href="<?php $params['page']['offset'] = $first; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'admin/jqadm', 'First' ) ); ?>">
				<span class="fa fa-fast-backward" aria-hidden="true"></span>
			</a>
		</li>
		<li class="page-item">
			<a class="page-link" href="<?php $params['page']['offset'] = $prev; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'admin/jqadm', 'Previous' ) ); ?>">
				<span class="fa fa-step-backward" aria-hidden="true"></span>
			</a>
		</li>
		<li class="page-item disabled"><a class="page-link" href="#"><?php echo $enc->html( sprintf( $this->translate( 'admin/jqadm', 'Page %1$d of %2$d' ), $pageCurrent, $pageTotal ) ); ?></a></li>
		<li class="page-item">
			<a class="page-link" href="<?php $params['page']['offset'] = $next; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'admin/jqadm', 'Next' ) ); ?>">
				<span class="fa fa-step-forward" aria-hidden="true"></span>
			</a>
		</li>
		<li class="page-item">
			<a class="page-link" href="<?php $params['page']['offset'] = $last; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'admin/jqadm', 'Last' ) ); ?>">
				<span class="fa fa-fast-forward" aria-hidden="true"></span>
			</a>
		</li>
	</ul>
	<div class="page-limit btn-group <?php echo ( $this->get( 'pos', 'top' ) === 'bottom' ? 'dropup' : '' ); ?>" role="group">
		<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<?php echo $limit; ?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li class="dropdown-item"><a href="<?php $params['page']['limit'] = 25; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">25</a></li>
			<li class="dropdown-item"><a href="<?php $params['page']['limit'] = 50; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">50</a></li>
			<li class="dropdown-item"><a href="<?php $params['page']['limit'] = 100; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">100</a></li>
			<li class="dropdown-item"><a href="<?php $params['page']['limit'] = 200; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">200</a></li>
			<li class="dropdown-item"><a href="<?php $params['page']['limit'] = 500; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">500</a></li>
		</ul>
	</div>
</nav>
