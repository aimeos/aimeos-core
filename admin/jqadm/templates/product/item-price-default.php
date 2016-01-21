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
		<div id="product-item-price-group" role="tablist" aria-multiselectable="true">

<?php foreach( (array) $this->get( 'priceData/price.currencyid', array() ) as $idx => $currencyid ) : ?>

			<div class="group-item card panel">
				<input type="hidden" name="price[product.lists.id][]" value="<?php echo $enc->attr( $this->get( 'priceData/product.lists.id/' . $idx ) ); ?>" />
				<div id="product-item-price-group-item-<?php echo $enc->attr( $idx ); ?>" class="card-header header collapsed" role="tab"
					data-toggle="collapse" data-target="#product-item-price-group-data-<?php echo $enc->attr( $idx ); ?>"
					aria-expanded="false" aria-controls="product-item-price-group-data-<?php echo $enc->attr( $idx ); ?>">
					<select class="combobox item-currencyid" name="price[price.currencyid][]">
						<option value="<?php echo $enc->attr( $currencyid ); ?>"><?php echo $enc->html( $currencyid ); ?></option>
					</select>
					<div class="btn btn-secondary fa fa-files-o"></div>
					<div class="btn btn-danger fa fa-trash"></div>
					<span class="item-label header-label"><?php echo $enc->html( $this->get( 'priceData/price.label/' . $idx ) ); ?></span>
				</div>
				<div id="product-item-price-group-data-<?php echo $enc->attr( $idx ); ?>" class="card-block panel-collapse collapse"
					role="tabpanel" aria-labelledby="product-item-price-group-item-<?php echo $enc->attr( $idx ); ?>">
					<div class="col-lg-6">
						<div class="form-group row mandatory">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Type' ) ); ?></label>
							<div class="col-lg-9">
								<select class="form-control c-select item-typeid" name="price[price.typeid][]">
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
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Label' ) ); ?></label>
							<div class="col-lg-9">
							<input class="form-control item-label" type="text" name="price[price.label][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Label' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.label/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Minimum quantity' ) ); ?></label>
							<div class="col-lg-9">
								<input class="form-control item-quantity" type="number" name="price[price.quantity][]" step="1" min="1" max="2147483647"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Minimum quantity' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.quantity/' . $idx, 1 ) ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group row mandatory">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Tax rate in %' ) ); ?></label>
							<div class="col-lg-9">
								<input class="form-control item-taxrate" type="text" name="price[price.taxrate][]" data-pattern="^[0-9]+(\.[0-9]+)?$"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Tax rate in %' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.taxrate/' . $idx, 0 ) ); ?>" />
							</div>
						</div>
						<div class="form-group row mandatory">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Actual current price' ) ); ?></label>
							<div class="col-lg-9">
								<input class="form-control item-value" type="text" name="price[price.value][]" data-pattern="^[0-9]+(\.[0-9]+)?$"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Actual current price' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.value/' . $idx, '0.00' ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Substracted rebate amount' ) ); ?></label>
							<div class="col-lg-9">
								<input class="form-control item-rebate" type="text" name="price[price.rebate][]" data-pattern="^([0-9]+(\.[0-9]+)?)?$"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Substracted rebate amount' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.rebate/' . $idx, '0.00' ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Costs per item' ) ); ?></label>
							<div class="col-lg-9">
								<input class="form-control item-costs" type="text" name="price[price.costs][]" data-pattern="^([0-9]+(\.[0-9]+)?)?$"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Costs per item' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'priceData/price.costs/' . $idx, '0.00' ) ); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>

<?php endforeach; ?>

		</div>

<?php echo $this->get( 'priceBody' ); ?>

	</div>
</div>
