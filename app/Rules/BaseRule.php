<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class BaseRule
{
    protected $validator = null;

    /**
     * @param mixed $value
     * @param array|string|Rule $rules
     * @param string $name Name of the property (optional)
     *
     * @return boolean
     */
    protected function validate($value, $rules, $name = 'variable')
    {
        if (!is_string($rules) && !is_array($rules)) {
            $rules = [$rules];
        }

        $this->validator = Validator::make([$name => $value], [$name => $rules]);

        return $this->validator->passes();
    }

    /**
     * @return null|Validator
     */
    protected function getValidator()
    {
        return $this->validator;
    }
}
