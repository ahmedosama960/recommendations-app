<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReadingIntervalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'=>['required',
                Rule::unique('reading_intervals')->where(function ($query){
                    return $query->where('user_id', request()->user_id)
                        ->where('book_id',request()->book_id)
                        ->where('start_page', request()->start_page)
                        ->where('end_page',request()->end_page);
                }),
                'exists:users,id'
            ],
            'book_id'=>[
                'required',
                'exists:books,id',
                'book_is_active'
            ],
            'start_page' => [
                'required',
                'integer',
                'min:1',
                'max:10000'
            ],
            'end_page'=>[
                'required',
                'integer',
                'min:1',
                'max:10000',
                'gt:start_page',
            ]
        ];
    }
    public function messages():array
    {
        return [
            'user_id.unique'=>'You have entered this interval before for this user',
            'start_page.unique'=>"You have entered",
            'book_id.book_is_active'=>"This book is not active to submit an interval"
        ]; // TODO: Change the autogenerated stub
    }
}
