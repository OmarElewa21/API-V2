<?php

namespace tcCore\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class CheckPermissionService
{
    const ROUTE_LIST_TO_CHECK = [
        // participants
        'participants.index', 'participants.store', 'participants.show', 'participants.update', 'participants.destroy',
        'participants.mass_delete', 'participants.regenerate_password',
        // difficulty_groups
        'difficulty_groups.index', 'difficulty_groups.store', 'difficulty_groups.show', 'difficulty_groups.update',
        'difficulty_groups.destroy', 'difficulty_groups.mass_delete',
        // collections
        'collections.index', 'collections.store', 'collections.show', 'collections.update', 'collections.destroy',
        'collections.mass_approve', 'collections.mass_delete',
        // tasks
        'tasks.index', 'tasks.store', 'tasks.show', 'tasks.destroy', 'tasks.updateTask', 'tasks.updateTaskContent',
        'tasks.updateRecommendations', 'tasks.updateAnswers', 'tasks.mass_delete',
        // schools
        'schools.index', 'schools.store', 'schools.show', 'schools.destroy', 'schools.mass_delete', 'schools.reject',
        'schools.mass_approve',
        // competitions
        'competitions.index', 'competitions.store', 'competitions.show', 'competitions.destroy', 'competitions.mass_delete',
        // sessions
        'round_levels.sessions.index', 'round_levels.sessions.store', 'sessions.show', 'sessions.update', 'sessions.destroy'
    ];

    /**
     * 
     *
     * @var array
     */
    private $routePermission = [
        'participant'   => [
            'create'        => 'participants.store',
            'edit'          => 'participants.update',
            'delete'        => ['participants.destroy', 'participants.mass_delete']
        ],
        'difficulty' => [
            'create'        => 'difficulty_groups.store',
            'edit'          => 'difficulty_groups.update',
            'delete'        => ['difficulty_groups.destroy', 'difficulty_groups.mass_delete']
        ],
        'collection' => [
            'create'            => 'collections.store',
            'edit'              => 'collections.update',
            'delete'            => ['collections.destroy', 'collections.mass_delete'],
            'approve_pending'   => 'collections.mass_approve'
        ],
        'task'  => [
            'create'            => 'tasks.store',
            'edit'              => 'tasks.update',
            'delete'            => ['tasks.destroy', 'tasks.mass_delete'],
            'approve_pending'   => 'tasks.mass_approve'
        ],
        'school' => [
            'create'            => 'schools.store',
            'edit'              => 'schools.update',
            'delete'            => ['schools.destroy', 'schools.mass_delete'],
            'approve_reject'    => ['schools.mass_approve', 'schools.reject']
        ],
        'competition' => [
            'create'            => 'competitions.store',
            'edit'              => 'competitions.update',
            'delete'            => ['competitions.destroy', 'competitions.mass_delete']
        ]
    ];

    /**
     * @param \App\Models\User
     * @param string
     */
    public static function checkAccessPermission(User $user, string $routeName){
        if($user->hasVariablePermissionSet){
            return self::checkAccessPermissionFromList($user->getUserPermissionSet, $routeName);
        }
        return true;
    }

    /**
     * @param string $routeName
     * @param \Illuminate\Support\Collection $userPermissionList
     */
    private static function checkAccessPermissionFromList(Collection $userPermissionList, string $routeName){
        dd($this->routePermission->flatten(1));
    }
}