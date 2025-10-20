<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'text' => ['required', 'string', 'max:100'],
            'media_path' => ['required', 'string', 'max:100'],
            'thumbnail_path' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp'],

        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'text' => 'Field text is riquerid.',
            'media_path' => 'Field text is riquerid.',
            'thumbnail_path' => 'Field text is riquerid.',

        ];
    }
}
