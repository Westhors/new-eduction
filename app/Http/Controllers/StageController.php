<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StageRequest;
use App\Http\Resources\StageResource;
use App\Interfaces\StageRepositoryInterface;
use App\Models\Stage;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class StageController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(StageRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Stages = StageResource::collection($this->crudRepository->all());
            return $Stages->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function listStages(Request $request)
    {
        try {
            $curriculumId = $request->input('curriculum_id');

            if (!$curriculumId) {
                return JsonResponse::respondError('curriculum_id is required');
            }

            $filters = $request->input('filters', []);
            $orderBy = $request->input('orderBy', 'id');
            $orderByDirection = $request->input('orderByDirection', 'asc');
            $perPage = $request->input('perPage', 100);
            $paginate = $request->boolean('paginate', false);

            $query = Stage::whereHas('curricula', function ($q) use ($curriculumId) {
                $q->where('curricula.id', $curriculumId);
            });

            // 🔍 فلترة بالاسم
            if (!empty($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            // 🔍 فلترة active
            if (isset($filters['active'])) {
                $query->where('active', (bool) $filters['active']);
            }

            $query->orderBy($orderBy, $orderByDirection);

            $stages = $paginate
                ? $query->paginate($perPage)
                : $query->get();

            return StageResource::collection($stages)
                ->additional([
                    'message' => 'Stages fetched successfully',
                    'result' => 'Success',
                ]);

        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function store(StageRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $data['image'] = $file->storeAs('stages', $filename, 'public');
            }

            $curriculumIds = $data['curriculum_ids'];
            unset($data['curriculum_ids']);

            $stage = $this->crudRepository->create($data);

            // 🔗 ربط المناهج
            $stage->curricula()->sync($curriculumIds);

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Stage $stage): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess('Item Fetched Successfully', new StageResource($stage));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceUpdate(StageRequest $request, Stage $stage)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                if ($stage->image && \Storage::disk('public')->exists($stage->image)) {
                    \Storage::disk('public')->delete($stage->image);
                }

                $data['image'] = $file->storeAs('stages', $filename, 'public');
            }

            $curriculumIds = $data['curriculum_ids'];
            unset($data['curriculum_ids']);

            $this->crudRepository->update($data, $stage->id);

            // 🔄 تحديث المناهج
            $stage->curricula()->sync($curriculumIds);

            activity()
                ->performedOn($stage)
                ->withProperties(['attributes' => $stage])
                ->log('update');

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('stages', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Stage::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Stage::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Stage::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function fetchStage(Request $request)
    {
        try {
            $StageData = Stage::get();
            return StageResource::collection($StageData)->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

