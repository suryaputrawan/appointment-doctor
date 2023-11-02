<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorLocationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor'        => ['required'],
            'hospital'      => ['required'],
            'day'           => ['array'],
            'day.*'         => ['required', 'min:3'],
            'time'          => ['array'],
            'time.*'        => ['required', 'min:5']
        ];
    }

    public function messages()
    {
        $messages = [];

        if ($this->get('day')) {
            foreach ($this->get('day') as $key => $val) {
                $messages["day.$key.required"] = "The day field is required";
                $messages["day.$key.min"] = "The day field must be at least :min characters.";
            }
        }

        if ($this->get('time')) {
            foreach ($this->get('time') as $key => $val) {
                $messages["time.$key.required"] = "The time field is required";
                $messages["time.$key.min"] = "The time field must be at least :min characters.";
            }
        }
        return $messages;
    }
}
