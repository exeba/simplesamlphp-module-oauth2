<?php

$config = [

    // This is a authentication source which handles admin authentication.
    'admin' => [
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.

        'core:AdminPassword',
    ],

    'demo1' => [
        'exampleauth:UserPass',
        'student1:student1pass' => [
            'uid' => 'student',
            'eduPersonAffiliation' => ['member', 'student'],
            'extras' => ['student1 extras'],
        ],
        'employee1:employee1pass' => [
            'uid' => 'employee',
            'eduPersonAffiliation' => ['member', 'employee'],
            'extras' => ['employee1 extras'],
        ],
    ],

    'demo2' => [
        'exampleauth:UserPass',
        'student2:student2pass' => [
            'uid' => 'student',
            'eduPersonAffiliation' => ['member', 'student'],
            'extras' => ['student2 extras'],
        ],
        'employee2:employee2pass' => [
            'uid' => 'employee',
            'eduPersonAffiliation' => ['member', 'employee'],
            'extras' => ['employee2 extras'],
        ],
    ],
];
