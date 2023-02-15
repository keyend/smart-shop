<?php
return [
    'bind' => [
    ],
    'listen' => [
        //短信方式
        'OssType'  => [
            'addon\alioss\event\OssType'
        ],
        'Put'      => [
            'addon\alioss\event\Put'
        ],
        'CloseOss' => [
            'addon\alioss\event\CloseOss'
        ],
    ],
    'subscribe' => [
    ],
];