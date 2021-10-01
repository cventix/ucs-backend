<?php


namespace App\Http\Controllers\API\V1;


use App\Exceptions\ApiExceptions\TagRequestTypeIsNotTaggableOrDoesNotExistsException;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Traits\CRUDActions;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use CRUDActions;

    /**
     * @param $tagName
     * @param $taggableType
     * @param $taggableId
     * @return JsonResponse
     * @throws TagRequestTypeIsNotTaggableOrDoesNotExistsException
     */
    public function attach($tagName, $taggableType, $taggableId) {
        $tag = Tag::findOrFail($tagName);

        $class = "\\App\\Models\\" . $taggableType;
        if (!class_exists($class))
            throw new TagRequestTypeIsNotTaggableOrDoesNotExistsException();

        $item = $class::findOrFail($taggableId);

        if (!method_exists($item, 'tags'))
            throw new TagRequestTypeIsNotTaggableOrDoesNotExistsException();

        $item->tags()->sync([$tag->name], false);

        return $this->successResponse();
    }

    /**
     * @param $tagName
     * @param $taggableType
     * @param $taggableId
     * @return JsonResponse
     * @throws TagRequestTypeIsNotTaggableOrDoesNotExistsException
     */
    public function detach($tagName, $taggableType, $taggableId) {
        $tag = Tag::findOrFail($tagName);

        $class = "\\App\\Models\\" . $taggableType;
        if (!class_exists($class))
            throw new TagRequestTypeIsNotTaggableOrDoesNotExistsException();

        $item = $class::findOrFail($taggableId);

        if (!method_exists($item, 'tags'))
            throw new TagRequestTypeIsNotTaggableOrDoesNotExistsException();

        $item->tags()->detach([$tag->name], false);

        return $this->successResponse();
    }
}
