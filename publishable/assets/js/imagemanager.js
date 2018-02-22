var vm = new Vue({
    el: "#im_app",
    data: {
        items: [],
        curRelativePath: "/",
        selectedItems: [],
        allDirectories: [],
        showDetails: false
    },
    computed: {
        detailAnchorText: function () {
            if (this.showDetails) {
                return "Hide Details";
            } else {
                return "Show Details";
            }
        },
        styleObjectMain: function () {
            if (this.showDetails) {
                return {width: "80%"};
            } else {
                return {width: "100%"};
            }
        },
        styleObjectSideDetail: function () {
            if (this.showDetails) {
                return {width: "20%", display: "block"};
            } else {
                return {width: "0%", display: "none"};
            }
        },
        showFileDetails: function () {
            return (
                this.selectionDetails.fileSelected === 1 &&
                this.selectionDetails.directorySelected === 0
            );
        },
        relativePaths: function () {
            if (this.curRelativePath !== "" && this.curRelativePath !== "/") {
                return this.curRelativePath.split("/");
            }
            return [];
        },
        selectionDetails: function () {
            let details = {
                selectedItem: null,
                directorySelected: 0,
                fileSelected: 0
            };
            let selLength = this.selectedItems.length;
            for (let i = 0; i < selLength; i++) {
                if (this.items[this.selectedItems[i]].type === "directory") {
                    details.directorySelected++;
                } else {
                    details.fileSelected++;
                }
            }
            if (details.fileSelected === 1 && details.directorySelected === 0) {
                details.selectedItem = this.items[this.selectedItems[0]];
            }
            return details;
        }
    },
    mounted() {
        this.loadItems();
        this.loadIDirectoies();
    },
    methods: {
        refresh: function () {
            this.loadItems();
        },
        toggleDetail: function () {
            this.showDetails = !this.showDetails;
        },
        checkSelection: function (index, e) {
            let indexPos;
            if (e.ctrlKey) {
                indexPos = this.selectedItems.indexOf(index);

                if (indexPos >= 0) {
                    this.selectedItems.splice(indexPos, 1);
                } else {
                    this.selectedItems.push(index);
                }
            } else {
                this.selectedItems = [index];
            }
        },
        goInFromBread: function (index) {
            let newRelativePath;
            if (index >= 0) {
                newRelativePath = this.relativePaths
                    .slice(0, index + 1)
                    .join("/");
            } else {
                newRelativePath = "/";
            }
            this.loadItems(newRelativePath);
        },
        loadItems: function (relativePath) {
            if (!relativePath) {
                relativePath = this.curRelativePath;
            }
            $.ajax({
                method: "POST",
                url: defaultOp.baseUrl + "/imagemanager/files",
                data: {
                    dir_location: relativePath,
                    _token: defaultOp.CSRF_TOKEN
                },
                dataType: "json",
                context: this
            })
                .success(function (dirData) {
                    this.items = dirData.items;
                    this.curRelativePath = dirData.relativePath;
                    this.selectedItems = [];
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                });
        },
        loadIDirectoies: function () {
            $.ajax({
                url: defaultOp.baseUrl + "/imagemanager/directories",
                dataType: "json",
                context: this
            })
                .done(function (dirData) {
                    this.allDirectories = dirData;
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                });
        }
    }
});

function imageManager() {
    function makeRandom() {
        var text = "";
        var possible =
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (let i = 0; i < 20; i++) {
            text += possible.charAt(
                Math.floor(Math.random() * possible.length)
            );
        }
        return text;
    }

    $("#refresh").click(function () {
        vm.loadItems();
    });

    $("#new_folder").click(function () {
        $("#new_folder_modal").modal("show");
    });

    $("#new_folder_modal").on("shown.bs.modal", function () {
        $("#new_folder_name").focus();
    });

    $("#new_folder_name").keydown(function (e) {
        if (e.which == 13) {
            $("#new_folder_submit").trigger("click");
        }
    });
    $("#new_folder_submit").click(function () {
        var newFolderName = $("#new_folder_name").val();
        if (newFolderName.trim() === "") {
            return;
        }
        $.ajax({
            method: "POST",
            url: defaultOp.baseUrl + "/imagemanager/new_folder",
            data: {
                dir_location: vm.curRelativePath,
                new_folder_name: newFolderName.trim(),
                _token: defaultOp.CSRF_TOKEN
            },
            beforeSend: function () {
                $("#new_folder_submit").attr("disabled", true);
            }
        })
            .success(function (data) {
                if (data.success) {
                    toastr.success(
                        "successfully created " + $("#new_folder_name").val(),
                        "Sweet Success!"
                    );
                    vm.loadItems();
                    vm.loadIDirectoies();
                } else {
                    toastr.error(data.error, "Whoops!");
                }

                $("#new_folder_name").val("");
                $("#new_folder_submit").attr("disabled", false);
                $("#new_folder_modal").modal("hide");
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error(errorThrown, textStatus);
            });
    });
    /******************* RENAME SECTION *************************/
    $("#rename").click(function () {
        if (
            vm.selectedItems.length === 1 &&
            typeof vm.items[vm.selectedItems[0]] !== "undefined"
        ) {
            $("#rename_file_modal").modal("show");
            $("#rename_file_folder_name").val(
                vm.items[vm.selectedItems[0]].name
            );
        } else {
            if (vm.selectedItems.length > 1) {
                toastr.error("Select a single item.", "Whoops!");
            } else {
                if (vm.selectedItems.length === 0) {
                    toastr.error("Select an item.", "Whoops!");
                } else {
                    toastr.error("There seems to be a problem.", "Whoops!");
                }
            }
        }
    });
    $("#rename_file_modal").on("shown.bs.modal", function () {
        $("#rename_file_folder_name").focus();
    });
    $("#rename_file_folder_name").keydown(function (e) {
        if (e.which === 13) {
            $("#rename_submit_btn").trigger("click");
        }
    });
    $("#rename_submit_btn").click(function () {
        let renameFileFolderName = $("#rename_file_folder_name").val();
        if (
            !renameFileFolderName.trim() ||
            !(vm.selectedItems.length === 1 &&
                typeof vm.items[vm.selectedItems[0]] !== "undefined")
        ) {
            return;
        }
        $.ajax({
            method: "POST",
            url: defaultOp.baseUrl + "/imagemanager/rename_file",
            data: {
                dir_location: vm.curRelativePath,
                old_name: vm.items[vm.selectedItems[0]].name,
                new_name: renameFileFolderName.trim(),
                _token: defaultOp.CSRF_TOKEN
            },
            beforeSend: function () {
                $("#rename_submit_btn").attr("disabled", true);
            }
        })
            .success(function (data, textStatus, jqXHR) {
                if (data.success === true) {
                    toastr.success(
                        "File/Folder successfully renamed.",
                        "Success!"
                    );
                    vm.loadItems();
                    vm.loadIDirectoies();
                } else {
                    toastr.error(data.error, "Whoops!");
                }
                $("#rename_file_folder_name").val("");
                $("#rename_file_modal").modal("hide");
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error(errorThrown, textStatus);
            }).always(function () {
            $("#rename_submit_btn").attr("disabled", false);
        });
    });
    /******************* DELETE SECTION *************************/
    $("#delete").click(function () {
        if (vm.selectedItems.length <= 0) {
            toastr.error("Select an item.", "Whoops!");
        } else {
            $("#confirm_multi_delete_modal").modal("show");
        }
    });
    $("#confirm_delete_multi_files").click(function () {
        var files_to_delete = [],
            elementIndex,
            dirs_to_delete = [],
            dLength = vm.selectedItems.length;
        for (let i = 0; i < dLength; i++) {
            elementIndex = vm.selectedItems.pop();
            if (
                elementIndex !== false &&
                typeof vm.items[elementIndex] !== "undefined"
            ) {
                if (vm.items[elementIndex].type === "directory") {
                    dirs_to_delete.push(vm.items[elementIndex].name);
                } else {
                    files_to_delete.push(vm.items[elementIndex].name);
                }
            }
        }
        if (files_to_delete.length > 0 || dirs_to_delete.length > 0) {
            $.ajax({
                method: "POST",
                url: defaultOp.baseUrl + "/imagemanager/delete_multi_files",
                data: {
                    dir_location: vm.curRelativePath,
                    "files_to_delete[]": files_to_delete,
                    "dirs_to_delete[]": dirs_to_delete,
                    _token: defaultOp.CSRF_TOKEN
                },
                beforeSend: function () {
                    $("#confirm_delete_multi_files").attr("disabled", true);
                }
            })
                .success(function (data, textStatus, jqXHR) {
                    if (data.countDelfiles || data.countDelDirs) {
                        toastr.success(
                            data.countDelfiles +
                            " file(s) and " +
                            data.countDelDirs +
                            " directory(s) successfully deleted",
                            "Success!"
                        );
                        vm.loadItems();
                        if (data.countDelDirs) {
                            vm.loadIDirectoies();
                        }
                    } else {
                        if (data.fileError) {
                            toastr.error(data.fileError, "Whoops!");
                        } else {
                            if (data.dirError) {
                                toastr.error(data.dirError, "Whoops!");
                            } else {
                                toastr.error(textStatus, "Whoops!");
                            }
                        }
                    }
                    $("#confirm_delete_multi_files").attr("disabled", false);
                    $("#confirm_multi_delete_modal").modal("hide");
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                });
        }
    });
    /******************* Move SECTION *************************/
    $("#move").click(function () {
        if (vm.selectedItems.length <= 0) {
            toastr.error("Select alleast an item to move.", "Whoops!");
        } else {
            $("#move_file_modal").modal("show");
        }
    });
    $("#move_btn").click(function () {
        var files_to_move = [],
            elementIndex,
            dirs_to_move = [],
            dLength = vm.selectedItems.length,
            destination = $("#move_folder_dropdown").val();

        if (!destination.trim()) {
            return;
        }
        for (let i = 0; i < dLength; i++) {
            elementIndex = vm.selectedItems.pop();
            if (
                elementIndex !== false &&
                typeof vm.items[elementIndex] !== "undefined"
            ) {
                if (vm.items[elementIndex].type === "directory") {
                    dirs_to_move.push(vm.items[elementIndex].name);
                } else {
                    files_to_move.push(vm.items[elementIndex].name);
                }
            }
        }
        if (files_to_move.length > 0 || dirs_to_move.length > 0) {
            $.ajax({
                method: "POST",
                url: defaultOp.baseUrl + "/imagemanager/move_file",
                data: {
                    destination_dir: destination.trim(),
                    source_dir: vm.curRelativePath,
                    "files_to_move[]": files_to_move,
                    "dirs_to_move[]": dirs_to_move,
                    _token: defaultOp.CSRF_TOKEN
                },
                beforeSend: function () {
                    $("#move_btn").attr("disabled", true);
                }
            })
                .success(function (data, textStatus, jqXHR) {
                    if (data.countMovefiles || data.countMoveDirs) {
                        toastr.success(
                            data.countMovefiles +
                            " file(s) and " +
                            data.countMoveDirs +
                            " directory(s) successfully moved",
                            "Success!"
                        );
                        vm.loadItems();
                        vm.loadIDirectoies();
                    } else {
                        if (data.fileError) {
                            toastr.error(data.fileError, "Whoops!");
                        } else {
                            if (data.dirError) {
                                toastr.error(data.dirError, "Whoops!");
                            } else {
                                toastr.error(textStatus, "Whoops!");
                            }
                        }
                    }
                    $("#move_btn").attr("disabled", false);
                    $("#move_file_modal").modal("hide");
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, textStatus);
                });
        }
    });

    /********** UPLOAD SECTION **********************/
    function destroyCropper() {
        defaultOp.cropBoxData = defaultOp.cropperImage.cropper(
            "getCropBoxData"
        );
        defaultOp.canvasData = defaultOp.cropperImage.cropper("getCanvasData");
        defaultOp.cropperImage.cropper("destroy");
    }

    function resetCropper(aspRatio, srcUrl) {
        if (defaultOp.cropperImage.data("cropper")) {
            destroyCropper();
        }
        if (aspRatio === false) {
            aspRatio = 16 / 9;
        }
        if (srcUrl) {
            defaultOp.cropperImage.attr("src", srcUrl);
            let tempImage = new Image();
            tempImage.src = srcUrl;
            tempImage.onload = function () {
                $("#original_width").text(tempImage.naturalWidth.toFixed(2));
                $("#original_height").text(tempImage.naturalHeight.toFixed(2));
            };
        }
        defaultOp.cropperImage.cropper({
            aspectRatio: aspRatio,
            autoCropArea: 0.75,
            ready: function () {
                defaultOp.cropperImage.cropper(
                    "setCanvasData",
                    defaultOp.canvasData
                );
                defaultOp.cropperImage.cropper(
                    "setCropBoxData",
                    defaultOp.cropBoxData
                );
            },
            crop: function (e) {
                $("#cropped_width").text(e.width.toFixed(2));
                $("#cropped_height").text(e.height.toFixed(2));
            }
        });
    }

    function renderImage(fileOb) {
        var imageType = /^image\//, fileReader;
        if (!defaultOp.cropperImage.data("cropper")) {
            return;
        }
        if (!imageType.test(fileOb.type)) {
            return;
        }

        fileReader = new FileReader();
        fileReader.onload = function (event) {
            var theImgDataSrc = event.target.result,
                thisImageType,
                posof = theImgDataSrc.indexOf(";base64");
            if (posof > -1) {
                thisImageType = theImgDataSrc.substring(0, posof);
            } else {
                thisImageType = "image/jpeg";
            }
            resetCropper(false, theImgDataSrc);
            defaultOp.cropperImage.removeClass("not_active");
        };
        defaultOp.cropperImage.file = fileOb;
        defaultOp.cropperImage.file_name = makeRandom();
        fileReader.readAsDataURL(fileOb);
    }

    $("#upload").click(function (e) {
        $("#upload_files_modal").modal("show");
    });

    $("#upload_files_modal")
        .on("shown.bs.modal", function () {
            resetCropper(null, defaultOp.placeHolderImageUrl);
        })
        .on("hidden.bs.modal", function () {
            destroyCropper();
            defaultOp.cropperImage.addClass("not_active");
        });

    $("#file_toupload").change(function (e) {
        renderImage(this.files[0]);
    });
    $(".aspect-ratio-btn-group").on("click", ".btn", function (e) {
        if (
            !defaultOp.cropperImage.data("cropper") ||
            defaultOp.cropperImage.hasClass("not_active")
        ) {
            return;
        }
        $(".aspect-ratio-btn-group .btn").prop("disabled", false);
        $(this).prop("disabled", true);
        let ratio = $(this).data("ratio");
        if (ratio === 1) {
            $("#devices-checkbox-group .device-checkbox").prop(
                "checked",
                false
            );
            $("#device-thumb").prop("disabled", false).prop("checked", true);
        } else {
            $("#device-thumb").prop("disabled", true).prop("checked", false);
        }
        resetCropper(ratio);
    });

    $("#submit_image").on("click", function (e) {
        let devicesVersion = [],
            formData,
            checkboxCheckeds = $("#devices-checkbox-group .device-checkbox:checked");
        if (
            !defaultOp.cropperImage.data("cropper") ||
            defaultOp.cropperImage.hasClass("not_active") ||
            !defaultOp.cropperImage.file
        ) {
            return false;
        }

        if (checkboxCheckeds.length > 0) {
            let checkboxCheckedCount = checkboxCheckeds.length;
            for (let i = 0; i < checkboxCheckedCount; i++) {
                devicesVersion.push($(checkboxCheckeds[i]).val());
            }
        }
        if (!devicesVersion.length) {
            return false;
        }

        formData = new FormData();

        formData.append("upload_path", vm.curRelativePath);
        formData.append("image_quality", $("#image_quality").val());
        formData.append("devices_version", JSON.stringify(devicesVersion));
        formData.append("_token", defaultOp.CSRF_TOKEN);

        if (defaultOp.cropperImage.file.type === "image/png") {
            formData.append("ext", ".png");
            formData.append(
                "croppedImage",
                defaultOp.cropperImage
                    .cropper("getCroppedCanvas")
                    .toDataURL("image/png")
            );
        } else {
            if (defaultOp.cropperImage.file.type === "image/gif") {
                formData.append("ext", ".gif");
                formData.append(
                    "croppedImage",
                    defaultOp.cropperImage
                        .cropper("getCroppedCanvas")
                        .toDataURL("image/gif")
                );
            } else {
                formData.append("ext", ".jpg");
                formData.append(
                    "croppedImage",
                    defaultOp.cropperImage
                        .cropper("getCroppedCanvas")
                        .toDataURL("image/jpeg")
                );
            }
        }
        formData.append("file_name", defaultOp.cropperImage.file_name);
        $.ajax({
            url: defaultOp.baseUrl + "/imagemanager/upload_cropped",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $("#submit_image").prop("disabled", true);
            },
            xhr: function () {
                let xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener(
                        "progress",
                        function (event) {
                            let percent = 0,
                                position = event.loaded || event.position,
                                total = event.total;

                            if (event.lengthComputable) {
                                percent = Math.ceil(position / total * 100);
                            }

                            $("#progress-wrp-row").fadeIn();
                            $("#progress-wrp .progress-bar").css(
                                "width",
                                +percent + "%"
                            );
                            $("#progress-wrp " + " .status").text(
                                percent + "%"
                            );
                        },
                        true
                    );
                }
                return xhr;
            },
            mimeType: "multipart/form-data",
            error: function () {
                toastr.error("Upload error", "Whoopsie!");
            },
            success: function () {
                toastr.success("Upload success", "Sweet Success!");
                vm.loadItems();
            },
            complete: function () {
                $("#submit_image").prop("disabled", false);
                $("#progress-wrp-row").fadeOut();
            }
        });
    });

    function bytesToSize(bytes) {
        let sizes = ["Bytes", "KB", "MB", "GB", "TB"], i;
        if (bytes === 0) {
            return "0 Bytes";
        }
        i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10);
        return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    }
}

$(document).ready(function () {
    imageManager();
});
