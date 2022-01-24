<?php
return [
    'frontend' => [
        'fromes-filter' => [
            'target' => \Buepro\Fromes\Middleware\FilterMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
