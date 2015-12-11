<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$target = $this->config( 'client/jqadm/url/search/target' );
$controller = $this->config( 'client/jqadm/url/search/controller', 'jqadm' );
$action = $this->config( 'client/jqadm/url/search/action', 'search' );
$config = $this->config( 'client/jqadm/url/search/config', array() );

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
<nav class="search-navigation">
	<ul class="pagination">
		<li>
			<a href="<?php $params['page']['offset'] = $first; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'First' ) ); ?>">
				<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
			</a>
		</li>
		<li>
			<a href="<?php $params['page']['offset'] = $prev; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Previous' ) ); ?>">
				<span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>
			</a>
		</li>
		<li class="disabled"><?php echo $enc->html( sprintf( $this->translate( 'client/jqadm', 'Page %1$d of %2$d' ), $pageCurrent, $pageTotal ) ); ?></li>
		<li>
			<a href="<?php $params['page']['offset'] = $next; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Next' ) ); ?>">
				<span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span>
			</a>
		</li>
		<li>
			<a href="<?php $params['page']['offset'] = $last; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>" aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Last' ) ); ?>">
				<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
			</a>
		</li>
	</ul>
	<ul class="size">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $limit; ?> <span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="<?php $params['page']['limit'] = 25; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">25</a></li>
				<li><a href="<?php $params['page']['limit'] = 50; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">50</a></li>
				<li><a href="<?php $params['page']['limit'] = 100; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">100</a></li>
				<li><a href="<?php $params['page']['limit'] = 200; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">200</a></li>
				<li><a href="<?php $params['page']['limit'] = 500; echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">500</a></li>
			</ul>
		</li>
	</ul>
</nav>
