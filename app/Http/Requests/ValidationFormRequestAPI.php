<?php

namespace App\Http\Requests;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ValidationFormRequestAPI extends FormRequest
{
    use ResponseTrait;

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // throw (new ValidationException($validator))
        //     ->errorBag($this->errorBag)
        //     ->redirectTo($this->getRedirectUrl());

        throw new HttpResponseException(
            $this->responseError((new ValidationException($validator))->errors())
        );
    }
}
