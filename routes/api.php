<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CourseCommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseDetailController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StudentCommentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherCommentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\WithdrawRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



//                                            ADMIN
Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware('auth:admins')->group(function () {
    Route::get('/admin/check-auth', [AdminController::class, 'checkAuth']);
    Route::post('/admin/logout', [AdminController::class, 'logout']);
});
Route::get('admin/dashboard-stats', [AdminController::class, 'index']);



//////////////////////////////////////////////////////////Teacher//////////////////////////////////////

Route::post('teachers/register', [TeacherController::class, 'register']);
Route::post('teachers/login', [TeacherController::class, 'login']);
Route::middleware('auth:sanctum')->get('teachers/check-auth', [TeacherController::class, 'checkAuth']);
Route::middleware('auth:sanctum')->post('teachers/update-profile', [TeacherController::class, 'updateProfile']);

Route::post('teacher/index', [TeacherController::class, 'index']);
Route::post('teacher/restore', [TeacherController::class, 'restore']);
Route::delete('teacher/delete', [TeacherController::class, 'destroy']);
Route::delete('teacher/force-delete', [TeacherController::class, 'forceDelete']);
Route::post('teacher/update/{teacher}', [TeacherController::class, 'forceUpdate']);
Route::put('/teacher/{id}/{column}', [TeacherController::class, 'toggle']);
Route::apiResource('teacher', TeacherController::class);
Route::middleware('auth:admins')->group(function () {
    Route::put('/teachers/{id}/commission', [TeacherController::class, 'updateCommission']);
});



//////////////////////////////////////////////////////////Teacher//////////////////////////////////////



//////////////////////////////////////////////////////////Course//////////////////////////////////////
Route::post('course/index', [CourseController::class, 'index']);
Route::middleware(['auth:admins,teachers'])->group(function () {
    Route::post('course-teacher/index', [CourseController::class, 'indexTeacher']);
    Route::post('course/restore', [CourseController::class, 'restore']);
    Route::delete('course/delete', [CourseController::class, 'destroy']);
    Route::delete('course/force-delete', [CourseController::class, 'forceDelete']);
    Route::post('course/update/{course}', [CourseController::class, 'forceUpdate']);
    Route::put('/course/{id}/{column}', [CourseController::class, 'toggle']);
    Route::apiResource('course', CourseController::class);
});
Route::delete('/courses/{course_id}/remove-student/{student_id}', [CourseController::class, 'removeStudentFromCourse']);

//////////////////////////////////////////////////////////Course//////////////////////////////////////


//////////////////////////////////////////////////////////Course Details//////////////////////////////////////
    Route::post('course-detail/index', [CourseDetailController::class, 'index']);
    Route::post('course-detail/restore', [CourseDetailController::class, 'restore']);
    Route::delete('course-detail/delete', [CourseDetailController::class, 'destroy']);
    Route::delete('course-detail/force-delete', [CourseDetailController::class, 'forceDelete']);
    Route::post('course-detail/update/{course_detail}', [CourseDetailController::class, 'forceUpdate']);
    Route::put('/course-detail/{id}/{column}', [CourseDetailController::class, 'toggle']);
    Route::apiResource('course-detail', CourseDetailController::class);
    Route::middleware('auth:students')->group(function () {
        Route::post('student/course-detail/watch', [CourseDetailController::class, 'saveWatchingData']);
    });

    Route::get('student-course/{course}', [CourseController::class, 'show']);
//////////////////////////////////////////////////////////Course Details//////////////////////////////////////



//////////////////////////////////////////////////////////Message//////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::post('chat/send', [MessageController::class, 'sendMessage']);
    Route::get('chat/messages', [MessageController::class, 'getMessages']);
    Route::post('courses/{course}/comments', [CourseCommentController::class, 'store']);
    Route::post('teachers/{teacher}/comments', [TeacherCommentController::class, 'store']);
    Route::post('student/{student}/comment', [StudentCommentController::class, 'store']);
});

//////////////////////////////////////////////////////////Message//////////////////////////////////////







//////////////////////////////////////////////////////////country//////////////////////////////////////

Route::post('/country/index', [CountryController::class, 'index']);
Route::put('/country/{id}/{column}', [CountryController::class, 'toggle']);
Route::apiResource('country', CountryController::class);
//////
Route::get('/country-public-list', [CountryController::class, 'publicIndex']);

//////////////////////////////////////////////////////////country//////////////////////////////////////

//////////////////////////////////////////////////////////Curriculum//////////////////////////////////////

Route::post('curriculum/index', [CurriculumController::class, 'index']);
Route::post('curriculum/restore', [CurriculumController::class, 'restore']);
Route::delete('curriculum/delete', [CurriculumController::class, 'destroy']);
Route::delete('curriculum/force-delete', [CurriculumController::class, 'forceDelete']);
Route::post('curriculum/update/{curriculum}', [CurriculumController::class, 'forceUpdate']);
Route::put('/curriculum/{id}/{column}', [CurriculumController::class, 'toggle']);
Route::apiResource('curriculum', CurriculumController::class);

//////////////////////////////////////////////////////////Curriculum//////////////////////////////////////



//////////////////////////////////////////////////////////Stage//////////////////////////////////////

Route::post('stage/index', [StageController::class, 'index']);
Route::post('stage/restore', [StageController::class, 'restore']);
Route::delete('stage/delete', [StageController::class, 'destroy']);
Route::delete('stage/force-delete', [StageController::class, 'forceDelete']);
Route::post('stage/update/{stage}', [StageController::class, 'forceUpdate']);
Route::put('/stage/{id}/{column}', [StageController::class, 'toggle']);
Route::apiResource('stage', StageController::class);

//////////////////////////////////////////////////////////Stage//////////////////////////////////////


//////////////////////////////////////////////////////////Subject//////////////////////////////////////

Route::post('subject/index', [SubjectController::class, 'index']);
Route::post('subject/restore', [SubjectController::class, 'restore']);
Route::delete('subject/delete', [SubjectController::class, 'destroy']);
Route::delete('subject/force-delete', [SubjectController::class, 'forceDelete']);
Route::post('subject/update/{subject}', [SubjectController::class, 'forceUpdate']);
Route::put('/subject/{id}/{column}', [SubjectController::class, 'toggle']);
Route::apiResource('subject', SubjectController::class);

//////////////////////////////////////////////////////////Subject//////////////////////////////////////




//////////////////////////////////////////////////////////Student//////////////////////////////////////
Route::post('student/register', [StudentController::class, 'register']);
Route::post('student/login', [StudentController::class, 'login']);
Route::middleware('auth:sanctum')->get('student/check-auth', [StudentController::class, 'checkAuth']);
Route::middleware('auth:sanctum')->post('/student/enroll', [StudentController::class, 'enroll']);
Route::middleware('auth:sanctum')->post('/student/unenroll', [StudentController::class, 'unenroll']);
Route::middleware('auth:sanctum')->post('student/update-profile', [StudentController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->delete('student/delete-account', [StudentController::class, 'destroyAccount']);

Route::post('student/index', [StudentController::class, 'index']);
Route::post('student/restore', [StudentController::class, 'restore']);
Route::delete('student/delete', [StudentController::class, 'destroy']);
Route::delete('student/force-delete', [StudentController::class, 'forceDelete']);
Route::post('student/update/{student}', [StudentController::class, 'forceUpdate']);
Route::put('/student/{id}/{column}', [StudentController::class, 'toggle']);
Route::apiResource('student', StudentController::class);

//////////////////////////////////////////////////////////Student//////////////////////////////////////




//////////////////////////////////////////////////////////Contact Us//////////////////////////////////////
Route::post('contact-us', [ContactUsController::class, 'store']);
Route::get('contact-us', [ContactUsController::class, 'index']);
//////////////////////////////////////////////////////////Contact Us//////////////////////////////////////




Route::post('student-libraries', [LibraryController::class, 'studentIndex']);
Route::post('teacher-libraries', [LibraryController::class, 'teacherIndex']);
Route::post('job-libraries', [LibraryController::class, 'jobIndex']); //
Route::apiResource('libraries', LibraryController::class);


Route::get('/report-homepage', [TeacherController::class, 'report']);


