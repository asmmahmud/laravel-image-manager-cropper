<?php

namespace Tasmnaguib\Imagemanager\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Constraint;
use Illuminate\Support\Str;

class ImageManagerController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /** @var $this ->filesystem string */
    private $filesystem;
    /** @var $this ->fileSystemConfigPath string */
    private $fileSystemConfigPath;
    /** @var $this ->fileSystemRootDir string */
    private $fileSystemRootDir;
    private $storageDisk;
    private $thumbDir = '';
    private $directory = '';
    private $allowedMimeType = array();

    public function __construct()
    {
        $adminMiddleware = config('imagemanager.admin_middleware');
        if ($adminMiddleware) {
            $this->middleware(['web', $adminMiddleware]);
        } else {
            $this->middleware('web');
        }
        $this->filesystem = config('imagemanager.storage.disk');
        $this->fileSystemConfigPath = 'filesystems.disks.' . $this->filesystem;
        $this->fileSystemRootDir = config($this->fileSystemConfigPath . '.root');
        $this->storageDisk = Storage::disk($this->filesystem);
        $this->thumbDir = 'thumbs';
        $this->allowedMimeType = array('image/jpeg', 'image/gif', 'image/png');
    }

    protected function getFileDirPath($name)
    {
        return $this->fileSystemRootDir . DIRECTORY_SEPARATOR . $name;
    }

    public function index(Request $request)
    {
        return view('imagemanager::index', compact('is_image_browser'));
    }

    public function files(Request $request)
    {
        $dirLocation = $request->input('dir_location');
        $items = $this->getFiles($dirLocation);

        return response()->json(
            array('relativePath' => $dirLocation, 'items' => $items)
        );
    }

    public function new_folder(Request $request)
    {
        $dirLocation = $request->input('dir_location');
        $dirLocation = $this->preparePath($dirLocation);
        $newFolderName = $request->input('new_folder_name');

        $success = false;
        $error = '';
        if (empty($newFolderName)) {
            $error = 'Folder name is empty.';

            return compact('success', 'error');
        }

        if ($dirLocation != '') {
            $newFolderPath = $dirLocation . '/' . $newFolderName;
        } else {
            $newFolderPath = $newFolderName;
        }

        if ($this->storageDisk->exists($newFolderPath)) {
            $error = 'Folder (' . $newFolderName . ') already exists.';
        } elseif ($this->storageDisk->makeDirectory($newFolderPath)) {
            $success = true;
        } else {
            $error = 'Something went wrong, please check your permissions.';
        }

        return compact('success', 'error');
    }

    protected function preparePath($path, $before = true, $after = true)
    {
        if ($before && strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }
        if ($after && strrpos($path, '/') === (strlen($path) - 1)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    public function rename_file(Request $request)
    {
        $dirLocation = $request->input('dir_location');
        $dirLocation = $this->preparePath($dirLocation);
        $oldName = $request->input('old_name');
        $newName = $request->input('new_name');
        $success = false;
        $error = false;

        if ($dirLocation != '') {
            $oldFullPath = $dirLocation . '/' . $oldName;
            $newFullPath = $dirLocation . '/' . $newName;
        } else {
            $oldFullPath = $oldName;
            $newFullPath = $newName;
        }

        if (!$this->storageDisk->exists($newFullPath)) {
            if ($this->storageDisk->exists($oldFullPath) && $this->storageDisk->move($oldFullPath, $newFullPath)) {
                $success = true;
            } else {
                $error = 'something went wrong, please check the correct permissions.';
            }
        } else {
            $error = 'File/Folder may already exist with that name. Please choose another name or delete the other file.';
        }

        return compact('success', 'error');
    }

    public function delete_multi_files(Request $request)
    {
        $dirLocation = $request->input('dir_location');
        $dirLocation = $this->preparePath($dirLocation);

        $filesToDelete = $request->input('files_to_delete');
        $dirsToDelete = $request->input('dirs_to_delete');

        $countSelectedFiles = count($filesToDelete);
        $countSelectedDirs = count($dirsToDelete);

        $countDelfiles = $countDelDirs = 0;
        $fileError = $dirError = "";
        if ($countSelectedFiles) {
            try {
                foreach ($filesToDelete as $itemToDelete) {
                    if ($dirLocation) {
                        $fullPath = $dirLocation . '/' . $itemToDelete;
                    } else {
                        $fullPath = $itemToDelete;
                    }

                    if ($this->storageDisk->delete($fullPath)) {
                        $countDelfiles++;
                    }
                }
            } catch (\Exception $e) {
                $fileError = $e->getMessage();
            }
        }
        if ($countSelectedDirs) {
            try {
                foreach ($dirsToDelete as $itemToDelete) {
                    if ($dirLocation) {
                        $fullPath = $dirLocation . '/' . $itemToDelete;
                    } else {
                        $fullPath = $itemToDelete;
                    }

                    if ($this->storageDisk->deleteDirectory($fullPath)) {
                        $countDelDirs++;
                    }
                }
            } catch (\Exception $e) {
                $dirError = $e->getMessage();
            }
        }

        return compact('fileError', 'dirError', 'countDelDirs', 'countDelfiles');

    }

    // GET ALL DIRECTORIES Working with Laravel 5.3
    public function get_all_dirs()
    {
        return response()->json($this->storageDisk->directories(null, true));
    }

    // NEEDS TESTING
    public function move_file(Request $request)
    {

        $destinationDir = $request->input('destination_dir');
        $destinationDir = $this->preparePath($destinationDir);

        $sourceDir = $request->input('source_dir');
        $sourceDir = $this->preparePath($sourceDir);

        $filesToMove = $request->input('files_to_move');
        $dirsToMove = $request->input('dirs_to_move');
/*        logger($filesToMove);
        logger($dirsToMove);*/
        $countSelectedFiles = count($filesToMove);
        $countSelectedDirs = count($dirsToMove);

        $countMovefiles = $countMoveDirs = 0;
        $fileError = $dirError = "";
        if ($countSelectedFiles) {
            try {
                foreach ($filesToMove as $item) {
                    if ($sourceDir) {
                        $sourceFullPath = $sourceDir . '/' . $item;
                    } else {
                        $sourceFullPath = $item;
                    }
                    if ($destinationDir) {
                        $destinationFullPath = $destinationDir . '/' . $item;
                    } else {
                        $destinationFullPath = $item;
                    }
/*                    logger($sourceFullPath . '--' . $destinationFullPath);*/
                    if ($this->storageDisk->move($sourceFullPath, $destinationFullPath)) {
                        logger('moved file: ' . $sourceFullPath . ' to ' . $destinationFullPath);
                        $countMovefiles++;
                    }
                }
            } catch (\Exception $e) {
                $fileError = $e->getMessage();
                logger($fileError);

            }
        }
        if ($countSelectedDirs) {
            try {
                foreach ($dirsToMove as $item) {
                    if ($sourceDir) {
                        $sourceFullPath = $sourceDir . '/' . $item;
                    } else {
                        $sourceFullPath = $item;
                    }
                    if ($destinationDir) {
                        $destinationFullPath = $destinationDir . '/' . $item;
                    } else {
                        $destinationFullPath = $item;
                    }

                    logger($sourceFullPath . '--' . $destinationFullPath);
                    if ($this->storageDisk->move($sourceFullPath, $destinationFullPath)) {
                        logger('moved file: ' . $sourceFullPath . ' to ' . $destinationFullPath);
                        $countMoveDirs++;
                    }
                }
            } catch (\Exception $e) {
                $dirError = $e->getMessage();
                logger($dirError);
            }
        }
        return compact('fileError', 'dirError', 'countMoveDirs', 'countMovefiles');
    }

    public function uploadCropped(Request $request)
    {
        $croppedImage = $request->input('croppedImage');
        $uploadPathBase = $request->input('upload_path');
        $uploadPathBase = $this->preparePath($uploadPathBase);
        $devicesVersion = json_decode($request->input('devices_version', ''));

        $imageQuality = $request->input('image_quality');
        $fileName = $request->input('file_name');
        $extension = $request->input('ext');
        if (!$fileName) {
            $fileName = Str::random(25);
        }
        if (!$extension) {
            $extension = '.jpg';
        }
/*        $interventionImage = Image::make((string)$croppedImage);*/
        $configQuality = config('imagemanager.quality');

        if (!$imageQuality && isset($configQuality) && $configQuality) {
            $imageQuality = $configQuality;
        } else if (!$imageQuality) {
            $imageQuality = 50;
        }

        $storagePathPrefix = $this->storageDisk->getDriver()->getAdapter()->getPathPrefix();

        if (count($devicesVersion)) {
            $uploadPath = $uploadPathBase;
            $devicesVersion[] = 'cropped';
            foreach ($devicesVersion as $version) {
                if ($version === 'thumb') {
                    $verDir = $version;
                    $verSize = config('imagemanager.thumbnail_size');
                } else if ($version === 'cropped') {
                    $verDir = '';
                    $verSize = false;
                } else {
                    $verDir = $version . 'px';
                    $verSize = $version;
                }
                if ($uploadPath && $verDir) {
                    $uploadPath .= '/' . $verDir . '/';
                } else if ($uploadPath) {
                    $uploadPath .= '/';
                } else if ($verDir) {
                    $uploadPath = $verDir . '/';
                }

                if (!$this->storageDisk->exists($uploadPath)) {
                    $this->storageDisk->makeDirectory($uploadPath);
                }

                $filePath = $storagePathPrefix . $uploadPath . $fileName . $extension;

                if ($verSize) {
                    Image::make((string)$croppedImage)->resize(
                        $verSize,
                        null,
                        function (Constraint $constraint) {
                            $constraint->aspectRatio();
                        })->save($filePath, $imageQuality);
                } else {
                    Image::make((string)$croppedImage)->save($filePath, $imageQuality);
                }
                $uploadPath = $uploadPathBase;
            }
        }

        $success = true;
        $message = 'Successfully uploaded new file!';
        return response()->json(compact('success', 'message'));
    }

    protected function uploadRescale($file, $filePath, $imageQuality, $width = null)
    {
        $smallImage = Image::make($file)->resize(
            null,
            null,
            function (Constraint $constraint) {
                $constraint->aspectRatio();
            });

        $smallImage->save($filePath, $imageQuality);
    }

    private function getImageThumb($file)
    {
        $path_parts = pathinfo($file);
        if (count($path_parts) == 4) {
            $fileName = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $this->thumbDir . DIRECTORY_SEPARATOR . $path_parts['filename'] . $path_parts['extension'];
            if ($this->storageDisk->exists($fileName)) {
                return $this->storageDisk->url($fileName);
            }
        }

        return $this->storageDisk->url($file);
    }

    private function getFiles($dir)
    {
        $files = [];
        $storageFiles = $this->storageDisk->files($dir);
        $storageFolders = $this->storageDisk->directories($dir);
        $index = 0;
        foreach ($storageFiles as $file) {
            $fileMimeType = $this->storageDisk->mimeType($file);
            if (!in_array($fileMimeType, $this->allowedMimeType)) {
                continue;
            }
            $imageInfo = getimagesize($this->getFileDirPath($file));
            $imageOriWidth = 0;
            if (isset($imageInfo[0])) {
                $imageOriWidth = $imageInfo[0];
            }
            $imageOriHeight = 0;
            if (isset($imageInfo[1])) {
                $imageOriHeight = $imageInfo[1];
            }
            $lastMod = $this->storageDisk->lastModified($file);
            $lastMod = \Carbon\Carbon::createFromTimestamp($lastMod, config('app.timezone'));
            $files[] = [
                'index' => $index,
                'is_selected' => false,
                'name' => strpos($file, '/') > 1 ? str_replace('/', '', strrchr($file, '/')) : $file,
                'type' => $fileMimeType,
                'public_url' => $this->storageDisk->url($file),
                'thumbnail' => $this->getImageThumb($file),
                'relative_path' => $file,
                'size' => $this->storageDisk->size($file),
                'last_modified' => $lastMod->format('Y-m-d  h:i:s A'),
                'dimention' => (($imageOriWidth && $imageOriHeight) ? $imageOriWidth . ' x ' . $imageOriHeight : null),
            ];
            $index++;
        }

        foreach ($storageFolders as $folder) {
            $files[] = [
                'index' => $index,
                'is_selected' => false,
                'name' => strpos($folder, '/') > 1 ? str_replace('/', '', strrchr($folder, '/')) : $folder,
                'type' => 'directory',
                'relative_path' => $folder,
                'items' => '',
                'last_modified' => '',
            ];
            $index++;
        }
        return $files;
    }
}
