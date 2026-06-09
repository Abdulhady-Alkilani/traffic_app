<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:citizen,police'],
        ];

        if ($this->input('role') === 'police') {
            $rules['police_badge_number'] = ['required', 'string', 'max:255'];
            $rules['police_full_name'] = ['required', 'string', 'max:255'];
            $rules['police_rank'] = ['required', 'string', 'in:شرطي,عريف,رقيب,رقيب أول,مساعد,مساعد أول,ملازم,ملازم أول,نقيب,رائد,مقدم,عقيد,عميد,لواء'];
            $rules['police_department'] = ['required', 'string', 'max:255'];
        } else {
            $rules['national_id'] = ['required', 'string', 'max:255', 'unique:citizens_data,national_id'];
            $rules['full_name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:255'];
            $rules['blood_type'] = ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'];
        }

        return $rules;
    }
}
