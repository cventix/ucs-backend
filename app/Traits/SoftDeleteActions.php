<?php


namespace App\Traits;


use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait SoftDeleteActions
{

    /**
     * @return JsonResponse
     */
    public function trashed()
    {
        $data = $this->getEntity()::onlyTrashed()->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function restore($id)
    {
        $trashed = $this->getEntity()::withTrashed()->findOrFail($id);
        $trashed->restore();

        $data = $trashed->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param $ids
     * @return JsonResponse
     */
    public function permanentlyDelete($ids)
    {
        $ids = explode(',', $ids);
        $result = $this->getEntity()::withTrashed()->whereIn('id', $ids)->forceDelete();
        return $this->successResponse(['deleted' => $result]);
    }
}
