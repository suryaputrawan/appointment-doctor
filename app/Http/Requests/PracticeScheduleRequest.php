<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PracticeScheduleRequest extends FormRequest
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
            'date'              => ['array'],
            'date.*'            => ['required'],
            'start_time'        => ['array'],
            'start_time.*'      => ['required'],
            'end_time'          => ['array'],
            'end_time.*'        => ['required'],
            'duration'          => ['array'],
            'duration.*'        => ['required']
        ];
    }

    public function messages()
    {
        $messages = [];

        if ($this->get('day')) {
            foreach ($this->get('date') as $key => $val) {
                $messages["date.$key.required"] = "The date field is required";
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
