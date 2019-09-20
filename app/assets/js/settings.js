pluginWebpack([1],{

/***/ 100:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "email-settings" }, [
    _vm._v("\n    The Email Settings Page\n")
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-ce397a20", esExports)
  }
}

/***/ }),

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _SettingsApp = __webpack_require__(34);

var _SettingsApp2 = _interopRequireDefault(_SettingsApp);

var _router = __webpack_require__(37);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.config.productionTip = false;

/* eslint-disable no-new */
new _vue2.default({
	el: '#dd404-settings-app',
	router: _router2.default,
	render: function render(h) {
		return h(_SettingsApp2.default);
	}
});

/***/ }),

/***/ 34:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_SettingsApp_vue__ = __webpack_require__(9);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_309372c1_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_SettingsApp_vue__ = __webpack_require__(36);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(35)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_SettingsApp_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_309372c1_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_SettingsApp_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/settings/SettingsApp.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-309372c1", Component.options)
  } else {
    hotAPI.reload("data-v-309372c1", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 35:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 36:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "dd404-admin-settings" } },
    [
      _vm.alert
        ? _c("div", { staticClass: "notice notice-success is-dismissible" }, [
            _c("p", [_vm._v(_vm._s(_vm.alert))])
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "nav",
        { staticClass: "nav-tab-wrapper" },
        [
          _c(
            "router-link",
            { staticClass: "nav-tab", attrs: { to: "/", exact: "" } },
            [
              _c("span", { staticClass: "dashicons dashicons-admin-generic" }),
              _vm._v("\n            General\n        ")
            ]
          ),
          _vm._v(" "),
          _c(
            "router-link",
            { staticClass: "nav-tab", attrs: { to: "/email" } },
            [
              _c("span", { staticClass: "dashicons dashicons-email" }),
              _vm._v("\n            Email\n        ")
            ]
          )
        ],
        1
      ),
      _vm._v(" "),
      _c("router-view")
    ],
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
    require("vue-hot-reload-api")      .rerender("data-v-309372c1", esExports)
  }
}

/***/ }),

/***/ 37:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _vueRouter = __webpack_require__(4);

var _vueRouter2 = _interopRequireDefault(_vueRouter);

var _General = __webpack_require__(93);

var _General2 = _interopRequireDefault(_General);

var _Email = __webpack_require__(98);

var _Email2 = _interopRequireDefault(_Email);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueRouter2.default);

exports.default = new _vueRouter2.default({
	linkActiveClass: 'nav-tab-active',
	routes: [{
		path: '/',
		name: 'General',
		component: _General2.default
	}, {
		path: '/email',
		name: 'Email',
		component: _Email2.default
	}]
});

/***/ }),

/***/ 9:
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

/* harmony default export */ __webpack_exports__["a"] = ({
	name: 'SettingsApp',
	data() {
		return {
			alert: false
		};
	}
});

/***/ }),

/***/ 92:
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

/* harmony default export */ __webpack_exports__["a"] = ({

	name: 'General',

	data() {
		return {
			alert: false,
			redirectType: null,
			redirectTo: null,
			redirectPage: null,
			redirectLink: null,
			redirectLog: null,
			emailNotification: null,
			disableGuessing: null,
			emailRecipient: null,
			excludePaths: null,
			waiting: false
		};
	},

	methods: {
		/**
   * Handle settings for submit.
   *
   * Validate the form before submitting it.
   *
   * @param e Event.
   *
   * @returns {boolean}
   */
		submitForm: function (e) {
			this.showSuccess();
			this.waiting = true;

			// Do not submit form.
			e.preventDefault();
		},

		showSuccess: function () {
			this.$parent.alert = 'Thanks for the message, Joel.';

			setTimeout(() => {
				this.$parent.alert = false;
			}, 3000);
		}
	}
});

/***/ }),

/***/ 93:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_General_vue__ = __webpack_require__(92);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1f5e8988_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_General_vue__ = __webpack_require__(95);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(94)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-1f5e8988"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_General_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1f5e8988_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_General_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/settings/components/General.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1f5e8988", Component.options)
  } else {
    hotAPI.reload("data-v-1f5e8988", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 94:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 95:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "form",
    {
      attrs: { id: "general-settings", action: "options.php", method: "post" },
      on: { submit: _vm.submitForm }
    },
    [
      _c("table", { staticClass: "form-table" }, [
        _c("tbody", [
          _c("tr", [
            _vm._m(0),
            _vm._v(" "),
            _c("td", [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.redirectType,
                    expression: "redirectType"
                  }
                ],
                attrs: { id: "name", type: "text", name: "name" },
                domProps: { value: _vm.redirectType },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.redirectType = $event.target.value
                  }
                }
              })
            ])
          ]),
          _vm._v(" "),
          _c("tr", [
            _vm._m(1),
            _vm._v(" "),
            _c("td", [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.redirectTo,
                    expression: "redirectTo"
                  }
                ],
                attrs: { id: "age", type: "number", name: "age", min: "0" },
                domProps: { value: _vm.redirectTo },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.redirectTo = $event.target.value
                  }
                }
              })
            ])
          ]),
          _vm._v(" "),
          _c("tr", [
            _vm._m(2),
            _vm._v(" "),
            _c("td", [
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.redirectPage,
                      expression: "redirectPage"
                    }
                  ],
                  attrs: { id: "movie", name: "movie" },
                  on: {
                    change: function($event) {
                      var $$selectedVal = Array.prototype.filter
                        .call($event.target.options, function(o) {
                          return o.selected
                        })
                        .map(function(o) {
                          var val = "_value" in o ? o._value : o.value
                          return val
                        })
                      _vm.redirectPage = $event.target.multiple
                        ? $$selectedVal
                        : $$selectedVal[0]
                    }
                  }
                },
                [
                  _c("option", [_vm._v("Star Wars")]),
                  _vm._v(" "),
                  _c("option", [_vm._v("Vanilla Sky")]),
                  _vm._v(" "),
                  _c("option", [_vm._v("Atomic Blonde")])
                ]
              )
            ])
          ]),
          _vm._v(" "),
          _c("tr", [
            _c("th", { attrs: { colspan: "2" } }, [
              _c("input", {
                staticClass: "button button-primary",
                attrs: {
                  type: "submit",
                  name: "submit",
                  value: "Save Changes",
                  disabled: _vm.waiting
                }
              })
            ])
          ])
        ])
      ])
    ]
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("th", [_c("label", { attrs: { for: "name" } }, [_vm._v("Name")])])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("th", [_c("label", { attrs: { for: "age" } }, [_vm._v("Age")])])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("th", [
      _c("label", { attrs: { for: "movie" } }, [_vm._v("Favorite Movie")])
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1f5e8988", esExports)
  }
}

/***/ }),

/***/ 97:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({

	name: 'Email',

	data() {
		return {
			redirectType: null,
			redirectTo: null,
			redirectPage: null,
			redirectLink: null,
			redirectLog: null,
			emailNotification: null,
			disableGuessing: null,
			emailRecipient: null,
			excludePaths: null
		};
	}
});

/***/ }),

/***/ 98:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Email_vue__ = __webpack_require__(97);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_ce397a20_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Email_vue__ = __webpack_require__(100);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(99)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-ce397a20"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Email_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_ce397a20_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Email_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/settings/components/Email.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ce397a20", Component.options)
  } else {
    hotAPI.reload("data-v-ce397a20", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 99:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

},[33]);