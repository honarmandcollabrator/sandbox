<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain default error messages used by
    | validator class. Some of these rules have multiple versions such
    | as size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'باید معتبر باشد.',
    'active_url' => 'یک آدرس معتبر نیست.',
    'after' => 'باید یک تاریخ بعد از :date باشد.',
    'after_or_equal' => 'باید یک تاریخ بعد یا برابر با :date باشد.',
    'alpha' => 'باید فقط شامل حروف باشد.',
    'alpha_dash' => 'باید فقط شامل حروف، اعداد، خط تیره کوچک و بزرگ باشد.',
    'alpha_num' => 'باید فقط شامل اعداد و حروف باشد.',
    'array' => 'باید یک آرایه باشد.',
    'before' => 'باید یک تاریخ قبل از :date باشد.',
    'before_or_equal' => 'باید یک تاریخ قبل یا برابر با :date باشد.',
    'between' => [
        'numeric' => 'باید بین :min و :max باشد.',
        'file' => 'باید بین :min و :max کیلوبایت باشد.',
        'string' => 'باید بین :min و :max کارکتر باشد.',
        'array' => 'باید بین :min و :max آیتم.',
    ],
    'boolean' => 'باید صحیح یا غلط باشد.',
    'confirmed' => ' با هم هماهنگ نیستند.',
    'date' => 'یک تاریخ درست نیست.',
    'date_equals' => 'باید یک تاریخ برابر با :date باشد.',
    'date_format' => 'با فرمت :format همخوانی ندارد.',
    'different' => 'این مقدار و :other باید متفاوت باشند.',
    'digits' => 'باید :digits رقمی باشد.',
    'digits_between' => 'باید بین :min و :max رقم باشد.',
    'dimensions' => 'ابعاد تصویر درست نیست.',
    'distinct' => 'یک مقدار تکرارشده است.',
    'email' => 'باید یک آدرس ایمیل صحیح باشد.',
    'exists' => 'مورد انتخاب شده معتبر نیست.',
    'file' => 'باید یک فایل باشد.',
    'filled' => 'باید یک مقدار داشته باشد.',
    'gt' => [
        'numeric' => 'باید بالاتر از :value. باشد',
        'file' => 'باید بیش از :value کیلوبایت باشد.',
        'string' => 'باید بیشتر از :value کارکتر داشته باشد.',
        'array' => 'باید بیشتر از :value آیتم داشته باشد.',
    ],
    'gte' => [
        'numeric' => 'باید بیشتر یا مساوی :value باشد.',
        'file' => 'باید بیشتر یا برابر با  :value کیلوبایت باشد.',
        'string' => 'باید بیشتر یا برابر با  :value کارکتر باشد.',
        'array' => 'باید دارای :value آیتم یا بیشتر باشد.',
    ],
    'image' => 'بایت یک تصویر باشد.',
    'in' => 'مقدار انتخاب شده معتبر نیست.',
    'in_array' => 'در :other وجود ندارد.',
    'integer' => 'باید عدد صحیح باشد.',
    'ip' => 'باید یک آی پی معتبر باشد.',
    'ipv4' => 'باید یک آی پی معتبر باشد.',
    'ipv6' => 'باید یک آی پی معتبر باشد.',
    'json' => 'باید یک رشته json معتبر باشد.',
    'lt' => [
        'numeric' => 'باید کمتر از :value.',
        'file' => 'باید کمتر از :value کیلوبایت.',
        'string' => 'باید کمتر از :value کارکتر.',
        'array' => 'must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'باید کمتر یا مساوی با :value باشد.',
        'file' => 'باید کمتر یا مساوی با :value کیلوبایت باشد.',
        'string' => 'باید کمتر یا مساوی با :value کارکتر باشد.',
        'array' => 'نباید بیشتر از  :value آیتم داشته باشد.',
    ],
    'max' => [
        'numeric' => 'نباید بیشتر از  :max باشد.',
        'file' => 'نباید بیشتر از  :max کیلوبایت باشد.',
        'string' => 'نباید بیشتر از  :max کارکتر باشد.',
        'array' => 'نباید بیشتر از :max آیتم داشته باشد.',
    ],
    'mimes' => 'نوع فایل های انتخابی: :values.',
    'mimetypes' => 'نوع فایل: :values.',
    'min' => [
        'numeric' => 'باید حداقل :min باشد.',
        'file' => 'باید حداقل :min کیلوبایت باشد.',
        'string' => 'باید حداقل :min کارکتر باشد.',
        'array' => 'باید حداقل :min آیتم داشته باشد.',
    ],
    'not_in' => 'مورد انتخاب شده معتبر نیست.',
    'not_regex' => 'فرمت انتخاب شده معتبر نیست.',
    'numeric' => 'باید یک عدد باشد.',
    'present' => 'باید وجود داشته باشد.',
    'regex' => 'فرمت معتبر نیست.',
    'required' => 'این مورد نمیتواند خالی باشد.',
    'required_if' => 'زمانی که مقدار :other برابر با :value باشد، این مورد نمیتواند خالی باشد.',
    'required_unless' => 'زمانی که مقدار :other در :values باشد، این مورد نمیتواند خالی باشد.',
    'required_with' => 'زمانی که مقدار :values وجود داشته باشد این مورد نمیتواند خالی باشد.',
    'required_with_all' => 'زمانی که مقادیر :values وجود داشته باشند، این مورد نمیتواند خالی باشد.',
    'required_without' => 'زمانی که مقادیر :values وجود نداشته باشند، این مورد نمیتواند خالی باشد.',
    'required_without_all' => 'زمانی که هیچ کدام از مقادیر :values وجود نداشته باشند، این مقدار نمیتواند خالی باشد.',
    'same' => 'این مورد و  :other باید برابر باشند.',
    'size' => [
        'numeric' => 'باید برابر با :size باشد.',
        'file' => 'باید :size کیلوبایت باشد.',
        'string' => 'باید :size کارکتر داشته باشد.',
        'array' => 'باید دارای :size آیتم باشد.',
    ],
    'starts_with' => 'باید با یکی از مقادیر رو به رو شروع شود: :values',
    'string' => 'باید یک رشته باشد.',
    'timezone' => 'باید یک تایم زون معتبر باشد.',
    'unique' => 'این مورد گرفته شده است.',
    'uploaded' => 'آپلود موفق نبود.',
    'url' => 'فرمت معتبر نیست.',
    'uuid' => 'باید معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
