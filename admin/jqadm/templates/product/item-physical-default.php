<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$items = $this->get( 'physicalItems', array() );

$value = function( $type ) use ( $items ) {
	return ( isset( $items[$type] ) ? $items[$type]->getValue() : '' );
};

$enc = $this->encoder();

?>
<div class="product-item-physical card panel">
	<div id="product-item-physical" class="header card-header collapsed" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-physical-data" aria-expanded="false" aria-controls="product-item-physical-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Physical values' ) ); ?>
	</div>
	<div id="product-item-physical-data" class="item-physical card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-physical">
		<div class="col-lg-6">
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Length' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-package-length" type="text" name="physical[package-length]"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product length (yard, inch, etc.)' ) ); ?>"
						value="<?php echo $enc->attr( $this->param( 'physical/package-length', $value( 'package-length' ) ) ); ?>" >
				</div>
			</div>
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Width' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-package-width" type="text" name="physical[package-width]"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product width (yard, inch etc.)' ) ); ?>"
						value="<?php echo $enc->attr( $this->param( 'physical/package-width', $value( 'package-width' ) ) ); ?>" >
				</div>
			</div>
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Height' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-package-height" type="text" name="physical[package-height]"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product height (yard, inch, etc.)' ) ); ?>"
						value="<?php echo $enc->attr( $this->param( 'physical/package-height', $value( 'package-height' ) ) ); ?>" >
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Weight' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-package-weight" type="text" name="physical[package-weight]"
						placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product weight (pound, ounce, etc.)' ) ); ?>"
						value="<?php echo $enc->attr( $this->param( 'physical/package-weight', $value( 'package-weight' ) ) ); ?>" >
				</div>
			</div>
		</div>
<?php echo $this->get( 'physicalBody' ); ?>
	</div>
</div>
