pluginWebpack([0],[
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.restGet = restGet;
exports.restPost = restPost;
exports.restDelete = restDelete;

var _apiFetch = __webpack_require__(8);

var _apiFetch2 = _interopRequireDefault(_apiFetch);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Send API rest GET request using apiFetch.
 *
 * This is a wrapper function to include nonce and
 * our custom route base url.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
function restGet(options) {
  options = options || {};

  options.method = 'GET';

  _apiFetch2.default.use(_apiFetch2.default.createNonceMiddleware(window.dd404.rest_nonce));
  _apiFetch2.default.use(_apiFetch2.default.createRootURLMiddleware(window.dd404.rest_url));

  // Add param support.
  if (options.params) {
    var urlParams = new URLSearchParams(Object.entries(options.params));

    options.path = options.path + '?' + urlParams;
  }

  return (0, _apiFetch2.default)(options);
}

/**
 * Send API rest POST request using apiFetch.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
function restPost(options) {
  options = options || {};

  options.method = 'POST';

  _apiFetch2.default.use(_apiFetch2.default.createNonceMiddleware(window.dd404.rest_nonce));
  _apiFetch2.default.use(_apiFetch2.default.createRootURLMiddleware(window.dd404.rest_url));

  return (0, _apiFetch2.default)(options);
}

/**
 * Send API rest DELETE request using apiFetch.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
function restDelete(options) {
  options = options || {};

  options.method = 'DELETE';

  _apiFetch2.default.use(_apiFetch2.default.createNonceMiddleware(window.dd404.rest_nonce));
  _apiFetch2.default.use(_apiFetch2.default.createRootURLMiddleware(window.dd404.rest_url));

  return (0, _apiFetch2.default)(options);
}

/***/ }),
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */
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
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__list_table_Table_vue__ = __webpack_require__(84);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_utils__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_utils___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_utils__);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Logs',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		Table: __WEBPACK_IMPORTED_MODULE_0__list_table_Table_vue__["a" /* default */]
	},

	created() {
		this.updateRows();
	},

	/**
  * Get the default set of data for the template.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	data() {
		return {
			columns: {
				'path': {
					label: 'Path',
					sortable: true
				},
				'date': {
					label: 'Date'
				},
				'referral': {
					label: 'Referral',
					sortable: true
				},
				'ip': {
					label: 'IP Address'
				},
				'ua': {
					label: 'User Agent',
					sortable: true
				}
			},
			rows: [],
			bulkActions: [{
				key: 'trash',
				label: 'Move to Trash'
			}],
			extraActions: [{
				key: 'group_by',
				label: 'Group by',
				options: [{ key: 'path', label: '404 Path' }, { key: 'referral', label: 'Referral' }, { key: 'ip', label: 'IP' }, { key: 'ua', label: 'User Agent' }]
			}],
			totalItems: 25,
			perPage: 4,
			currentPage: 1
		};
	},

	methods: {
		/**
   * Update the settings by sending the value to DB.
   *
   * Should handle the error response properly and disply
   * a generic error message.
   *
   * @since 4.0.0
   *
   * @returns {boolean}
   */
		updateRows(page = null) {
			this.currentPage = page || this.currentPage;

			Object(__WEBPACK_IMPORTED_MODULE_1__helpers_utils__["restGet"])({
				path: 'logs',
				params: {
					page: this.currentPage,
					per_page: this.perPage
				}
			}).then(response => {
				if (response.success === true) {
					this.rows = response.data;
					this.totalItems = response.data.length;
				} else {
					this.rows = [];
					this.totalItems = 0;
				}

				// End waiting mode.
				this.waiting = false;
			});
		}
	}
});

/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Header_vue__ = __webpack_require__(85);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__Row_vue__ = __webpack_require__(88);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__NavTop_vue__ = __webpack_require__(90);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__NavBottom_vue__ = __webpack_require__(94);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Table',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		Header: __WEBPACK_IMPORTED_MODULE_0__Header_vue__["a" /* default */], Row: __WEBPACK_IMPORTED_MODULE_1__Row_vue__["a" /* default */], NavTop: __WEBPACK_IMPORTED_MODULE_2__NavTop_vue__["a" /* default */], NavBottom: __WEBPACK_IMPORTED_MODULE_3__NavBottom_vue__["a" /* default */]
	},

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		columns: {
			type: Object,
			required: true,
			default: {}
		},
		rows: {
			type: Array,
			required: true,
			default: []
		},
		tableClass: {
			type: String,
			default: 'wp-list-table widefat fixed striped'
		},
		totalItems: {
			type: Number,
			default: 101
		},
		perPage: {
			type: Number,
			default: 20
		},
		currentPage: {
			type: Number,
			default: 1
		},
		sortBy: {
			type: String,
			default: null
		},
		sortOrder: {
			type: String,
			default: 'asc'
		},
		showCb: {
			type: Boolean,
			default: true
		},
		bulkActions: {
			type: Array,
			required: false,
			default: []
		},
		extraActions: {
			type: Array,
			required: false,
			default: []
		},
		paginationCallback: {
			type: Function,
			required: false
		}
	},

	/**
  * Get the default set of data for the template.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	data() {

		return {
			labels: {
				emptyRows: 'No data found.'
			}
		};
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Is there any data available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		hasRows() {
			return this.rows.length > 0;
		},

		/**
   * Is there any data available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		columnCount() {
			let size = Object.keys(this.columns).length;

			if (this.showCb) {
				size = size + 1;
			}

			return size;
		}
	}
});

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Column_vue__ = __webpack_require__(24);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Header',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		Column: __WEBPACK_IMPORTED_MODULE_0__Column_vue__["a" /* default */]
	},

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		columns: {
			type: Object,
			required: true,
			default: {}
		},
		showCb: {
			type: Boolean,
			default: true
		}
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Select and unselect all child items.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		selectAll: {
			get: function () {},
			set: function () {}
		}
	}
});

/***/ }),
/* 24 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Column_vue__ = __webpack_require__(25);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7dad728e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Column_vue__ = __webpack_require__(86);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Column_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7dad728e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Column_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/Column.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7dad728e", Component.options)
  } else {
    hotAPI.reload("data-v-7dad728e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 25 */
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

/* harmony default export */ __webpack_exports__["a"] = ({

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Column',

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		isHead: {
			type: Boolean,
			default: false
		},
		columnClass: {
			type: Array,
			default: ['column']
		},
		scope: {
			type: String,
			default: null
		}
	}
});

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Column_vue__ = __webpack_require__(24);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Row',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		Column: __WEBPACK_IMPORTED_MODULE_0__Column_vue__["a" /* default */]
	},

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		id: {
			type: Number,
			required: true
		},
		columns: {
			type: Object,
			required: true,
			default: {}
		},
		row: {
			type: Object,
			required: true,
			default: {}
		},
		showCb: {
			type: Boolean,
			default: true
		}
	},

	/**
  * Get the default set of data for the template.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	data() {
		return {
			checkedItems: []
		};
	}
});

/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__BulkAction_vue__ = __webpack_require__(28);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__Pagination_vue__ = __webpack_require__(30);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'NavTop',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		BulkAction: __WEBPACK_IMPORTED_MODULE_0__BulkAction_vue__["a" /* default */], Pagination: __WEBPACK_IMPORTED_MODULE_1__Pagination_vue__["a" /* default */]
	},

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		bulkActions: {
			type: Array,
			required: false,
			default: []
		},
		extraActions: {
			type: Array,
			required: false,
			default: [{}]
		},
		totalItems: {
			type: Number,
			default: 0
		},
		perPage: {
			type: Number,
			default: 20
		},
		currentPage: {
			type: Number,
			default: 1
		},
		paginationCallback: {
			type: Function,
			required: false
		}
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Is there bulk actions available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		hasBulkActions() {
			return this.bulkActions.length > 0;
		},

		/**
   * Is there extra actions available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		hasExtraActions() {
			return this.extraActions.length > 0;
		}
	},

	data() {
		return {
			bulkAction: -1
		};
	}
});

/***/ }),
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_BulkAction_vue__ = __webpack_require__(29);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_64645c20_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_BulkAction_vue__ = __webpack_require__(91);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_BulkAction_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_64645c20_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_BulkAction_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/BulkAction.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-64645c20", Component.options)
  } else {
    hotAPI.reload("data-v-64645c20", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 29 */
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


/* harmony default export */ __webpack_exports__["a"] = ({

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'BulkAction',

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		actionKey: {
			type: String,
			required: true
		},
		actionLabel: {
			type: String,
			required: true
		},
		actionOptions: {
			type: Array,
			required: true
		},
		actionSubmit: {
			type: String,
			required: false,
			default: 'Submit'
		},
		actionClick: {
			type: Boolean,
			default: true
		},
		isTop: {
			type: Boolean,
			default: true
		}
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Is there bulk actions available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		hasOptions() {
			return this.actionOptions.length > 0;
		},

		/**
   * Is there bulk actions available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		actionId() {
			if (this.isTop) {
				return this.actionKey;
			} else {
				return this.actionKey + '-bottom';
			}
		}
	},

	data() {
		return {
			bulkAction: -1
		};
	},

	methods: {
		actionClickHandler(action) {
			if (this.actionClick) {
				this.$router.push({ name: 'Logs', query: Object.assign({}, this.$route.query, { [action]: this.bulkAction }) });
			}
		}
	}
});

/***/ }),
/* 30 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Pagination_vue__ = __webpack_require__(31);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_d731765c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Pagination_vue__ = __webpack_require__(92);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Pagination_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_d731765c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Pagination_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/Pagination.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-d731765c", Component.options)
  } else {
    hotAPI.reload("data-v-d731765c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 31 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_router__ = __webpack_require__(3);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Pagination',

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		totalItems: {
			type: Number,
			required: true
		},
		perPage: {
			type: Number,
			required: true
		},
		currentPage: {
			type: Number,
			required: true
		},
		isTop: {
			type: Boolean,
			default: true
		},
		paginationCallback: {
			type: Function,
			required: false
		}
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Calculate the total no. of pages.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		totalPages() {
			return Math.ceil(this.totalItems / this.perPage);
		},

		/**
   * Calculate if last page should be disabled.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		disableLastPage() {
			return this.totalPages - this.currentPageNumber <= 1;
		},

		/**
   * Calculate if first page should be disabled.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		disableFirstPage() {
			return this.currentPageNumber <= 2;
		},

		/**
   * Calculate if next page should be disabled.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		disableNextPage() {
			return this.totalPages <= this.currentPageNumber;
		},

		/**
   * Calculate if previous page should be disabled.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		disablePrevPage() {
			return this.currentPageNumber <= 1;
		},

		/**
   * Check if we can paginate.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		canPaginate() {
			return this.perPage < this.totalItems;
		}
	},

	data() {
		return {
			bulkAction: -1,
			prevPage: 0,
			nextPage: 2,
			currentPageNumber: this.currentPage
		};
	},

	methods: {
		paginate(page) {
			if (page > this.totalPages || page < 1) {
				return;
			}

			this.nextPage = page + 1;
			this.prevPage = page - 1;
			this.currentPageNumber = page;

			if (this.paginationCallback) {
				//this.paginationCallback( page );
			}
			this.$router.push({ name: 'Logs', query: Object.assign({}, this.$route.query, { page: page }) });
		}
	}
});

/***/ }),
/* 32 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__BulkAction_vue__ = __webpack_require__(28);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__Pagination_vue__ = __webpack_require__(30);
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

	/**
  * Current component name.
  *
  * @since 4.0.0
  */
	name: 'Settings',

	/**
  * Required components in this component.
  *
  * @since 4.0.0
  */
	components: {
		BulkAction: __WEBPACK_IMPORTED_MODULE_0__BulkAction_vue__["a" /* default */], Pagination: __WEBPACK_IMPORTED_MODULE_1__Pagination_vue__["a" /* default */]
	},

	/**
  * Define properties of this component.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	props: {
		bulkActions: {
			type: Array,
			required: false,
			default: []
		},
		totalItems: {
			type: Number,
			default: 0
		},
		perPage: {
			type: Number,
			default: 20
		},
		currentPage: {
			type: Number,
			default: 1
		},
		paginationCallback: {
			type: Function,
			required: false
		}
	},

	/**
  * Dynamic methods to handle table.
  *
  * @since 4.0.0
  *
  * @returns {object}
  */
	computed: {
		/**
   * Is there bulk actions available.
   *
   * @since 4.0.0
   *
   * @returns {object}
   */
		hasBulkActions() {
			return this.bulkActions.length > 0;
		}
	},

	data() {
		return {
			bulkAction: -1
		};
	}
});

/***/ }),
/* 33 */
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
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
/* 48 */,
/* 49 */,
/* 50 */,
/* 51 */,
/* 52 */,
/* 53 */,
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */,
/* 74 */,
/* 75 */,
/* 76 */,
/* 77 */,
/* 78 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _LogsApp = __webpack_require__(79);

var _LogsApp2 = _interopRequireDefault(_LogsApp);

var _router = __webpack_require__(82);

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
/* 79 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_LogsApp_vue__ = __webpack_require__(20);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e77ba77e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_LogsApp_vue__ = __webpack_require__(81);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(80)
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
/* 80 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 81 */
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
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _vue = __webpack_require__(1);

var _vue2 = _interopRequireDefault(_vue);

var _vueRouter = __webpack_require__(3);

var _vueRouter2 = _interopRequireDefault(_vueRouter);

var _Logs = __webpack_require__(83);

var _Logs2 = _interopRequireDefault(_Logs);

var _Settings = __webpack_require__(99);

var _Settings2 = _interopRequireDefault(_Settings);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_vue2.default.use(_vueRouter2.default);

exports.default = new _vueRouter2.default({
	routes: [{
		path: '/:page?/:group?',
		name: 'Logs',
		component: _Logs2.default
	}, {
		path: '/settings',
		name: 'Settings',
		component: _Settings2.default
	}]
});

/***/ }),
/* 83 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Logs_vue__ = __webpack_require__(21);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7ed40d97_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Logs_vue__ = __webpack_require__(98);
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
/* 84 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Table_vue__ = __webpack_require__(22);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9ae94874_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Table_vue__ = __webpack_require__(97);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Table_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_9ae94874_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Table_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/Table.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-9ae94874", Component.options)
  } else {
    hotAPI.reload("data-v-9ae94874", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 85 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Header_vue__ = __webpack_require__(23);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_710b0e45_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Header_vue__ = __webpack_require__(87);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Header_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_710b0e45_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Header_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/Header.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-710b0e45", Component.options)
  } else {
    hotAPI.reload("data-v-710b0e45", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 86 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.isHead
    ? _c(
        "th",
        { class: _vm.columnClass, attrs: { scope: _vm.scope } },
        [_vm._t("default")],
        2
      )
    : _c(
        "td",
        { class: _vm.columnClass, attrs: { scope: _vm.scope } },
        [_vm._t("default")],
        2
      )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7dad728e", esExports)
  }
}

/***/ }),
/* 87 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("thead", [
    _c(
      "tr",
      [
        _vm.showCb
          ? _c(
              "Column",
              {
                attrs: {
                  columnClass: ["manage-column", "column-cb", "check-column"]
                }
              },
              [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.selectAll,
                      expression: "selectAll"
                    }
                  ],
                  attrs: { type: "checkbox" },
                  domProps: {
                    checked: Array.isArray(_vm.selectAll)
                      ? _vm._i(_vm.selectAll, null) > -1
                      : _vm.selectAll
                  },
                  on: {
                    change: function($event) {
                      var $$a = _vm.selectAll,
                        $$el = $event.target,
                        $$c = $$el.checked ? true : false
                      if (Array.isArray($$a)) {
                        var $$v = null,
                          $$i = _vm._i($$a, $$v)
                        if ($$el.checked) {
                          $$i < 0 && (_vm.selectAll = $$a.concat([$$v]))
                        } else {
                          $$i > -1 &&
                            (_vm.selectAll = $$a
                              .slice(0, $$i)
                              .concat($$a.slice($$i + 1)))
                        }
                      } else {
                        _vm.selectAll = $$c
                      }
                    }
                  }
                })
              ]
            )
          : _vm._e(),
        _vm._v(" "),
        _vm._l(_vm.columns, function(value, key) {
          return _c("Column", { attrs: { columnClass: ["column", key] } }, [
            _vm._v("\n        " + _vm._s(value.label) + "\n    ")
          ])
        })
      ],
      2
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-710b0e45", esExports)
  }
}

/***/ }),
/* 88 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Row_vue__ = __webpack_require__(26);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4f6c8bd2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Row_vue__ = __webpack_require__(89);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Row_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4f6c8bd2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Row_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/Row.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4f6c8bd2", Component.options)
  } else {
    hotAPI.reload("data-v-4f6c8bd2", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 89 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "tr",
    [
      _vm.showCb
        ? _c(
            "Column",
            {
              attrs: {
                "column-class": ["check-column"],
                "is-head": true,
                scope: "row"
              }
            },
            [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.checkedItems,
                    expression: "checkedItems"
                  }
                ],
                attrs: { type: "checkbox", name: "item[]" },
                domProps: {
                  value: _vm.id,
                  checked: Array.isArray(_vm.checkedItems)
                    ? _vm._i(_vm.checkedItems, _vm.id) > -1
                    : _vm.checkedItems
                },
                on: {
                  change: function($event) {
                    var $$a = _vm.checkedItems,
                      $$el = $event.target,
                      $$c = $$el.checked ? true : false
                    if (Array.isArray($$a)) {
                      var $$v = _vm.id,
                        $$i = _vm._i($$a, $$v)
                      if ($$el.checked) {
                        $$i < 0 && (_vm.checkedItems = $$a.concat([$$v]))
                      } else {
                        $$i > -1 &&
                          (_vm.checkedItems = $$a
                            .slice(0, $$i)
                            .concat($$a.slice($$i + 1)))
                      }
                    } else {
                      _vm.checkedItems = $$c
                    }
                  }
                }
              })
            ]
          )
        : _vm._e(),
      _vm._v(" "),
      _vm._l(_vm.columns, function(value, key) {
        return _c(
          "Column",
          { attrs: { "column-class": ["column", key], "is-head": false } },
          [_vm._v("\n        " + _vm._s(_vm.row[key]) + "\n    ")]
        )
      })
    ],
    2
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-4f6c8bd2", esExports)
  }
}

/***/ }),
/* 90 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NavTop_vue__ = __webpack_require__(27);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_21bb3a6a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NavTop_vue__ = __webpack_require__(93);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NavTop_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_21bb3a6a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NavTop_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/NavTop.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-21bb3a6a", Component.options)
  } else {
    hotAPI.reload("data-v-21bb3a6a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 91 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.hasOptions
    ? _c("div", { staticClass: "alignleft actions bulkactions" }, [
        _c(
          "label",
          { staticClass: "screen-reader-text", attrs: { for: _vm.actionId } },
          [_vm._v(_vm._s(_vm.actionLabel))]
        ),
        _vm._v(" "),
        _c(
          "select",
          {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.bulkAction,
                expression: "bulkAction"
              }
            ],
            attrs: { name: "action", id: _vm.actionId },
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
                _vm.bulkAction = $event.target.multiple
                  ? $$selectedVal
                  : $$selectedVal[0]
              }
            }
          },
          [
            _c("option", { attrs: { value: "-1" } }, [
              _vm._v(_vm._s(_vm.actionLabel))
            ]),
            _vm._v(" "),
            _vm._l(_vm.actionOptions, function(option) {
              return _c("option", { domProps: { value: option.key } }, [
                _vm._v(_vm._s(option.label))
              ])
            })
          ],
          2
        ),
        _vm._v(" "),
        _c(
          "button",
          {
            staticClass: "button",
            attrs: { type: "button", id: _vm.actionId + "-submit" },
            on: {
              click: function($event) {
                return _vm.actionClickHandler(_vm.actionId)
              }
            }
          },
          [_vm._v(_vm._s(_vm.actionSubmit))]
        )
      ])
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-64645c20", esExports)
  }
}

/***/ }),
/* 92 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "tablenav-pages" }, [
    _c("span", { staticClass: "displaying-num" }, [
      _vm._v(_vm._s(_vm.totalItems) + " items")
    ]),
    _vm._v(" "),
    _vm.canPaginate
      ? _c("span", { staticClass: "pagination-links" }, [
          _c(
            "span",
            {
              staticClass: "tablenav-pages-navspan button",
              attrs: { "aria-hidden": "true", disabled: _vm.disableFirstPage },
              on: {
                click: function($event) {
                  return _vm.paginate(1)
                }
              }
            },
            [_vm._v("«")]
          ),
          _vm._v(" "),
          _c(
            "span",
            {
              staticClass: "tablenav-pages-navspan button",
              attrs: { "aria-hidden": "true", disabled: _vm.disablePrevPage },
              on: {
                click: function($event) {
                  return _vm.paginate(_vm.prevPage)
                }
              }
            },
            [_vm._v("‹")]
          ),
          _vm._v(" "),
          _vm.isTop
            ? _c("span", { staticClass: "paging-input" }, [
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
                    min: "1",
                    max: _vm.totalPages,
                    id: "current-page-selector",
                    type: "text",
                    name: "paged",
                    size: "1"
                  },
                  domProps: { value: _vm.currentPageNumber }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "tablenav-paging-text" }, [
                  _vm._v(" of "),
                  _c("span", { staticClass: "total-pages" }, [
                    _vm._v(_vm._s(_vm.totalPages))
                  ])
                ])
              ])
            : _c("span", { staticClass: "paging-input" }, [
                _c("span", { staticClass: "tablenav-paging-text" }, [
                  _vm._v(_vm._s(_vm.currentPageNumber) + " of "),
                  _c("span", { staticClass: "total-pages" }, [
                    _vm._v(_vm._s(_vm.totalPages))
                  ])
                ])
              ]),
          _vm._v(" "),
          _c(
            "span",
            {
              staticClass: "tablenav-pages-navspan button",
              attrs: { "aria-hidden": "true", disabled: _vm.disableNextPage },
              on: {
                click: function($event) {
                  return _vm.paginate(_vm.nextPage)
                }
              }
            },
            [_vm._v("›")]
          ),
          _vm._v(" "),
          _c(
            "span",
            {
              staticClass: "tablenav-pages-navspan button",
              attrs: { "aria-hidden": "true", disabled: _vm.disableLastPage },
              on: {
                click: function($event) {
                  return _vm.paginate(_vm.totalPages)
                }
              }
            },
            [_vm._v("»")]
          )
        ])
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-d731765c", esExports)
  }
}

/***/ }),
/* 93 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "tablenav top" },
    [
      _vm.hasBulkActions
        ? _c("BulkAction", {
            attrs: {
              "action-key": "bulk-actions",
              "action-label": "Bulk Actions",
              "action-options": _vm.bulkActions,
              "action-click": false
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm._l(_vm.extraActions, function(action) {
        return _vm.hasExtraActions
          ? _c("BulkAction", {
              attrs: {
                "action-key": action.key,
                "action-label": action.label,
                "action-options": action.options
              }
            })
          : _vm._e()
      }),
      _vm._v(" "),
      _c("Pagination", {
        attrs: {
          "total-items": _vm.totalItems,
          "current-page": _vm.currentPage,
          "per-page": _vm.perPage,
          "pagination-callback": _vm.paginationCallback
        }
      }),
      _vm._v(" "),
      _c("br", { staticClass: "clear" })
    ],
    2
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-21bb3a6a", esExports)
  }
}

/***/ }),
/* 94 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NavBottom_vue__ = __webpack_require__(32);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_37c7c926_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NavBottom_vue__ = __webpack_require__(96);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(95)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-37c7c926"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_NavBottom_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_37c7c926_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_NavBottom_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/src/js/admin/logs/components/list-table/NavBottom.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-37c7c926", Component.options)
  } else {
    hotAPI.reload("data-v-37c7c926", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 95 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 96 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "tablenav bottom" },
    [
      _vm.hasBulkActions
        ? _c("BulkAction", {
            attrs: {
              "action-key": "bulk-actions",
              "action-label": "Bulk Actions",
              "action-options": _vm.bulkActions,
              "is-top": false
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _c("Pagination", {
        attrs: {
          "total-items": _vm.totalItems,
          "current-page": _vm.currentPage,
          "per-page": _vm.perPage,
          "is-top": false,
          "pagination-callback": _vm.paginationCallback
        }
      }),
      _vm._v(" "),
      _c("br", { staticClass: "clear" })
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
    require("vue-hot-reload-api")      .rerender("data-v-37c7c926", esExports)
  }
}

/***/ }),
/* 97 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "form",
    { attrs: { method: "post" } },
    [
      _c("NavTop", {
        attrs: {
          "bulk-actions": _vm.bulkActions,
          "extra-actions": _vm.extraActions,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "total-items": _vm.totalItems,
          "pagination-callback": _vm.paginationCallback
        }
      }),
      _vm._v(" "),
      _c(
        "table",
        { class: _vm.tableClass },
        [
          _c("Header", {
            attrs: { columns: _vm.columns, "show-cb": _vm.showCb }
          }),
          _vm._v(" "),
          _c(
            "tbody",
            [
              _vm._l(_vm.rows, function(row) {
                return _vm.hasRows
                  ? _c("Row", {
                      attrs: {
                        row: row,
                        id: row.id,
                        columns: _vm.columns,
                        "show-cb": _vm.showCb
                      }
                    })
                  : _vm._e()
              }),
              _vm._v(" "),
              !_vm.hasRows
                ? _c("tr", { staticClass: "no-items" }, [
                    _c(
                      "td",
                      {
                        staticClass: "colspanchange",
                        attrs: { colspan: _vm.columnCount }
                      },
                      [_vm._v(_vm._s(_vm.labels.emptyRows))]
                    )
                  ])
                : _vm._e()
            ],
            2
          )
        ],
        1
      ),
      _vm._v(" "),
      _c("NavBottom", {
        attrs: {
          "bulk-actions": _vm.bulkActions,
          "per-page": _vm.perPage,
          "current-page": _vm.currentPage,
          "total-items": _vm.totalItems,
          "pagination-callback": _vm.paginationCallback
        }
      })
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
    require("vue-hot-reload-api")      .rerender("data-v-9ae94874", esExports)
  }
}

/***/ }),
/* 98 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { attrs: { id: "poststuff" } }, [
    _c("div", { attrs: { id: "post-body" } }, [
      _c("div", { attrs: { id: "post-body-content" } }, [
        _c(
          "div",
          { staticClass: "meta-box-sortables ui-sortable" },
          [
            _c("Table", {
              attrs: {
                columns: _vm.columns,
                rows: _vm.rows,
                "bulk-actions": _vm.bulkActions,
                "extra-actions": _vm.extraActions,
                "pagination-callback": _vm.updateRows,
                "total-items": _vm.totalItems,
                "per-page": _vm.perPage,
                "current-page": _vm.currentPage
              }
            })
          ],
          1
        )
      ])
    ])
  ])
}
var staticRenderFns = []
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
/* 99 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_Settings_vue__ = __webpack_require__(33);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_47ecc20b_hasScoped_true_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_Settings_vue__ = __webpack_require__(101);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(100)
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
/* 100 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 101 */
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
],[78]);