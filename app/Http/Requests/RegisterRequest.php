<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $token = $this->input('g-recaptcha-response');
            $secret = config('services.recaptcha.secret_key');
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $body = $response->json();

            if (!$body['success'] || $body['score'] < config('services.recaptcha.score_threshold')) {
                $validator->errors()->add('g-recaptcha-response', 'reCAPTCHA verification failed. Please try again.');
            }
        });
    }
}