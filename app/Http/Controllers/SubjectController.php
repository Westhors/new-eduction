<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\SubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Interfaces\SubjectRepositoryInterface;
use App\Models\Subject;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(SubjectRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Subjects = SubjectResource::collection($this->crudRepository->all());
            return $Subjects->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function listSubjects(Request $request)
    {
        try {
            $stageId = $request->input('stage_id');

            if (!$stageId) {
                return JsonResponse::respondError('stage_id is required');
            }

            $filters = $request->input('filters', []);
            $orderBy = $request->input('orderBy', 'id');
            $orderByDirection = $request->input('orderByDirection', 'asc');
            $perPage = $request->input('perPage', 100);
            $paginate = $request->boolean('paginate', false);

            $query = Subject::whereHas('stages', function ($q) use ($stageId) {
                $q->where('stages.id', $stageId);
            });

            if (!empty($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            if (isset($filters['active'])) {
                $query->where('active', (bool)$filters['active']);
            }

            $query->orderBy($orderBy, $orderByDirection);

            $subjects = $paginate
                ? $query->paginate($perPage)
                : $query->get();

            return SubjectResource::collection($subjects)
                ->additional([
                    'message' => 'Subjects fetched successfully',
                    'result' => 'Success',
                ]);

        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function store(SubjectRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('subjects', $filename, 'public');
                $data['image'] = $path;
            }

            $subject = $this->crudRepository->create($data);

            // ربط المادة بالمراحل
            $subject->stages()->sync($data['stage_ids']);

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Subject $subject): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item Fetched Successfully', new SubjectResource($subject));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceUpdate(SubjectRequest $request, Subject $subject)
    {
        try {
            $data = $request->validated();

            // 📸 تحديث الصورة
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                if ($subject->image && \Storage::disk('public')->exists($subject->image)) {
                    \Storage::disk('public')->delete($subject->image);
                }

                $path = $file->storeAs('subjects', $filename, 'public');
                $data['image'] = $path;
            }

            // ❌ نشيل stage_ids من الداتا علشان مش عمود في subjects
            $stageIds = $data['stage_ids'] ?? [];
            unset($data['stage_ids']);

            // 📝 تحديث بيانات المادة
            $this->crudRepository->update($data, $subject->id);

            // 🔗 تحديث المراحل (إضافة / حذف / تعديل)
            if (!empty($stageIds)) {
                $subject->stages()->sync($stageIds);
            }

            activity()
                ->performedOn($subject)
                ->withProperties(['attributes' => $subject])
                ->log('update');

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('subjects', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Subject::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Subject::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Subject::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function fetchSubject(Request $request)
    {
        try {
            $SubjectData = Subject::get();
            return SubjectResource::collection($SubjectData)->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

