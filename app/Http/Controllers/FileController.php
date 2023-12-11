<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(string $folder = null)
    {
        try {
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
                ->paginate(10);

            $files = FileResource::collection($files);

            $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

            $folder = new FileResource($folder);

            return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
        } catch (\Exception $e) {
            throw new \Exception("show files page: " . $e->getMessage());
        }
    }

    public function createFolder(StoreFolderRequest $request)
    {
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
    private function getRoot()
    {
        try {
            return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception("Error fetching root folder: " . $e->getMessage());
        }
    }
}
