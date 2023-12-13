<?php

namespace App\Http\Requests;

use App\Models\File;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{

    protected function prepareForValidation()
    {
        $paths = array_filter($this->relative_paths ?? [], fn($f) => $f != null);

        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                function($attribute, $value, $fail) {
                    if(!$this->folder_name) {
                        /** @var $value \Illuminate\Http\UploadedFile */
                        $file = File::query()->where('name', $value->getClientOriginalName())
                            ->where('created_by', Auth::id())
                            ->where('parent_id', $this->parent_id)
                            ->whereNull('deleted_at')
                            ->exists();

                        if($file) {
                            $fail('File "' . $value->getClientOriginalName(). '" already exists');
                        }
                    }
                }
            ],
            'folder_name' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if($value) {
                        // This returns the proper folder name. hurray... progression
//                        dd($value);
                        /** @var $value \Illuminate\Http\UploadedFile */
                        $file = File::query()->where('name', $value)
                            ->where('created_by', Auth::id())
                            ->where('parent_id', $this->parent_id)
                            ->whereNull('deleted_at')
                            ->exists();

                        if($file) {
                            $fail('Folder "' . $value . '" already exists.');
                        }
                    }
                }
            ]
        ]);
    }

    public function detectFolderName($paths) {
        if(!$paths) {
            return null;
        }

        $parts = explode("/", $paths[0]);
        return $parts[0];
    }

    protected function passedValidation()
    {
        $data = $this->validated();

        // Dump here shows me that the file_paths are empty. You're getting closer. FAAN
//        dd("this->file_paths", $this->file_paths);

        dd($this);

        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files'])
        ]);
    }

    private function buildFileTree($filePaths, $files) {

        // why is the buildTree Function not dumped when I make a folder?
        // So... when I upload a folder... the dump does not work. However, when I upload a single file... the dump fires.
        // Additional info... the filePaths is empty... this could be the reason why the tree isn't matching what I see on my local machine.
//        dd("filePaths --- ", $filePaths);

        // dumping of files work and there is also data.
//        dd("the files --- ", $files);

        $filePaths = array_slice($filePaths, 0, count($files));
        $filePaths = array_filter($filePaths, fn($f) => $f != null);

        $tree = [];

        foreach ($filePaths as $ind => $filePath) {

            $currentNode = &$tree;
            $parts = explode('/', $filePath);
            foreach ($parts as $i => $part) {
                if(!isset($currentNode[$part])) {
                    $currentNode = [];
                }

                if($i == count($parts) - 1) {
                    $currentNode[$part] = $files[$ind];
                } else {
                    $currentNode = &$currentNode[$part];
                }
            }
        }

        return $tree;
    }
}

