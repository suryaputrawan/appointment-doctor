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
            'doctor'            => ['required'],
            'hospital'          => ['required'],
            'day'               => ['array'],
            'day.*'             => ['required', 'min:3'],
            'start_time'        => ['array'],
            'start_time.*'      => ['required'],
            'end_time'          => ['array'],
            'end_time.*'        => ['required']
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

        if ($this->get('start_time')) {
            foreach ($this->get('start_time') as $key => $val) {
                $messages["start_time.$key.required"] = "The time field is required";
            }
        }

        if ($this->get('end_time')) {
            foreach ($this->get('end_time') as $key => $val) {
                $messages["end_time.$key.required"] = "The time field is required";
            }
        }
        return $messages;
    }
}
