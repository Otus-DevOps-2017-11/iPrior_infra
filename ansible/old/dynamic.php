#!/usr/bin/env php
<?php
/**
 * Author: Ivan Priorov [iPrior] darkmonk9@gmail.com
 * Project: iPrior_infra
 * Date: 31.01.2018
 *
 * Copyright 2017 Ivan Priorov [iPrior]
 */

include __DIR__ . DIRECTORY_SEPARATOR . 'dynamic/include.php';

$app = new HostItem('35.205.249.85');
$db = new HostItem('35.205.85.215');
$appGroup = new HostsGroup('app', [$app]);
$dbGroup = new HostsGroup('db', [$db]);

/** ** ** **/

$databases = new HostsGroup(
    'databases',
    [
        new HostItem('host1.example.com'),
        new HostItem('host2.example.com'),
    ],
    [
        "a" => true
    ]
);

$webservers = new HostsGroup(
    'webservers',
    [
        new HostItem('host2.example.com'),
        new HostItem('host3.example.com')
    ]
);

$marietta = new HostsGroup('marietta', [new HostItem('host6.example.com')]);
$fivePoints = new HostsGroup('5points', [new HostItem('host.example.com')]);

$atlanta = new HostsGroup(
    'atlanta',
    [
        new HostItem('host1.example.com', ['asdf' => 1234]),
        new HostItem('host4.example.com'),
        new HostItem('host5.example.com', ['asdf' => 5678])
    ],
    [
        'b' => false
    ]
);
$atlanta->setChildren([$marietta, $fivePoints]);

$inventory = new Inventory([
    $appGroup, $dbGroup,
    $webservers, $marietta, $fivePoints, $atlanta
]);

if ('--list' === $argv[1] ?? '') {
    die($inventory->toList());
} elseif ('--host' === $argv[1] ?? '') {
    die($inventory->toHost($argv[2] ?? ''));
}

