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
            'image' => [
                'nullable',
                function($attribute, $value, $fail) {
                    // لو هو رابط URL
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        return; // الرابط صحيح، يبقى مقبول
                    }

                    // لو هو ملف
                    if (request()->hasFile('image')) {
                        $file = request()->file('image');
                        if (!$file->isValid() || !in_array($file->extension(), ['jpg','jpeg','png'])) {
                            $fail($attribute.' must be a valid image file.');
                        }
                    } else {
                        $fail($attribute.' must be a valid image URL or uploaded file.');
                    }
                }
            ],
            // 'intro_video_url'  => 'nullable|url',
        ];
    }
}




