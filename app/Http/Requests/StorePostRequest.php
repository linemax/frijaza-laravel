<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['string', 'required'],
            'read_time'=>['string', 'required'],
            'introduction'=>['string', 'required'],
            'body'=>['string', 'required'],
            'author_id'=>['uuid', 'required'],
            'category_id'=>['uuid', 'required'],
        ];
    }
}
