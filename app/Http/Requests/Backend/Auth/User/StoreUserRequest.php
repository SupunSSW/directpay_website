<?php

namespace App\Http\Requests\Backend\Auth\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreUserRequest.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return $this->user()->isAdmin();
        if($this->user()->isAdmin() || $this->user()->can('view userManagement')){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'     => ['required','min:3', 'max:191'],
            'last_name'  => ['required','min:3', 'max:191'],
            'email'    => ['required', 'email', 'max:191', Rule::unique('users')],
            //'password' => ['required', 'min:6', 'confirmed'],
            'roles' => ['required', 'array'],
        ];
    }
}
