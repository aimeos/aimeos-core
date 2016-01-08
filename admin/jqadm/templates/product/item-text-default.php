<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

?>
<div class="product-item-text card panel">
	<div id="product-item-text" class="header card-header collapsed" role="tab"
		data-toggle="collapse" data-parent="#accordion" data-target="#product-item-text-data"
		aria-expanded="false" aria-controls="product-item-text-data">
		<?php echo $enc->html( $this->translate( 'admin', 'Texts' ) ); ?>
	</div>
	<div id="product-item-text-data" class="item-text card-block panel-collapse collapse" role="tabpanel" aria-labelledby="product-item-text">

<?php foreach( (array) $this->get( 'textData/langid', array() ) as $idx => $langid ) : ?>

		<div id="product-item-text-group" role="tablist" aria-multiselectable="true">
			<div class="card panel">
				<div id="product-item-text-group-item-<?php echo $enc->attr( $idx ); ?>" class="card-header collapsed" role="tab"
					data-toggle="collapse" data-target="#product-item-text-group-head-<?php echo $enc->attr( $idx ); ?>"
					aria-expanded="false" aria-controls="#product-item-text-group-head-<?php echo $enc->attr( $idx ); ?>">
					<select name="text[langid][]" class="form-control combobox text-langid">
						<option value="<?php echo $enc->attr( $langid ); ?>"><?php echo $enc->html( $langid ); ?></option>
					</select>
					<?php echo $enc->html( $this->get( 'textData/name/content/' . $idx ) ); ?>
					<div class="btn btn-secondary fa fa-files-o"></div>
					<div class="btn btn-danger fa fa-trash"></div>
				</div>
				<div id="product-item-text-group-data-<?php echo $enc->attr( $idx ); ?>" class="card-block panel-collapse collapse"
					role="tabpanel" aria-labelledby="product-item-text-group-head-<?php echo $enc->attr( $idx ); ?>">
					<div class="col-sm-6">
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Product name' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[name][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/name/listid/' . $idx ) ); ?>" />
								<input type="text" class="form-control" name="text[name][content][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product name' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/name/content/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Short description' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[short][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/short/listid/' . $idx ) ); ?>" />
								<textarea class="form-control" rows="2" name="text[short][content][]" placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Short description' ) ); ?>" >
									<?php echo $enc->attr( $this->get( 'textData/short/content/' . $idx ) ); ?>
								</textarea>
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Long description' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[long][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/long/listid/' . $idx ) ); ?>" />
								<textarea class="form-control htmleditor" rows="6" name="text[long][content][[]" placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Long description' ) ); ?>" >
									<?php echo $enc->attr( $this->get( 'textData/long/content/' . $idx ) ); ?>
								</textarea>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'URL segment' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[url][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/url/listid/' . $idx ) ); ?>" />
								<input type="text" class="form-control" name="text[url][content][]"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'URL segment' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/url/content/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Meta keywords' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[meta-keyword][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/meta-keyword/listid/' . $idx ) ); ?>" />
								<textarea class="form-control" rows="2" name="text[meta-keyword][content][]" placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Meta keywords' ) ); ?>" >
									<?php echo $enc->attr( $this->get( 'textData/meta-keyword/content/' . $idx ) ); ?>
								</textarea>
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-sm-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Meta description' ) ); ?></label>
							<div class="col-sm-9">
								<input type="hidden" name="text[meta-description][listid][]" value="<?php echo $enc->attr( $this->get( 'textData/meta-description/listid/' . $idx ) ); ?>" />
								<textarea class="form-control" rows="6" name="text[meta-description][content][]" placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Meta description' ) ); ?>">
									<?php echo $enc->attr( $this->get( 'textData/meta-description/content/' . $idx ) ); ?>
								</textarea>
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
