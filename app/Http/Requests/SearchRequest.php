<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\SearchData;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string',
            'resource_name' => 'nullable|string',
        ];
    }

    public function getData(): SearchData
    {
        return SearchData::from(
            trim($this->string('query')->value()),
            trim($this->string('resource_name')->value()),
        );
    }
}
