<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

$target = $this->config( 'admin/jqadm/url/save/target' );
$cntl = $this->config( 'admin/jqadm/url/save/controller', 'jqadm' );
$action = $this->config( 'admin/jqadm/url/save/action', 'save' );
$config = $this->config( 'admin/jqadm/url/save/config', array() );

$listTarget = $this->config( 'admin/jqadm/url/search/target' );
$listCntl = $this->config( 'admin/jqadm/url/search/controller', 'jqadm' );
$listAction = $this->config( 'admin/jqadm/url/search/action', 'search' );
$listConfig = $this->config( 'admin/jqadm/url/search/config', array() );

$params = $this->get( 'itemParams', array() );
$params['id'] = $this->param( 'id', '' );

?>
<?php $this->block()->start( 'jqadm_content' ); ?>

<form class="item item-product form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $enc->attr( $this->url( $target, $cntl, $action, $params, array(), $config ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>

	<div id="accordion" role="tablist" aria-multiselectable="true">

		<div class="product-item card panel">
			<div id="product-item" class="header card-header" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-data" aria-expanded="true" aria-controls="product-item-data">
				<?php echo $enc->html( $this->translate( 'admin', 'Basic' ) ); ?>
			</div>
			<div id="product-item-data" class="item-basic card-block panel-collapse collapse in" role="tabpanel" aria-labelledby="product-item">
				<div class="col-lg-6">
					<div class="form-group row">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'ID' ) ); ?></label>
						<div class="col-sm-9">
							<input class="item-id" type="hidden" name="item[product.id]" value="<?php echo $enc->attr( $this->get( 'itemData/product.id' ) ); ?>" />
							<p class="form-control-static item-id"><?php echo $enc->attr( $this->get( 'itemData/product.id' ) ); ?></p>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Status' ) ); ?></label>
						<div class="col-sm-9">
							<select class="form-control c-select item-status" name="item[product.status]">
								<option value="1" <?php echo $selected( $this->get( 'itemData/product.status', 1 ), 1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?></option>
								<option value="0" <?php echo $selected( $this->get( 'itemData/product.status', 1 ), 0 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?></option>
								<option value="-1" <?php echo $selected( $this->get( 'itemData/product.status', 1 ), -1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:review' ) ); ?></option>
								<option value="-2" <?php echo $selected( $this->get( 'itemData/product.status', 1 ), -2 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Type' ) ); ?></label>
						<div class="col-sm-9">
							<select class="form-control c-select item-typeid" name="item[product.typeid]">
<?php foreach( $this->get( 'itemTypes', array() ) as $id => $typeItem ) : ?>
								<option value="<?php echo $enc->attr( $id ); ?>" data-code="<?php echo $enc->attr( $typeItem->getCode() ); ?>" <?php echo $selected( $this->get( 'itemData/product.typeid' ), $id ); ?>><?php echo $enc->html( $typeItem->getLabel() ); ?></option>
<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Code' ) ); ?></label>
						<div class="col-sm-9">
							<input class="form-control item-code" type="text" name="item[product.code]"
								placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'EAN, SKU or article number (required)' ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'itemData/product.code' ) ); ?>">
						</div>
					</div>
					<div class="form-group row mandatory">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
						<div class="col-sm-9">
							<input class="form-control item-label" type="text" name="item[product.label]"
								placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Internal name (required)' ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'itemData/product.label' ) ); ?>">
						</div>
					</div>
					<div class="form-group row optional">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Start date' ) ); ?></label>
						<div class="col-sm-9">
							<input class="form-control item-datestart date" type="text" name="item[product.datestart]" data-format="<?php echo $this->translate( 'admin', 'yy-mm-dd' ); ?>"
								placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'itemData/product.datestart' ) ); ?>">
						</div>
					</div>
					<div class="form-group row optional">
						<label class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'admin', 'End date' ) ); ?></label>
						<div class="col-sm-9">
							<input class="form-control item-dateend date" type="text" name="item[product.dateend]" data-format="<?php echo $this->translate( 'admin', 'yy-mm-dd' ); ?>"
								placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'YYYY-MM-DD hh:mm:ss (optional)' ) ); ?>"
								value="<?php echo $enc->attr( $this->get( 'itemData/product.dateend' ) ); ?>" >
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<table class="item-config table table-striped">
						<thead>
							<tr>
								<th><?php echo $enc->html( $this->translate( 'admin', 'Option' ) ); ?></th>
								<th><?php echo $enc->html( $this->translate( 'admin', 'Value' ) ); ?></th>
								<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
							</tr>
						</thead>
						<tbody>
<?php	foreach( (array) $this->get( 'itemData/config/key', array() ) as $idx => $key ) : ?>
							<tr class="config-item">
								<td><input type="text" class="config-key form-control" name="item[config][key][]" value="<?php echo $enc->attr( $this->get( 'itemData/config/key/' . $idx, $key ) ); ?>"></td>
								<td><input type="text" class="config-value form-control" name="item[config][val][]" value="<?php echo $enc->attr( $this->get( 'itemData/config/val/' . $idx ) ); ?>"></td>
								<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
							</tr>
<?php	endforeach; ?>
							<tr class="prototype">
								<td><input type="text" class="config-key form-control" name="item[config][key][]" value="" disabled="disabled"></td>
								<td><input type="text" class="config-value form-control" name="item[config][val][]" value="" disabled="disabled"></td>
								<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

<?php echo $this->get( 'itemBody' ); ?>

	</div>

	<div class="actions-group">
		<button class="btn btn-primary">
			<?php echo $enc->html( $this->translate( 'admin', 'Save' ) ); ?>
		</button>
		<a class="btn btn-warning" href="<?php echo $enc->attr( $this->url( $listTarget, $listCntl, $listAction, $params, array(), $listConfig ) ); ?>">
			<?php echo $enc->html( $this->translate( 'admin', 'Cancel' ) ); ?>
		</a>
	</div>
</form>

<?php $this->block()->stop(); ?>


<?php echo $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-default.php' ) ); ?>
