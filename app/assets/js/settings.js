/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./app/src/js/settings.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./app/src/js/settings.js":
/*!********************************!*\
  !*** ./app/src/js/settings.js ***!
  \********************************/
/*! no exports provided */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/babel-loader/lib/index.js):\nError: Cannot find module '@babel/code-frame'\n    at Function.Module._resolveFilename (module.js:548:15)\n    at Function.Module._load (module.js:475:25)\n    at Module.require (module.js:597:17)\n    at require (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/v8-compile-cache/v8-compile-cache.js:161:20)\n    at _codeFrame (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transformation/normalize-file.js:69:16)\n    at parser (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transformation/normalize-file.js:191:29)\n    at normalizeFile (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transformation/normalize-file.js:138:11)\n    at runSync (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transformation/index.js:44:43)\n    at runAsync (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transformation/index.js:35:14)\n    at process.nextTick (/var/www/html/development/wordpress/wp_404/wp-content/plugins/404-to-301-new/node_modules/@babel/core/lib/transform.js:34:34)");

/***/ })

/******/ });