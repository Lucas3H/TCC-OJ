<?php

return [
    'online'                => '在线',
    'login'                 => '登录',
    'logout'                => '登出',
    'setting'               => '设置',
    'name'                  => '名称',
    'username'              => '用户名',
    'password'              => '密码',
    'password_confirmation' => '确认密码',
    'remember_me'           => '记住我',
    'user_setting'          => '用户设置',
    'avatar'                => '头像',
    'list'                  => '列表',
    'new'                   => '新增',
    'create'                => '创建',
    'delete'                => '删除',
    'remove'                => '移除',
    'edit'                  => '编辑',
    'continue_editing'      => '继续编辑',
    'continue_creating'     => '继续创建',
    'view'                  => '查看',
    'detail'                => '详细',
    'browse'                => '浏览',
    'reset'                 => '重置',
    'export'                => '导出',
    'batch_delete'          => '批量删除',
    'save'                  => '保存',
    'refresh'               => '刷新',
    'order'                 => '排序',
    'expand'                => '展开',
    'collapse'              => '收起',
    'filter'                => '筛选',
    'search'                => '搜索',
    'close'                 => '关闭',
    'show'                  => '显示',
    'entries'               => '条',
    'captcha'               => '验证码',
    'action'                => '操作',
    'title'                 => '标题',
    'description'           => '简介',
    'back'                  => '返回',
    'back_to_list'          => '返回列表',
    'submit'                => '提交',
    'menu'                  => '菜单',
    'input'                 => '输入',
    'succeeded'             => '成功',
    'failed'                => '失败',
    'delete_confirm'        => '确认删除?',
    'delete_succeeded'      => '删除成功 !',
    'delete_failed'         => '删除失败 !',
    'update_succeeded'      => '更新成功 !',
    'save_succeeded'        => '保存成功 !',
    'refresh_succeeded'     => '刷新成功 !',
    'login_successful'      => '登录成功 !',
    'choose'                => '选择',
    'choose_file'           => '选择文件',
    'choose_image'          => '选择图片',
    'more'                  => '更多',
    'deny'                  => '无权访问',
    'administrator'         => '管理员',
    'roles'                 => '角色',
    'permissions'           => '权限',
    'slug'                  => '标识',
    'created_at'            => '创建时间',
    'updated_at'            => '更新时间',
    'alert'                 => '注意',
    'parent_id'             => '父级菜单',
    'icon'                  => '图标',
    'uri'                   => '路径',
    'operation_log'         => '操作日志',
    'parent_select_error'   => '父级选择错误',
    'pagination'            => [
        'range' => '从 :first 到 :last ，总共 :total 条',
    ],
    'role'                  => '角色',
    'permission'            => '权限',
    'route'                 => '路由',
    'confirm'               => '确认',
    'cancel'                => '取消',
    'http'                  => [
        'method' => 'HTTP方法',
        'path'   => 'HTTP路径',
    ],
    'all_methods_if_empty'  => '为空默认为所有方法',
    'all'                   => '全部',
    'current_page'          => '当前页',
    'selected_rows'         => '选择的行',
    'upload'                => '上传',
    'new_folder'            => '新建文件夹',
    'time'                  => '时间',
    'size'                  => '大小',
    'listbox'               => [
        'text_total'         => '总共 {0} 项',
        'text_empty'         => '空列表',
        'filtered'           => '{0} / {1}',
        'filter_clear'       => '显示全部',
        'filter_placeholder' => '过滤',
    ],
    'grid_items_selected'    => '已选择 {n} 项',
    'menu_titles'            => [],
    'prev'                   => '上一步',
    'next'                   => '下一步',
    'quick_create'           => '快速创建',
    'menu_titles' => [
        'dashboard'         => '仪表盘',
        'adminCatg'         => '管理员',
        'users'             => '用户',
        'abuses'            => '违规行为',
        'carousels'         => '轮播图',
        'announcements'     => '通知与公告',
        'problemsCatg'      => '题库',
        'problems'          => '题目',
        'solutions'         => '题解',
        'submissions'       => '提交',
        'contests'          => '比赛',
        'groups'            => '群组',
        'judgeserver'       => '评测机',
        'judger'            => '评测代理',
        'tester'            => '代码测试器',
        'dojoCatg'          => '训练场',
        'dojos'             => '训练任务',
        'dojophases'        => '训练阶层',
        'dojopasses'        => '训练通过记录',
        'helpers'           => '辅助工具',
        'scaffold'          => '脚手架',
        'database'          => '数据库终端',
        'artisan'           => 'Artisan命令行工具',
        'routes'            => '路由列表',
        'logs'              => '日志查看器',
        'media'             => '媒体管理器',
        'scheduling'        => '任务调度',
        'backup'            => '备份',
        'redis'             => 'Redis管理器',
        'babelCatg'         => 'BABEL拓展包',
        'babelinstalled'    => 'BABEL管理工具',
        'babelmarketspace'  => 'BABEL应用市场',
        'settings'          => '系统设置',
    ],
    'home' => [
        'dashboard'         => '仪表盘',
        'general'           => '总体情况',
        'description'       => config("app.name").'的总体情况',
        'version'           => 'NOJ版本',
        'latest'            => '最新版本',
        'problems'          => '题目',
        'solutions'         => '题解',
        'submissions'       => '提交',
        'contests'          => '比赛',
        'users'             => '用户',
        'groups'            => '群组',
        'alreadylatest'     => '已经是最新版',
        'environment'       => '系统环境',
        'dependencies'      => '依赖版本',
        'envs' => [
            'php'           => 'PHP版本',
            'laravel'       => 'Laravel版本',
            'cgi'           => '通用网关接口',
            'uname'         => '操作系统信息',
            'server'        => '服务器软件',
            'cache'         => '缓存驱动',
            'session'       => '会话驱动',
            'queue'         => '队列驱动',
            'timezone'      => '时区配置',
            'locale'        => '本地化配置',
            'env'           => '环境配置',
            'url'           => 'URL',
            'babelMirror'   => 'BABEL镜像',
            'tlsv13'        => 'TLS v1.3',
        ],
    ],
    'users' => [
        'name'              => '用户名',
        'email'             => '邮箱',
        'basic'             => '基本信息',
        'password'          => '密码',
        'index' => [
            'header'        => '用户',
            'description'   => '所有用户列表',
        ],
        'show' => [
            'header'        => '用户详情',
            'description'   => '查看用户详情',
        ],
        'edit' => [
            'header'        => '编辑用户',
            'description'   => '编辑用户信息',
        ],
        'create' => [
            'header'        => '创建新用户',
            'description'   => '创建一个新用户',
        ],
    ],
    'judgers' => [
        'handle'            => '登录凭证',
        'password'          => '登录密码',
        'availability'      => '可用性',
        'available'         => '可用',
        'unavailable'       => '不可用',
        'password'          => '密码',
        'oj'                => 'OJ平台',
        'user_id'           => '评测代理凭证ID',
        'index' => [
            'header'        => '评测代理',
            'description'   => '所有评测代理列表',
        ],
        'show' => [
            'header'        => '评测代理详情',
            'description'   => '查看评测代理详情',
        ],
        'edit' => [
            'header'        => '编辑评测代理',
            'description'   => '编辑评测代理信息',
        ],
        'create' => [
            'header'        => '创建新评测代理',
            'description'   => '创建一个新评测代理',
        ],
        'help' => [
            'handle'        => 'BABEL拓展使用登录凭证登录。',
            'password'      => 'BABEL拓展使用登录密码登录。',
            'user_id'       => '一些BABEL拓展，例如UVa与UVaLive需要提供评测代理凭证ID，这是一个纯数字的字符串。',
        ],
    ],
    'submissions' => [
        'time'              => '时间占用',
        'timeFormat'        => ':time毫秒',
        'memory'            => '空间占用',
        'memoryFormat'      => ':memory千比特',
        'verdict'           => '结果',
        'color'             => '色彩类',
        'language'          => '编程语言',
        'submission_date'   => '提交日期',
        'user_name'         => '用户名称',
        'contest_name'      => '比赛名称',
        'readable_name'     => '题目名称',
        'judger_name'       => '评测代理',
        'share'             => '提交分享',
        'disableshare'      => '关闭',
        'enableshare'       => '开启',
        'rawscore'          => '原始得分',
        'parsed_score'      => '得分',
        'remote_id'         => '远程ID',
        'cid'               => '比赛',
        'uid'               => '用户',
        'pid'               => '题目',
        'jid'               => '评测代理',
        'coid'              => '编译器',
        'vcid'              => '虚拟比赛',
        'solution'          => '提交解答',
        'index' => [
            'header'        => '提交',
            'description'   => '所有提交列表',
        ],
        'show' => [
            'header'        => '提交详情',
            'description'   => '查看提交详情',
        ],
        'edit' => [
            'header'        => '编辑提交',
            'description'   => '编辑提交信息',
        ],
        'create' => [
            'header'        => '创建新提交',
            'description'   => '创建一个新提交',
        ],
    ],
    'dojos' => [
        'name'              => '任务名',
        'phase'             => '所属训练阶层',
        'passline'          => '最少解题数',
        'description'       => '描述',
        'precondition'      => '前置训练',
        'totproblem'        => '题目总数',
        'problems'          => '任务包含题目',
        'problem'           => '题目',
        'problemorder'      => '题目排序',
        'order'             => '排序',
        'index' => [
            'header'        => '训练任务',
            'description'   => '所有训练任务列表',
        ],
        'show' => [
            'header'        => '训练任务详情',
            'description'   => '查看训练任务详情',
        ],
        'edit' => [
            'header'        => '编辑训练任务',
            'description'   => '编辑训练任务信息',
        ],
        'create' => [
            'header'        => '创建新训练任务',
            'description'   => '创建一个新的训练任务',
        ],
    ],
    'dojophases' => [
        'name'              => '阶层名',
        'passline'          => '最少通过训练数',
        'description'       => '描述',
        'order'             => '排序',
        'index' => [
            'header'        => '训练阶层',
            'description'   => '所有训练阶层列表',
        ],
        'show' => [
            'header'        => '训练阶层详情',
            'description'   => '查看训练阶层详情',
        ],
        'edit' => [
            'header'        => '编辑训练阶层',
            'description'   => '编辑训练阶层信息',
        ],
        'create' => [
            'header'        => '创建新训练阶层',
            'description'   => '创建一个新的训练阶层',
        ],
    ],
    'judgeservers' => [
        'scode'             => '评测机标识符',
        'name'              => '评测机名称',
        'host'              => '评测机地址',
        'port'              => '评测机端口',
        'token'             => '访问密钥',
        'availability'      => '可用性',
        'available'         => '评测机可用',
        'unavailable'       => '评测机不可用',
        'oj'                => 'OJ平台',
        'usage'             => '资源使用率',
        'status'            => '健康状态',
        'status_update_at'  => '健康状态更新时间',
        'index' => [
            'header'        => '评测机',
            'description'   => '所有评测机列表',
        ],
        'show' => [
            'header'        => '评测机详情',
            'description'   => '查看评测机详情',
        ],
        'edit' => [
            'header'        => '编辑评测机',
            'description'   => '编辑评测机信息',
        ],
        'create' => [
            'header'        => '创建新评测机',
            'description'   => '创建一个新的评测机',
        ],
        'help' => [
            'onlinejudge'   => '仅可选择拥有online-judge包类型的BABEL拓展，例如NOJ。',
        ],
    ],
    'announcements' => [
        'user'              => '发布用户',
        'title'             => '标题',
        'content'           => '内容',
        'index' => [
            'header'        => '通知与公告',
            'description'   => '所有通知与公告',
        ],
        'show' => [
            'header'        => '通知与公告详情',
            'description'   => '查看通知与公告详情',
        ],
        'edit' => [
            'header'        => '编辑通知与公告',
            'description'   => '编辑通知与公告信息',
        ],
        'create' => [
            'header'        => '创建新通知与公告',
            'description'   => '创建一个新的通知与公告',
        ],
        'help' => [
            'markdown'      => '此处只有一小部分Markdown语法可用，如字体字形。',
        ],
    ],
    'tester' => [
        'oj'                => 'OJ平台',
        'pid'               => '题目',
        'coid'              => '编译器',
        'solution'          => '代码',
        'tester' => [
            'header'        => '代码测试器',
            'description'   => '编写代码并通过代码测试器获取调试结果',
            'title'         => '提交代码',
            'run'           => '测试结果',
        ],
        'help' => [
            'onlinejudge'   => '仅可选择BABEL拓展包NOJ。',
            'installfirst'  => '请首先安装BABEL拓展包NOJ。',
        ],
    ],
    'settings' => [
        'index' => [
            'header'        => '系统设置',
            'description'   => '配置系统相关参数',
        ],
        'form' => [
            'header'        => '高级设置',
            'terms'         => '自定义站点使用条款',
        ],
        'help' => [
            'terms'         => '自定义您的站点条款，如果您系统通过配置env文件使用模板条款请留空。',
        ],
        'tooltip' => [
            'success' => [
                'message'   => '系统设置已成功保存。',
            ],
        ],
    ],
    'carousels' => [
        'image'             => '轮播图片',
        'url'               => '超链接',
        'title'             => '标题',
        'availability'      => '可用性',
        'available'         => '启用',
        'unavailable'       => '禁用',
        'index' => [
            'header'        => '轮播图',
            'description'   => '所有轮播图',
        ],
        'show' => [
            'header'        => '轮播图详情',
            'description'   => '查看轮播图详情',
        ],
        'edit' => [
            'header'        => '编辑轮播图',
            'description'   => '编辑轮播图信息',
        ],
        'create' => [
            'header'        => '创建新轮播图',
            'description'   => '创建一个新的轮播图',
        ],
    ],
    'chunkUpload' => [
        'tooltip'           => '或者拖拽文件到此处',
        'start'             => '开始上传',
    ],
    'dojopasses' => [
        'dojo'              => '训练任务',
        'user'              => '用户',
        'updated_at'        => '任务完成时间',
        'index' => [
            'header'        => '训练通过记录',
            'description'   => '所有训练通过记录',
        ],
        'show' => [
            'header'        => '训练通过记录详情',
            'description'   => '查看训练通过记录详情',
        ],
        'edit' => [
            'header'        => '编辑训练通过记录',
            'description'   => '编辑训练通过记录信息',
        ],
        'create' => [
            'header'        => '创建新训练通过记录',
            'description'   => '创建一个新的训练通过记录',
        ],
    ],
];