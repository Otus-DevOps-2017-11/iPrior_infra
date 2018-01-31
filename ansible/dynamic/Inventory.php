<?php
/**
 * Author: Ivan Priorov [iPrior] darkmonk9@gmail.com
 * Project: iPrior_infra
 * Date: 31.01.2018
 *
 * Copyright 2017 Ivan Priorov [iPrior]
 */

class Inventory
{
    /**
     * @var HostsGroup[]
     */
    protected $groups = [];

    /**
     * Inventory constructor.
     * @param HostsGroup[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->setGroups($groups);
    }

    /**
     * @return HostsGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param HostsGroup[] $groups
     * @return Inventory
     */
    public function setGroups(array $groups): Inventory
    {
        $this->groups = [];
        foreach ($groups as $group) {
            $this->setGroup($group);
        }

        return $this;
    }

    /**
     * @param HostsGroup $group
     * @return Inventory
     */
    public function setGroup(HostsGroup $group): Inventory
    {
        $this->groups[$group->getName()] = $group;

        return $this;
    }

    /**
     * @return string - JSON
     */
    public function toList(): string
    {
        $list = [
            '_meta' => $this->getMeta()
        ];

        foreach ($this->getGroups() as $group) {
            $list[$group->getName()] = ['hosts' => []];
            $g = &$list[$group->getName()];
            foreach ($group->getHosts() as $host) {
                $g['hosts'][] = $host->getHost();
            }

            if ($group->getVars()) {
                $g['vars'] = [];
                foreach ($group->getVars() as $k => $v) {
                    $g['vars'][$k] = $v;
                }
            }

            if ($group->getChildren()) {
                $g['children'] = [];
                foreach ($group->getChildren() as $child) {
                    $g['children'][] = $child->getName();
                }
            }
        }

        return json_encode($list);
    }

    /**
     * @return array
     */
    protected function getMeta()
    {
        $meta = ['hostvars' => []];
        $vars = &$meta['hostvars'];

        foreach ($this->getGroups() as $group) {
            foreach ($group->getHosts() as $host) {
                if ($host->getVars()) {
                    $vars[$host->getHost()] = $host->getVars();
               }
            }
        }

        return $meta;
    }

    /**
     * @param string $host
     * @return string - JSON
     */
    public function toHost(string $host): string
    {
        $host = trim($host);
        $vars = null;
        foreach ($this->getGroups() as $group) {
            $vars = $this->findHost($group, $host);
            if (null !== $vars) {
                break;
            }
        }

        return json_encode((null === $vars ? [] : $vars));
    }

    /**
     * @param HostsGroup $group
     * @param string $host
     * @return array|null
     */
    protected function findHost(HostsGroup $group, string $host): ?array
    {
        $vars = null;
        foreach ($group->getHosts() as $h) {
            if ($h->getHost() === $host) {
                $vars = $h->getVars();
                break;
            }
            foreach ($group->getChildren() as $child) {
                $vars = $this->findHost($child, $host);
                if (null !== $vars) {
                    break;
                }
            }
        }

        return $vars;
    }
}
