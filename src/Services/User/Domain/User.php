<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Services\User\Infrastructure\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(
    repositoryClass: UserRepository::class
)]
#[ORM\Index(name: 'username_idx', columns: ['username'])]
#[UniqueEntity("email")]
class User extends AbstractDomainEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var Collection<Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'gamesMaster', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gamesMastered;

    /**
     * @var Collection<Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $games;

    /**
     * @var Collection<Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', cascade: ['persist'], fetch: 'EAGER', indexBy: 'handle')]
    private Collection $roles;

    /**
     * @var Collection<UserFeat>
     */
    #[ORM\ManyToMany(targetEntity: UserFeat::class, cascade: ['persist'], fetch: 'EAGER', indexBy: 'handle')]
    private Collection $feats;

    #[ORM\OneToOne(mappedBy: 'user', fetch: 'EAGER')]
    private ?UserBlock $userBlock = null;

    /**
     * @param string $email
     * @param string $username
     * @param string $password
     * @param UserAccountType $userAccountType
     * @param bool $hasAcceptedCookies
     * @param bool $profilePublic
     * @param string|null $avatarUrl
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\Email]
        private string $email,

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $username,

        #[ORM\Column(type: 'string', length: 255)]
        private string $password = '',

        #[ORM\ManyToOne(targetEntity: UserAccountType::class, fetch: 'EAGER')]
        private UserAccountType $userAccountType,

        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $hasAcceptedCookies = false,

        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $profilePublic = false,

        #[ORM\Column(type: 'string', length: 2048, nullable: true)]
        private ? string $avatarUrl = null,

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->roles = new ArrayCollection();
        $this->gamesMastered = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->feats = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getUserAccountType(): UserAccountType
    {
        return $this->userAccountType;
    }

    public function setUserAccountType(UserAccountType $userAccountType): void {
        $this->userAccountType = $userAccountType;
    }

    public function getHasAcceptedCookies(): bool
    {
        return $this->hasAcceptedCookies;
    }

    public function setHasAcceptedCookies(bool $hasAcceptedCookies): void
    {
        $this->hasAcceptedCookies = $hasAcceptedCookies;
    }

    public function getIsProfilePublic(): bool
    {
        return $this->profilePublic;
    }

    public function setIsProfilePublic(bool $isProfilePublic): void
    {
        $this->profilePublic = $isProfilePublic;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(? string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesMastered(bool $includeArchived = false): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('archivedDate'));

        return $includeArchived ? $this->gamesMastered : $this->gamesMastered->matching($criteria);
    }

    public function getArchivedGamesMastered(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNotNull('archivedDate'));

        return $this->gamesMastered->matching($criteria);
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_map(function ($element) {
            return $element->getHandle();
        }, $this->roles->toArray());
    }

    public function getHydratedRoles(): Collection {
        return $this->roles;
    }

    public function hasRole(Role $role): bool {
        return $this->roles->contains($role);
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(Role $role): void {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    /**
     * @return Collection<UserFeat>
     */
    public function getFeats(): Collection {
        return $this->feats;
    }

    public function setFeats(Collection $feats): void {
        $this->feats = $feats;
    }
    public function awardFeat(UserFeat $userFeat): void {
        $this->feats->add($userFeat);
    }

    public function getUserBlock(): ?UserBlock {
        return $this->userBlock;
    }


    public function isBlocked(): bool
    {
        return $this->userBlock !== null;
    }

    public function canCreateGames(): bool
    {
        return count($this->getGamesMastered()) < $this->getUserAccountType()->getMaximumConcurrentGames();
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
