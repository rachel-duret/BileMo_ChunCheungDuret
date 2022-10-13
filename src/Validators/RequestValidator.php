<?php

namespace App\Validators;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{


    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(Request $request)
    {

        $query = $request->query->all();
        $params = $request->get('_route_params');
        if ($query) {

            $constraints =  new Assert\Collection([
                "page" => [new Assert\Type(['type' => 'numeric'])],
                "limit" => [new Assert\Type(['type' => 'numeric'])]
            ]);
            $errors = $this->validator->validate($query, $constraints);
            if ($errors->count() > 0) {
                return $errors;
            }
        }

        if ($params) {
            $constraints =  new Assert\Collection([
                "id" => [new Assert\Type(['type' => 'numeric'])],
            ]);
            $errors = $this->validator->validate($params, $constraints);
            if ($errors->count() > 0) {
                return $errors;
            }
        }
    }
}
