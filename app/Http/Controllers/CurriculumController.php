<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CurriculaRequest;
use App\Http\Resources\CurriculaResource;
use App\Interfaces\CurriculaRepositoryInterface;
use App\Models\Curriculum;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class CurriculumController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(CurriculaRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Curricu = CurriculaResource::collection($this->crudRepository->all());
            return $Curricu->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(CurriculaRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('curriculums', $filename, 'public');
                $data['image'] = $path;
            }

            $this->crudRepository->create($data);

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Curriculum $curriculum)
    {
        try {
            return JsonResponse::respondSuccess('Item Fetched Successfully', new CurriculaResource($curriculum));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceUpdate(CurriculaRequest $request, Curriculum $curriculum)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                if ($curriculum->image && \Storage::disk('public')->exists($curriculum->image)) {
                    \Storage::disk('public')->delete($curriculum->image);
                }

                $path = $file->storeAs('curriculums', $filename, 'public');
                $data['image'] = $path;
            }

            $this->crudRepository->update($data, $curriculum->id);
            activity()->performedOn($curriculum)->withProperties(['attributes' => $curriculum])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('curricula', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Curriculum::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Curriculum::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Curriculum::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function fetchCurriculum(Request $request)
    {
        try {
            $CurriculumData = Curriculum::get();
            return CurriculaResource::collection($CurriculumData)->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

