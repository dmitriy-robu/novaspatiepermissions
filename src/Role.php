<?php

namespace Itsmejoshua\Novaspatiepermissions;

use App\Enums\Permissions;
use Laravel\Nova\Resource;
use Spatie\Permission\Models\Role as SpatieRole;
use Laravel\Nova\Http\Requests\NovaRequest;


class Role extends Resource
{
    use PermissionsBasedAuthTrait;

    public static array $permissionsForAbilities = [
        'all' => 'View roles',
    ];

	/**
	 * The model the resource corresponds to.
	 *
	 * @var string
	 */
	public static $model = SpatieRole::class;

	/**
	 * The single value that should be used to represent the resource when being displayed.
	 *
	 * @var string
	 */
	public static $title = 'name';

	/**
	 * The columns that should be searched.
	 *
	 * @var array
	 */
	public static $search = [
		'name',
	];

	/**
	 * Indicates if the resource should be displayed in the sidebar.
	 *
	 * @var bool
	 */
	public static $displayInNavigation = false;

    public static function getModel()
    {
        return app(PermissionRegistrar::class)->getRoleClass();
    }

    public function fields(NovaRequest $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        $permissionResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass());

        return [
            ID::make()->sortable(),

            Text::make(__('nova-spatie-permissions::lang.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.roles'))
                ->updateRules('unique:' . config('permission.table_names.roles') . ',name,{{resourceId}}'),

            Select::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            DateTime::make(__('nova-spatie-permissions::lang.created_at'), 'created_at')->exceptOnForms(),

            DateTime::make(__('nova-spatie-permissions::lang.updated_at'), 'updated_at')->exceptOnForms(),

            BelongsToMany::make($permissionResource::label(), 'permissions', $permissionResource)->searchable(),

            MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
        ];
    }

	/**
	 * Get the displayable label of the resource.
	 *
	 * @return string
	 */
	public static function label()
	{
		return __('nova-spatie-permissions::lang.Roles');
	}

	/**
	 * Get the displayable singular label of the resource.
	 *
	 * @return string
	 */
	public static function singularLabel()
	{
		return __('nova-spatie-permissions::lang.Role');
	}
}
