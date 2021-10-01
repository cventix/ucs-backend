<?php


namespace App\Traits;


use App\Exceptions\ApiExceptions\CRUDEntityDoesNotExistsException;
use App\Exceptions\ApiExceptions\CRUDGeneralSaveException;
use App\Exceptions\ApiExceptions\CRUDNotFoundException;
use App\Models\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as ValidationExceptionAlias;

trait CRUDActions
{
    protected $entity = null;

    protected function beforeShowEntity(Model $item) {}
    protected function beforeDeleteEntity(Model $item) {}
    protected function afterDeleteEntity(Model $item) {}
    protected function beforeUpdateEntity(array $validated, Model $item) {}
    protected function afterUpdateEntity(array $validated, Model $item) {}
    protected function beforeSaveEntity(array $validated, Model $item) {}
    protected function afterSaveEntity(array $validated, Model $item) {}

    protected function getEntity() {
        $entity = $this->entity ?? "App\\Models\\" . Str::replaceLast('Controller', '', Str::afterLast(get_class(), "\\"));
        if (!class_exists($entity))
            throw new CRUDEntityDoesNotExistsException();

        return $entity;
    }

    protected function getEntityName() {
        return Str::afterLast($this->getEntity(), "\\");
    }

    protected function getEntityHumanizedName()
    {
        return str_replace('-', ' ', Str::kebab($this->getEntityName()));
    }

    protected function getRequestClass()
    {
        return "App\\Http\\Requests\\" . $this->getEntityName() . "Request";
    }

    protected function getUpdateRequestClass()
    {
        $class = "App\\Http\\Requests\\" . $this->getEntityName() . "UpdateRequest";
        return class_exists($class) ? $class : $this->getRequestClass();
    }

    /**
     * @return JsonResponse
     * @throws CRUDEntityDoesNotExistsException
     */
    public function index()
    {
        $this->authorize('viewAny', $this->getEntity());

        $data = $this->getEntity()::query()->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws CRUDEntityDoesNotExistsException
     * @throws CRUDNotFoundException
     */
    public function show($id)
    {
        $item = $this->getEntity()::find($id);
        if (!$item)
        throw new CRUDNotFoundException($this->getEntityHumanizedName() . " not found.");
        
        $this->authorize('view', $item);

        $this->beforeShowEntity($item);

        $data = $item->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CRUDEntityDoesNotExistsException
     * @throws CRUDGeneralSaveException
     * @throws ValidationExceptionAlias
     */
    public function store(Request $request)
    {
        $this->authorize('create', $this->getEntity());

        $requestClass = $this->getRequestClass();
        $requestInstance = new $requestClass();
        $validator = Validator::make($request->all(), $requestInstance->rules());
        $validator->validate();

        $validated = $validator->validated();

        $entity = $this->getEntity();
        /** @var Model $item */
        $item = new $entity($validated);
        if (!$item->incrementing && $item->getKeyName() == 'id') {
            $item->id = Str::uuid();
        }

        $this->beforeSaveEntity($validated, $item);

        $result = $item->save();

        if (!$result)
            throw new CRUDGeneralSaveException();

        $this->afterSaveEntity($validated, $item);

        $data = $item->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws CRUDEntityDoesNotExistsException
     * @throws CRUDGeneralSaveException
     * @throws CRUDNotFoundException
     * @throws ValidationExceptionAlias
     */
    public function update(Request $request, $id)
    {
        $item = $this->getEntity()::find($id);
        if (!$item)
        throw new CRUDNotFoundException($this->getEntityHumanizedName() . " not found.");
        
        $this->authorize('update', $item);
        
        $requestClass = $this->getUpdateRequestClass();
        $requestInstance = new $requestClass();
        $validator = Validator::make($request->all(), $requestInstance->rules());
        $validator->validate();
        
        $validated = $validator->validated();
        

        $this->beforeUpdateEntity($validated, $item);

        $result = $item->update($validated);

        if (!$result)
            throw new CRUDGeneralSaveException();

        $this->afterUpdateEntity($validated, $item);

        $data = $item->transformIt();
        return $this->successResponse($data);
    }

    /**
     * @param $ids
     * @return JsonResponse
     * @throws CRUDEntityDoesNotExistsException
     */
    public function destroy($ids)
    {
        $ids = explode(',', $ids);
//        $result = $this->entity::query()->whereIn('id', $ids)->delete();
        $result = 0;
        foreach ($ids as $id) {
            $temp = $this->getEntity()::find($id);
            if ($temp) {
                $this->authorize('delete', $temp);

                $this->beforeDeleteEntity($temp);

                $temp->delete();

                $this->afterDeleteEntity($temp);

                $result++;
            }
        }

        return $this->successResponse(['deleted' => $result]);
    }
}
