<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @extends BaseResource<\App\Models\User>
 */
class User extends BaseResource
{
    /** @var class-string<\App\Models\User> */
    public static string $model = \App\Models\User::class;

    /** @var string */
    public static $title = 'name';

    /** @var array<int, string> */
    public static $search = [
        'id',
        'name',
        'email',
    ];

    /**
     * @return array<int, Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Gravatar::make()->maxWidth(50),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),
            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
        ];
    }
}
