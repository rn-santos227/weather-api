<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $city = $this->route('city');

        if (is_string($city)) {
            $city = trim($city);
            $city = strip_tags($city);
            $city = preg_replace('/\s+/', ' ', $city);
            $city = str_replace('-', ' ', $city);
            $city = mb_convert_case($city, MB_CASE_TITLE, 'UTF-8');
        }

        $this->merge([
            'city' => $city,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-\.]+$/u',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'city.regex' => 'City name contains invalid characters.',
        ];
    }

    public function validationData(): array
    {
        return [
            ...$this->all(),
            'city' => $this->route('city'),
        ];
    }
}
