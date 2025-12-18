<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'    => 'required|exists:courses,id',
            'title'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',

            // مش required
            'content_type' => 'nullable|in:video,pdf,file,zoom',

            'content_link' => 'sometimes|required_if:content_type,video,zoom|url',
            'file'         => 'sometimes|required_if:content_type,pdf,file|file|mimes:pdf,doc,docx,ppt,pptx|max:5120',

            'session_date' => 'nullable|date',
            'session_time' => 'nullable|date_format:H:i',
        ];
    }
}
    