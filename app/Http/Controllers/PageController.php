<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\SearchException;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResource;
use App\Models\Resource;
use App\Queries\PageQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pages/search",
     *     tags={"Page"},
     *     summary="Page search",
     *     description="Returns a list of pages matching a search query",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="url", type="string", example="https://btpensii.ro/bt-pensii/despre-noi/"),
     *                     @OA\Property(property="title", type="string", example="BT Pensii"),
     *                     @OA\Property(property="content", type="string", example="RAD Art Fair este unul dintre cele mai mari evenimente din zona de artă contemporană
     *                      din România și este organizat de cele mai cunoscute galerii de renume de la noi din țară.În 2023 a avut loc prima ediție care a fost și un succes:
     *                      peste 3000 de vizitatori, 20 de galerii participante, un parc de sculpturi de 5000 mp și 17 speakeri experți în piață.Pentru cea de-a doua ediție
     *                      a târgului, ne bucurăm să facem parte din poveste ca presenting partner.")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/pages/search?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/pages/search?page=6"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://localhost/api/pages/search?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=6),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/pages/search"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10),
     *                 @OA\Property(property="total", type="integer", example=58)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unexpected error")
     *         )
     *     )
     * )
     * @throws SearchException
     */
    public function search(SearchRequest $request, PageQuery $pageQuery): AnonymousResourceCollection
    {
        $searchData = $request->getData();

        $resourceId = null;

        if (!empty($searchData->resourceName)) {
            $resource = Resource::whereName($searchData->resourceName)->first();

            if (empty($resource)) {
                throw new SearchException('Resource not found');
            }

            $resourceId = $resource->id;
        }

        $pages = $pageQuery->search($searchData->query, $searchData->perPage, $resourceId);

        return SearchResource::collection($pages);
    }
}
