<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'sender_id' => 'required|exists:accounts,id',
            'receiver_id' => 'required|exists:accounts,id',
            'amount' => 'required',
            'scheduled' => 'nullable',
            'schedule_date' => 'required_if:scheduled,true|date_format:Y-m-d'
        ];
    }
}
