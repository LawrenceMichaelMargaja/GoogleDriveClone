<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use function Symfony\Component\Translation\t;

class ParentIdBaseRequest extends FormRequest
{
    public ?File $parent = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->parent = File::query()->where('id', $this->input('parent_id'))->first();
//        dd($this->parent);
        if ($this->parent && !$this->parent->isOwnedBy(Auth::id())) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        try {
            return [
                'parent_id' => [
                    Rule::exists(File::class, 'id')
                        ->where(function(Builder $query) {
                            return $query
                                ->where('is_folder', '=', '1')
                                ->where('created_by', '=', Auth::id());
                        })
                ]
            ];
        } catch (\Exception $e) {
            throw new \Exception("error running rules for ParentIdBaseRequest: " . $e->getMessage());
        }
    }
}
