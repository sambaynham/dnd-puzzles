<?php

declare(strict_types=1);

namespace App\Services\Page\Domain;

class NavItem {

    /**
     * @param string $label
     * @param string $route
     * @param array<string, string> $routeArguments
     * @param array<NavItem $children
     * @param bool $isActive
     */
    public function __construct(
        private readonly string $handle,
        private readonly string $label,
        private readonly string $route,
        private readonly array $routeArguments = [],
        private array $children = [],
        private bool $isActive = false,
        private bool $isActiveTrail = false
    ) {}

    public function getHandle(): string
    {
        return $this->handle;
    }
    public function getLabel(): string {
        return $this->label;
    }

    public function getRoute(): string {
        return $this->route;
    }

    public function getRouteArguments(): array {
        return $this->routeArguments;
    }
    public function isActive(): bool {
        return $this->isActive;
    }

    public function isActiveTrail(): bool {
        return $this->isActiveTrail;
    }

    public function setActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    public function addChild(NavItem $child): void {
        $this->children[] = $child;
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function setActiveTrail(bool $isActiveTrail): void {
        $this->isActiveTrail = $isActiveTrail;
    }

}
