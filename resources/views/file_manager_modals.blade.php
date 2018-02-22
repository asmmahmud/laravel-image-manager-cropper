<!-- Move File Modal -->
<div class="modal fade modal-warning" id="move_file_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="voyager-move"></i> Move File/Folder</h4>
			</div>
			<div class="modal-body">
				<h4>Destination Folder</h4>
				<div class="input-group">
					<select class="form-control" id="move_folder_dropdown">
						<option value="">--Select--</option>
						<template v-for="dir in allDirectories">
							<option v-bind:value="dir">@{{ dir }}</option>
						</template>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-warning" id="move_btn">Move</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-warning" id="rename_file_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
				<h4 class="modal-title"><i class="glyphicon glyphicon-pencil"></i> Rename File/Folder</h4>
			</div>
			<div class="modal-body">
				<h4>New File/Folder Name</h4>
				<input id="rename_file_folder_name" class="form-control" type="text" value="" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-warning" id="rename_submit_btn">Rename</button>
			</div>
		</div>
	</div>
</div>