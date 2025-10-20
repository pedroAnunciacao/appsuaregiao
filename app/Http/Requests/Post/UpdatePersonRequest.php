<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'id' => ['required', 'integer'],
            'text' => ['required', 'string', 'max:100'],
            'media_path' => ['required', 'string', 'max:100'],
            'thumbnail_path' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp'],
        ];
        return $rules;
    }
    public function messages() {}
}
