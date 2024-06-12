<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/page/search",
     *     tags={"Page"},
     *     summary="Get example",
     *     description="Returns a list of pages matching a search query",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *             )
     *         )
     *     )
     * )
     * @return array<int, string>
     */
    public function search(): array
    {
        return [];
    }
}
