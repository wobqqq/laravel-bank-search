<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResource;
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
     *     @OA\Parameter(
     *        name="query",
     *        in="query",
     *        required=true,
     *        @OA\Schema(type="string"),
     *            description="Search query string"
     *     ),
     *     @OA\Parameter(
     *        name="page",
     *        in="query",
     *        required=true,
     *        @OA\Schema(type="integer", default=1),
     *            description="Number of page"
     *     ),
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
     *                     @OA\Property(property="url", type="string", example="https://comunitate.bancatransilvania.ro/proiecte/educatie/clujul-are-suflet/"),
     *                     @OA\Property(property="title", type="string", example="Clujul are Suflet"),
     *                     @OA\Property(property="content", type="string", example="bună.Peste 1500 de adolescenț­i au beneficiat de asistență educativă prin sprijin oferit de către profesorii centrului,")
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
     */
    public function search(SearchRequest $request, PageQuery $pageQuery): AnonymousResourceCollection
    {
        $searchData = $request->getData();

        $pages = $pageQuery->search(
            $searchData->query,
            $searchData->perPage,
            $searchData->page,
        );

        return SearchResource::collection($pages);
    }
}
