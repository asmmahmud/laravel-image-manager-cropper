<div class="modal fade upload-modal" id="upload_files_modal" aria-labelledby="upload_modalLabel" role="dialog" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">X</span>
				</button>
				<label class="btn btn-primary file-btn" for="file_toupload" style="">Select an Image</label>
			</div>
			<div class="modal-body" style="">
				<div class="container-fluid">
					<div class="row upload-cropper-holder" >
						<img id="upload-cropper-img" src="{{ Voyager::image('others/placeholder-image.jpg') }}" />
					</div>
					<div class="row" id="progress-wrp-row" style="display: none;">
						<div class="col-md-12">
							<div id="progress-wrp"><div class="progress-bar"></div ><div class="status">0%</div></div>
							<div id="output"></div>
						</div>
					</div>
					<div class="row size-showing-row">
						<div class="col-sm-3">Original Width: <button class="btn btn-default" id="original_width"> </button></div>
						<div class="col-sm-3">Original Height: <button class="btn btn-default" id="original_height"> </button></div>
						<div class="col-sm-3">Cropped Width: <button class="btn btn-default" id="cropped_width"> </button></div>
						<div class="col-sm-3">Cropped Height: <button class="btn btn-default" id="cropped_height"> </button></div>
					</div>
					<div class="row aspect-ratio-btn-row">
						<div class="col-sm-12  aspect-ratio-btn-group">
							<button disabled class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (16/9) }}">16 : 9</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (8/5) }}">8 : 5</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (5/3) }}">5 : 3</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (4/3) }}">4 : 3</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (3/2) }}">3 : 2</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ 1 }}">1 / 1</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ '' }}">FREE</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (9/16) }}">9 : 16</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (5/8) }}">5 : 8</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (3/5) }}">3 : 5</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (3/4) }}">3 : 4</button>
							<button class="btn btn-primary aspect-ratio-btn" data-ratio="{{ (2/3) }}">2 : 3</button>
						</div>
					</div>
					<div id="devices-checkbox-group" class="checkbox-group form-group row">
						<div class="col-xs-10">
							<label class="checkbox-inline"><input class="device-checkbox" id="device-thumb" name="devices[]" type="checkbox" value="thumb">Thumbnail Image</label>
							<label class="checkbox-inline"><input class="device-checkbox" id="device-320" name="devices[]" type="checkbox" checked value="320">Small Mobiles</label>
							<label class="checkbox-inline"><input class="device-checkbox" id="device-480" name="devices[]" type="checkbox" checked value="480">Regular Mobiles</label>
							<label class="checkbox-inline"><input class="device-checkbox" id="device-768" name="devices[]" type="checkbox" checked value="768">Tablet Devices</label>
						</div>
						<div class="col-xs-2">
							<label for="image_quality">Image Quality</label>
							<input class="form-control" id="image_quality" type="text" value="50" />
						</div>
					</div>
					<div class="upload-thumb-holder" style="display: none;">
						<div class="form-group " >
							<input accept="image/*" type="file" class="form-control-file" id="file_toupload" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				
				<button type="button" id="submit_image" class="btn btn-success" >Submit</button>
			</div>
		</div>
	</div>
</div>