<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-price card panel">
	<div id="product-item-price" class="header card-header collapsed" role="tab"
		data-toggle="collapse" data-parent="#accordion" data-target="#product-item-price-data"
		aria-expanded="false" aria-controls="product-item-price-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Prices' ) ); ?>
	</div>
	<div id="product-item-price-data" class="item-price card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-price">

<?php foreach( (array) $this->get( 'priceData/price.currencyid', array() ) as $idx => $currencyid ) : ?>

		<div id="product-item-price-group" role="tablist" aria-multiselectable="true">
			<input type="hidden" name="price[product.lists.id][]" value="<?php echo $enc->attr( $this->get( 'priceData/product.lists.id/' . $idx ) ); ?>" />
			<div class="card panel">
				<div id="product-item-price-group-item-<?php echo $enc->attr( $idx ); ?>" class="card-header collapsed" role="tab"
					data-toggle="collapse" data-target="#product-item-price-group-head-<?php echo $enc->attr( $idx ); ?>"
					aria-expanded="false" aria-controls="#product-item-price-group-head-<?php echo $enc->attr( $idx ); ?>">
					<?php echo $enc->html( $this->get( 'priceData/price.label/' . $idx ) ); ?>
					<div class="btn btn-secondary fa fa-files-o"></div>
					<div class="btn btn-danger fa fa-trash"></div>
				</div>
				<div id="product-item-price-group-data-<?php echo $enc->attr( $idx ); ?>" class="card-block panel-collapse collapse"
					role="tabpanel" aria-labelledby="product-item-price-group-head-<?php echo $enc->attr( $idx ); ?>">
					<div class="col-sm-6">
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
							<div class="col-sm-9">
							<input type="text" class="form-control" name="price[price.label][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Label' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.label/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Type' ) ); ?></label>
							<div class="col-sm-9">
								<select name="price[price.typeid][]" class="form-control combobox price-typeid">
<?php	foreach( (array) $this->get( 'priceTypes', array() ) as $typeId => $typeItem ) : ?>
<?php		if( $typeId == $this->get( 'priceData/price.typeid/' . $idx ) ) : ?>
									<option value="<?php echo $enc->attr( $typeId ); ?>" selected="selected"><?php echo $enc->html( $typeItem->getLabel() ); ?></option>
<?php		else : ?>
									<option value="<?php echo $enc->attr( $typeId ); ?>"><?php echo $enc->html( $typeItem->getLabel() ); ?></option>
<?php		endif; ?>
<?php	endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Currency' ) ); ?></label>
							<div class="col-sm-9">
								<select name="price[price.currencyid][]" class="form-control combobox price-currencyid">
									<option value="<?php echo $enc->attr( $currencyid ); ?>"><?php echo $enc->html( $currencyid ); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Quantity' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="price[price.quantity][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Minimum quantity' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.quantity/' . $idx ) ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Tax rate' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="price[price.taxrate][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Tax rate in percent' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.taxrate/' . $idx ) ); ?>" /></div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Value' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="price[price.value][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Price value' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.value/' . $idx ) ); ?>" /></div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Costs' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="price[price.costs][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Service costs' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.costs/' . $idx ) ); ?>" /></div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Rebate' ) ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="price[price.rebate][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Granted rebate value' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.rebate/' . $idx ) ); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

<?php endforeach; ?>

		</div>

<?php echo $this->get( 'textBody' ); ?>

	</div>
</div>
