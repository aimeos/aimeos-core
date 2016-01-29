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
		<div id="product-item-text-group" role="tablist" aria-multiselectable="true">

<?php foreach( (array) $this->get( 'textData/langid', array() ) as $idx => $langid ) : ?>

			<div class="group-item card panel">
				<div id="product-item-text-group-item-<?php echo $enc->attr( $idx ); ?>" class="card-header header collapsed" role="tab"
					data-toggle="collapse" data-target="#product-item-text-group-data-<?php echo $enc->attr( $idx ); ?>"
					aria-expanded="false" aria-controls="product-item-text-group-data-<?php echo $enc->attr( $idx ); ?>">
					<select class="combobox text-langid" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'langid', '' ) ) ); ?>">
						<option value="<?php echo $enc->attr( $langid ); ?>"><?php echo $enc->html( $langid ); ?></option>
					</select>
					<div class="btn btn-secondary fa fa-files-o"></div>
					<div class="btn btn-danger fa fa-trash"></div>
					<span class="item-name-content header-label"><?php echo $enc->html( $this->get( 'textData/name/content/' . $idx ) ); ?></span>
				</div>
				<div id="product-item-text-group-data-<?php echo $enc->attr( $idx ); ?>" class="card-block panel-collapse collapse"
					role="tabpanel" aria-labelledby="product-item-text-group-item-<?php echo $enc->attr( $idx ); ?>">
					<div class="col-lg-6">
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Product name' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-name-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'name', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/name/listid/' . $idx ) ); ?>" />
								<input class="form-control item-name-content" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'name', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Product name' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/name/content/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Short description' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-short-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'short', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/short/listid/' . $idx ) ); ?>" />
								<textarea class="form-control item-short-content" rows="2" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'short', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Short description' ) ); ?>" ><?php echo $enc->attr( $this->get( 'textData/short/content/' . $idx ) ); ?></textarea>
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Long description' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-long-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'long', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/long/listid/' . $idx ) ); ?>" />
								<textarea class="form-control htmleditor item-long-content" rows="6" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'long', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Long description' ) ); ?>" ><?php echo $enc->attr( $this->get( 'textData/long/content/' . $idx ) ); ?></textarea>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'URL segment' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-url-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'url', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/url/listid/' . $idx ) ); ?>" />
								<input class="form-control item-url-content" type="text" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'url', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'URL segment' ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/url/content/' . $idx ) ); ?>" />
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Meta keywords' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-meta-keyword-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'meta-keyword', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/meta-keyword/listid/' . $idx ) ); ?>" />
								<textarea class="form-control item-meta-keyword-content" rows="2" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'meta-keyword', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Meta keywords' ) ); ?>" ><?php echo $enc->attr( $this->get( 'textData/meta-keyword/content/' . $idx ) ); ?></textarea>
							</div>
						</div>
						<div class="form-group row optional">
							<label class="col-lg-3 form-control-label"><?php echo $enc->html( $this->translate( 'admin', 'Meta description' ) ); ?></label>
							<div class="col-lg-9">
								<input class="item-meta-description-listid" type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'meta-description', 'listid', '' ) ) ); ?>"
									value="<?php echo $enc->attr( $this->get( 'textData/meta-description/listid/' . $idx ) ); ?>" />
								<textarea class="form-control item-meta-description-content" rows="6" name="<?php echo $enc->attr( $this->formparam( array( 'text', 'meta-description', 'content', '' ) ) ); ?>"
									placeholder="<?php echo $enc->attr( $this->translate( 'admin', 'Meta description' ) ); ?>"><?php echo $enc->attr( $this->get( 'textData/meta-description/content/' . $idx ) ); ?></textarea>
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
