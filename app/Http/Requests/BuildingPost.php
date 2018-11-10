<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class BuildingPost
 *
 * @property string $type
 * @property int $level
 * @property int $number
 * @property int $key
 * @package App\Http\Requests
 */
class BuildingPost extends FormRequest
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

    public function attributes()
    {
        return [
            'type' => '类型',
            'level' => '级别',
            'number' => '数量',
            'key' => '建筑队序列',
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|string|max:20',
            'level' => 'required|numeric|min:1',
            'number' => 'numeric|min:1',
            'key' => 'numeric|min:0',
        ];
    }
}
