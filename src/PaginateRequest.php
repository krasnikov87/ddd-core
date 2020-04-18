<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use App\Http\Requests\FormRequest;

/**
 * Class PaginateRequest
 * @package Krasnikov\EloquentJSON
 */
class PaginateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sort' => 'nullable|max:50',
            'page' => 'nullable|array',
            'page.number' => 'nullable|numeric',
            'page.size' => 'nullable|numeric',
        ];
    }
}
