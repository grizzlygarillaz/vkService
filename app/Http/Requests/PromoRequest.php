<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'promo_name' => 'required|max:55|unique:promo,name',
            'promoStart' => 'required|date_format:d.m.Y H:i|after:today',
            'promoEnd' => 'required|date_format:d.m.Y H:i|after:promoStart',
            'promo_images' => 'required|array',
            'promo_images.*' => 'image|mimes:png,jpg,jpeg',
            'promo_layout' => 'required|max:500'
        ];
    }
}
