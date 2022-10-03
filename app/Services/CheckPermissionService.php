<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;

class CheckPermissionService
{
    /**
     * array of routes that should be checked with its equivalent permission key
     * @var array
     */
    const ROUTE_CHECK_LIST = [
        // participants
        'participants.index'                => 'participant.allow_access',
        'participants.show'                 => 'participant.allow_access',
        'participants.store'                => 'participant.create',
        'participants.update'               => 'participant.update',
        'participants.destroy'              => 'participant.delete',
        'participants.mass_delete'          => 'participant.delete',
        'participants.regenerate_password'  => 'participant.allow_access',
        // difficulty_groups
        'difficulty_groups.index'           => 'difficulty.allow_access',
        'difficulty_groups.show'            => 'difficulty.allow_access',
        'difficulty_groups.store'           => 'difficulty.create',
        'difficulty_groups.update'          => 'difficulty.edit',
        'difficulty_groups.destroy'         => 'difficulty.delete',
        'difficulty_groups.mass_delete'     => 'difficulty.delete',
        // collections
        'collections.index'                 => 'collection.allow_access',
        'collections.store'                 => 'collection.create',
        'collections.show'                  => 'collection.allow_access',
        'collections.update'                => 'collection.edit',
        'collections.destroy'               => 'collection.delete',
        'collections.mass_approve'          => 'collection.approve_pending',
        'collections.mass_delete'           => 'collection.delete',
        // tasks
        'tasks.index'                       => 'task.allow_access',
        'tasks.store'                       => 'task.create',
        'tasks.show'                        => 'task.allow_access',
        'tasks.destroy'                     => 'task.delete',
        'tasks.updateTask'                  => 'task.edit',
        'tasks.updateTaskContent'           => 'task.edit',
        'tasks.updateRecommendations'       => 'task.edit',
        'tasks.updateAnswers'               => 'task.edit',
        'tasks.mass_delete'                 => 'task.delete',
        // schools
        'schools.index'                     => 'school.allow_access',
        'schools.store'                     => 'school.create',
        'schools.show'                      => 'school.allow_access',
        'schools.update'                    => 'school.edit',
        'schools.destroy'                   => 'school.delete',
        'schools.mass_delete'               => 'school.delete',
        'schools.reject'                    => 'school.approve_reject',
        'schools.mass_approve'              => 'school.approve_reject',
        // competitions
        'competitions.index'                => 'competition.allow_access',
        'competitions.store'                => 'competition.create',
        'competitions.show'                 => 'competition.allow_access',
        'competitions.update'               => 'competition.edit',
        'competitions.destroy'              => 'competition.delete',
        'competitions.mass_delete'          => 'competition.delete',
        // sessions
        'round_levels.sessions.index'       => 'session.allow_access',
        'round_levels.sessions.store'       => 'session.create',
        'sessions.show'                     => 'session.allow_access',
        'sessions.update'                   => 'session.edit',
        'sessions.destroy'                  => 'session.delete'
    ];

    /**
     * @param \App\Models\User
     * @param string
     */
    public static function checkAccessPermission(User $user, string $routeName){
        if($user->hasVariablePermissionSet()){
            return self::checkAccessPermissionFromList($user->getUserPermissionSet()->toArray(), $routeName);
        }
        return true;
    }

    /**
     * @param string $routeName
     * @param array $userPermissionList
     */
    private static function checkAccessPermissionFromList($userPermissionList, string $routeName){
        if(array_key_exists('all', $userPermissionList) && $userPermissionList['all'] == true){
            return true;
        }

        $permission_key = self::ROUTE_CHECK_LIST[$routeName];
        if(Arr::has($userPermissionList, $permission_key)){
            return Arr::get($userPermissionList, $permission_key);
        }
        return false;
    }
}