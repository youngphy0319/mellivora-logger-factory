<?php

// formatters - 用于最终输出日志消息的格式
$formatters = [
    // 简单消息输出
    'simple' => [
        'class'  => Monolog\Formatter\LineFormatter::class,
        'params' => [
            'format' => "[%datetime%][%level_name%] %message% %context%\n",
        ],
    ],
    // 输出消息详情
    'venbose' => [
        'class'  => Monolog\Formatter\LineFormatter::class,
        'params' => [
            'format' => "[%datetime%][%channel%][%level_name%] %message% %context% %extra%\n",
        ],
    ],
    // JSON 格式输出，便于 ELK 收集
    'json' => [
        'class' => Monolog\Formatter\JsonFormatter::class,
    ],
];

// processors - 注册的 processor 将会附加在消息的 extra 字段中
$processors = [
    // 用于日志输出所在 的 file, line, class, method, ...
    'intro' => [
        'class'  => Monolog\Processor\IntrospectionProcessor::class,
        'params' => ['level' => 'ERROR', 'skipStackFramesCount' => 2],
    ],

    // 用于捕获 http web 请求头信息
    'web' => [
        'class'  => Mellivora\Logger\Processor\WebProcessor::class,
        'params' => ['level' => 'ERROR'],
    ],

    // 用于捕获脚本运行信息
    'script' => [
        'class'  => Mellivora\Logger\Processor\ScriptProcessor::class,
        'params' => ['level' => 'ERROR'],
    ],

    // 用于性能分析
    'profiler' => [
        'class'  => Mellivora\Logger\Processor\ProfilerProcessor::class,
        'params' => ['level' => 'DEBUG'],
    ],
];

// handlers - 用于消息输出方式的设定
$handlers = [
    'file' => [
        'class'  => 'Mellivora\Logger\Handler\NamedRotatingFileHandler',
        'params' => [
            'filename'    => '%root%/logs/%channel%.log',
            'maxBytes'    => 100000000, // 100Mb，文件最大尺寸
            'backupCount' => 10, // 文件保留数量
            'bufferSize'  => 10, // 缓冲区大小(日志数量)
            'level'       => 'INFO',
        ],
        'formatter'  => 'json',
        'processors' => ['intro', 'web', 'script', 'profiler'],
    ],
    'cli' => [
        'class'  => 'Monolog\Handler\StreamHandler',
        'params' => [
            'stream' => 'php://stdout',
            'level'  => 'DEBUG',
        ],
        'formatter' => 'simple',
    ],
    'mail' => [
        'class'  => 'Mellivora\Logger\Handler\SmtpHandler',
        'params' => [
            'sender'     => 'logger-factory <sender@mailhost.com>',
            'receivers'  => [
                'zhouyl <81438567@qq.com>',
            ],
            'subject'      => '[ERROR] FROM Logger-Factory',
            'certificates' => [
                'host'     => 'smtp.mailhost.com',
                'port'     => 25,
                'username' => 'sender@mailhost.com',
                'password' => 'sender-passwd',
            ],
            'maxRecords' => 10,
            'level'      => 'CRITICAL',
        ],
        'formatter'  => 'venbose',
        'processors' => ['intro', 'web', 'script', 'profiler'],
    ],
];

// loggers -  当声明的 logger 不在以下列表中时，默认为 default
$loggers = [
    'default'   => ['cli', 'file', 'mail'],
    'cli'       => ['cli', 'file', 'mail'],
    'exception' => ['cli', 'file', 'mail'],
];

return [
    'formatters' => $formatters,
    'handlers'   => $handlers,
    'loggers'    => $loggers,
];
