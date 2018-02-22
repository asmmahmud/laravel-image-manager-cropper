@extends('imagemanager::master_clean')
@section('css')
    <link rel="stylesheet" href="{{ imagemanager_asset('css/imagemanager.css') }}">
    <link rel="stylesheet" href="{{ imagemanager_asset('lib/css/cropper.min.css') }}">
@endsection
@section('content')
    @include('imagemanager::datalist')
    <div class="page-content image-manager-page-content container-fluid">
        <div class="row">
            <div id="image-manager" class="col-md-12">
                <div class="admin-section-title">
                    <h3><i class="glyphicon glyphicon-picture"></i> Image Manager</h3>
                </div>
                <div class="clear"></div>
                <div id="im-toolbar">
                    <div class="btn-group offset-left">
                        <button type="button" class="btn btn-primary" id="upload">
                            <i class="glyphicon glyphicon-cloud-upload"></i>Upload
                        </button>
                        <button type="button" class="btn btn-default" id="refresh">
                            <i class="glyphicon glyphicon-refresh"></i></button>
                    </div>
                    <div class="btn-group offset-right">
                        <button type="button" class="btn btn-primary" id="new_folder">
                            <i class="glyphicon glyphicon-folder-close"></i>Add folder
                        </button>
                        <button type="button" class="btn btn-default" id="move"><i class="glyphicon glyphicon-move"></i>Move
                        </button>
                        <button type="button" class="btn btn-default" id="rename">
                            <i class="glyphicon glyphicon-pencil"></i>Rename
                        </button>
                        <button type="button" class="btn btn-warning" id="delete">
                            <i class="glyphicon glyphicon-trash"></i>Delete
                        </button>
                    </div>
                </div>
                <div id="im_app">
                    <div id="im-breadcrumb" class="row breadcrumb-container">
                        <ol class="breadcrumb">
                            <li v-on:click="goInFromBread(-1)">
                                <i class="glyphicon glyphicon-home"></i><strong>Home</strong>
                            </li>
                            <template v-for="(path,index) in relativePaths">
                                <li v-on:click="goInFromBread(index)"><strong>@{{ path }}</strong></li>
                            </template>
                        </ol>
                        <a href="#" class="see-details" v-on:click="toggleDetail">@{{ detailAnchorText }}</a>
                    </div>
                    <div id="im-content" class="row">
                        <div id="im-content-body" class="im-content-body" v-bind:style="styleObjectMain">
                            <template v-for="dirItem in items">
                                <div v-if="dirItem.type != 'directory'"
                                     v-bind:class="{ selected: (selectedItems.indexOf(dirItem.index) != -1), item: true, file: true}"
                                     v-bind:data-index="dirItem.index"
                                     v-on:click="checkSelection(dirItem.index, $event)">
                                    <img v-bind:src="dirItem.thumbnail" class="thumb"/>
                                    <div class="item-name">@{{ dirItem.name }}</div>
                                    <div class="item-dimention">@{{ dirItem.dimention }}</div>
                                </div>
                                <div v-else
                                     v-bind:class="{ selected: (selectedItems.indexOf(dirItem.index) != -1), item: true, dir: true}"
                                     v-bind:data-index="dirItem.index" v-on:dblclick="loadItems(dirItem.relative_path)"
                                     v-on:click="checkSelection(dirItem.index, $event)">
                                    <div class="folder"><i class="glyphicon glyphicon-folder-close"></i></div>
                                    <div class="item-name">@{{ dirItem.name }}</div>
                                </div>
                            </template>
                        </div>
                        <div id="item-details-info" class="item-details-info" v-bind:style="styleObjectSideDetail">
                            <ul>
                                <template v-if="showFileDetails">
                                    <li class="img">
                                        <img v-bind:src="selectionDetails.selectedItem.thumbnail" class="thumb"/>
                                    </li>
                                    <li class="item-name"><strong>@{{ selectionDetails.selectedItem.name }}</strong>
                                    </li>
                                    <li class="public_url"><a v-bind:href="selectionDetails.selectedItem.public_url"
                                                              target="_blank">Public Url</a></li>
                                    <li class="size"><strong>@{{ selectionDetails.selectedItem.size }}</strong></li>
                                    <li class="dimention"><strong>@{{ selectionDetails.selectedItem.dimention
                                            }}</strong></li>
                                    <li class="last_modified"><strong>@{{ selectionDetails.selectedItem.last_modified
                                            }}</strong></li>
                                </template>
                                <template
                                        v-else-if="selectionDetails.directorySelected === 0 && selectionDetails.fileSelected === 0">
                                    <li>Nothing is selected.</li>
                                </template>
                                <template v-else>
                                    <li> @{{ selectionDetails.directorySelected }} directory(s) and @{{
                                        selectionDetails.fileSelected }} file(s) are selected.
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @include('imagemanager::file_manager_modals')
                </div>
                @include('imagemanager::new_folder')
                @include('imagemanager::deletes')
                @include('imagemanager::upload')
            </div>
        </div>
    </div>
    <input type="hidden" id="storage_path" value="{{ storage_path() }}">
@stop
@section('javascript')
    <script src="{{ imagemanager_asset('lib/js/cropper.min.js') }}"></script>
    <script src="{{ imagemanager_asset('lib/js/vue.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(document).ajaxStart(function () {
                $('#imagemanager-loader').show();
            });
            $(document).ajaxComplete(function () {
                $('#imagemanager-loader').hide();
            });
        });
        var defaultOp = {
            baseUrl: "/{{ config('imagemanager.admin_url_prefix') }}",
            CSRF_TOKEN: $('meta[name="csrf-token"]').attr('content'),
            placeHolderImageUrl: "{{ imagemanager_asset('images/placeholder-image.jpg') }}",
            cropperImage: $('#upload-cropper-img').addClass('not_active'),
            fileDirSelector: '#im-content .item',
            breadcrumbId: '#im-breadcrumb',
            contentId: '#im-content',
            cropperOption: {
                aspectRatio: 16 / 9,
                autoCropArea: 0.75,
            },
            canvasData: '',
            cropBoxData: ''
        };
    </script>
    <script src="{{ imagemanager_asset('js/imagemanager.js') }}"></script>
@endsection