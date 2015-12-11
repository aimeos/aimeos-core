<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$status = function( \Aimeos\MShop\Product\Item\Iface $item, $value ) {
	return ( $item->getStatus() == $value ? 'selected="selected"' : '' );
};

$type = function( \Aimeos\MShop\Product\Item\Iface $item, $value ) {
	return ( $item->getTypeId() == $value ? 'selected="selected"' : '' );
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
$listParam = array();

?>
<?php echo $this->partial( $this->config( 'client/jqadm/partial/navigation', 'common/partials/navigation-default.php' ), array() ); ?>

<form class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $enc->attr( $this->url( $target, $cntl, $action, array(), array(), $config ) ); ?>">
<?php echo $this->csrf()->formfield(); ?>

	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

		<div class="product-basic panel panel-default">
			<div class="panel-heading" role="tab" id="product-basic">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#product-basic-data" aria-expanded="true" aria-controls="product-basic-data">
						<?php echo $enc->html( $this->translate( 'client/jqadm', 'Basic' ) ); ?>
					</a>
				</h4>
			</div>
			<div id="product-basic-data" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="product-basic">
				<div class="panel-body">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'ID' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="item[product.id]" value="<?php echo $this->item->getId(); ?>" />
								<p class="form-control-static"><?php echo $this->item->getId(); ?></p>
							</div>
						</div>
						<div class="form-group">
							<label for="product-status" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Status' ) ); ?> *</label>
							<div class="col-sm-9">
								<select class="form-control selectmenu" id="product-status" name="item[product.status]">
									<option value="1" <?php echo $status( $this->item, 1 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Enabled' ) ); ?></option>
									<option value="0" <?php echo $status( $this->item, 0 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Disabled' ) ); ?></option>
									<option value="-1" <?php echo $status( $this->item, -1 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Review' ) ); ?></option>
									<option value="-2" <?php echo $status( $this->item, -2 ); ?>><?php echo $enc->html( $this->translate( 'client/jqadm', 'Archive' ) ); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="product-type-id" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Type' ) ); ?> *</label>
							<div class="col-sm-9">
								<select class="form-control selectmenu" id="product-typeid" name="item[product.typeid]">
<?php foreach( $this->get( 'itemTypes', array() ) as $id => $typeItem ) : ?>
									<option value="<?php echo $id; ?>" data-code="<?php echo $typeItem->getCode(); ?>" <?php echo $type( $this->item, $id ); ?>><?php echo $typeItem->getLabel(); ?></option>
<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="product-code" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Code' ) ); ?> *</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="product-code" name="item[product.code]"
									placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Unique code (SKU, EAN)' ) ); ?>"
									value="<?php echo $this->item->getCode(); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="product-label" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Label' ) ); ?> *</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="product-label" name="item[product.label]"
									placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Internal label' ) ); ?>"
									value="<?php echo $this->item->getLabel(); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="product-datestart" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'Start date' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control date" id="product-datestart" name="item[product.datestart]"
									placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'Start date (YYYY-mm-dd HH:mm)' ) ); ?>"
									value="<?php echo $this->item->getDateStart(); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="product-dateend" class="col-sm-3 control-label"><?php echo $enc->html( $this->translate( 'client/jqadm', 'End date' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control date" id="product-dateend" name="item[product.dateend]"
									placeholder="<?php echo $enc->attr( $this->translate( 'client/jqadm', 'End date (YYYY-mm-dd HH:mm)' ) ); ?>"
									value="<?php echo $this->item->getDateEnd(); ?>" >
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<table class="config table table-striped">
							<thead>
								<tr>
									<th><?php echo $enc->html( $this->translate( 'client/jqadm', 'Option' ) ); ?></th>
									<th><?php echo $enc->html( $this->translate( 'client/jqadm', 'Value' ) ); ?></th>
									<th class="actions"><div class="btn btn-primary glyphicon glyphicon-plus"></div></th>
								</tr>
							</thead>
							<tbody>
<?php foreach( $this->item->getConfig() as $key => $value ) : ?>
								<tr>
									<td><input type="text" class="form-control" name="product.config[key][]" value="<?php echo $enc->attr( $key ); ?>"></td>
									<td><input type="text" class="form-control" name="product.config[val][]" value="<?php echo $enc->attr( $value ); ?>"></td>
									<td class="actions"><div class="btn btn-danger glyphicon glyphicon-trash"></div></td>
								</tr>
<?php endforeach; ?>
								<tr class="prototype">
									<td><input type="text" class="form-control" name="product.config[key][]" value=""></td>
									<td><input type="text" class="form-control" name="product.config[val][]" value=""></td>
									<td class="actions"><div class="btn btn-danger glyphicon glyphicon-trash"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="center-block">
		<button class="btn btn-primary">
			<?php echo $enc->html( $this->translate( 'client/jqadm', 'Save' ) ); ?>
		</button>
		<a class="btn btn-warning" href="<?php echo $enc->attr( $this->url( $listTarget, $listCntl, $listAction, $listParam, array( 'resource' => 'product' ), $listConfig ) ); ?>">
			<?php echo $enc->html( $this->translate( 'client/jqadm', 'Cancel' ) ); ?>
		</a>
	</div>
</form>
