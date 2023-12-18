<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilesActionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(Request $request, string $folder = null)
    {
        try {
//            echo phpinfo();
//            exit;
            if ($folder) {
                $folder = File::query()->where('created_by', Auth::id())
                    ->where('path', $folder)
                    ->firstOrFail();
            }

            if (!$folder) {
                $folder = $this->getRoot();
            }

            $files = File::query()
                ->where('parent_id', $folder->id)
                ->where('created_by', Auth::id())
                ->orderBy('is_folder', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $files = FileResource::collection($files);

            if($request->wantsJson()) {
                return $files;
            }

            $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

            $folder = new FileResource($folder);

//            dd($folder);

            return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
        } catch (\Exception $e) {
            throw new \Exception("show files page: " . $e->getMessage());
        }
    }

    public function createFolder(StoreFolderRequest $request)
    {

        // This die dump is not working when I create a folder. I don't know why yet.
//        dd($request);

        try {
            $data = $request->validated();
            $parent = $request->parent;
//            dd($parent);
            if (!$parent) {
                $parent = $this->getRoot();
            }

            $file = new File();
            $file->is_folder = 1;
            $file->name = $data['name'];

            $parent->appendNode($file);
        } catch (\Exception $e) {
            throw new \Exception("Error creating folder: " . $e->getMessage());
        }
    }

    /**
     * Fetches the "root" file (meaning the one created during registration).
     *
     * This is actually brilliant, the use-case it solves is when there
     * is no parent provided on the new folder to be created, it uses the "root"
     * as the parent. Basically, the File will then be a "top-level" file, with
     * only the root being a level higher.
     *
     * @return mixed
     * @throws \Exception
     */

    public function store(StoreFileRequest $request) {

        $data = $request->validated();

        // This dd tells me that the backend is getting the right data at this point.
//        dd($data);
//        dd($request->parent);

        $parent = $request->parent;
        $user = $request->user();
        $fileTree = $request->file_tree;

        // this dd doesn't match what the folders look like in the local machine.
//        dd($fileTree);

        if(!$parent) {
            $parent = $this->getRoot();
        }

        if(!empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                /** @var UploadedFile $file */
//                echo "fuck!";
                var_dump("the file", $file);
//                echo($file);
//                echo($this->saveFile($file, $user, $parent));
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    private function getRoot() {
        try {
            return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception("Error fetching root folder: " . $e->getMessage());
        }
    }

    public function saveFileTree($fileTree, $parent, $user) {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {

                $this->saveFile($file, $user, $parent);
            }
        }
    }


    public function destroy(FilesActionRequest $request) {
        $data = $request->validated();
        $parent = $request->parent;

        if($data['all']) {
            $children = $parent->children;

            foreach($children as $child) {
                $child->delete();
            }
        } else {
            foreach ($data['ids'] ?? [] as $id) {
                $file = File::find($id);
                if($file) {
                    $file->delete();
                }
            }
        }

        return to_route('myFiles', ['folder' => $parent->path]);
    }

    public function download(FilesActionRequest $request) {
        $data = $request->validated();
        $parent = $request->parent;

        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if(!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download.'
            ];
        }

        if($all) {
            $url = $this->createZip($parent->children);
            $fileName = $parent->name . '.zip';
        } else {
            if(count($ids) == 1) {
                $file = File::find($ids[0]);
                if($file->is_folder) {
                    if($file->children->count() == 0) {
                         return [
                             'message' => 'The folder is empty.'
                         ];
                    }
                    $url = $this->createZip($parent->children);
                    $fileName = $file->name . '.zip';
                } else {
                    $destination = 'public/' . pathinfo($file->storage_path);
                    Storage::copy($file->storage_path, $destination);

                    $url = asset(Storage::url($destination));
                    $fileName = $file->name;
                }
            } else {
                $files = File::query()->whereIn('id', $ids)->get();
                $url = $this->createZip($files);

                $fileName = $parent->name . '.zip';
            }
        }

        return [
            'url' => $url,
            'fileName' => $fileName
        ];
    }

    /**
     * @param $file
     * @param $user
     * @param $parent
     * @return void
     */
    public function saveFile($file, $user, $parent): void {
        $path = $file->store('/files/' . $user->id);

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        $parent->appendNode($model);
    }

    public function createZip($files): string {
        $zipPath = 'zip/' . Str::random() . '.zip';
        $publicPath = "public/$zipPath";

        if(!is_dir(dirname($publicPath))) {
            Storage::makeDirectory(dirname($publicPath));
        }

        $zipFile = Storage::path($publicPath);

        $zip = new \ZipArchive();

        if($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $this->addFilesToZip($zip, $files);
        }

        $zip->close();

        return asset(Storage::url($zipPath));
    }

    private function addFilesToZip($zip, $files, $ancestors) {
        foreach($files as $file) {
            if($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancestors . $file->name .'/');
            } else {
                $zip->addFile(Storage::path($file->store_path), $ancestors . $file->name);
            }
        }
    }
}
