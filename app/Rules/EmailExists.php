<?php

namespace App\Rules;

use App\Recipient;
use Illuminate\Contracts\Validation\Rule;

class EmailExists implements Rule
{
    private $email;
    private $group_id;

    /**
     * Create a new rule instance.
     *
     * @param $email
     * @param $group_id
     */
    public function __construct($email, $group_id)
    {
        $this->email = $email;
        $this->group_id = $group_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $count = Recipient::withTrashed()
            ->whereId($value)
            ->whereEmail($this->email)
            ->whereGroupId($this->group_id)
            ->count();
        if ($count === 0) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '登録されたメールアドレスではありません。入力し直してください';
    }
}
