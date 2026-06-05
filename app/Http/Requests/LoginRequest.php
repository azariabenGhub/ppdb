<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
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