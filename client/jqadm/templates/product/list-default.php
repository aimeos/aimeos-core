<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$sort = function( array $params, $code ) {
	return ( isset( $params['sort'] ) && $params['sort'] === $code ? '-' . $code : $code );
};

$enc = $this->encoder();

$target = $this->config( 'client/jqadm/url/search/target' );
$controller = $this->config( 'client/jqadm/url/search/controller', 'jqadm' );
$action = $this->config( 'client/jqadm/url/search/action', 'search' );
$config = $this->config( 'client/jqadm/url/search/config', array() );

$newTarget = $this->config( 'client/jqadm/url/create/target' );
$newCntl = $this->config( 'client/jqadm/url/create/controller', 'jqadm' );
$newAction = $this->config( 'client/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'client/jqadm/url/create/config', array() );

$getTarget = $this->config( 'client/jqadm/url/get/target' );
$getCntl = $this->config( 'client/jqadm/url/get/controller', 'jqadm' );
$getAction = $this->config( 'client/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'client/jqadm/url/get/config', array() );

$delTarget = $this->config( 'client/jqadm/url/delete/target' );
$delCntl = $this->config( 'client/jqadm/url/delete/controller', 'jqadm' );
$delAction = $this->config( 'client/jqadm/url/delete/action', 'delete' );
$delConfig = $this->config( 'client/jqadm/url/delete/config', array() );

$params = $this->param();
unset( $params['filter'] );

$filterParams = array(
	'filterAttributes' => $this->get( 'filterAttributes', array() ),
	'filterOperators' => $this->get( 'filterOperators', array() ),
);

$pageParams = array( 'total' => $this->get( 'total', 0 ) );
$pagination = $this->partial( $this->config( 'client/jqadm/partial/pagination', 'common/partials/pagination-default.php' ), $pageParams );

$default = $this->config( 'client/jqadm/product/fields', array( 'product.status', 'product.typeid', 'product.code', 'product.label' ) );
$fields = $this->param( 'fields', $default );

?>
<?php echo $this->partial( $this->config( 'client/jqadm/partial/navigation', 'common/partials/navigation-default.php' ), array() ); ?>

<form class="filter" method="POST" action="<?php echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>
<?php echo $this->partial( $this->config( 'client/jqadm/partial/filter', 'common/partials/filter-default.php' ), $filterParams ); ?>
	<div class="checkbox">
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'ID' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Status' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Type' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Code' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Label' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Start date' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'End date' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Created' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Modified' ) ); ?></label>
		<label><input type="checkbox" name="fields[]"> <?php echo $enc->html( $this->translate( 'client/jqadm', 'Editor' ) ); ?></label>
	</div>
</form>

<?php echo $pagination; ?>

<table class="table table-hover">
	<thead>
		<tr>
<?php if( in_array( 'product.id', $fields ) ) : ?>
			<th class="product.id">
				<a href="<?php $params['sort'] = $sort( $params, 'product.id' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'ID' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.status', $fields ) ) : ?>
			<th class="product.status">
				<a href="<?php $params['sort'] = $sort( $params, 'product.status' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Status' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.typeid', $fields ) ) : ?>
			<th class="product.type">
				<a href="<?php $params['sort'] = $sort( $params, 'product.typeid' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Type' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.code', $fields ) ) : ?>
			<th class="product.code">
				<a href="<?php $params['sort'] = $sort( $params, 'product.code' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Code' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.label', $fields ) ) : ?>
			<th class="product.label">
				<a href="<?php $params['sort'] = $sort( $params, 'product.label' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Label' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.datestart', $fields ) ) : ?>
			<th class="product.datestart">
				<a href="<?php $params['sort'] = $sort( $params, 'product.datestart' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Start date' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.dateend', $fields ) ) : ?>
			<th class="product.dateend">
				<a href="<?php $params['sort'] = $sort( $params, 'product.dateend' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'End date' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.ctime', $fields ) ) : ?>
			<th class="product.ctime">
				<a href="<?php $params['sort'] = $sort( $params, 'product.ctime' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Created' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.mtime', $fields ) ) : ?>
			<th class="product.mtime">
				<a href="<?php $params['sort'] = $sort( $params, 'product.mtime' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Modified' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
<?php if( in_array( 'product.editor', $fields ) ) : ?>
			<th class="product.editor">
				<a href="<?php $params['sort'] = $sort( $params, 'product.editor' ); echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
					<?php echo $enc->html( $this->translate( 'client/jqadm', 'Editor' ) ); ?>
					<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
				</a>
			</th>
<?php endif; ?>
			<th class="actions">
				<a class="btn btn-primary glyphicon glyphicon-plus"
					href="<?php echo $enc->attr( $this->url( $newTarget, $newCntl, $newAction, array( 'resource' => 'product' ), array(), $newConfig ) ); ?>"
					aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'New' ) ); ?>">
				</a>
			</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->get( 'items', array() ) as $id => $item ) : ?>
		<tr>
<?php if( in_array( 'product.id', $fields ) ) : ?>
			<td class="product.id"><?php echo $enc->html( $item->getId() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.status', $fields ) ) : ?>
			<td class="product.status"><div class="glyphicon status-<?php echo $enc->attr( $item->getStatus() ); ?>"></div></td>
<?php endif; ?>
<?php if( in_array( 'product.typeid', $fields ) ) : ?>
			<td class="product.type"><?php echo $enc->html( $item->getType() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.code', $fields ) ) : ?>
			<td class="product.code"><?php echo $enc->html( $item->getCode() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.label', $fields ) ) : ?>
			<td class="product.label"><?php echo $enc->html( $item->getLabel() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.datestart', $fields ) ) : ?>
			<td class="product.datestart"><?php echo $enc->html( $item->getDateStart() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.dateend', $fields ) ) : ?>
			<td class="product.dateend"><?php echo $enc->html( $item->getDateEnd() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.ctime', $fields ) ) : ?>
			<td class="product.ctime"><?php echo $enc->html( $item->getTimeCreated() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.mtime', $fields ) ) : ?>
			<td class="product.mtime"><?php echo $enc->html( $item->getTimeModified() ); ?></td>
<?php endif; ?>
<?php if( in_array( 'product.editor', $fields ) ) : ?>
			<td class="product.editor"><?php echo $enc->html( $item->getEditor() ); ?></td>
<?php endif; ?>
			<td class="actions"><!--
				--><a class="btn btn-primary glyphicon glyphicon-pencil"
					href="<?php echo $enc->attr( $this->url( $getTarget, $getCntl, $getAction, array( 'resource' => 'product', 'id' => $id ), array(), $getConfig ) ); ?>"
					aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Edit' ) ); ?>"></a><!--
				--><a class="btn btn-danger glyphicon glyphicon-trash"
					href="<?php echo $enc->attr( $this->url( $delTarget, $delCntl, $delAction, array( 'resource' => 'product', 'id' => $id ), array(), $delConfig ) ); ?>"
					aria-label="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Delete' ) ); ?>"></a><!--
			--></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php echo $pagination; ?>
