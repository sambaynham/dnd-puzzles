<?php

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Services\Puzzle\Domain\PuzzleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use http\Exception\InvalidArgumentException;

class RoleFixtures extends Fixture
{

    private const array PERMISSIONS = [
        'view_pages' => [
            'label' => 'View Pages',
            'description' => 'View pages'
        ],

        'admin_dash' => [
            'label' => 'Admin Dashboard',
            'description' => 'Access the Administrator Dashboard'
        ],

        'manage_users' => [
            'label' => 'Manage Users',
            'description' => 'Manage Users'
        ],

        'block_users' => [
            'label' => 'Block Users',
            'description' => 'Block and unblock users, adjudicate abuse reports.'
        ],

        'administer_games' => [
            'label' => 'Administer Games',
            'description' => 'Update and create any game, whether its owner or not.'
        ],
        'assign_roles' => [
            'label' => 'Assign Roles',
            'description' => 'Assign Roles to users'
        ],

        'administer_roles' => [
            'label' => 'Administer Roles',
            'description' => 'Created, update and edit roles and permissions.'
        ],
    ];

    private const array ROLES = [
        'ROLE_USER' => [
            'name' => 'User',
            'permissions' => [
                'view_pages'
            ]
        ],
        'ROLE_ADMIN' => [
            'name' => 'Admin',
            'permissions' => [
                'view_pages',
                'admin_dash',
                'manage_users',
                'block_users',
                'administer_games',
                'assign_roles',
                'administer_roles'
            ]
        ],
        'ROLE_MODERATOR' => [
            'name' => 'Moderator',
            'permissions' => [
                'view_pages',
                'admin_dash',
                'block_users',
            ]
        ]
    ];

    public function load(ObjectManager $manager): void
    {

        $this->processPermissions($manager);
        $this->processRoles($manager);

    }

    private function processRoles(ObjectManager $manager): void {
        /**
         * @var RoleRepository $repo
         */
        $roleRepo = $manager->getRepository(Role::class);

        /**
         * @var PermissionRepository $repo
         */
        $permissionsRepo = $manager->getRepository(Permission::class);

        foreach (self::ROLES as $handle => $definition) {
            if (!$roleRepo->findByHandle($handle)) {

                $role = new Role(
                    name: $definition['name'],
                    handle: $handle,
                );
                foreach ($definition['permissions'] as $permissionHandle) {

                    $permission = $permissionsRepo->findOneByHandle($permissionHandle);
                    if (null === $permission) {
                        throw new InvalidArgumentException(sprintf("Permission %s is not a valid handle", $permissionHandle));
                    }
                    $role->addPermission($permission);
                }

                $manager->persist($role);
            }
        }
        $manager->flush();
    }
    private function processPermissions(ObjectManager $manager): void {
        /**
         * @var PermissionRepository $repo
         */
        $permissionsRepo = $manager->getRepository(Permission::class);
        foreach (self::PERMISSIONS as $handle => $definition) {
            if (!$permissionsRepo->findOneByHandle($handle)) {
                $manager->persist(new Permission(label: $definition['label'], handle: $handle, description: $definition['description']));
            }
        }
        $manager->flush();
    }
}
