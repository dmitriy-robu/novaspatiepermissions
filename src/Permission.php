<?php

namespace Itsmejoshua\Novaspatiepermissions;


use App\Enums\Permissions;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;


class Permission extends Resource
{
    use PermissionsBasedAuthTrait;

    public static $permissionsForAbilities = [
        'all' => 'View permissions',
    ];


    public static function getModel()
    {
        return app(PermissionRegistrar::class)->getPermissionClass();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\NovaRequest $request
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        $roleResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass());

        return [
            ID::make()->sortable(),

            Text::make(__('nova-spatie-permissions::lang.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.permissions'))
                ->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Text::make(__('nova-spatie-permissions::lang.display_name'),function (){
                return __('nova-spatie-permissions::lang.display_names.'.$this->name);
            })->canSee(function (){
                return is_array(__('nova-spatie-permissions::lang.display_names'));
            }),

            Select::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            DateTime::make(__('nova-spatie-permissions::lang.created_at'), 'created_at')->exceptOnForms(),

            DateTime::make(__('nova-spatie-permissions::lang.updated_at'), 'updated_at')->exceptOnForms(),

            BelongsToMany::make($roleResource::label(), 'roles', $roleResource)->searchable(),

            //MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
        ];
    }

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SpatiePermission::class;

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

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('nova-spatie-permissions::lang.Permissions');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('nova-spatie-permissions::lang.Permission');
    }
}
