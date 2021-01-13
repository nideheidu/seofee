<?php
/**
 * Created by PhpStorm.
 * User: longhu
 * Date: 2018/11/28
 * Time: 9:16
 */

return [

    //管理员菜单
    'admin_menu'=>[
        [
            "title"=> "管理中心"
            ,"icon"=>"layui-icon-home"
            ,"jump"=> "/"

        ],
        [
            "name"=> "seoxxxxxx"
            ,"title"=> "SEO管理"
            ,"icon"=> "layui-icon-menu-fill"
            ,"list"=>[
//                [
//                    "name"=> "seo_task_shenhe"
//                    ,"title"=> "新增任务审核"
//                    ,"jump"=> "seo/task/shenhe"
//                ],
                [
                    "name"=> "seo_task_index"
                    ,"title"=> "优化中的任务"
                    ,"jump"=> "seo/task/index"
                ],
                [
                    "name"=> "seo_task_feedback"
                    ,"title"=> "客户反馈词"
                    ,"jump"=> "seo/task/feedback"
                ],
//                [
//                   "name"=> "seo_queryrank"
//                   ,"title"=> "申请停止任务"
//                   ,"jump" => "query/queryrank"
//                ],
                [
                   "name"=> "seo_queryrankstop"
                   ,"title"=> "已停止的任务"
                   ,"jump" => "query/queryrankstop"
                ]
            ]
        ],
         [
           "name"=> "queryx"
           ,"title"=> "客户管理"
           ,"icon"=> "layui-icon-username"
           ,"list"=>[
               [
                    "name"=> "seo_agent"
                    ,"title"=> "代理商管理"
                    ,"jump"=> "/seo/agent/index"
                ],
                [
                    "name"=> "seo_customer"
                    ,"title"=> "客户管理"
                    ,"jump"=> "/seo/customer/index"
                ], [
                    "name"=> "seo_web"
                    ,"title"=> "客户网站"
                    ,"jump"=> "/seo/web/index"
                ],
           ]
       ],
        [
            "name"=> "finance"
            ,"title"=> "财务管理"
            ,"icon"=> "layui-icon-rmb"
            ,"list"=> [
                [
                    "name"=> "consumption"
                    ,"title"=> "客户消费统计"
                    ,"jump"=> "finance/consumption/index"
                ],
                [
                    "name"=> "recharge"
                    ,"title"=> "客户充值记录"
                    ,"jump"=> "finance/recharge/index"
                ],
                [
                    "name"=> "balance"
                    ,"title"=> "客户余额管理"
                    ,"jump"=> "finance/balance/index"
                ],
            ]
        ],
        [
            "name"=> "simulation"
            ,"title"=> "模拟点击"
            ,"icon"=> "layui-icon-engine"
            ,"list"=> [
                [
                "name"=> "command"
                ,"title"=> "动作设置"
                ,"jump"=> "simulation/command/index"
              ],
              [
                  "name"=> "commandtemplate"
                  ,"title"=> "操作模板"
                ,"jump"=> "simulation/template/index"
              ],
          ]
        ],
        [
            "name"=> "member"
            ,"title"=> "会员设置"
            ,"icon"=> "layui-icon-set"
            ,"list"=> [
              [
                "name"=> "seo"
                ,"title"=> "指数扣费设置"
                ,"jump"=> "set/seo/index"
            ],[
                "name"=> "settlementx"
                ,"title"=> "搜索引擎设置"
                ,"jump"=> "set/settlement/set"

            ],[
                "name"=> "group"
                ,"title"=> "代理商组设置"
                ,"jump"=> "set/group/index"

            ],[
                "name"=> "reg"
                ,"title"=> "注册设置"
                ,"jump"=> "set/reg/"

            ]

          ]
        ],
        [
            "name"=> "oem"
            ,"title"=> "OEM设置"
            ,"icon"=> "layui-icon-set"
            ,"list"=> [
              [
                "name"=> "domainmanage"
                ,"title"=> "域名审核"
                ,"jump"=> "set/template/manage"

            ],[
                "name"=> "templateset"
                ,"title"=> "OEM设置"
                ,"jump"=> "set/template/index"

            ]

          ]
        ],
        [
            "name"=> "setx"
            ,"title"=> "系统设置"
            ,"icon"=> "layui-icon-set"
            ,"list"=> [[
                "name"=> "notice"
                ,"title"=> "系统公告"
                ,"jump"=> "set/system/notice"

            ],[
                "name"=> "systemindex"
                ,"title"=> "系统设置"
                ,"jump"=> "set/system/index"

            ],
            [
                "name"=> "update"
                ,"title"=> "手动操作"
                ,"jump"=> "set/settlement/index"

            ]

        ]
        ],
    ],

    //代理商菜单
    'agent_menu'=>[
        [
            "title"=> "控制台"
            ,"icon"=>"layui-icon-home"
            ,"jump"=> "/"

        ],
        [
            "name"=> "sys_seo"
            ,"title"=> "SEO管理"
            ,"icon"=> "layui-icon-menu-fill"
            ,"list"=>[
                 [
                    "name"=> "seo_task_shenhe"
                    ,"title"=> "新增任务审核"
                    ,"jump"=> "seo/task/shenhe"
                ], [
                    "name"=> "seo_task_index"
                    ,"title"=> "优化中的任务"
                    ,"jump"=> "seo/task/index"
                ], [
                   "name"=> "query_queryrank"
                   ,"title"=> "申请停止任务"
                   ,"jump" => "query/queryrank"
                ], [
                   "name"=> "query_queryrankstop"
                   ,"title"=> "已停止的任务"
                   ,"jump" => "query/queryrankstop"
                ]
            ]
        ],
        [
            "name"=> "customer"
            ,"title"=> "客户管理"
            ,"icon"=> "layui-icon-rmb"
            ,"list"=> [
              [
                  "name"=> "seo_customer"
                  ,"title"=> "客户管理"
                  ,"jump"=> "seo/customer/index"
              ]
            ]
        ],
        [
            "name"=> "finance"
            ,"title"=> "财务管理"
            ,"icon"=> "layui-icon-rmb"
            ,"list"=> [
              [
                "name"=> "consumption "
                ,"title"=> "客户消费统计"
                ,"jump"=> "finance/consumption/index"
              ],
              [
                  "name"=> "recharge"
                  ,"title"=> "客户充值记录"
                ,"jump"=> "finance/recharge/index"
              ],
            ]
        ],
        [
            "name"=> "set"
            ,"title"=> "设置"
            ,"icon"=> "layui-icon-set"
            ,"list"=> [[
                "name"=> "seo"
                ,"title"=> "指数扣费设置"
                ,"jump"=> "set/seo/index"
            ],
            [
                "name"=> "group"
                ,"title"=> "会员组设置"
                ,"jump"=> "set/group/index"

            ],[
                "name"=> "settlement"
                ,"title"=> "搜索引擎设置"
                ,"jump"=> "set/settlement/set"

            ],[
                "name"=> "recharge"
                ,"title"=> "充值设置"
                ,"jump"=> "set/recharge/"

            ],[
                "name"=> "templateset"
                ,"title"=> "OEM设置"
                ,"jump"=> "set/template/index"

            ],[
                "name"=> "system"
                ,"title"=> "系统设置"
                ,"jump"=> "set/system/index"

            ]   ]
        ],
    ],

    //客户菜单
    'customers_menu'=>[
        [
            "title"=> "主页"
            ,"icon"=>"layui-icon-home"
            ,"jump"=> "/"
        ],
        [
            "name"=> "customers"
            ,"title"=> "SEO管理"
            ,"icon"=> "layui-icon-menu-fill"
            ,"spread"=> true
            ,"list"=>[
              
//            [
//                "name"=> "task-add"
//                ,"title"=> "添加优化任务"
//                ,"jump" =>'/customers/tasks/add'
//             ],
            [
                "name"=> "task-list-1"
                ,"title"=> "优化中的任务"
                ,"jump"=> '/customers/tasks/lists'
            ],
            [
                "name"=> "task-list-2"
                ,"title"=> "非首页反馈记录"
                ,"jump"=> '/customers/tasks/feedback'
            ],
//            [
//                "name"=> "task-list-3"
//                ,"title"=> "申请停止任务"
//                ,"jump"=> '/customers/tasks/nocheck'
//            ],
//            [
//                "name"=> "task-list-4"
//                ,"title"=> "合作停止任务"
//                ,"jump"=> '/customers/tasks/stop'
//            ],[
//                "name"=> "task-inquiry"
//                ,"title"=> "价格查询"
//                ,"jump" =>'/customers/tasks/inquiry'
//             ],
        ]
        ],
       //     [
       //     "name"=> "query"
       //     ,"title"=> "关键字排名"
       //     ,"icon"=> "layui-icon-search"
       //     ,"list"=>[
       //         // [
       //         //     "name"=> "tasks"
       //         //     ,"title"=> "关键字价格查询"
       //         // ], 
       //         [
       //             "name"=> "queryRank"
       //             ,"title"=> "关键字排名查询"
       //             ,"jump" => "/query/query_rank"
       //         ] 
       //         //, [
       //         //     "name"=> "set"
       //         //     ,"title"=> "关键字指数查询"
       //         // ], [
       //         //     "name"=> "seoranking"
       //         //     ,"title"=> "关键词历史排名"
       //         // ]
       //     ]
       // ],
        [
            "name"=> "detailed"
            ,"title"=> "我的明细"
            ,"icon"=> "layui-icon-rmb"
            ,"spread"=> true
            ,"list"=>[
                [
                    "name"=> "free"
                    ,"title"=> "资金变动明细"
                    ,"jump" => "/detailed/free"
                ],
                [
                    "name"=> "dayfree"
                    ,"title"=> "账号每日消费"
                    ,"jump" => "/detailed/dayfree"
                ],
                [
                    "name"=> "addfree"
                    ,"title"=> "账号充值记录"
                    ,"jump" => "/detailed/addfree"
                ]
            ]
        ],
        [
        "name"=> "personal"
        ,"title"=> "个人中心"
        ,"icon"=> "layui-icon-user"
        ,"spread"=> true
        ,"list"=>[
//            [
//                "name"=> "recharge"
//                ,"title"=> "余额充值"
//                ,"jump"=>"/personal/recharge"
//            ],
            [
                "name"=> "realnameauth"
                ,"title"=> "修改资料"
                ,"jump"=>"/personal/info"
            ],
            

        ]
    ]
    ],

    //客服工具菜单
    'servicetool_menu' =>[
        [
            "title"=> "闲置词库"
            ,"jump"=> "/"

        ],
        [
            "title"=> "账号余额查询"
            ,"jump"=> "/balance/index"

        ]
    ]
];