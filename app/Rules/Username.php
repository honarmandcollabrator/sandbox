<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class Username extends BaseRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->validate($value, [
            'alpha_dash', 'regex:/(?!.*__.*)(^[a-zA-Z])([(a-zA-Z0-9_]*)([a-zA-Z0-9]$)/'], $attribute);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'فقط شامل حروف انگلیسی، اعداد و خط تیره بزرگ باشد، بدون فاصله باشد، با عدد و خط تیره شروع یا تمام نشود.';
    }

}
