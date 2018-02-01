<?php
/**
 * Author: Ivan Priorov [iPrior] darkmonk9@gmail.com
 * Project: iPrior_infra
 * Date: 31.01.2018
 *
 * Copyright 2017 Ivan Priorov [iPrior]
 */

class HostItem
{
    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * HostItem constructor.
     * @param string $host
     * @param array $vars
     */
    public function __construct(string $host, array $vars = [])
    {
        $this->setHost($host)
            ->setVars($vars);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return HostItem
     */
    public function setHost(string $host): HostItem
    {
        $this->host = trim($host);

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
     * @return HostItem
     */
    public function setVars(array $vars): HostItem
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
     * @return HostItem
     */
    public function setVar(string $key, $value): HostItem
    {
        $key = trim($key);
        $this->vars[$key] = $value;

        return $this;
    }
}
