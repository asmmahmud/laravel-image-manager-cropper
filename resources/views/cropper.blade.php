<div class="modal fade bd-example-modal-lg" id="image_cropper_modal"
		aria-labelledby="cropper_modalLabel" role="dialog" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog  modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="cropper_modalLabel"><i class="fa fa-edit"></i> Edit Image</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<img id="cropper_image" src="" style="max-width: 100%; height: auto;">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-primary" id="image_rotate">
								Rotate
							</button>
							<ul style="list-style: none">
								<li>Image naturalWidth: <span id="cropper_naturalWidth"></span></li>
								<li>Image naturalHeight: <span id="cropper_naturalHeight"></span></li>
								<li>Cropped Width: <span id="cropper_cropped_width"></span></li>
								<li>Cropped Height: <span id="cropper_cropped_height"></span></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="cropper_save_btn">Save</button>
			</div>
		</div>
	</div>
</div>