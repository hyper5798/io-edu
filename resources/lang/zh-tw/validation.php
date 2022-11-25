\<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute 必須被接受。',
    'active_url' => ':attribute 不是有效的URL',
    'after' => ':attribute 必須是 :date 之後的日期。',
    'after_or_equal' => ':attribute 必須為 :date 以後的日期。',
    'alpha' => ' :attribute 只能包含字母。',
    'alpha_dash' => ':attribute 只能包含字母，數字，破折號和下劃線。',
    'alpha_num' => ':attribute 只能包含字母和數字。',
    'array' => ':attribute 必須是一個陣列。',
    'before' => ':attribute must 必須是 :date 之前的日期。',
    'before_or_equal' => ':attribute 必須是等於或小於 :date.',
    'between' => [
        'numeric' => ':attribute 必須在 :min 和 :max 之間。',
        'file' => ':attribute 必須在 :min 和 :max 千位元組之間。',
        'string' => ':attribute 須在 :min 和 :max 字元之間。',
        'array' => ':attribute 須在 :min 和 :max 個元素之間。',
    ],
    'boolean' => ' :attribute 欄位必須為true或false。',
    'confirmed' => ' :attribute 確認不匹配。',
    'date' => ' :attribute 不是有效日期。',
    'date_equals' => ' :attribute 必須是等於 :date.',
    'date_format' => ' :attribute 格式 :format 不匹配。',
    'different' => ' :attribute 和 :or 必須不同。',
    'digits' => ' :attribute 必須為 :digits 數字。',
    'digits_between' => ' :attribute 必須在 :min 和 :max 數字之間。',
    'dimensions' => ' :attribute 圖片尺寸無效。',
    'distinct' => ' :attribute 欄位具有重複值。',
    'email' => ' :attribute 必須是有效的電子郵件地址。',
    'ends_with' => ' :attribute 必須以下列之一結尾: :values',
    'exists' => '選定的 :attribute 無效。',
    'file' => ' :attribute 必須是檔案。',
    'filled' => ' :attribute 欄位必須有一個值。',
    'gt' => [
        'numeric' => ' :attribute 必須大於 :value。',
        'file' => ' :attribute 必須大於 :value 千位元組。',
        'string' => ' :attribute必須大於 :value 字元。',
        'array' => ' :attribute 必須大於 :value 個元素。',
    ],
    'gte' => [
        'numeric' => ' :attribute 必須大於或等於 :value。',
        'file' => ' :attribute 必須大於或等於 :value多千位元組。',
        'string' => ' :attribute 必須大於或等於 :value 字元。',
        'array' => ' :attribute 必須有 :value 個元素或者更多。',
    ],
    'image' => ' :attribute 必須是圖片。',
    'in' => '選定的 :attribute 無效。',
    'in_array' => ' :attribute 欄位不存在 :other 之中。',
    'integer' => ' :attribute 必須是一個整數。',
    'ip' => ' :attribute 必須是有效的 IP 位址.',
    'ipv4' => ' :attribute 必須是有效的 IPv4 位址.',
    'ipv6' => ' :attribute 必須是有效的 IPv6 位址.',
    'json' => ' :attribute 必須是有效的 JSON string.',
    'lt' => [
        'numeric' => ' :attribute 必須小於 :value.',
        'file' => ' :attribute 必須小於 :value 千位元組。',
        'string' => ' :attribute 必須小於 :value 字元。',
        'array' => ' :attribute 必須小於 :value 個元素。',
    ],
    'lte' => [
        'numeric' => ' :attribute 必須小於或等於 :value.',
        'file' => ' :attribute 必須小於或等於 :value 千位元組。',
        'string' => ' :attribute 必須小於或等於l :value 字元。',
        'array' => ' :attribute 必須不能大於 :value 個元素。',
    ],
    'max' => [
        'numeric' => ' :attribute 必須不能大於 :max.',
        'file' => ' :attribute 必須不能大於 :max 千位元組。',
        'string' => ' :attribute 必須不能大於 :max 字元。',
        'array' => ' :attribute 必須不能大於 :max 個元素。',
    ],
    'mimes' => ' :attribute 必須是一個文件類型: :values.',
    'mimetypes' => ' :attribute 必須是一個文件類型: :values.',
    'min' => [
        'numeric' => ' :attribute 必須最少 :min.',
        'file' => ' :attribute 必須最少 :min 千位元組。',
        'string' => ' :attribute 必須最少 :min 字元。',
        'array' => ' :attribute 必須最少 :min 個元素。',
    ],
    'not_in' => '選定的 :attribute 無效。',
    'not_regex' => ' :attribute format 無效。',
    'numeric' => ' :attribute 必須是數字。',
    'present' => ' :attribute 欄位必須存在。',
    'regex' => ' :attribute 格式無效。',
    'required' => ' :attribute 欄位為必填。',
    'required_if' => ' :attribute 欄位必填當 :other 是 :value。',
    'required_unless' => ' :attribute 欄位必填除非 :other 是在 :values 之中。',
    'required_with' => ' :attribute 欄位必填當 :values 存在。',
    'required_with_all' => ' :attribute 欄位必填當 :values 存在。',
    'required_without' => ' :attribute 欄位必填當 :values 不存在。',
    'required_without_all' => ' :attribute 欄位必填當 :values 都不存在時。',
    'same' => ' :attribute 和 :other 必填匹配。',
    'size' => [
        'numeric' => ' :attribute 必須 :size.',
        'file' => ' :attribute 必須 :size 千位元組。',
        'string' => ' :attribute 必須 :size 字元。',
        'array' => ' :attribute 必須包含 :size 個元素。',
    ],
    'starts_with' => ' :attribute 必須以下列其中一項開頭: :values',
    'string' => ' :attribute 必須是一個字串。',
    'timezone' => ' :attribute 必須有效的 zone.',
    'unique' => ' :attribute 已經有憑證',
    'uploaded' => ' :attribute 上傳失敗。',
    'url' => ' :attribute format 無效。',
    'uuid' => ' :attribute 必須有效的 UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
