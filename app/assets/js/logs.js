pluginWebpack([1],{

/***/ 19:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({
	name: 'LogsApp'
});

/***/ }),

/***/ 20:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({

	name: 'Logs',

	data() {
		return {
			message: 'Error logs logs will be here soon.'
		};
	}
});

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({

    name: 'Settings',

    data() {
        return {};
    }
});

/***/ }),

/***/ 66:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _LogsApp = __webpack_require__(67);

var _LogsApp2 = _interopRequireDefault(_LogsApp);

var _router = __webpack_require__(70);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.config.productionTip = false;

/* eslint-disable no-new */
new _vue2.default({
	el: '#dd404-logs-app',
	router: _router2.default,
	render: function render(h) {
		return h(_LogsApp2.default);
	}
});

/***/ }),

/***/ 67:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_LogsApp_vue__ = __webpack_require__(19);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e77ba77e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_LogsApp_vue__ = __webpack_require__(69);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(68)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_LogsApp_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e77ba77e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_LogsApp_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/LogsApp.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e77ba77e", Component.options)
  } else {
    hotAPI.reload("data-v-e77ba77e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 68:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 69:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "dd404-admin-logs" } },
    [_c("router-view")],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-e77ba77e", esExports)
  }
}

/***/ }),

/***/ 70:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _vueRouter = __webpack_require__(5);

var _vueRouter2 = _interopRequireDefault(_vueRouter);

var _Logs = __webpack_require__(71);

var _Logs2 = _interopRequireDefault(_Logs);

var _Settings = __webpack_require__(73);

var _Settings2 = _interopRequireDefault(_Settings);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueRouter2.default);

exports.default = new _vueRouter2.default({
	routes: [{
		path: '/',
		name: 'Logs',
		component: _Logs2.default
	}, {
		path: '/settings',
		name: 'Settings',
		component: _Settings2.default
	}]
});

/***/ }),

/***/ 71:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Logs_vue__ = __webpack_require__(20);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7ed40d97_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Logs_vue__ = __webpack_require__(72);
var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Logs_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7ed40d97_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Logs_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/Logs.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7ed40d97", Component.options)
  } else {
    hotAPI.reload("data-v-7ed40d97", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 72:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm._m(0)
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { attrs: { id: "poststuff" } }, [
      _c("div", { attrs: { id: "post-body" } }, [
        _c("div", { attrs: { id: "post-body-content" } }, [
          _c("div", { staticClass: "meta-box-sortables ui-sortable" }, [
            _c("form", { attrs: { method: "post" } }, [
              _c("div", { staticClass: "tablenav top" }, [
                _c("div", { staticClass: "alignleft actions bulkactions" }, [
                  _c(
                    "label",
                    {
                      staticClass: "screen-reader-text",
                      attrs: { for: "bulk-action-selector-top" }
                    },
                    [_vm._v("Select bulk action")]
                  ),
                  _vm._v(" "),
                  _c(
                    "select",
                    {
                      attrs: { name: "action", id: "bulk-action-selector-top" }
                    },
                    [
                      _c("option", { attrs: { value: "-1" } }, [
                        _vm._v("Bulk Actions")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "bulk_delete" } }, [
                        _vm._v("Delete Selected")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "bulk_clean" } }, [
                        _vm._v("Delete All")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "bulk_delete_all" } }, [
                        _vm._v("Delete All (Keep redirects)")
                      ])
                    ]
                  ),
                  _vm._v(" "),
                  _c("input", {
                    staticClass: "button action",
                    attrs: { type: "submit", id: "doaction", value: "Apply" }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "alignleft actions bulkactions" }, [
                  _c(
                    "select",
                    {
                      staticClass: "404_group_by",
                      attrs: { name: "group_by_top" }
                    },
                    [
                      _c("option", { attrs: { value: "" } }, [
                        _vm._v("Group by")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "url" } }, [
                        _vm._v("404 Path")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "ref" } }, [
                        _vm._v("From")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "ip" } }, [
                        _vm._v("IP Address")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "ua" } }, [
                        _vm._v("User Agent")
                      ])
                    ]
                  ),
                  _vm._v(" "),
                  _c("input", {
                    staticClass: "button",
                    attrs: {
                      type: "submit",
                      name: "filter_action",
                      id: "post-query",
                      value: "Apply"
                    }
                  })
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "tablenav-pages one-page" }, [
                  _c("span", { staticClass: "displaying-num" }, [
                    _vm._v("3 items")
                  ]),
                  _vm._v(" "),
                  _c("span", { staticClass: "pagination-links" }, [
                    _c(
                      "span",
                      {
                        staticClass: "tablenav-pages-navspan button disabled",
                        attrs: { "aria-hidden": "true" }
                      },
                      [_vm._v("«")]
                    ),
                    _vm._v(" "),
                    _c(
                      "span",
                      {
                        staticClass: "tablenav-pages-navspan button disabled",
                        attrs: { "aria-hidden": "true" }
                      },
                      [_vm._v("‹")]
                    ),
                    _vm._v(" "),
                    _c("span", { staticClass: "paging-input" }, [
                      _c(
                        "label",
                        {
                          staticClass: "screen-reader-text",
                          attrs: { for: "current-page-selector" }
                        },
                        [_vm._v("Current Page")]
                      ),
                      _vm._v(" "),
                      _c("input", {
                        staticClass: "current-page",
                        attrs: {
                          id: "current-page-selector",
                          type: "text",
                          name: "paged",
                          value: "1",
                          size: "1",
                          "aria-describedby": "table-paging"
                        }
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "tablenav-paging-text" }, [
                        _vm._v(" of "),
                        _c("span", { staticClass: "total-pages" }, [
                          _vm._v("1")
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c(
                      "span",
                      {
                        staticClass: "tablenav-pages-navspan button disabled",
                        attrs: { "aria-hidden": "true" }
                      },
                      [_vm._v("›")]
                    ),
                    _vm._v(" "),
                    _c(
                      "span",
                      {
                        staticClass: "tablenav-pages-navspan button disabled",
                        attrs: { "aria-hidden": "true" }
                      },
                      [_vm._v("»")]
                    )
                  ])
                ]),
                _vm._v(" "),
                _c("br", { staticClass: "clear" })
              ]),
              _vm._v(" "),
              _c(
                "table",
                { staticClass: "wp-list-table widefat fixed striped" },
                [
                  _c("thead", [
                    _c("tr", [
                      _c(
                        "td",
                        {
                          staticClass: "manage-column column-cb check-column",
                          attrs: { id: "cb" }
                        },
                        [
                          _c("input", {
                            attrs: { type: "checkbox", value: "" }
                          })
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "th",
                        {
                          staticClass: "manage-column column-primary",
                          attrs: { scope: "col" }
                        },
                        [_vm._v("Title")]
                      ),
                      _vm._v(" "),
                      _c(
                        "th",
                        {
                          staticClass: "manage-column",
                          attrs: { scope: "col" }
                        },
                        [_vm._v("Author")]
                      ),
                      _vm._v(" "),
                      _c(
                        "th",
                        {
                          staticClass: "manage-column sortable",
                          attrs: { scope: "col" }
                        },
                        [_vm._v("Host")]
                      ),
                      _vm._v(" "),
                      _c(
                        "th",
                        {
                          staticClass: "manage-column sortable",
                          attrs: { scope: "col" }
                        },
                        [_vm._v("Date")]
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _c("tbody", [
                    _c("tr", [
                      _c("th"),
                      _vm._v(" "),
                      _c("td", { attrs: { colspan: "4" } }, [
                        _c("strong", [_vm._v("You haven’t added a video yet")])
                      ])
                    ])
                  ])
                ]
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "tablenav cp-admin-pagination" })
          ])
        ])
      ])
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7ed40d97", esExports)
  }
}

/***/ }),

/***/ 73:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Settings_vue__ = __webpack_require__(21);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_47ecc20b_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Settings_vue__ = __webpack_require__(75);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(74)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-47ecc20b"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Settings_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_47ecc20b_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Settings_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/Settings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-47ecc20b", Component.options)
  } else {
    hotAPI.reload("data-v-47ecc20b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 74:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 75:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "app-settings" }, [
    _vm._v("\n    The Settings Page\n")
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-47ecc20b", esExports)
  }
}

/***/ })

},[66]);