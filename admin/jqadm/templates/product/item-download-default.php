<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();

?>
<div class="product-item-download card panel">
	<div id="product-item-download" class="header card-header" role="tab" data-toggle="collapse" data-parent="#accordion" data-target="#product-item-download-data" aria-expanded="true" aria-controls="product-item-download-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Download' ) ); ?>
	</div>
	<div id="product-item-download-data" class="item-download card-block panel-collapse collapse table-responsive" role="tabpanel" aria-labelledby="product-item-download">
		<div class="col-lg-6">
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'File' ) ); ?></label>
				<div class="col-sm-9">
					<input class="item-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'product.lists.id' ) ) ); ?>"
						value="<?php echo $enc->attr( $this->get( 'downloadData/product.lists.id' ) ); ?>" />
					<input class="item-id" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.id' ) ) ); ?>"
						value="<?php echo $enc->attr( $this->get( 'downloadData/attribute.id' ) ); ?>" />
					<input class="fileupload" type="file" name="download[file]" />
				</div>
			</div>
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Status' ) ); ?></label>
				<div class="col-sm-9">
					<select class="form-control c-select item-status" name="<?php echo $enc->attr( $this->formparam( array( 'download', 'product.lists.status' ) ) ); ?>">
						<option value="1" <?php echo $selected( $this->get( 'downloadData/product.lists.status', 1 ), 1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:enabled' ) ); ?></option>
						<option value="0" <?php echo $selected( $this->get( 'downloadData/product.lists.status', 1 ), 0 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:disabled' ) ); ?></option>
						<option value="-1" <?php echo $selected( $this->get( 'downloadData/product.lists.status', 1 ), -1 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:review' ) ); ?></option>
						<option value="-2" <?php echo $selected( $this->get( 'downloadData/product.lists.status', 1 ), -2 ); ?>><?php echo $enc->html( $this->translate( 'admin', 'status:archive' ) ); ?></option>
					</select>
				</div>
			</div>
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Name' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-label" type="text"
						name="<?php echo $enc->attr( $this->formparam( array( 'download', 'attribute.label' ) ) ); ?>"
						value="<?php echo $enc->attr( $this->get( 'downloadData/attribute.label' ) ); ?>" />
				</div>
			</div>
			<div class="form-group row optional">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Replace file' ) ); ?></label>
				<div class="col-sm-9">
					<input class="form-control item-overwrite" type="checkbox"
						name="<?php echo $enc->attr( $this->formparam( array( 'download', 'overwrite' ) ) ); ?>" value="1"
						<?php echo $selected( $this->get( 'downloadData/overwrite' ), 1 ); ?> />
				</div>
			</div>
		</div>
		<div class="col-lg-6">
<?php if( $this->get( 'downloadData/path' ) != '' ) : ?>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Path' ) ); ?></label>
				<div class="col-sm-9">
					<p class="form-control-static item-file"><?php echo $enc->html( $this->get( 'downloadData/path' ) ); ?></p>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Size' ) ); ?></label>
				<div class="col-sm-9">
					<p class="form-control-static item-file"><?php echo $enc->html( number_format( $this->get( 'downloadData/size' ) / 1024, 0, '.', ' ' ) ); ?> KB</p>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Uploaded' ) ); ?></label>
				<div class="col-sm-9">
					<p class="form-control-static item-file"><?php echo $enc->html( date( 'Y-m-d H:i:s', $this->get( 'downloadData/time' ) ) ); ?></p>
				</div>
			</div>
<?php endif; ?>
		</div>
<?php echo $this->get( 'downloadBody' ); ?>
	</div>
</div>
