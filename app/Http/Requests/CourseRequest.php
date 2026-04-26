<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
            'curricula_id'         => 'nullable|exists:curricula,id',
            'stage_id'         => 'nullable|exists:stages,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'country_id'       => 'nullable|exists:countries,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'semester'             => 'required|in:one,two,three',
            // 'type'             => 'required|in:online,recorded',
            // 'course_type'      => 'nullable|in:private,group',
            // 'count_student'    => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:255',
            'original_price'            => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0|max:100',
            'what_you_will_learn' => 'nullable|string',
            'file'         => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:5120|required_if:content_type,pdf,file',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // 'intro_video_url'  => 'nullable|url',
        ];
    }
}




