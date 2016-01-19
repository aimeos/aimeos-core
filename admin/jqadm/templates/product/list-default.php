<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$checked = function( array $list, $code ) {
	return ( in_array( $code, $list ) ? 'checked="checked"' : '' );
};

$sort = function( $sortcode, $code ) {
	return ( $sortcode === $code ? '-' . $code : $code );
};

$enc = $this->encoder();

$target = $this->config( 'admin/jqadm/url/search/target' );
$controller = $this->config( 'admin/jqadm/url/search/controller', 'jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', array() );

$newTarget = $this->config( 'admin/jqadm/url/create/target' );
$newCntl = $this->config( 'admin/jqadm/url/create/controller', 'jqadm' );
$newAction = $this->config( 'admin/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'admin/jqadm/url/create/config', array() );

$getTarget = $this->config( 'admin/jqadm/url/get/target' );
$getCntl = $this->config( 'admin/jqadm/url/get/controller', 'jqadm' );
$getAction = $this->config( 'admin/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'admin/jqadm/url/get/config', array() );

$copyTarget = $this->config( 'admin/jqadm/url/copy/target' );
$copyCntl = $this->config( 'admin/jqadm/url/copy/controller', 'jqadm' );
$copyAction = $this->config( 'admin/jqadm/url/copy/action', 'copy' );
$copyConfig = $this->config( 'admin/jqadm/url/copy/config', array() );

$delTarget = $this->config( 'admin/jqadm/url/delete/target' );
$delCntl = $this->config( 'admin/jqadm/url/delete/controller', 'jqadm' );
$delAction = $this->config( 'admin/jqadm/url/delete/action', 'delete' );
$delConfig = $this->config( 'admin/jqadm/url/delete/config', array() );

$params = $this->get( 'pageParams', array() );

$filterParams = array(
	'operators' => $this->get( 'filterOperators', array() ),
	'default' => 'product.label',
);

$default = $this->config( 'admin/jqadm/product/fields', array( 'product.status', 'product.typeid', 'product.code', 'product.label' ) );
$fields = $this->param( 'fields', $default );

$pageParams = array( 'total' => $this->get( 'total', 0 ), 'pageParams' => $params );
$sortcode = $this->param( 'sort' );

?>
<?php $this->block()->start( 'jqadm_content' ); ?>

<form class="list-search" method="POST" action="<?php echo $enc->attr( $this->url( $target, $controller, $action, $params, array(), $config ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>

	<div class="list-fields">
		<span class="label label-primary label-pill action action-open fa">Fields</span>
		<ul class="fields-items search-item">
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.id" <?php echo $checked( $fields, 'product.id' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'ID' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.status" <?php echo $checked( $fields, 'product.status' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Status' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.typeid" <?php echo $checked( $fields, 'product.typeid' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Type' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.code" <?php echo $checked( $fields, 'product.code' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Code' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.label" <?php echo $checked( $fields, 'product.label' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.datestart" <?php echo $checked( $fields, 'product.datestart' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Start date' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.dateend" <?php echo $checked( $fields, 'product.dateend' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'End date' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.ctime" <?php echo $checked( $fields, 'product.ctime' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Created' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.mtime" <?php echo $checked( $fields, 'product.mtime' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Last modified' ) ); ?></label></li>
			<li class="fields-item"><label><input type="checkbox" name="fields[]" value="product.editor" <?php echo $checked( $fields, 'product.editor' ); ?>> <?php echo $enc->html( $this->translate( 'admin', 'Editor' ) ); ?></label></li>
		</ul>
	</div>

	<div class="list-filter">
		<span class="label label-primary label-pill action action-open fa">Filter</span>
<?php echo $this->partial( $this->config( 'admin/jqadm/partial/filter', 'common/partials/filter-default.php' ), $filterParams ); ?>
	</div>

	<div class="actions-group">
		<button class="btn btn-primary"><?php echo $this->translate( 'admin', 'Search' ); ?></button>
		<a class="btn btn-warning" href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'resource' => 'product' ), array(), $config ) ); ?>"><?php echo $this->translate( 'admin', 'Clear' ); ?></a>
	</div>
</form>

<?php echo $this->partial( $this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-default.php' ), $pageParams + array( 'pos' => 'top' ) ); ?>

<div class="table-responsive">
	<table class="list-items table table-hover">
		<thead class="header">
			<tr>
<?php if( in_array( 'product.id', $fields ) ) : ?>
				<th class="product.id">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.id' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'ID' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.status', $fields ) ) : ?>
				<th class="product.status">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.status' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Status' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.typeid', $fields ) ) : ?>
				<th class="product.type">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.typeid' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Type' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.code', $fields ) ) : ?>
				<th class="product.code">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.code' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Code' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.label', $fields ) ) : ?>
				<th class="product.label">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.label' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.datestart', $fields ) ) : ?>
				<th class="product.datestart">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.datestart' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Start date' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.dateend', $fields ) ) : ?>
				<th class="product.dateend">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.dateend' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'End date' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.ctime', $fields ) ) : ?>
				<th class="product.ctime">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.ctime' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Created' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.mtime', $fields ) ) : ?>
				<th class="product.mtime">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.mtime' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Last modified' ) ); ?>
					</a>
				</th>
<?php endif; ?>
<?php if( in_array( 'product.editor', $fields ) ) : ?>
				<th class="product.editor">
					<a href="<?php echo $enc->attr( $this->url( $target, $controller, $action, array( 'sort' => $sort( $sortcode, 'product.editor' ) ) + $params, array(), $config ) ); ?>">
						<?php echo $enc->html( $this->translate( 'admin', 'Editor' ) ); ?>
					</a>
				</th>
<?php endif; ?>
				<th class="actions">
					<a class="btn btn-primary fa fa-plus"
						href="<?php echo $enc->attr( $this->url( $newTarget, $newCntl, $newAction, $params, array(), $newConfig ) ); ?>"
						aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Add' ) ); ?>">
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
<?php foreach( $this->get( 'items', array() ) as $id => $item ) : ?>
<?php	$url = $enc->attr( $this->url( $getTarget, $getCntl, $getAction, array( 'id' => $id ) + $params, array(), $getConfig ) ); ?>
			<tr>
<?php if( in_array( 'product.id', $fields ) ) : ?>
				<td class="product.id"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getId() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.status', $fields ) ) : ?>
				<td class="product.status"><a class="items-field" href="<?php echo $url; ?>"><div class="fa status-<?php echo $enc->attr( $item->getStatus() ); ?>"></div></a></td>
<?php endif; ?>
<?php if( in_array( 'product.typeid', $fields ) ) : ?>
				<td class="product.type"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getType() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.code', $fields ) ) : ?>
				<td class="product.code"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getCode() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.label', $fields ) ) : ?>
				<td class="product.label"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getLabel() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.datestart', $fields ) ) : ?>
				<td class="product.datestart"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getDateStart() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.dateend', $fields ) ) : ?>
				<td class="product.dateend"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getDateEnd() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.ctime', $fields ) ) : ?>
				<td class="product.ctime"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getTimeCreated() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.mtime', $fields ) ) : ?>
				<td class="product.mtime"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getTimeModified() ); ?></a></td>
<?php endif; ?>
<?php if( in_array( 'product.editor', $fields ) ) : ?>
				<td class="product.editor"><a class="items-field" href="<?php echo $url; ?>"><?php echo $enc->html( $item->getEditor() ); ?></a></td>
<?php endif; ?>
				<td class="actions"><!--
					--><a class="btn btn-secondary fa fa-files-o"
						href="<?php echo $enc->attr( $this->url( $copyTarget, $copyCntl, $copyAction, array( 'id' => $id ) + $params, array(), $copyConfig ) ); ?>"
						aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Copy' ) ); ?>"></a><!--
					--><a class="btn btn-danger fa fa-trash"
						href="<?php echo $enc->attr( $this->url( $delTarget, $delCntl, $delAction, array( 'id' => $id ) + $params, array(), $delConfig ) ); ?>"
						aria-label="<?php echo $enc->attr( $this->translate( 'admin', 'Delete' ) ); ?>"></a><!--
				--></td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php echo $this->partial( $this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-default.php' ), $pageParams + array( 'pos' => 'bottom' ) ); ?>

<?php $this->block()->stop(); ?>

<?php echo $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-default.php' ) ); ?>
