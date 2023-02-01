<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Tags\Tag;

class TagValidationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $tag;

    public function __construct($state)
    {
        $this->tag = $state;
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
        $tags = $value;

        if(str_contains($tags, ','))
        {
            return false;
        }

        $firstTag = strtolower(trim(str_replace('#', ' ', $tags)));

        $tag = Tag::where('name->en', $firstTag)->first();

        if($tag)
        {
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
        if(str_contains($this->tag, ','))
        {
            return 'Tag should not contain comma.';
        }

        return 'The tag already exists.';
    }
}
