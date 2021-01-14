/**
 *  ?????????????????????
 *  
 */
var sceneList = function(_Dopen, _global) {
    var sceneList = {}; 

	var newArticleList = {
        init:function () {
            var self = this;
            /* ???????????????????????? ????????????????????? sceneLists:reload?????? */
            if(!sceneList._str) { 
                $.each($('.wqdelementEdit[data-elementtype="sceneList"]'), function (i, _) {
                    // ???????????????????????????
                    if($(_).attr('artnavtype')) {
                        sceneList.loadEvent1($(_));
                        sceneList.loadScene($(_), sceneList.listStr[sceneList.getListName($(_))], $(_).attr('navids'));
                    }
                });
            }
            if(sceneList._str) {
                sceneList.bindTrigger();   
            }
        },
        /**
         * ???????????????????????????
         */
        bindDefaultDetail: function ($elem, dfd) {
            var hasDetailPage = $(".menu-list-page>li[data-type=HouseKeeping]"),
                addPageDfd = $.Deferred(),pageId,needCatch;
            if(hasDetailPage.length) {
                addPageDfd.resolve(hasDetailPage.eq(0).attr("data-pageid"));
            } else {
                needCatch = true;
                //??????????????????
                if((_global.permission.langCount||_global.page.data.length) < _global.permission.pageCount){
                    $(document).trigger("page:add.page",{
                        // pageId : sceneList._isDesignPhone == 'pc' ? "36365" : "36366",  // ??????
                        pageId : sceneList._isDesignPhone == 'pc' ? "11338" : "11339",
                        type : "HouseKeeping",
                        // pageId : "36364",
                        // type : "article",
                        dfd : addPageDfd
                    });
                }else{
                    var Ismall=(_global.permission.version.indexOf("mall") >=0)?true:false;
                    var Bool=!!isagentUser?true:false;
                    $(document).trigger("Popup:popup",{
                        title: "????????????",
                        class: "pupAgent",
                        info: "??????????????????????????????????????????",
                        desc: (!Ismall&&!Bool)?"??????????????????????????????????????????????????????":"",
                        done: (!Ismall&&!Bool)?"??????":"??????",
                        href:(!Ismall&&!Bool)?WWWCTX+"pricingpackage.html":"javascript:void(0)",
                        cancel:(!Ismall&&!Bool)?"??????":"",
                        href:(!Ismall&&!Bool)?WWWCTX+"pricingpackage.html":"javascript:void(0)",
                        target:"_blank",
                        clickOK: function(obj){
                             obj.find(".pupcloseBtn").click();
                        },
                        clickCancel:function(obj){
                            obj.find(".pupcloseBtn").click();
                        }
                    });
                }
            }
            //?????????????????????????????????
            $.when(addPageDfd).done(function (_id) {
                pageId = _id || "";
                $elem.attr("data-pageid",pageId);
                needCatch && $(document).trigger("cache:push");
                dfd.resolve();
            });
        }
    };
    /* ???????????????????????? */
    var newsAJAX = {
        navAJAX: {
            url: SAAS_NEWS+"/api/news/navigationbars/",
            // +$obj.attr('userid')+"/"+($obj.attr('artnavtype') || "CATEGORY")  url????????????
            type: 'GET',
            dataType: 'jsonp',
            jsonp: 'callback',
            data: {}
        },
        listAJAX: {
            url: SAAS_NEWS+'/api/news/page',
            type: 'GET',
            dataType: 'jsonp',
            jsonp: 'callback',
            // data: json,
        }
    };
    /* ?????????????????????????????? */
    var sceneAJAX = {
        navAJAX: {
            url: (window['DisplayModel'] ? '/pcdesign/' : '/')+'scene/listBasicType',
            type: 'POST',
            dataType: 'json',
            jsonp: '',
            data: {}
        },
        advSearchAJAX: {
            url: (window['DisplayModel'] ? '/pcdesign/' : '/')+'scene/listUserSearchFields',
            type: 'POST',
            dataType: 'json',
            jsonp: '',
            data: {}
        },
        listAJAX: {
            url: (window['DisplayModel'] ? '/pcdesign/' : '/')+'scene/listRecords',
            type: 'POST',
            dataType: 'json',
            jsonp: '',
            data: {}
        }
    };

    /* ?????? ??????????????? */
    var sceneListStr = {
        listSearch: {
            search1: [  // ?????? pc
                '<div class="advSearch-box wqd-mgn-t">',
                    // '<div class="select-checked">',
                    //     '<div class="check-item">',
                    //         '<div class="label">????????????</div>',
                    //         '<em>??????</em><em>??????</em><em>??????</em>',
                    //     '</div>',
                    //     '<div class="check-item">',
                    //         '<div class="label">??????</div>',
                    //         '<em>1~2???</em><em>3~4???</em>',
                    //     '</div>',
                    // '</div>',
                    '<div class="select-group wqd-brs wqd-brc wqd-brw">',
                        '<div class="item wqd-brc wqd-brw only">', 
                            '<span class="name wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</span>',
                            '<div class="sel-tags">',
                                '<span class="tag on wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-code="-1">??????</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">?????????</span>',
                            '</div>',
                            '<div class="add-more wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" style="display:none;">??????</div>',
                        '</div>',
                        '<div class="item wqd-brc wqd-brw only">',
                            '<span class="name wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">?????????</span>',
                            '<div class="sel-tags">',
                                '<span class="tag on wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-code="-1">??????</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">2?????????</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">3~4???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">5~8???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">8?????????</span>',
                            '</div>', 
                            '<div class="add-more wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????</div>',
                        '</div>',
                        '<div class="item wqd-brc wqd-brw">',
                            '<span class="name wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????(??????)???</span>',
                            '<div class="sel-tags">',
                                '<span class="tag on wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-code="-1">??????</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">30?????????</span>', 
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">31~35???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">36~40???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">41~45???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">46~50???</span>', 
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">51~55???</span>',
                                '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">55?????????</span>',
                            '</div>',
                            '<div class="add-more wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????</div>',
                        '</div>',
                    '</div>',
                '</div>'
            ].join(''),
            search2: [ // ?????? phone
                '<div class="advSearch-box1 wqd-mgn-t">',
                    '<div class="btn-self">',
                        // '<span class="wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst wqd-brs wqd-brc wqd-brw">?????????</span>',
                        // '<span class="wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst wqd-brs wqd-brc wqd-brw">?????????</span>',
                        '<span class="wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst wqd-brs wqd-brc wqd-brw">??????</span>',
                    '</div>',
                    '<div class="sel-box">',
                        '<div class="self-mark">',
                            '<div class="sel-popover">',
                                '<div class="sel-group-box">',
                                    '<div class="gr-box">',
                                        '<div class="b-hd">',
                                            '<div class="left-content">????????????</div>',
                                            '<div class="right-content"><span>??????</span>',
                                                '<svg class="down" viewBox="0 0 1026 1024"><path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z" p-id="7641"></path></svg>',
                                                '<svg class="up" viewBox="0 0 150.3 150" style="display:none;"><path d="M2.1,113.3c1.3,1.3,3.3,1.3,4.6,0l68.9-68.9l68.9,68.9c1.3,1.3,3.3,1.3,4.6,0c1.3-1.3,1.3-3.3,0-4.6L78,37.5  c-0.6-0.6-1.5-1-2.3-1s-1.7,0.3-2.3,1L2.1,108.7C0.8,110,0.8,112,2.1,113.3z"></path></svg>',
                                            '</div>',
                                            '<div class="center-content"></div>',
                                        '</div>',
                                        '<div class="b-bd">',
                                            '<div class="item on">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '?????????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '????????????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '?????????',
                                            '</div>',
                                        '</div>',
                                    '</div>',
                                    '<div class="gr-box">',
                                        '<div class="b-hd">',
                                            '<div class="left-content">??????</div>',
                                            '<div class="right-content"><span>??????</span>',
                                                '<svg class="down" viewBox="0 0 1026 1024"><path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z" p-id="7641"></path></svg>',
                                                '<svg class="up" viewBox="0 0 150.3 150" style="display:none;"><path d="M2.1,113.3c1.3,1.3,3.3,1.3,4.6,0l68.9-68.9l68.9,68.9c1.3,1.3,3.3,1.3,4.6,0c1.3-1.3,1.3-3.3,0-4.6L78,37.5  c-0.6-0.6-1.5-1-2.3-1s-1.7,0.3-2.3,1L2.1,108.7C0.8,110,0.8,112,2.1,113.3z"></path></svg>',
                                            '</div>',
                                            '<div class="center-content"></div>',
                                        '</div>',
                                        '<div class="b-bd only">',
                                            '<div class="item on">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                            '<div class="item">',
                                                '<svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>',
                                                '??????',
                                            '</div>',
                                        '</div>',
                                    '</div>',
                                '</div>',
                            '</div>',
                            '<div class="sel-group-btn">',
                                '<div class="can-cel">??????</div>',
                                '<div class="sub-mit">??????</div>',
                            '</div>',
                        '</div>',
                    '</div>',
                '</div>'
            ].join('')
        },
        listLoad: {
            load1: [    // ?????? pc???phone
                '<div class="load-more">',
                    '<p class="wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">????????????</p>',
                '</div>',
            ].join(''),
            load2: [    // ?????? pc
                '<div class="load-more">',
                    '<div class="pagination">',
                        '<p class="first wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">??????</p>',
                        '<p class="prev wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">?????????</p>',
                        '<div class="pages">',
                            '<p class="item wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">1</p>',
                            '<p class="item wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">2</p>',
                            '<p class="item on wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">3</p>',
                            '<p class="item wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">4</p>',
                            '<p class="item wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">5</p>',
                            '<p class="more wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">...</p>',
                            '<p class="item wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">25</p>',
                        '</div>',
                        '<p class="next wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr">?????????</p>',
                    '</div>',
                '</div>',
            ].join('')
        },
        listNav: {
            nav1: [   // ?????? pc
                '<div class="nav">',
                    '<hr class="line">',
                    '<div class="cont-nav">',
                        '<span class="on wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="width:19%;margin:0 2px;">??????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="width:19%;margin:0 2px;">?????????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="width:19%;margin:0 2px;">????????????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="width:19%;margin:0 2px;">?????????</span>',
                        '<span class="showMore wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="width:19%;margin:0 2px;">??????</span>',
                    '</div>',
                    '<div class="nav-more"></div>',
                '</div>'
            ].join(''),
            nav2: [   // ??????  pc???phone
                '<div class="nav float-left">',
                    '<hr class="line">',
                    '<div class="cont-nav">',
                        '<span class="on wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">??????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">?????????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">????????????</span>',
                        '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">?????????</span>',
                    '</div>',
                    '<div class="nav-more"></div>',
                '</div>'
            ].join('')
        },
        /* ??????????????? ???????????? -- common */
        getLoadMoreStyle: function ($obj) {
            return sceneListStr.listLoad[$obj && $obj.attr('artload') || 'load1'];
        },
        /* ??????????????? ?????????????????? -- common */
        getSearchStyle: function ($obj) {
            return sceneListStr.listSearch[$obj && $obj.attr('artsearch') || 'search1'];
        },
        /* ??????????????? ???????????? -- common */
        getNavStyle: function ($obj) {
            return sceneListStr.listNav[$obj && $obj.attr('artnav') || 'nav1'];
        }
    }
    /**
     * ???????????? 1   ?????? pc
     * pcdesign and host ?????????
     * ?????? 
     *    1. ???????????????1 ??????  isNav1
     *    2. ??????????????????1????????? ??????  renderNav1
     *    3. ???????????????????????? ??????  doEvent , ?????? ??????????????? callback???????????? ?????? ??????????????????????????????
     */
    var Nav1 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            isNav1: function ($obj) {
                return $obj.attr('artnav') == 'nav1' ? true : false;
            },
            /* ???????????? ????????? ????????? pc */
            renderNav: function ($obj, navHtml) {
                /* ???????????????navHtml ?????????????????????????????????????????????????????????????????????????????????????????????????????? */
                if(!!navHtml) {
                    /* ???????????? ?????????  ???????????????????????????????????? */
                    $obj.find('.nav').empty().append('<hr class="line"><div class="cont-nav"></div><div class="nav-more"></div>');
                    /* ??????-?????? */
                    var $nav = $obj.find('.nav .cont-nav');
                    /* ??????-?????? */
                    var $moreNav = $obj.find('.nav .nav-more');
                    /* ????????????????????????????????? */
                    $nav.append(navHtml + '<span class="show-more wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">??????</span>');
                } else {
                    /* ??????-?????? */
                    var $nav = $obj.find('.nav .cont-nav');
                    /* ??????-?????? */
                    var $moreNav = $obj.find('.nav .nav-more');
                    /* ????????????????????????????????? */
                    $nav.find('.show-more').length && $nav.find('.show-more').remove();
                    $nav.append($moreNav.html()+ '<span class="show-more wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc">??????</span>');
                    $moreNav.html('');
                }

                /* ??????????????? */
                var $spanArr = $nav.find('span').removeAttr('style', ''), length = $spanArr.length;
                /* ???????????? */    
                var maxWidth = $obj.find('.nav').outerWidth()-5;
                /* ???????????? ???????????????25px/725px */
                var sum1 = $spanArr.eq(length-1).width(), temp1 = 4 + (50/725*maxWidth); 
                /* ?????????padding + margin??? */
                var sum2 = temp1;
                /* padding 25/725  margin:2px */
                var i = 0, flag = true;
                $.each($spanArr, function (_, val) {
                    if(_ == length - 1) return false;
                    /* ???????????????????????????????????? */
                    if(flag) {
                        var temp = sum1 + sum2 + $(val).width() + temp1;
                        if(temp > maxWidth) {
                            flag = false;
                            $moreNav.append($(val)[0].outerHTML);
                            $(val).remove();
                            return true;
                        }
                        if(temp == maxWidth) flag = false;
                        sum1 += $(val).width();
                        sum2 += temp1;
                        i ++;
                    } else {
                        if(!$(val).hasClass('show-more')) {
                            $moreNav.append($(val)[0].outerHTML);
                            $(val).remove();
                        }
                    }
                });
                /* ?????????????????????????????????????????? */
                if(i >= length - 1) {
                    var $move = $obj.find('.nav .show-more');
                    sum1 -= $move.width();
                    $move.remove();
                }
                var lastLength = $nav.find('span').length;
                if(i >= length - 1) {
                    $spanArr.css({
                        "padding": '0 '+25/725*maxWidth+'px',
                        "margin": '0 2px'
                        // "margin": '0 '+parseInt((maxWidth - sum1 - lastLength*(50/725*maxWidth))/(lastLength*2))+'px'
                    });
                } else {
                    $spanArr.css({
                        "padding": '0 '+parseInt((maxWidth - sum1 - lastLength*4)/(lastLength*2))+'px',
                        "margin": '0 2px'
                    });
                    $moreNav.find('span').css({
                        "padding": '0 '+parseInt((maxWidth - sum1 - lastLength*4)/(lastLength*2))+'px',
                        "margin": '0 2px'
                    });
                }
            },
            /* ???????????? ?????????1 ????????? pc  ????????????????????? */
            renderNav1: function ($obj, navHtml) {
                if(!!navHtml) {
                    /* ???????????? ?????????  ???????????????????????????????????? */
                    $obj.find('.nav').html(navHtml);
                }
            },
            bindEvent: function ($obj, callback) {
                var self = this;
                 $obj.find('.nav').off('click.nav').on('click.nav', '.cont-nav span', function () {
                    var that = $(this);
                    if(that.hasClass('show-more')) return ;
                    if($obj.attr('navids')) {
                        // ????????????span????????????on
                        $obj.find('.nav span').removeClass('on');
                        
                        // ?????????????????????????????????span??????  ?????????????????????
                        if(that.parents('.nav-more').length) {
                            var _that = that.clone();
                            $obj.find('.nav span.show-more').before(_that);
                            that.remove();
                            that = _that;
                            that.parents('.nav-more').hide();
                        }
                        self.renderNav($obj, '');
                        that.addClass('on');
                        callback($obj);
                    } else {
                        that.addClass('on');
                    }
                    // ????????????????????????????????????????????????????????????????????????????????? ???flag
                    $obj.attr('curPage', 1);
                    $obj.attr('artload') == 'load1' && $obj.find('.load-more p').removeAttr('style').text('????????????');
                    $obj.find('.nav-more').hide();
                });

                /* ?????????????????? ?????????????????? -- ???????????? nav1 */
                $obj.find('.nav').off('mouseenter.show-more').on('mouseenter.show-more', '.cont-nav .show-more', function () {
                    $obj.find('.nav-more').show();
                });
                /* ?????????????????? ?????????????????? -- ???????????? nav1 */
                $obj.find('.nav').off('mouseleave.nav-more').on('mouseleave.nav-more', '.nav-more', function () {
                    $obj.find('.nav-more').hide();
                });
            }
        };
        return new _obj();
    })();

    /**
     * ???????????? 2   ?????? pc???phone
     * pcdesign and host ?????????
     * ?????? 
     *    1. ???????????????2 ??????  isNav2
     *    2. ??????????????????2????????? ??????  renderNav2
     *    3. ???????????????????????? ??????  doEvent , ?????? ??????????????? callback???????????? ?????? ??????????????????????????????
     */
    var Nav2 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            isNav2: function ($obj) {
                return $obj.attr('artnav') == 'nav2' ? true : false;
            },
            renderNav2: function ($obj, navHtml) {
                if(!!navHtml) {
                    /* ???????????? ?????????  ???????????????????????????????????? */
                    $obj.find('.nav .cont-nav').html(navHtml);
                } 
            },
            /* ???????????????????????????????????? */
            bindEvent: function ($obj, callback) {
                /* ???????????? ?????? */
                $obj.find('.nav').off('click.nav').on('click.nav', '.cont-nav span', function () {
                    var that = $(this);
                    if(that.hasClass('show-more')) return ;
                    if($obj.attr('navids')) {
                        var warp = that.parents(".nav"),
                        warpWidth = warp.width(),
                        thisOL_W = that.parent().width(),
                        centerLeft = warpWidth/2,
                        index = that.index();
                        if(thisOL_W > warpWidth) {
                            var lastLeft = 0;
                            that.parent().children().each(function (i, _) {
                                if(i == index) { return false; }
                                lastLeft += $(_).outerWidth(true);
                            });
                            lastLeft += that.outerWidth()/2;
                            // ?????? ??????????????????
                            if(centerLeft > lastLeft) {
                                lastLeft = 0;
                            } else {
                                lastLeft = centerLeft - lastLeft;
                            }
                            /* ????????????????????????????????? */
                            var _getStyle = function (val, time) {
                                return {
                                    '-webkit-transition': '-webkit-transform '+time+'ms',
                                    '-moz-transition': '-webkit-transform '+time+'ms',
                                    '-o-transition': '-webkit-transform '+time+'ms',
                                    'transition': 'transform '+time+'ms',
                                    '-webkit-transform': 'translate3d('+val+'px,0,0)',
                                    '-moz-transform': 'translate3d('+val+'px,0,0)',
                                    '-o-transform': 'translate3d('+val+'px,0,0)',
                                    'transform': 'translate3d('+val+'px,0,0)',
                                    '-webkit-backface-visibility': 'hidden'
                                };
                            }

                            that.parent().css(_getStyle(lastLeft, 300));
                        };
            
                        if(!that.hasClass('on')) {
                            that.addClass('on').siblings('span').removeClass('on');
                            callback($obj);
                        }
                    } else {
                        that.addClass('on').siblings('span').removeClass('on');
                    }
                });
            }
        };
        return new _obj();
    })();
    var Pagination1 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            isPagination1: function ($obj) {
                return $obj.attr('artload') == 'load1' ? true : false;
            },
            bindEvent: function ($obj, callback) {
                $obj.off('click.load-more').on('click.load-more', '.load-more p', function (e) {
                    var id = $obj.find('.nav span.on').data('categoryid'), curPage = 0;
                    if(id) {
                        curPage = parseInt($obj.attr('curPage')) + 1;
                        $obj.attr('curPage', curPage)
                        callback($obj);
                    } 
                });
            }
        };
        return new _obj();
    })();
    /**
     * ????????????2  ????????? pc
     * pcdesign and host ?????????
     *
     */
    var Pagination2 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            /* ?????????????????????????????????????????? */
            pageData: {
                totalCount: 25,
                curPage: 3          // ???????????? ????????????????????????attr????????? ?????????????????? 
            },
            isPagination2: function ($obj) {
                return $obj.attr('artload') == 'load2' ? true : false;
            },
            /* ???????????? ?????? ????????? pc */
            renderPagination: function ($obj) {
                var self = this;
                var page = Math.ceil(self.pageData.totalCount / ((-(-$obj.attr('col'))||1) * (-(-$obj.attr('row'))||1)));
                var str = '';
                /* ??????7?????????...?????? */
                if(page >= 7) {
                    /* ?????????????????????...??????????????? */
                    if(self.pageData.curPage+2 >= page) {
                        for(var i=0;i<5; i++) {
                            str = '<p class="item '+(page-i == self.pageData.curPage ? 'on':'')+'">'+(page-i)+'</p>' + str;
                        }
                    } else {
                        var prevCount = nextCount = self.pageData.curPage; /* ??????5??? */
                        str += '<p class="item on">'+self.pageData.curPage+'</p>';
                        for(var i=0; i<2; i++) {
                            prevCount --;nextCount ++;
                            if(prevCount > 0) {
                                str = '<p class="item">'+prevCount+'</p>'+str+'<p class="item">'+nextCount+'</p>';
                            } else {
                                str += '<p class="item">'+nextCount+'</p>';
                                nextCount ++;
                                str += '<p class="item">'+nextCount+'</p>';
                            }
                        }
                        str += '<p class="more">...</p><p class="item">'+page+'</p>';
                    }
                } else {
                    for(var i=1; i<=page; i++) {
                        str += '<p class="item '+(i == self.pageData.curPage ? 'on':'')+'">'+i+'</p>';
                    }
                }
                $obj.find('.load-more .pages').html(str);
                $obj.find('.load-more .pages p').addClass('wqd-fw wqd-fst wqd-ff wqd-bgclr wqd-clr');
            },
            /* ???????????? ????????????????????????????????????????????????????????????????????????????????????????????? */
            bindEvent: function ($obj, callback) {
                var self = this;
                $obj.off('click.load-more').on('click.load-more', '.load-more p', function (e) {
                    var id = $obj.find('.nav span.on').data('categoryid'), curPage = 0, that = $(e.target);
                    if(id != undefined || id != '') {
                        if(that.hasClass('item')) {
                            curPage = that.text();
                        } else if(that.hasClass('first')) {
                            curPage = 1;
                        } else if(that.hasClass('prev')) {
                            curPage = parseInt($obj.attr('curPage')) - 1;
                            if(curPage <= 0) return ;
                        } else if(that.hasClass('next')) {
                            curPage = parseInt($obj.attr('curPage')) + 1;
                            if(curPage > Math.ceil(self.pageData.totalCount / ((-(-$obj.attr('col'))||1) * (-(-$obj.attr('row'))||1)))) return ;
                        } else {
                            return ;
                        }
                        $obj.attr('curPage', curPage);
                        callback($obj);
                    } 
                });
            }
        };
        return new _obj();
    })();

    /**
     * ?????????????????? 1   ????????? pc
     * pcdesign and host ?????????
     * ?????? 
     *    1. ?????????????????????1 ??????  isAdvSearch1
     *    2. ????????????????????????1????????? ??????  renderAdvSearch1
     *    3. ?????????????????????????????? ??????  bindEvent , ?????? ??????????????? callback????????????
     */
    var AdvSearch1 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            isAdvSearch1: function ($obj) {
                return $obj.attr('artsearch') == 'search1' ? true : false;
            },
            renderAdvSearch1: function ($obj, data) {
                var advSearchHtml = '';
                for(var i=0; i<data.length; i++) {
                    if(data[i].code == 'hometown') continue;
                    var ranges = data[i].ranges;
                    var str = (ranges[0] && ranges[0].rangeType || '') != 'Multi' ? 'only' : '';
                    var tagStr = '<span class="tag on wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-code="-1">??????</span>';
                    for(var j=0; j<ranges.length; j++) {
                        tagStr += '<span class="tag wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-val="'+ranges[j].fixedValue+'" data-code="'+data[i].code+'" data-type="'+ranges[j].rangeType+'">'+(ranges[j].fixedAlias || ranges[j].fixedValue)+'</span>';
                    }
                    advSearchHtml += [
                        '<div class="item wqd-brc wqd-brw '+str+'">',
                            '<span class="name wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">'+data[i].fieldName+(str != 'only'?'(??????)':'')+'???</span>',
                            '<div class="sel-tags">',
                                tagStr,
                            '</div>',
                            '<div class="add-more wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????</div>',
                        '</div>'
                    ].join('');
                }
                $obj.find('.advSearch-box .select-group').html(advSearchHtml);
                /* ??????????????????????????????????????? */
                $obj.find('.advSearch-box .select-group .item').each(function (i, _) {
                    var totalWidth = $(_).find('.sel-tags').outerWidth();
                    var sumWidth = 0;
                    $(_).find('.sel-tags span').each(function (i, _) {
                        sumWidth += $(_).outerWidth();
                    });
                    if(sumWidth < totalWidth) { $(_).find('.add-more').hide(); } else { $(_).find('.add-more').show(); }
                });
            },
            bindEvent: function ($obj, callback) {
                /* ?????? ???????????? */
                $obj.off('click.sel-tag').on('click.sel-tag', '.sel-tags .tag', function (e) {
                    if($(this).parents('.item').hasClass('only')) {
                        $(this).addClass('on').siblings().removeClass('on');
                    } else {
                        /* ????????????????????? */
                        if($(this).index() == 0) {
                            if(!$(this).hasClass('on')) {
                                $(this).addClass('on').siblings().removeClass('on');
                            }
                        } else {
                            if($(this).hasClass('on')) {
                                $(this).removeClass('on');
                                if(!$(this).siblings().hasClass('on')) {
                                    $(this).siblings().eq(0).addClass('on');
                                }
                            } else {
                                $(this).addClass('on');
                                $(this).siblings().eq(0).removeClass('on');
                            }
                        }
                    }

                    /* ????????????????????????????????????????????? */
                    callback($obj);
                });
                /* ?????? ???????????? ?????????????????? */
                $obj.off('click.sel-tag-more').on('click.sel-tag-more', '.add-more', function (e) {
                    if($(this).hasClass('on')) {
                        $(this).removeClass('on');
                        $(this).prev().removeAttr('style', '');
                    } else {
                        $(this).addClass('on');
                        $(this).prev().css({
                            'white-space': 'normal'
                        });
                    }
                });
            }
        };
        return new _obj();
    })();

    /**
     * ?????????????????? 2   ????????? phone
     * pcdesign and host ?????????
     * ?????? 
     *    1. ?????????????????????2 ??????  isAdvSearch2
     *    2. ????????????????????????2????????? ??????  renderAdvSearch2
     *    3. ?????????????????????????????? ??????  bindEvent , ?????? ??????????????? callback????????????
     */
    var AdvSearch2 = (function () {
        var _obj = function () {};
        _obj.prototype = {
            isAdvSearch2: function ($obj) {
                return $obj.attr('artsearch') == 'search2' ? true : false;
            },
            renderAdvSearch2: function ($obj, data) {
                var advSearchHtml = '';
                for(var i=0; i<data.length; i++) {
                    if(data[i].code == 'hometown') continue;
                    var ranges = data[i].ranges;
                    var str = (ranges[0] && ranges[0].rangeType || '') != 'Multi' ? 'only' : '';
                    var tagStr = '<div class="item on wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-code="-1"><svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>??????</div>';
                    for(var j=0; j<ranges.length; j++) {
                        tagStr += '<div class="item wqd-bgclr wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst" data-val="'+ranges[j].fixedValue+'" data-code="'+data[i].code+'" data-type="'+ranges[j].rangeType+'"><svg style="display:none;" viewBox="0 0 1024 1024"><path d="M380.416 822.144c-10.432 0-20.864-3.968-28.8-11.968L75.968 534.592c-15.936-15.936-15.936-41.664 0-57.6 15.872-15.872 41.664-15.872 57.536 0L380.416 723.84l510.08-510.016c15.872-15.936 41.664-15.936 57.536 0 15.936 15.936 15.936 41.664 0 57.6L409.216 810.24c-7.936 7.936-18.368 11.904-28.8 11.904z" /></svg>'+(ranges[j].fixedAlias || ranges[j].fixedValue)+'</div>';
                    }
                    advSearchHtml += [
                        '<div class="gr-box">',
                            '<div class="b-hd">',
                                '<div class="left-content">'+data[i].fieldName+(str != 'only'?'(??????)':'')+'</div>',
                                '<div class="right-content"><span>??????</span>',
                                    '<svg class="down" viewBox="0 0 1026 1024"><path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z" p-id="7641"></path></svg>',
                                    '<svg class="up" viewBox="0 0 150.3 150" style="display:none;"><path d="M2.1,113.3c1.3,1.3,3.3,1.3,4.6,0l68.9-68.9l68.9,68.9c1.3,1.3,3.3,1.3,4.6,0c1.3-1.3,1.3-3.3,0-4.6L78,37.5  c-0.6-0.6-1.5-1-2.3-1s-1.7,0.3-2.3,1L2.1,108.7C0.8,110,0.8,112,2.1,113.3z"></path></svg>',
                                '</div>',
                                '<div class="center-content"></div>',
                            '</div>',
                            '<div class="b-bd '+str+'">',
                                tagStr,
                            '</div>',
                        '</div>'
                    ].join('');
                }
                $obj.find('.advSearch-box1 .sel-group-box').html(advSearchHtml);
            },
            bindEvent: function ($obj, callback) {
                /* ?????? ???????????? */
                $('body').off('click.gr-box-bd-advSearch2').on('click.gr-box-bd-advSearch2', '.advSearch-box1.show .gr-box .b-bd .item', function (e) {
                    if($(this).parents('.b-bd').hasClass('only')) {
                        $(this).addClass('on').siblings().removeClass('on');
                    } else {
                        /* ????????????????????? */
                        if($(this).index() == 0) {
                            if(!$(this).hasClass('on')) {
                                $(this).addClass('on').siblings().removeClass('on');
                            }
                        } else {
                            if($(this).hasClass('on')) {
                                $(this).removeClass('on');
                                if(!$(this).siblings().hasClass('on')) {
                                    $(this).siblings().eq(0).addClass('on');
                                }
                            } else {
                                $(this).addClass('on');
                                $(this).siblings().eq(0).removeClass('on');
                            }
                        }
                    }

                    /* ?????????????????? ???????????????????????? */
                    var str = '';
                    $(this).parents('.b-bd').find('.item').each(function (i, _) {
                        if($(_).text() != '??????' && $(_).hasClass('on')) {
                            str += $(_).text() + '???';
                        }
                    });
                    $(this).parents('.b-bd').prev().find('.center-content').html(str.replace(/???$/gi, ''));
                    str == '' && $(this).parents('.b-bd').prev().find('.right-content').removeClass('on');
                    str != '' && $(this).parents('.b-bd').prev().find('.right-content').addClass('on');

                    return false;
                });
                /* ?????? ???????????? ?????????????????? */
                $('body').off('click.sel-show-all-advSearch2').on('click.sel-show-all-advSearch2', '.advSearch-box1.show .gr-box .b-hd', function (e) {
                    if($(this).hasClass('on')) {
                        $(this).removeClass('on');
                        $(this).next().removeClass('on');
                    } else {
                        $(this).addClass('on');
                        $(this).next().addClass('on');
                    }

                    return false;
                });
                // ??????????????? ???host?????? ????????????
                if(!sceneList._str) {
                    $obj.off('click.btn-self-advSearch2').on('click.btn-self-advSearch2', '.btn-self span', function (e) {
                        if($(this).html() == "??????" && !$('body').find('.advSearch-box1.self').length) {
                            var _obj = $obj.find('.advSearch-box1').clone().addClass('self');
                            $('body').append(_obj[0].outerHTML);
                            setTimeout(function () {
                                $('body').find('.advSearch-box1.self').addClass('show');
                            }, 50);
                            // ??????
                            $('body').off('click.clear-advSearch2').on('click.clear-advSearch2', '.advSearch-box1.self .sub-mit', function (e) {
                                // ????????? ????????? ??????????????????
                                /* ????????????????????????????????????????????? */
                                callback($obj);

                                $('body').find('.advSearch-box1.self').removeClass('show');
                                setTimeout(function () {
                                    var _obj = $('body').find('.advSearch-box1.self').clone();
                                    $obj.find('.advSearch-box1').replaceWith(_obj.removeClass('self')[0].outerHTML);
                                    $('body').find('.advSearch-box1.self').remove();
                                }, 700);

                                return false;
                            });
                            // ??????
                            $('body').off('click.reset-advSearch2').on('click.reset-advSearch2', '.advSearch-box1.self .can-cel', function (e) {
                                $('body').find('.advSearch-box1.show .gr-box .b-bd').each(function (i, _) {
                                    $(_).find('.item').eq(0).click();
                                });

                                return false;
                            }); 
                            // ??????
                            $('body').off('click.disappear-advSearch2').on('click.disappear-advSearch2', '.advSearch-box1.self .self-mark', function (e) {
                                if($(e.target).hasClass('self-mark')) {
                                    $('body').find('.advSearch-box1.self').removeClass('show');
                                    setTimeout(function () {
                                        var _obj = $('body').find('.advSearch-box1.self').clone();
                                        $obj.find('.advSearch-box1').replaceWith(_obj.removeClass('self')[0].outerHTML);
                                        $('body').find('.advSearch-box1.self').remove();
                                    }, 700);
                                }
                            });
                        }
                    });
                }

            }
        };
        return new _obj();
    })();

    /**
     * pc???phone??????????????? ??? pcdesign ??? host ???????????????
     * ?????? 
     *    1. ?????????????????? ??????  setSectionHeight  
     *    2. ???????????????????????? ??????  isSetSectionHeight
     *    3. ?????????????????????????????? ??????  resizeImageShow
     *    4. ??????????????????????????????????????? ??????  getLastTopElem
     */
    var sceneCommon = (function () {
        var _obj = function () {};
        _obj.prototype = {
            /* ????????????????????????????????????????????????????????????????????? ?????????????????? */
            bindLoadMoreEvent: function ($obj, callback) {
                Pagination1.isPagination1($obj) && Pagination1.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
                Pagination2.isPagination2($obj) && Pagination2.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
            },
            /* ???????????????????????????????????????????????????????????? ?????????????????? */
            bindNavEvent: function ($obj, callback) {
                Nav2.isNav2($obj) && Nav2.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
                Nav1.isNav1($obj) && Nav1.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
            },
            /* ???????????????????????????????????????????????????????????????????????? ?????????????????? */
            bindAdvSearchEvent: function ($obj, callback) {
                AdvSearch1.isAdvSearch1($obj) && AdvSearch1.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
                AdvSearch2.isAdvSearch2($obj) && AdvSearch2.bindEvent($obj, function ($obj) {
                    callback($obj);
                });
            },
            /**
             * ??????section?????????  -  ???????????? ????????? 
             * ????????????????????? ?????????????????????
             */
            setSectionHeight: function ($obj, sJSON) {
                var self = this;
                var $parent = $obj.parent('section');
                var eJSON = self.getLastTopElem($obj);
                self.isSetSectionHeight($parent) && $parent.css({
                    'height': eJSON.lastTop + (eJSON.$lastElem && eJSON.$lastElem.outerHeight()) + 'px'
                }); 
            },
            /**
             * ?????? ???????????????????????????????????????????????????  ?????????????????????
             */
            isSetSectionHeight: function ($obj) {
                if($obj.closest('.wqdSectiondiv').data('coltype') == 'newColList') {
                    return false;
                } else if($obj.closest('.wqdSectiondiv').data('coltype') == 'sceneColList') {
                    return false;
                }
                return true;
            },
            /**
             * ?????? ?????????????????????????????????????????????????????????????????????????????????
             */
            resizeImageShow: function ($obj) {
                // ??????????????????????????????????????????
                $obj.find('img').each(function (i, _) {
                    var img = new Image();
                    img.src = $(_).attr('src');
                    if(img.complete) {
                        if(img.width/img.height > ($(_).parent().width()/$(_).parent().height())) {
                            $(_).css({ "width": "auto", "height": "100%" });
                        } else {
                            $(_).css({ "width": "100%", "height": "auto" });
                        }
                    } else {
                        (function (img, _) {
                            img.onload = function () {
                                if(this.width/this.height > ($(_).parent().width()/$(_).parent().height())) {
                                    $(_).css({ "width": "auto", "height": "100%" });
                                } else {
                                    $(_).css({ "width": "100%", "height": "auto" });
                                }  
                            }
                        })(img, _);
                    }
                });
            },
            /**
             * ??????????????????  -  ???????????? ?????????
             * ????????????????????????????????? ??????????????? ?????????
             */
            getLastTopElem: function ($obj) {
                var json = {};
                var $parent = $obj.parent('section');
                var $arr = $parent.children('.wqdelementEdit');
                json.top = $obj.position().top;
                json.H = $obj.outerHeight();

                json.lastTop = json.top;
                json.$lastElem = $obj;
                json.offsetH = $parent.outerHeight() - json.lastTop - (json.$lastElem && json.$lastElem.outerHeight());
                $arr.each(function (i, _) {
                    var tempTop = $(_).position().top;
                    if(json.lastTop+json.$lastElem.outerHeight() < tempTop+$(_).outerHeight()) {
                        json.lastTop = tempTop;
                        json.$lastElem = $(_);
                    }
                });
                json.offsetH = $parent.outerHeight() - json.lastTop - (json.$lastElem && json.$lastElem.outerHeight());
                return json;
            },
            AJAX: function (_obj, callback) {
                $.ajax({
                    url: _obj.url,
                    dataType: _obj.dataType,
                    jsonp: _obj.jsonp || '',
                    type: _obj.type,
                    data: _obj.data,
                    success: function (data) {
                        callback(data);
                    }
                });
            } 
        }
        return new _obj();
    })();


    /** 
     * ??????????????????   ????????????
     * ??????????????????  ????????????  ????????????????????????????????? 
     */
    sceneList = {
        /* ?????? ????????????????????? */
        addSectionStr: [
            '<div class="yzmoveContent">',
                '<div class="wqdSectiondiv" data-type="wqdSectiondiv" data-unused="height,elem">',
                    '<section class="wqd1445504393015css wqdBkEditos sectionV2 moveMainArea elementsContainer" style="margin:0 auto;position:relative;"></section>',
                '</div>',
            '</div>' 
        ].join(''),
        listStr: {
            sceneList1: [
                sceneListStr.getNavStyle(),
                sceneListStr.getSearchStyle(),
                '<ul class="comList">',
                    '<li class="list-cell wqd-brc wqd-brw">',
                        '<span class="svg-box"><svg fill="#ff3333" viewBox="0 0 200 200"> <path d="M43.6,42.3L12.4,157.7h144l31.1-115.5H43.6z M112.3,133.3H97.6V80.6c-4.8,3.5-10.1,6.4-15.9,8.7V76   c6.1-2.8,11.7-6.1,17.1-9.9h13.6V133.3z"></path> </svg> </span> ',
                        '<span class="newsTitle wqd-ff wqd-fs wqd-clr wqd-fw wqd-fst">???????????????????????????????????????????????????????????????????????????700??????????????????????????????</span>',
                        '<span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst"> <i> <svg viewBox="0 0 48 48"> <path class="wqd-bgclr" d="M23.4,15.9c-4.2,0-7.7,3.6-7.7,8s3.5,8,7.7,8c1.2,0,2.3-0.3,3.3-0.8c0.5-0.2,0.7-0.9,0.5-1.4c-0.2-0.5-0.8-0.7-1.3-0.5    c-0.8,0.4-1.6,0.6-2.5,0.6c-3.1,0-5.7-2.7-5.7-5.9s2.6-5.9,5.7-5.9c3.1,0,5.7,2.7,5.7,5.9c0,0.6-0.1,1.1-0.2,1.6    c-0.1,0.6,0.1,1.1,0.7,1.3c0.5,0.2,1.1-0.2,1.2-0.7c0.2-0.7,0.3-1.5,0.3-2.2C31.1,19.5,27.7,15.9,23.4,15.9z M19.8,22.4    c-0.2,0.5-0.3,1-0.3,1.5c0,0.3,0.2,0.5,0.5,0.5c0.3,0,0.5-0.2,0.5-0.5c0-0.4,0.1-0.7,0.2-1.1c0.4-1.2,1.5-1.9,2.7-1.9    c0.3,0,0.5-0.2,0.5-0.5c0-0.3-0.2-0.5-0.5-0.5C21.8,19.8,20.4,20.9,19.8,22.4z M46.2,22.6C40.5,15.2,32.1,9.7,23.4,9.7    c-8.7,0-15.9,5.4-21.6,12.8c-0.1,0.2-0.2,0.4-0.2,0.6v1.3c0,0.2,0.1,0.5,0.2,0.6C7.5,32.6,14.7,38,23.4,38c8.7,0,17-5.4,22.8-12.9    c0.1-0.2,0.2-0.4,0.2-0.6v-1.3C46.4,23,46.4,22.8,46.2,22.6z M44.4,24.2c-5.4,6.8-13,11.8-21,11.8s-14.5-5-19.8-11.8v-0.6    C9,16.8,15.4,11.8,23.4,11.8c8,0,15.6,5,21,11.8C44.4,23.6,44.4,24.2,44.4,24.2z"></path> </svg> </i>9999W+ </span> ',
                    '</li>',
                    sceneListStr.getLoadMoreStyle(),
                '</ul>'
            ].join(''),
            sceneList2: [
                sceneListStr.getNavStyle(),
                sceneListStr.getSearchStyle(),
                '<div class="comList">',
                    '<div class="list-cell">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????</p>',
                        '<div class="imgGroup">',
                            '<div class="img-box wqd-h"><img src="http://ohuih434n.bkt.clouddn.com/qngroup001%2Fu434341%2F1%2F0%2Fdb3ee3c73ace40119a96f965d8b78b18.jpg" ondragstart="return false;"></div>',
                            '<div class="img-box wqd-h"><img src="http://ohuih434n.bkt.clouddn.com/qngroup001%2Fu434341%2F1%2F0%2F980d7bb8ecca47508871edaaaf9ba03c.jpg" ondragstart="return false;"></div>',
                            '<div class="img-box wqd-h"><img src="http://ohuih434n.bkt.clouddn.com/qngroup001%2Fu434341%2F1%2F0%2F66a8cb03179c41ce830879f953c87208.jpg" ondragstart="return false;"></div>',
                        '</div>',
                        '<div class="footer">',
                            '<div class="fl">',
                                '<span class="show-time wqd-clr wqd-bgclr wqd-fw wqd-fst">2017-08-17</span>',
                                '<span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff midSpan">?????????</span>',
                                '<span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">????????????</span>',
                            '</div>',
                            '<div class="fr">',
                                '<span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst"><i class="icon-fav"> <svg viewBox="0 0 48 48">  <g> <g> <path class="st0 wqd-bgclr" d="M36.4,19.2C36,19,35.5,19,34.9,19H31c0.5-2.3,0.6-5.6,0.6-7.6c0-2.3-1.9-4.2-4.2-4.2c-2.3,0-4.2,1.9-4.2,4.2    v0.8c0,4.6-3.7,8.3-8.2,8.4c-0.1,0-0.1,0-0.2,0h-4.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h4.2    c0.4,0,0.8-0.4,0.8-0.9V22.2c5.2-0.4,9.3-4.7,9.3-10v-0.8c0-1.3,1.1-2.5,2.5-2.5c1.3,0,2.5,1.1,2.5,2.5c0,3.8-0.3,6.8-0.8,8.1    c-0.1,0.2,0,0.5,0.1,0.8c0.1,0.2,0.4,0.4,0.7,0.4h5c0.4,0,0.8,0,1.1,0.1c2.3,0.6,3.6,2.9,3,5.1c-0.7,2.5-4.1,11.2-4.4,12    c-0.5,0.8-1.2,1.2-2.2,1.2H19.8c-0.4,0-0.8,0.3-0.8,0.8c0,0.4,0.3,0.8,0.8,0.8h12.6c0,0,0.1,0,0.2,0c1.4,0,2.7-0.8,3.5-2.1    c0,0,0,0,0-0.1c0.1-0.4,3.8-9.6,4.5-12.2C41.4,23.3,39.6,20,36.4,19.2z M13.9,39h-3.4c-0.9,0-1.7-0.8-1.7-1.7V23.9    c0-0.9,0.8-1.7,1.7-1.7h3.4V39z"></path> </g> </g> </svg> </i>3672</span>',
                                '<span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst"><i class="icon-view"> <svg viewBox="0 0 48 48"> <g> <g> <path class="wqd-bgclr" d="M23.4,15.9c-4.2,0-7.7,3.6-7.7,8s3.5,8,7.7,8c1.2,0,2.3-0.3,3.3-0.8c0.5-0.2,0.7-0.9,0.5-1.4c-0.2-0.5-0.8-0.7-1.3-0.5    c-0.8,0.4-1.6,0.6-2.5,0.6c-3.1,0-5.7-2.7-5.7-5.9s2.6-5.9,5.7-5.9c3.1,0,5.7,2.7,5.7,5.9c0,0.6-0.1,1.1-0.2,1.6    c-0.1,0.6,0.1,1.1,0.7,1.3c0.5,0.2,1.1-0.2,1.2-0.7c0.2-0.7,0.3-1.5,0.3-2.2C31.1,19.5,27.7,15.9,23.4,15.9z M19.8,22.4    c-0.2,0.5-0.3,1-0.3,1.5c0,0.3,0.2,0.5,0.5,0.5c0.3,0,0.5-0.2,0.5-0.5c0-0.4,0.1-0.7,0.2-1.1c0.4-1.2,1.5-1.9,2.7-1.9    c0.3,0,0.5-0.2,0.5-0.5c0-0.3-0.2-0.5-0.5-0.5C21.8,19.8,20.4,20.9,19.8,22.4z M46.2,22.6C40.5,15.2,32.1,9.7,23.4,9.7    c-8.7,0-15.9,5.4-21.6,12.8c-0.1,0.2-0.2,0.4-0.2,0.6v1.3c0,0.2,0.1,0.5,0.2,0.6C7.5,32.6,14.7,38,23.4,38c8.7,0,17-5.4,22.8-12.9    c0.1-0.2,0.2-0.4,0.2-0.6v-1.3C46.4,23,46.4,22.8,46.2,22.6z M44.4,24.2c-5.4,6.8-13,11.8-21,11.8s-14.5-5-19.8-11.8v-0.6    C9,16.8,15.4,11.8,23.4,11.8c8,0,15.6,5,21,11.8C44.4,23.6,44.4,24.2,44.4,24.2z"></path> </g> </g> </svg> </i>2560</span>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                    sceneListStr.getLoadMoreStyle(),
                '</div>'
            ].join(''),
            sceneList3: [
                sceneListStr.getNavStyle(),
                sceneListStr.getSearchStyle(),
                '<div class="comList">',
                    '<div class="list-cell">',
                        '<div class="left-con wqd-w wqd-h"> <img src="http://ohuih434n.bkt.clouddn.com/qngroup001%2Fu434341%2F1%2F0%2Fdb3ee3c73ace40119a96f965d8b78b18.jpg " ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????? ???H5???????????????????????????</p>',
                            '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst ">?????????????????????H5?????????????????????????????????????????????????????????????????????HTML5????????????????????????</p>',
                            '<div class="infoBar">',
                                '<div class="tags"> <span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">??????</span> <span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">??????</span> </div>',
                                '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst">2016/6/18 15:50</div>',
                                '<div class="info"> <span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:1829</span> <span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:39</span> </div>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                    sceneListStr.getLoadMoreStyle(),
                '</div>'
            ].join(''),
            sceneList4: [
                '<div class="nav">',
                    '<hr class="line">',
                    '<span class="on wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="margin:0 2px;">????????????</span>',
                    '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="margin:0 2px;">????????????</span>',
                    '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="margin:0 2px;">????????????</span>',
                    '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc" style="margin:0 2px;">????????????</span>',
                '</div>',
                sceneListStr.getSearchStyle(),
                '<div class="comList">',
                    '<div class="list-cell">',
                        '<div class="left-con wqd-w wqd-h"> <img src="http://ohuih434n.bkt.clouddn.com/qngroup001%2Fu434341%2F1%2F0%2Fdb3ee3c73ace40119a96f965d8b78b18.jpg " ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????? ???H5???????????????????????????</p>',
                            '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst ">?????????????????????H5?????????????????????????????????????????????????????????????????????HTML5????????????????????????</p>',
                            '<div class="infoBar">',
                                '<div class="tags"> <span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">??????</span> <span class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">??????</span> </div>',
                                '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst">2016/6/18 15:50</div>',
                                '<div class="info"> <span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:1829</span> <span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:39</span> </div>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                    sceneListStr.getLoadMoreStyle(),
                '</div>'
            ].join(''),
            sceneList5: [
                sceneListStr.getNavStyle(),
                sceneListStr.getSearchStyle(),
                '<div class="comList">',
                    '<div class="list-cell wqd-brs wqd-brc wqd-brw wqd-mgn-t">',
                        '<div class="left-con wqd-w wqd-h wqd-mgn-r"> <img src="http://u10161-b56ce923f0cc4660a7b8c3d8b0496e44.ktb.wqdian.xin/qngroup001%2Fu10161%2F1%2F0%2F977c45086bdd4662aa39a4ec7c595f74.jpg" ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<div style="line-height:0;font-size:0;"><span style="display:-webkit-box;display:-moz-box;display:box;width:1px;height:1px;"></span></div>',  //????????? ??????????????????????????????????????????????????????????????????
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">?????????</p>',
                            '<div class="tags">',
                                '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                            '</div>',
                            '<div class="detail wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">45??? | ?????? | 115???</div>',
                            '<p class="money wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???60 <em class=" wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">/ ??????</em></p>',
                        '</div>',
                    '</div>',
                    sceneListStr.getLoadMoreStyle(),
                '</div>'
            ].join(''),
            sceneList6: [
                sceneListStr.getNavStyle(),
                sceneListStr.getSearchStyle(),
                '<div class="comList">',
                    '<div class="list-cell wqd-brs wqd-brc wqd-brw wqd-mgn-t">',
                        '<div class="top-con wqd-h"> <img src="http://u10161-b56ce923f0cc4660a7b8c3d8b0496e44.ktb.wqdian.xin/qngroup001%2Fu10161%2F1%2F0%2F0378948d64b944efb29c0715c81e7ad5.jpg" ondragstart="return false; "> </div>',
                        '<div class="bottom-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">????????? ??? ???????????????????????? -1?????? </p>',
                            '<p class="detail wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst ">??????????????????????????????????????? </p>',
                            '<hr class="line wqd-brc wqd-brw">',
                            '<div class="money wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???259.00/???</div>',
                            '<div class="fav wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????338?????????</div>',
                        '</div>',
                    '</div>',
                    sceneListStr.getLoadMoreStyle(),
                '</div>'
            ].join('')
        },
        listNullStr: {
            sceneList1: [
                '<li class="list-cell artPlugin-no-data wqd-brc wqd-brw">',
                    '<span class="svg-box"></span>',
                    '<span class="newsTitle wqd-ff wqd-fs wqd-clr wqd-fw wqd-fst">???????????????</span>',
                    '<span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst"></span>',
                '</li>'
            ].join(''),
            sceneList2: [
                '<div class="list-cell artPlugin-no-data">',
                    '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</p>',
                    '<div class="imgGroup"></div>',
                    '<div class="footer">',
                        '<div class="fl"><span></span></div>',
                        '<div class="fr"></div>',
                    '</div>',
                    '<hr class="line wqd-brc wqd-brw">',
                '</div>'
            ].join(''),
            sceneList3: [
                '<div class="list-cell artPlugin-no-data">',
                    '<div class="right-con" style="margin-left:0;">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</p>',
                        '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst"></p>',
                        '<div class="infoBar">',
                            '<div class="tags"></div>',
                            '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst"></div>',
                            '<div class="info"></div>',
                        '</div>',
                    '</div>',
                    '<hr class="line wqd-brc wqd-brw">',
                '</div>'
            ].join(''),
            sceneList4: [
                '<div class="list-cell artPlugin-no-data">',
                    '<div class="right-con" style="margin-left:0;">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</p>',
                        '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst"></p>',
                        '<div class="infoBar">',
                            '<div class="tags"></div>',
                            '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst"></div>',
                            '<div class="info"></div>',
                        '</div>',
                    '</div>',
                    '<hr class="line wqd-brc wqd-brw">',
                '</div>'
            ].join(''),
            sceneList5: [
                '<div class="list-cell artPlugin-no-data">',
                    '<div class="right-con" style="margin-left:0;">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</p>',
                        '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst"></p>',
                        '<div class="infoBar">',
                            '<div class="tags"></div>',
                            '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst"></div>',
                            '<div class="info"></div>',
                        '</div>',
                    '</div>',
                    '<hr class="line wqd-brc wqd-brw">',
                '</div>'
            ].join(''),
            sceneList6: [
                '<div class="list-cell artPlugin-no-data">',
                    '<div class="right-con" style="margin-left:0;">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???????????????</p>',
                    '</div>',
                '</div>'
            ].join('')
        },
        listFun: {
            sceneList1Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];
                // ??????svg??????
                var svgArr = [
                    ['<svg fill="#ff3333" viewBox="0 0 200 200"><path d="M43.6,42.3L12.4,157.7h144l31.1-115.5H43.6z M112.3,133.3H97.6V80.6c-4.8,3.5-10.1,6.4-15.9,8.7V76   c6.1-2.8,11.7-6.1,17.1-9.9h13.6V133.3z"></path></svg>'].join(''),
                    ['<svg fill="#ff5c5c" viewBox="0 0 200 200"><path d="M43.6,42L12.4,157.5h144L187.6,42H43.6z M123.3,133.2H76.7v-13.3c1-1.2,3.6-3.6,7.8-7c16.3-13.8,24.3-23.3,24-28.4   c-0.5-5.4-3.2-8.2-8.1-8.5c-4.9,0.5-7.6,3.8-8.1,10H77.8c0.2-12.6,7.8-19.1,22.5-19.6c14.5,0.7,22,6.9,22.5,18.5   c0,7.6-7.1,17.4-21.4,29.2c-5.2,4.4-8.6,7.6-10.3,9.6h32.1V133.2z"></path></svg>'].join(''),
                    ['<svg fill="#ff9999" viewBox="0 0 200 200"><path d="M43.6,42L12.4,157.5h144L187.6,42H43.6z M99.5,133.2c-14.6,0-21.9-6.2-21.9-18.6h14.3c0.2,5.8,2.9,9.1,8,9.9   c5.1-0.5,7.9-3.8,8.4-9.9c-0.2-7.3-5.5-10.8-15.7-10.6v-9.1c10.7-0.2,16-3.7,15.7-10.2c-0.2-5.6-2.9-8.5-8-8.8   c-4.4,0.7-6.9,3.7-7.7,8.8H78.3c0.7-11.7,8-17.8,21.9-18.3c13.6,1.2,20.9,6.7,21.9,16.4c-0.2,8.8-4.4,14.1-12.4,16.1   c8.5,2.4,12.8,8.3,12.8,17.5C121.3,126.6,113.6,132.2,99.5,133.2z"></path></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><polygon points="101.4,108.1 101.4,84.3 85.2,108.1  "></polygon> <path d="M43.6,42.3L12.4,157.7h144l31.1-115.5H43.6z M124.9,119.1h-9.4v14.4h-14v-14.4H74.2v-12.8l27.2-39.7h14v41.6h9.4V119.1z"></path></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M43.6,42.3L12.4,157.7h144l31.1-115.5H43.6z M101.1,133.4c-14.2-0.3-21.5-6-22-17.2h14.2c0.7,5.2,3.5,8,8.2,8.2   c6-0.2,9.2-5,9.7-14.2c-0.5-7.5-3.6-11.5-9.3-12c-4,0.3-6.7,2.7-8.2,7.5l-14.2-0.4l1.9-38.9h40.3v9.3h-28l-0.7,17.6   c3.2-3.2,7.3-4.9,12.3-4.9c13.2,0.5,19.9,8.1,20.2,22.8C124.1,125.1,116,132.4,101.1,133.4z"></path></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M100.5,99.9c-5.4,0.2-8.3,4.3-8.8,12.1c0.5,8.1,3.4,12.2,8.8,12.5c5.6-0.2,8.6-4.4,8.8-12.5   C109.1,104.2,106.1,100.2,100.5,99.9z"/><path d="M43.6,42.3L12.4,157.7h144l31.1-115.5H43.6z M118.5,126.3c-4.9,4.4-11.1,6.7-18.7,7c-15.2,0-22.7-9.2-22.7-27.5   c-0.7-26.6,7.8-39.9,25.7-39.6c12.7,0.7,19.2,6.5,19.4,17.2h-13.9c0-2.9-0.6-5-1.8-6.2c-1.5-1-3.3-1.5-5.5-1.5   c-5.9,0.2-8.9,6.7-9.2,19.4c2.9-3.2,7-4.8,12.1-4.8c12.7,0.7,19.4,7.5,20.2,20.2C124,116.9,122.2,122.2,118.5,126.3z"/></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M44.1,42.3L12.9,157.7h144l31.1-115.5H44.1z M124,78.7l-20.9,54.6H88.8l21.6-56.1h-30v-11H124V78.7z"/></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M100.5,104.2c-5.8,0.5-8.9,3.9-9.1,10.2c0.2,6.6,3.4,10,9.5,10.2c5.6-0.5,8.6-3.9,9.1-10.2   C109.5,107.8,106.3,104.4,100.5,104.2z"/><path d="M100.1,76.2c-5.6,0.5-8.5,3.3-8.7,8.4c0.2,5.6,3.5,8.5,9.8,8.7c5.6-0.5,8.5-3.4,8.7-8.7C109.7,79.2,106.5,76.4,100.1,76.2z   "/><path d="M44.1,42.3L12.9,157.7h144l31.1-115.5H44.1z M100.5,133.3c-15-0.5-22.8-6.7-23.3-18.6c0.5-8,5-13.3,13.5-16v-0.4   c-8.3-2.4-12.4-7.3-12.4-14.6c1-10.2,8.4-15.9,22.2-17.1c14.6,0.7,22.1,6.4,22.6,17.1c-0.7,7.8-5,12.6-12.7,14.6   c8.7,2.4,13.2,7.9,13.5,16.4C122.8,126.4,115.1,132.6,100.5,133.3z"/></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M103.4,76.9c-6.1,0-9.1,3.9-9.1,11.7c0,7.3,3,11,9.1,11c5.4-0.2,8.2-3.9,8.4-11C111.6,81.1,108.8,77.2,103.4,76.9z"/><path d="M44.3,43.1L13.1,158.6h144l31.1-115.5H44.3z M102.3,134.3c-14.1,0.2-21.3-5.4-21.6-16.8H95c1,5.4,3.3,8,6.9,8   c7.3,0,10.6-6.8,9.9-20.5c-3.7,2.9-7.9,4.3-12.8,4c-11.7-1.2-18.2-7.8-19.4-19.7c1-13.6,8.6-21,23-21.9   c15.8-0.2,23.6,9.4,23.4,28.9C127.1,122.1,119.2,134.8,102.3,134.3z"/></svg>'].join(''),
                    ['<svg fill="#cccccc" viewBox="0 0 200 200"><path d="M121.9,77c-5.6,0.2-8.4,8.4-8.4,24.6c0,15.9,2.8,24,8.4,24.2c5.6-0.2,8.4-8.3,8.4-24.2C130.3,85.4,127.5,77.2,121.9,77z"/><path d="M44.3,43.1L13.1,158.6h144l31.1-115.5H44.3z M88.2,133.8H73.9v-51c-4.6,3.4-9.8,6.2-15.4,8.4V78.4   c5.9-2.7,11.4-5.9,16.5-9.5h13.2V133.8z M121.5,134.5c-14.4-0.2-21.9-11.2-22.4-33c0.5-22.5,7.9-33.9,22.4-34.1   c15.2,0.2,23,11.6,23.5,34.1C144.8,123.3,136.9,134.3,121.5,134.5z"/></svg>'].join('')
                ];

                var news = data.news;
                return [
                '<a href="page_'+pageId + '_' +news.id+'.html">',
                    '<li class="list-cell wqd-brc wqd-brw">',
                        '<span class="svg-box">',
                            svgArr[index] || (index+1),
                        '</span>',
                        '<span class="newsTitle wqd-ff wqd-fs wqd-clr wqd-fw wqd-fst">',
                            (news.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                            news.title,
                        '</span>',
                        '<span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst">',
                            '<i>',
                                '<svg viewBox="0 0 48 48"><path class="wqd-bgclr" d="M23.4,15.9c-4.2,0-7.7,3.6-7.7,8s3.5,8,7.7,8c1.2,0,2.3-0.3,3.3-0.8c0.5-0.2,0.7-0.9,0.5-1.4c-0.2-0.5-0.8-0.7-1.3-0.5    c-0.8,0.4-1.6,0.6-2.5,0.6c-3.1,0-5.7-2.7-5.7-5.9s2.6-5.9,5.7-5.9c3.1,0,5.7,2.7,5.7,5.9c0,0.6-0.1,1.1-0.2,1.6    c-0.1,0.6,0.1,1.1,0.7,1.3c0.5,0.2,1.1-0.2,1.2-0.7c0.2-0.7,0.3-1.5,0.3-2.2C31.1,19.5,27.7,15.9,23.4,15.9z M19.8,22.4    c-0.2,0.5-0.3,1-0.3,1.5c0,0.3,0.2,0.5,0.5,0.5c0.3,0,0.5-0.2,0.5-0.5c0-0.4,0.1-0.7,0.2-1.1c0.4-1.2,1.5-1.9,2.7-1.9    c0.3,0,0.5-0.2,0.5-0.5c0-0.3-0.2-0.5-0.5-0.5C21.8,19.8,20.4,20.9,19.8,22.4z M46.2,22.6C40.5,15.2,32.1,9.7,23.4,9.7    c-8.7,0-15.9,5.4-21.6,12.8c-0.1,0.2-0.2,0.4-0.2,0.6v1.3c0,0.2,0.1,0.5,0.2,0.6C7.5,32.6,14.7,38,23.4,38c8.7,0,17-5.4,22.8-12.9    c0.1-0.2,0.2-0.4,0.2-0.6v-1.3C46.4,23,46.4,22.8,46.2,22.6z M44.4,24.2c-5.4,6.8-13,11.8-21,11.8s-14.5-5-19.8-11.8v-0.6    C9,16.8,15.4,11.8,23.4,11.8c8,0,15.6,5,21,11.8C44.4,23.6,44.4,24.2,44.4,24.2z"></path></svg>',
                            '</i>'+(news.initialPageView+news.pageView)+' </span>',
                    '</li>',
                '</a>'
                ].join('');
            },
            sceneList2Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];
                var news = data.news;

                /**
                 * ?????????????????????
                 * @param {[Array]} arr ????????????????????????
                 */
                function getTags (arr) {
                    var i = 0, str='';
                    for (;i<arr.length;i++) {
                        str += '<span data-tagid="'+arr[i].id+'" class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">'+arr[i].name+'</span>';
                    }
                    return str;
                }

                return [
                '<a href="page_'+pageId + '_' +news.id+'.html">',
                    '<div class="list-cell">',
                        '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">',
                            (news.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                            news.title,
                        '</p>',
                        '<div class="imgGroup">',
                            '<div class="img-box wqd-h"><img src="' + (!!news.coverPicture.split(',')[0] ? (/(http)|(https)/gi.test(news.coverPicture.split(',')[0])?(news.coverPicture.split(',')[0]):(CSSURLPATH + news.coverPicture.split(',')[0])) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false;" onerror="this.setAttribute(\'src\',\''+sceneList._str+'images/news-demo/imgerror.png\');onerror = null;"/></div>',
                            '<div class="img-box wqd-h"><img src="' + (!!news.coverPicture.split(',')[1] ? (/(http)|(https)/gi.test(news.coverPicture.split(',')[1])?(news.coverPicture.split(',')[1]):(CSSURLPATH + news.coverPicture.split(',')[1])) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false;" onerror="this.setAttribute(\'src\',\''+sceneList._str+'images/news-demo/imgerror.png\');onerror = null;"/></div>',
                            '<div class="img-box wqd-h"><img src="' + (!!news.coverPicture.split(',')[2] ? (/(http)|(https)/gi.test(news.coverPicture.split(',')[2])?(news.coverPicture.split(',')[2]):(CSSURLPATH + news.coverPicture.split(',')[2])) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false;" onerror="this.setAttribute(\'src\',\''+sceneList._str+'images/news-demo/imgerror.png\');onerror = null;"/></div>',
                        '</div>',
                        '<div class="footer">',
                            '<div class="fl">',
                                '<span class="show-time wqd-clr wqd-bgclr wqd-fw wqd-fst">'+news.issueDate.split(' ')[0]+'</span>',
                                getTags(data.tags),
                            '</div>',
                            '<div class="fr">',
                                '<span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst"> <i class="icon-fav">',
                                    '<svg viewBox="0 0 48 48" >',
                                        '<path class="st0 wqd-bgclr" d="M36.4,19.2C36,19,35.5,19,34.9,19H31c0.5-2.3,0.6-5.6,0.6-7.6c0-2.3-1.9-4.2-4.2-4.2c-2.3,0-4.2,1.9-4.2,4.2    v0.8c0,4.6-3.7,8.3-8.2,8.4c-0.1,0-0.1,0-0.2,0h-4.2c-1.9,0-3.4,1.5-3.4,3.4v13.4c0,1.9,1.5,3.4,3.4,3.4h4.2    c0.4,0,0.8-0.4,0.8-0.9V22.2c5.2-0.4,9.3-4.7,9.3-10v-0.8c0-1.3,1.1-2.5,2.5-2.5c1.3,0,2.5,1.1,2.5,2.5c0,3.8-0.3,6.8-0.8,8.1    c-0.1,0.2,0,0.5,0.1,0.8c0.1,0.2,0.4,0.4,0.7,0.4h5c0.4,0,0.8,0,1.1,0.1c2.3,0.6,3.6,2.9,3,5.1c-0.7,2.5-4.1,11.2-4.4,12    c-0.5,0.8-1.2,1.2-2.2,1.2H19.8c-0.4,0-0.8,0.3-0.8,0.8c0,0.4,0.3,0.8,0.8,0.8h12.6c0,0,0.1,0,0.2,0c1.4,0,2.7-0.8,3.5-2.1    c0,0,0,0,0-0.1c0.1-0.4,3.8-9.6,4.5-12.2C41.4,23.3,39.6,20,36.4,19.2z M13.9,39h-3.4c-0.9,0-1.7-0.8-1.7-1.7V23.9    c0-0.9,0.8-1.7,1.7-1.7h3.4V39z"></path>',                                    '</svg>',
                                '</i>'+ (news.initialPraiseAmount+news.praiseAmount) +'</span>',
                                '<span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst"> <i class="icon-view">',
                                    '<svg viewBox="0 0 48 48"><path class="wqd-bgclr" d="M23.4,15.9c-4.2,0-7.7,3.6-7.7,8s3.5,8,7.7,8c1.2,0,2.3-0.3,3.3-0.8c0.5-0.2,0.7-0.9,0.5-1.4c-0.2-0.5-0.8-0.7-1.3-0.5    c-0.8,0.4-1.6,0.6-2.5,0.6c-3.1,0-5.7-2.7-5.7-5.9s2.6-5.9,5.7-5.9c3.1,0,5.7,2.7,5.7,5.9c0,0.6-0.1,1.1-0.2,1.6    c-0.1,0.6,0.1,1.1,0.7,1.3c0.5,0.2,1.1-0.2,1.2-0.7c0.2-0.7,0.3-1.5,0.3-2.2C31.1,19.5,27.7,15.9,23.4,15.9z M19.8,22.4    c-0.2,0.5-0.3,1-0.3,1.5c0,0.3,0.2,0.5,0.5,0.5c0.3,0,0.5-0.2,0.5-0.5c0-0.4,0.1-0.7,0.2-1.1c0.4-1.2,1.5-1.9,2.7-1.9    c0.3,0,0.5-0.2,0.5-0.5c0-0.3-0.2-0.5-0.5-0.5C21.8,19.8,20.4,20.9,19.8,22.4z M46.2,22.6C40.5,15.2,32.1,9.7,23.4,9.7    c-8.7,0-15.9,5.4-21.6,12.8c-0.1,0.2-0.2,0.4-0.2,0.6v1.3c0,0.2,0.1,0.5,0.2,0.6C7.5,32.6,14.7,38,23.4,38c8.7,0,17-5.4,22.8-12.9    c0.1-0.2,0.2-0.4,0.2-0.6v-1.3C46.4,23,46.4,22.8,46.2,22.6z M44.4,24.2c-5.4,6.8-13,11.8-21,11.8s-14.5-5-19.8-11.8v-0.6    C9,16.8,15.4,11.8,23.4,11.8c8,0,15.6,5,21,11.8C44.4,23.6,44.4,24.2,44.4,24.2z"></path>   </svg>',
                                '</i>'+ (news.initialPageView+news.pageView) +'</span>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                '</a>'
                ].join('');
            },
            sceneList3Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];
                var news = data.news;

                /**
                 * ?????????????????????
                 * @param {[Array]} arr ????????????????????????
                 */
                function getTags (arr) {
                    var i = 0, str = '';
                    for (; i<arr.length; i++) {
                        str += '<span data-tagid="'+arr[i].id+'" class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">'+arr[i].name+'</span>';
                    }
                    return str;
                }

                return [
                '<a href="page_'+pageId + '_' +news.id+'.html">',
                    '<div class="list-cell">',
                        '<div class="left-con wqd-w wqd-h"> <img src="' + (!!news.coverPicture.split(',')[0] ? (/(http)|(https)/gi.test(news.coverPicture.split(',')[0])?(news.coverPicture.split(',')[0]):(CSSURLPATH + news.coverPicture.split(',')[0])) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">',
                                (news.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                                news.title,
                            '</p>',
                            '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">'+ news.digest +'</p>',
                            '<div class="infoBar">',
                                '<div class="tags">'+getTags(data.tags)+'</div>',
                                '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst">'+news.issueDate.split(' ')[0]+'</div>',
                                '<div class="info"> <span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:'+ (news.initialPageView+news.pageView) +'</span> <span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:'+ (news.initialPraiseAmount+news.praiseAmount) +'</span> </div>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                '</a>'
                ].join('');
            },
            sceneList4Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];
                var news = data.news;

                /**
                 * ?????????????????????
                 * @param {[Array]} arr ????????????????????????
                 */
                function getTags (arr) {
                    var i = 0, str = '';
                    for (; i<arr.length; i++) {
                        str += '<span data-tagid="'+arr[i].id+'" class="tag wqd-clr wqd-bgclr wqd-fw wqd-fst wqd-ff">'+arr[i].name+'</span>';
                    }
                    return str;
                }

                return [
                '<a href="page_'+pageId + '_' +news.id+'.html">',
                    '<div class="list-cell">',
                        '<div class="left-con wqd-w wqd-h"> <img src="' + (!!news.coverPicture.split(',')[0] ? (/(http)|(https)/gi.test(news.coverPicture.split(',')[0])?(news.coverPicture.split(',')[0]):(CSSURLPATH + news.coverPicture.split(',')[0])) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">',
                                (news.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                                news.title,
                            '</p>',
                            '<p class="content wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">'+ news.digest +'</p>',
                            '<div class="infoBar">',
                                '<div class="tags">'+getTags(data.tags)+'</div>',
                                '<div class="time wqd-clr wqd-bgclr wqd-fw wqd-fst">'+news.issueDate.split(' ')[0]+'</div>',
                                '<div class="info"> <span class="view wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:'+ (news.initialPageView+news.pageView) +'</span> <span class="fav wqd-clr wqd-bgclr wqd-fw wqd-fst">??????:'+ (news.initialPraiseAmount+news.praiseAmount) +'</span> </div>',
                            '</div>',
                        '</div>',
                        '<hr class="line wqd-brc wqd-brw">',
                    '</div>',
                '</a>'
                ].join('');
            },
            sceneList5Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];
                /**
                 * ?????????????????????
                 * @param {[number]} star ????????????????????????
                 */
                function getTags (star) {
                    var i = 0, str = '', star = star || 0;
                    for (; i<parseInt(star); i++) {
                        str += '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>';
                    }
                    return str;
                }
                return [
                '<a href="page_'+data.sceneId+'_'+pageId + '_' +data.id+'.html">',
                    '<div class="list-cell wqd-brs wqd-brc wqd-brw wqd-mgn-t">',
                        '<div class="left-con wqd-w wqd-h wqd-mgn-r"> <img src="' + (!!data.icon ? (CSSURLPATH + data.icon) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false; "> </div>',
                        '<div class="right-con">',
                            '<div style="line-height:0;font-size:0;"><span style="display:-webkit-box;display:-moz-box;display:box;width:1px;height:1px;"></span></div>',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">',
                                '<span style="visibility:hidden;">|</span>',
                                // (data.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                                data.name,
                            '</p>',
                            '<div class="tags">',
                                data.star != undefined && '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                data.star != undefined && '<svg class="wqd-h" viewBox="0 0 500 476.6" xml:space="preserve"><path class="wqd-bgclr" fill="rgb(255, 177, 61)" d="M490.2,209.6c17.2-16.7,7.7-45.9-16-49.3l-122.1-17.7c-9.4-1.4-17.6-7.3-21.7-15.8L275.8,16.1c-10.6-21.5-41.3-21.5-51.9,0l-54.6,110.6c-4.2,8.6-12.4,14.4-21.7,15.8L25.5,160.2c-23.7,3.4-33.2,32.6-16,49.3l88.3,86c6.9,6.6,10,16.2,8.4,25.6L85.3,442.6c-4,23.7,20.7,41.6,42,30.5l109.1-57.4c8.5-4.5,18.5-4.5,26.9,0l109.1,57.4c21.3,11.2,46-6.9,42-30.5l-20.8-121.5c-1.6-9.4,1.5-19,8.4-25.6L490.2,209.6z"></path></svg>',
                                getTags(data.star),
                            '</div>',
                            '<div class="detail wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">'+data.age+'??? | '+data.hometown+' | '+data.deals+'???</div>',
                            '<p class="money wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???'+data.price+' <em class="wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">/ '+data.workUnitStr+'</em></p>',
                        '</div>',
                    '</div>',
                '</a>'
                ].join('');
            },
            sceneList6Fun: function () {
                var data = arguments[0], flagStr = arguments[1], index = arguments[2];
                var pageId = arguments[3];

                return [
                '<a href="page_'+data.sceneId+'_'+pageId + '_' +data.id+'.html">',
                    '<div class="list-cell wqd-brs wqd-brc wqd-brw wqd-mgn-t">',
                        '<div class="top-con wqd-h"> <img src="' + (!!data.icon ? (CSSURLPATH + data.icon) : sceneList._str+'images/news-demo/wqd_no_img.png') +'" ondragstart="return false; "> </div>',
                        '<div class="bottom-con">',
                            '<p class="title wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">',
                                '<span style="visibility:hidden;">|</span>',
                                // (data.isRecommend == "YES" ? '<em class="recommend">??????</em>' : ''),
                                data.name,
                            '</p>',
                            '<p class="detail wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst "><span style="visibility:hidden;">|</span>'+ (data.certificate && data.certificate.split(',').join('???')) +'</p>',
                            '<hr class="line wqd-brc wqd-brw">',
                            '<div class="money wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">???'+data.price+'/'+data.workUnitStr+'</div>',
                            '<div class="fav wqd-clr wqd-ff wqd-fs wqd-fw wqd-fst">??????'+ data.deals +'?????????</div>',
                        '</div>',
                    '</div>',
                '</a>'
                ].join('');
            }
        },
        /* lib?????? ?????????????????? ?????????????????????????????? */
        arrArtStr: [
            ".sceneList1", 
            ".sceneList2", 
            ".sceneList3", 
            ".sceneList4", 
            ".sceneList5", 
            ".sceneList6"
        ].join(','),
        /* ?????? ?????????????????? ???????????????????????? */
        arrArtContStr: [
            ".ListScene_1",
            ".ListScene_2",
            ".ListScene_3",
            ".ListScene_4",
            ".ListScene_5",
            ".ListScene_6"
        ].join(','),
        /* ???????????? /pcdesign/ ??????  host??? ?????? DisplayModel ??????  */
        _str: window['DisplayModel'] ? '/pcdesign/' : '',
        /* ???phone ?????? pc host????????? */
        _isPhone: !!navigator.userAgent.match(/AppleWebKit.*Mobile.*/),
        /* ???phone ?????? pc design ????????? */
        _isDesignPhone: $('body').hasClass('pc') ? 'pc':'phone',
        /**
         * ???????????? ?????????????????????
         * @param {[Object]} $obj ????????????????????????
         * return - str {[string]} ?????????????????????????????????
         */
        getListName: function ($obj) {
            if($obj.hasClass('sceneList1')) {
                return 'sceneList1';
            } else if($obj.hasClass('sceneList2')) {
                return 'sceneList2';
            } else if($obj.hasClass('sceneList3')) {
                return 'sceneList3';
            } else if($obj.hasClass('sceneList4')) {
                return 'sceneList4';
            } else if($obj.hasClass('sceneList5')) {
                return 'sceneList5';
            } else if($obj.hasClass('sceneList6')) {
                return 'sceneList6';
            }
        },
        /* ???????????? */
        _renderNav: function ($obj, navHtml) {
            var self = this;
            if(Nav2.isNav2($obj)) {
                Nav2.renderNav2($obj, navHtml);
            } else {
                if($obj.hasClass('sceneList4')) {
                    Nav1.renderNav1($obj, navHtml);
                } else {
                    Nav1.renderNav($obj, navHtml);
                }
            }
        },
        /* ?????????????????? */
        _renderAdvSearch: function ($obj, data) {
            AdvSearch1.isAdvSearch1($obj) && AdvSearch1.renderAdvSearch1($obj, data);
            AdvSearch2.isAdvSearch2($obj) && AdvSearch2.renderAdvSearch2($obj, data);
        },
        /* ?????????????????? */
        _renderLoadMore: function ($obj) {
            Pagination2.isPagination2($obj) && Pagination2.renderPagination($obj);
        },
        /**  
         * ???????????? -- common
         * ?????????????????????????????? 
         * ??????????????????????????? ???????????????????????????????????????
         */
        setDisplay: function ($obj) {
            var self = this, o = $obj.data(), _obj = $obj.find(self.arrArtContStr);
            for (var i in o) {
                switch(i) {
                    case 'setTitle': !o[i] ? _obj.addClass('no-title') : _obj.removeClass('no-title'); break;
                    case 'setContent': !o[i] ? _obj.addClass('no-content') : _obj.removeClass('no-content'); break;
                    case 'setImg': !o[i] ? _obj.addClass('no-img') : _obj.removeClass('no-img'); break;
                    case 'setBorder': !o[i] ? _obj.addClass('no-border') : _obj.removeClass('no-border'); break;
                    case 'setAllBorder': !o[i] ? _obj.addClass('no-all-border') : _obj.removeClass('no-all-border'); break;
                    case 'setNav': !o[i] ? _obj.addClass('no-nav') : _obj.removeClass('no-nav'); break;
                    case 'setHalvingLine': !o[i] ? _obj.addClass('no-line') : _obj.removeClass('no-line'); break;
                    case 'setContBG': !o[i] ? _obj.addClass('no-bg') : _obj.removeClass('no-bg'); break;
                    case 'setLoadMore': !o[i] ? _obj.addClass('no-loadmore') : _obj.removeClass('no-loadmore'); break;
                    case 'setLabel': !o[i] ? _obj.addClass('no-tag') : _obj.removeClass('no-tag'); break;
                    case 'setTime': !o[i] ? _obj.addClass('no-time') : _obj.removeClass('no-time'); break;
                    case 'setGoods': !o[i] ? _obj.addClass('no-goods') : _obj.removeClass('no-goods'); break;
                    case 'setViews': !o[i] ? _obj.addClass('no-view') : _obj.removeClass('no-view'); break;
                    case 'setMoney': !o[i] ? _obj.addClass('no-money') : _obj.removeClass('no-money'); break;
                    case 'setAdvSearch': !o[i] ? _obj.addClass('no-search') : _obj.removeClass('no-search'); break;
                    case 'setAdvSearchItemName': !o[i] ? _obj.addClass('no-searchItemName') : _obj.removeClass('no-searchItemName'); break;
                    case 'setAdvSearchItemLine': !o[i] ? _obj.addClass('no-searchItemLine') : _obj.removeClass('no-searchItemLine'); break;
                    case 'setAdvSearchBorder': !o[i] ? _obj.addClass('no-searchborder') : _obj.removeClass('no-searchborder'); break;
                    default: break;
                }
            }
        },
        /* ?????????????????? -- common */
        resizeHeight: function ($obj) {
            var self = this;
            sceneCommon.resizeImageShow($obj);
            if(sceneCommon.isSetSectionHeight($obj)) {
                $obj.css({ 'height': $obj.find(self.arrArtContStr).outerHeight()+'px' });
            }
        },
        /* ??????????????? -- common */
        addSection: function (_parent) {
            var self = this,
                secObj = $(self.addSectionStr),
                newId =  "wqd" + new Date().getTime() + "serial";
            /* ????????? */
            secObj.find('.wqdSectiondiv').attr({
                id : newId,
                partid : newId,
                sectionname : '????????????',
                "data-colType" : 'sceneColList'
            });
            $(_parent).length ? $(_parent).parents('.yzmoveContent').after(secObj) : $('#HTMLDATACENTER').find('.wqdAreaView').append(secObj);
            var section = $('#'+newId);
            /* ????????????????????? */
            if( $("body").hasClass("phone") ){
                var scrollTo = section.offset().top - $("#HTMLDATABOX").offset().top + $("#hdb-cont").scrollTop();
                $('#hdb-cont').animate({ scrollTop: scrollTo }, 250);
            }else{
                var scrollTo = section.offset().top - 50;
                $('body,html').animate({ scrollTop: scrollTo }, 250);
            }
            return section;
        },
        /* ?????? ???????????????????????? ---- ????????????  ???????????? ???????????? */
        bindTrigger: function () {
            var self = this;

            /* ?????? ?????????????????? ??????,????????????????????????????????? */
            $(document).on("drag:endSceneColList", function (e, _obj) {
                var section = self.addSection(_obj.parent);
                section.find('section').html(_obj.element);
                section.find('.wqdelementEdit').css({
                    "position": "relative",
                    "margin": "0 auto"
                });
                section.find('section').children().css({width:'100%'});
            });
            $(document).on("designer:setFinish", function (e, data) {
                self.resizeHeight(data._obj);
            });
            $(document).on("element:change", ".wqdelementEdit", function (e, data) {
                var _obj = $(e.target).closest('.wqdelementEdit[data-elementtype=sceneList]');
                if(_obj.length) {
                    /* ???????????????????????????????????? */
                    self.resizeHeight(_obj);
                    /* ?????????????????????????????? */
                    self._renderNav(_obj, '');
                }
            });

            /* trigger ???????????? */
            $(document).on("sceneLists:reload", function () {
                $(".wqdelementEdit[data-elementtype=sceneList]").each(function (i, _) {
                    // ??????????????????????????????
                    if(!$(_).attr('artnavtype')) {
                        return true;
                    }
                    sceneList.loadScene($(_), sceneList.listStr[sceneList.getListName($(_))], $(_).attr('navids'));
                });
            })
            .on("sceneLists:load", self.arrArtStr, function (e) {
                var $that = $(this);
                var dfd = $.Deferred();
                /* ???????????? ????????? */
                newArticleList.bindDefaultDetail($that, dfd);
                /* ???????????????????????????????????? */
                self.loadScene($that, self.listStr[self.getListName($that)]);
            })
            /* PC???????????? ?????? */
            .on("setNavTypeScene:toPc", function (e, data) {
                var $that = $(data);
                /* ???????????????????????????????????? */
                self.loadScene($that, self.listStr[self.getListName($that)], $that.attr('navids'));
                /* ???????????? */
                $(document).trigger("cache:push");
            })
            .on("setSceneList:toPc", function (e, data, setType) {
                var $that = $(data);
                var oldType = $that.attr('data-conf'), type = $that.attr('artformat') || '';
                // ????????????
                if(type) {
                    var oldNum = oldType.replace(/.*([\d])/gi, '$1');
                    var num = type.replace(/.*([\d])/gi, '$1');
                    $that.find('.ListScene_'+oldNum).length && $that.find('.ListScene_'+oldNum).removeClass('ListScene_'+oldNum).addClass('ListScene_'+num);
                    $that.attr('data-conf', '_newList_pc'+num);
                    $that.removeClass('sceneList'+oldNum).addClass('sceneList'+num);

                    if(num == 1) {
                        // ????????????????????????????????????
                        $that.find('.comList .load-more').length && $that.find('.comList .load-more').remove();
                    }
                }
                // ????????????????????? ?????????????????????
                if(setType == "setFormat") {
                    // ?????????????????????
                    $('#wqd-boxD').find('.hd-close').click();
                    // ???????????????????????????
                    _Dopen.init(window.wqdDesign[type], $that.attr('id'));
                }
                // ????????????????????????????????????
                if(!$that.attr('artnavtype') || !$that.attr('navids')) {
                    /* ???????????????????????????????????? */
                    self.loadScene($that, self.listStr[self.getListName($that)], $that.attr('navids'));
                } else {
                    self.renderSceneList($that);
                }
                /* ???????????? */
                $(document).trigger("cache:push");
            })
            /* ?????????????????? */
            .on("isShowSetScene:toPC", function (e, data) {
                data.elem.data(data.val.val, data.val.isCheck)
                self.setDisplay(data.elem);
                /* ???????????? */
                $(document).trigger("cache:push");
            })
            /* ?????????????????? */
            .on("setSceneLoading:toPC", function (e, data, val) {
                $(data).find('.load-more').replaceWith(sceneListStr.listLoad[val]);
                self._renderLoadMore($(data));
                /* ???????????? */ 
                $(document).trigger("cache:push");
            });
        },
        /**
         * ???????????????    ???????????????????????????????????????   ?????? ?????????????????????????????????
         * @param {[Object]} $obj ????????????????????????
         * @param {[string]} str ????????????????????????????????????
         * @param {[string]} ????????????id????????????
         */
        loadScene: function ($obj, str, strings) {
            var self = this;
            // ??????????????????strings   ????????????????????????????????????  ????????????????????????
            if(!strings) {
                // ???????????? ??????????????????
                $obj.find(self.arrArtContStr).html(str);
                // ???????????????  ??????????????????   ????????????   ??????????????????
                $obj.find('.comList .load-more').replaceWith(sceneListStr.getLoadMoreStyle($obj));
                $obj.find('.nav').replaceWith(sceneListStr.getNavStyle($obj));
                $obj.find('.advSearch-box').replaceWith(sceneListStr.getSearchStyle($obj));

                var $loadMore = $obj.find('.comList .load-more');
                // ??????????????????????????? newsList   ?????? ???????????? list-cell;
                var $newList = $obj.find('.comList');
                var $newsListCell =  $newList.find('.list-cell');
                // ???????????????
                var strHtml = $newsListCell[0].outerHTML;
                var size = (-(-$obj.attr('col'))|| 1) * (-(-$obj.attr('row'))|| 1);
                var i = 0, html = '';
                for(; i< size; i++) {
                    html += strHtml;
                }
                // ???????????????????????????????????? ?????????
                if($loadMore.length) {
                    // ????????????????????????????????? ???????????? ?????????????????????????????? ????????????????????????
                    $newsListCell.length && $newsListCell.remove()
                    $loadMore.before(html);
                } else {
                    $obj.find('.comList').html(html);
                }
                self.renderColumnNumStyle($obj);
                /* ?????????????????? */
                // self.resizeHeight($obj);
                return;
            }

            var dfd = $.Deferred();

            var navBack = function (dfd) {
                var navHtml = '';
                sceneCommon.AJAX(sceneAJAX.navAJAX, function (data) { 
                    var arr = strings.split(',');
                    for(var i=0; i<arr.length; i++) {
                        for(var j=0; j<data.length; j++) {
                            if(arr[i] == data[j].value) {
                                navHtml += '<span class="wqd-clr wqd-fw wqd-fst wqd-bgclr wqd-ff wqd-fs wqd-brc ' + (i == 0 ? 'on':'') + '" data-categoryid="'+data[j].value+'">'+data[j].text+'</span>';
                                break;
                            }
                        }
                    }
                    /* ?????????????????????????????? */
                    self._renderNav($obj, navHtml); 
                    /* ?????????????????? */
                    // self.resizeHeight($obj);
                    dfd.resolve(); 
                });
                return dfd.promise();  
            };
            var advSearchBack = function (dfd) {
                sceneCommon.AJAX(sceneAJAX.advSearchAJAX, function (data) {
                    self._renderAdvSearch($obj, data);
                    dfd.resolve();
                }); 
                return dfd.promise();
            };
            $.when(navBack(dfd), advSearchBack(dfd))
                .done(function (v1, v2) {
                    // ????????????
                    if(!sceneList._str) { 
                        sceneList.loadEvent1($obj);
                    } else {
                        sceneList.loadEvent($obj);
                    }
                    // ?????? ??????????????????
                    self.renderSceneList($obj);
                });
        },
        /**
         * ??????????????????
         * @param {[data]} data ????????????????????????
         * @param {[Object]} flagStr ????????????????????????????????????????????????
         * @param {[Number]} index ?????????????????????????????? 
         * @param {[Number]} pageId ?????????????????????id 
         */
        getListHtml: function (data, flagStr, index, pageId) {
            var self = this;
            /* ??????????????????????????????data??????????????????????????????????????? ?????????????????? */
            if($.isEmptyObject(data)) { return self.listNullStr[flagStr]; }
            return self.listFun[flagStr+'Fun'].call(self, data, flagStr, index, pageId);
        },
        /** 
         * ?????????????????????????????????????????? 
         * @param {[Object]} $obj ????????????????????????
         * @param {[Object]} data ??????????????????????????????
         * @param {[string]} data.categoryId ????????????ID
         * @param {[string]} data.pageNo ????????????
         * @param {[string]} data.pageSize ???????????????????????????????????????
         * @param {[string]} data.userId ????????????ID
         * @param {[string]} data.sortType ?????????????????????????????????
         */
        renderSceneList: function ($obj, data) {
            var self = this;
            // ?????????????????????????????????????????????????????????
            var json = {};
            /* ?????????????????? */
            json.basicType = $obj.find('.nav span.on').attr('data-categoryid');
            /* ???????????????????????? */
            json.pageNo = parseInt($obj.attr('curPage') || 1);
            json.pageSize = (-(-$obj.attr('col'))||1) * (-(-$obj.attr('row'))||1);
            /* ???????????????????????? */
            var arr = [], _i = 0;
            if($obj.attr('artsearch') == 'search1') {
                $obj.find('.advSearch-box .select-group .item').each(function (i, _) {
                    if($(_).find('span.tag.on').attr('data-code') == "-1") { return true; }
                    arr.push({code:'', ope: '', value: ''});
                    /* ?????????????????? */
                    var type = '';
                    $(_).find('span.tag.on').each(function (j, _) {
                        type = $(_).data('type');
                        arr[_i].code = $(_).data('code');
                        if(type == 'Fixed') {
                            arr[_i].ope = '=';
                            arr[_i].value = $(_).data('val');
                        } else if(type == 'Multi') {
                            arr[_i].ope = 'like';
                            arr[_i].value += $(_).data('val') + ',';
                        } else if(type == 'Range') {
                            arr[_i].ope = $(_).data('val').split(',')[0];
                            arr[_i].value = $(_).data('val').split(',')[1];
                        }
                    });

                    if(type == 'Multi') arr[_i].value = arr[_i].value.replace(/,$/gi, '');
                    _i ++;
                });
            } else if($obj.attr('artsearch') == 'search2') {
                $('body').find('.advSearch-box1.self .sel-group-box .gr-box').each(function (i, _) {
                    if($(_).find('.b-bd .item.on').attr('data-code') == "-1") { return true; }
                    arr.push({code:'', ope: '', value: ''});
                    /* ?????????????????? */
                    var type = '';
                    $(_).find('.b-bd .item.on').each(function (j, _) {
                        type = $(_).data('type');
                        arr[_i].code = $(_).data('code');
                        if(type == 'Fixed') {
                            arr[_i].ope = '=';
                            arr[_i].value = $(_).data('val');
                        } else if(type == 'Multi') {
                            arr[_i].ope = 'like';
                            arr[_i].value += $(_).data('val') + ',';
                        } else if(type == 'Range') {
                            arr[_i].ope = $(_).data('val').split(',')[0];
                            arr[_i].value = $(_).data('val').split(',')[1];
                        }
                    });

                    if(type == 'Multi') arr[_i].value = arr[_i].value.replace(/,$/gi, '');
                    _i ++;
                });
            }

            json.querys = arr.length?JSON.stringify({params:arr}):'';

            sceneAJAX.listAJAX.data = json;
            // ????????????  ????????????
            var newsListHtml = '';
            sceneCommon.AJAX(sceneAJAX.listAJAX, function (data) {
                    var data = data.data;
                    /* ???????????? */
                    Pagination2.pageData.totalCount = parseInt(data.totalCount);
                    Pagination2.pageData.curPage = parseInt(data.pageNo || 1);
                    $obj.attr('curPage', parseInt(data.pageNo || 1));


                    if(sceneCommon.isSetSectionHeight($obj)) { var sJSON = sceneCommon.getLastTopElem($obj); }
                    // ????????????????????????
                    var $loadMore = $obj.find('.comList .load-more');
                    if(json.pageNo > data.totalPages) {
                        if($loadMore.length && $obj.attr('artload') == 'load1') {
                            $loadMore.find('p').css({ 'background':'#eee', 'color':'#000' }).text('???????????????');
                        }
                        return;
                    }

                    if(data.data && data.data.length) {
                        $.each(data.data, function (i, val) {
                            newsListHtml += self.getListHtml(val, self.getListName($obj), i, $obj.attr('data-pageid'));
                        });
                        $loadMore.length && $loadMore.attr("style", '');
                    } else {
                        newsListHtml = self.getListHtml({}, self.getListName($obj));
                        // ????????????????????????????????????
                        $loadMore.length && $loadMore.hide();
                    }

                    // ??????????????????????????? newsList   ?????? ???????????? list-cell;
                    var $newList = $obj.find('.comList');
                    var $newsListCell =  $newList.find('.list-cell');
                    // ???????????????????????????????????? ?????????
                    if($loadMore.length) {
                        // ????????????????????????????????? ???????????? ?????????????????????????????? ????????????????????????
                        if(json.pageNo > 1 && $obj.attr('artload') != 'load2') {
                            $loadMore.before(newsListHtml);
                        } else {
                            $newsListCell.length && $newsListCell.parent('a').length && $newsListCell.parent('a').remove();
                            $newsListCell.length && $newsListCell.remove();
                            $loadMore.before(newsListHtml);
                        }
                        // ?????????????????????????????? ?????????????????????????????????   ??????????????????????????? ???????????????
                        if($obj.attr('artload') != 'load2') {
                            if(data.totalPages == $obj.attr('curPage')) {
                                $loadMore.find('p').css({ 'background':'#eee', 'color':'#000' }).text('???????????????');
                            } else {
                                $loadMore.find('p').text('????????????').removeAttr('style');
                            }
                        }
                    }
                    $newsListCell =  $newList.find('.list-cell');


                    // ??????????????????????????????  --- ??????
                    if(data.data && data.data.length) {
                        self.renderColumnNumStyle($obj);
                    }
                    /* ?????????????????? */
                    self.resizeHeight($obj);
                    if(sceneCommon.isSetSectionHeight($obj)) { sceneCommon.setSectionHeight($obj, sJSON); }
                    /* ?????????????????? */
                    Pagination2.isPagination2($obj) && Pagination2.renderPagination($obj);
                    $(document).trigger("cache:push");
                }
            );
        },
        /**
         * ????????????  ???????????? 
         * @augments $obj ????????????????????????
         */
        loadEvent: function ($obj) {
            var self = this;
            sceneCommon.bindNavEvent($obj, function ($obj) {
                self.renderSceneList($obj);
            });
            sceneCommon.bindAdvSearchEvent($obj, function ($obj) {
                self.renderSceneList($obj);
            });
        },
        /**
         * ??????????????????????????????????????? 
         * @param {[Object]} $obj ????????????????????????
         */ 
        renderColumnNumStyle: function ($obj) {
            var $sceneListCell =  $obj.find('.comList .list-cell');
            var col = ($obj.attr('col') || 1);
            var width = ((99.5 - ($obj.attr('colnumval') || 0)/$obj.find('.comList').outerWidth()*100*(col-1))/col).toFixed(2);
            $sceneListCell.each(function (i, _) {
                if(i%col == 0) {
                    $(_).css({
                        'width': width + '%',
                        'display': 'inline-block',
                        'padding-right': '10px'
                    });
                } else {
                    $(_).css({
                        'width': width + '%',
                        'margin-left': (($obj.attr('colnumval') || 0)/$obj.find('.comList').outerWidth()*100).toFixed(2) + '%',
                        'display': 'inline-block',
                        'padding-right': '10px'
                    });
                }
            });
        },
        /* ???????????? */
        /**
         * ????????????  host???  
         * @augments $obj ????????????????????????
         */
        loadEvent1: function ($obj) {
            var self = this;
            sceneCommon.bindNavEvent($obj, function ($obj) {
                self.renderSceneList($obj);
            });
            sceneCommon.bindLoadMoreEvent($obj, function ($obj) {
                self.renderSceneList($obj);
            });
            sceneCommon.bindAdvSearchEvent($obj, function ($obj) {
                self.renderSceneList($obj);
            });
        }
    }

    newArticleList.init();

    return newArticleList;
        
};

if(window['DisplayModel']) {
    define(['designerOpen', 'global'], function (_Dopen, _global) {
        return sceneList(_Dopen, _global);
    });
} else {
    sceneList();
}
