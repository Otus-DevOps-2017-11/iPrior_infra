<?php
/**
 * Author: Ivan Priorov [iPrior] darkmonk9@gmail.com
 * Project: iPrior_infra
 * Date: 31.01.2018
 *
 * Copyright 2017 Ivan Priorov [iPrior]
 */

class HostsGroup
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var HostItem[]
     */
    protected $hosts = [];

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * @var HostsGroup[]
     */
    protected $children = [];

    /**
     * HostsGroup constructor.
     * @param string $name
     * @param array $hosts
     * @param array $vars
     */
    public function __construct(string $name, array $hosts = [], array $vars = [])
    {
        $this->setName($name)
            ->setHosts($hosts)
            ->setVars($vars);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return HostsGroup
     */
    public function setName(string $name): HostsGroup
    {
        $this->name = trim($name);

        return $this;
    }

    /**
     * @return HostItem[]
     */
    public function getHosts(): array
    {
        return $this->hosts;
    }

    /**
     * @param HostItem[] $hosts
     * @return HostsGroup
     */
    public function setHosts(array $hosts): HostsGroup
    {
        $this->hosts = [];
        foreach ($hosts as $host) {
            $this->appendHost($host);
        }

        return $this;
    }

    public function appendHost(HostItem $host): HostsGroup
    {
        $this->hosts[] = $host;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     * @return HostsGroup
     */
    public function setVars(array $vars): HostsGroup
    {
        $this->vars = [];
        foreach ($vars as $k => $v) {
            $this->setVar($k, $v);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return HostsGroup
     */
    public function setVar(string $key, $value): HostsGroup
    {
        $key = trim($key);
        $this->vars[$key] = $value;

        return $this;
    }

    /**
     * @return HostsGroup[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param HostsGroup[] $children
     * @return HostsGroup
     */
    public function setChildren(array $children): HostsGroup
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->setChild($child);
        }

        return $this;
    }

    /**
     * @param HostsGroup $hostsGroup
     * @return HostsGroup
     */
    public function setChild(HostsGroup $hostsGroup): HostsGroup
    {
        $this->children[$hostsGroup->getName()] = $hostsGroup;

        return $this;
    }
}
