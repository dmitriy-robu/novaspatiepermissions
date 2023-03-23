<?php

namespace Itsmejoshua\Novaspatiepermissions;

use App\Enums\Permissions;
use Laravel\Nova\Resource;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends Resource
{
	use RoleResourceTrait, PermissionsBasedAuthTrait;

    public static $permissionsForAbilities = [
        'create' => Permissions::CREATE_ROLE,
        'update' => Permissions::UPDATE_ROLE,
        'delete' => Permissions::DELETE_ROLE,
        'viewAny' => Permissions::VIEW_ROLE,
        'view' => Permissions::VIEW_ROLES,
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
