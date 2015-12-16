<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

$target = $this->config( 'client/jqadm/url/save/target' );
$cntl = $this->config( 'client/jqadm/url/save/controller', 'jqadm' );
$action = $this->config( 'client/jqadm/url/save/action', 'save' );
$config = $this->config( 'client/jqadm/url/save/config', array() );

$listTarget = $this->config( 'client/jqadm/url/search/target' );
$listCntl = $this->config( 'client/jqadm/url/search/controller', 'jqadm' );
$listAction = $this->config( 'client/jqadm/url/search/action', 'search' );
$listConfig = $this->config( 'client/jqadm/url/search/config', array() );

$params = $this->param();

?>
<?php echo $this->partial( $this->config( 'client/jqadm/partial/navigation', 'common/partials/navigation-default.php' ), array() ); ?>

<?php echo $this->partial( $this->config( 'client/jqadm/partial/error', 'common/partials/error-default.php' ), array( 'errors' => $this->get( 'errors', array() ) ) ); ?>

<form class="item item-product form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $enc->attr( $this->url( $target, $cntl, $action, $params, array(), $config ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>

	<div id="accordion" role="tablist" aria-multiselectable="true">

		<div class="product-item card">
			<div class="card-header" role="tab" id="product-item"role="button" data-toggle="collapse" data-parent="#accordion" href="#product-item-data" aria-expanded="true" aria-controls="product-item-data">
				<?php echo $enc->html( $this->translate( 'client/jqadm', 'Basic' ) ); ?>
			</div>
			<div id="product-item-data" class="item-basic card-block panel-collapse collapse in" role="tabpanel" aria-labelledby="product-item">
				<div class="col-sm-6">
					<div class="form-group row">
						<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'ID' ) ); ?></label>
						<div class="col-sm-9">
							<input type="hidden" name="item[product.id]" value="<?php echo $enc->attr( $this->item->getId() ); ?>" />
							<p class="form-control-static"><?php echo $enc->attr( $this->item->getId() ); ?></p>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label for="product-status" class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Status' ) ); ?></label>
						<div class="col-sm-9">
							<select class="form-control" id="product-status" name="item[product.status]">
								<option value="1" <?php echo $selected( $this->value( 'item/product.status', $this->item->getStatus() ), 1 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Enabled' ) ); ?></option>
								<option value="0" <?php echo $selected( $this->value( 'item/product.status', $this->item->getStatus() ), 0 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Disabled' ) ); ?></option>
								<option value="-1" <?php echo $selected( $this->value( 'item/product.status', $this->item->getStatus() ), -1 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Review' ) ); ?></option>
								<option value="-2" <?php echo $selected( $this->value( 'item/product.status', $this->item->getStatus() ), -2 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Archive' ) ); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label for="product-type-id" class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Type' ) ); ?></label>
						<div class="col-sm-9">
							<select class="form-control" id="product-typeid" name="item[product.typeid]">
<?php foreach( $this->get( 'itemTypes', array() ) as $id => $typeItem ) : ?>
								<option value="<?php echo $enc->attr( $this->value( 'item/product.typeid', $id ) ); ?>" data-code="<?php echo $enc->attr( $typeItem->getCode() ); ?>" <?php echo $selected( $this->item->getTypeId(), $id ); ?>><?php echo $enc->html( $typeItem->getLabel() ); ?></option>
<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group row mandatory">
						<label for="product-code" class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Code' ) ); ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="product-code" name="item[product.code]"
								placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Unique code (SKU, EAN)' ) ); ?>"
								value="<?php echo $enc->attr( $this->value( 'item/product.code', $this->item->getCode() ) ); ?>">
						</div>
					</div>
					<div class="form-group row mandatory">
						<label for="product-label" class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Label' ) ); ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="product-label" name="item[product.label]"
								placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Internal label' ) ); ?>"
								value="<?php echo $enc->attr( $this->value( 'item/product.label', $this->item->getLabel() ) ); ?>">
						</div>
					</div>
					<div class="form-group row optional">
						<label for="product-datestart" class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Start date' ) ); ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control date" id="product-datestart" name="item[product.datestart]" data-format="<?php echo $this->translate( 'client/jqadm', 'yy-mm-dd' ); ?>"
								placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Start date (YYYY-mm-dd HH:mm:ss)' ) ); ?>"
								value="<?php echo $enc->attr( $this->value( 'item/product.datestart', $this->item->getDateStart() ) ); ?>">
						</div>
					</div>
					<div class="form-group row optional">
						<label for="product-dateend" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'End date' ) ); ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control date" id="product-dateend" name="item[product.dateend]" data-format="<?php echo $this->translate( 'client/jqadm', 'yy-mm-dd' ); ?>"
								placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'End date (YYYY-mm-dd HH:mm:ss)' ) ); ?>"
								value="<?php echo $enc->attr( $this->value( 'item/product.dateend', $this->item->getDateEnd() ) ); ?>" >
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<table class="item-config table table-striped">
						<thead>
							<tr>
								<th><?php echo $enc->html( $this->translate( 'client/jqadm', 'Option' ) ); ?></th>
								<th><?php echo $enc->html( $this->translate( 'client/jqadm', 'Value' ) ); ?></th>
								<th class="actions"><div class="btn btn-primary fa fa-plus"></div></th>
							</tr>
						</thead>
						<tbody>
<?php if( is_array( $this->param( 'item/config/key' ) ) ) : ?>
<?php	foreach( $this->value( 'item/config/key', array() ) as $idx => $key ) : ?>
							<tr class="config-item">
								<td><input type="text" class="config-key form-control" name="item[config][key][]" value="<?php echo $enc->attr( $this->value( 'item/config/key/' . $idx, $key ) ); ?>"></td>
								<td><input type="text" class="config-value form-control" name="item[config][val][]" value="<?php echo $enc->attr( $this->value( 'item/config/val/' . $idx ) ); ?>"></td>
								<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
							</tr>
<?php	endforeach; ?>
<?php else : ?>
<?php	foreach( $this->item->getConfig() as $key => $value ) : ?>
							<tr class="config-item">
								<td><input type="text" class="config-key form-control" name="item[config][key][]" value="<?php echo $enc->attr( $key ); ?>"></td>
								<td><input type="text" class="config-value form-control" name="item[config][val][]" value="<?php echo $enc->attr( $value ); ?>"></td>
								<td class="actions"><div class="btn btn-danger fa fa-trash"></div></td>
							</tr>
<?php	endforeach; ?>
<?php endif; ?>
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
	</div>

	<div class="actions-group">
		<button class="btn btn-primary">
			<?php echo $enc->html( $this->translate( 'client/jqadm', 'Save' ) ); ?>
		</button>
		<a class="btn btn-warning" href="<?php echo $enc->attr( $this->url( $listTarget, $listCntl, $listAction, $params, array(), $listConfig ) ); ?>">
			<?php echo $enc->html( $this->translate( 'client/jqadm', 'Cancel' ) ); ?>
		</a>
	</div>
</form>
