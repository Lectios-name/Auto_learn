<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'email.required' => 'Укажите email.',
            'email.email' => 'Некорректный формат email.',
            'email.unique' => 'Этот email уже занят.',
            'avatar.image' => 'Аватар должен быть изображением.',
            'avatar.mimes' => 'Допустимые форматы: jpg, png, webp.',
            'avatar.max' => 'Изображение не должно превышать 2 МБ.',
        ];
    }
}