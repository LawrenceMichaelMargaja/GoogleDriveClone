<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFolderRequest extends ParentIdBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Point of interest:
     * The problem is this parent_id, when it's not being called, when we add this validation,
     * the validation fails.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        try {
            return array_merge(parent::rules(),
                [
                    'name' => [
                        'required',
                        Rule::unique(File::class, 'name')
                            ->where('created_by', Auth::id())
                            ->where('parent_id', $this->parent_id)
                            ->whereNull('deleted_at')
                    ]
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception("error running rules for StoreFolderRequest: " . $e->getMessage());
        }
    }

    public function messages() {
        return [
            'name.unique' => 'Folder ":input" already exists'
        ];
    }
}
