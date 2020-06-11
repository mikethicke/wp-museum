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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

module.exports = _arrayLikeToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

module.exports = _arrayWithHoles;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}

module.exports = _iterableToArrayLimit;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

module.exports = _nonIterableRest;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles */ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js");

var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit */ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");

var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");

var nonIterableRest = __webpack_require__(/*! ./nonIterableRest */ "./node_modules/@babel/runtime/helpers/nonIterableRest.js");

function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();
}

module.exports = _slicedToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}

module.exports = _unsupportedIterableToArray;

/***/ }),

/***/ "./src/admin-page/index.js":
/*!*********************************!*\
  !*** ./src/admin-page/index.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__);






var SiteInfo = function SiteInfo(props) {
  var currentlyConnecting = props.currentlyConnecting,
      connectionError = props.connectionError,
      siteData = props.siteData;
  var title = siteData.title,
      description = siteData.description,
      url = siteData.url,
      collections = siteData.collections,
      objectCount = siteData.object_count;
  var collectionCount = Array.isArray(collections) ? collections.length : 0;

  if (currentlyConnecting) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "site-info"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "connection-status connecting"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Spinner"], null), "Connecting..."));
  }

  if (connectionError) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "site-info"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "connection-status error"
    }, "Connection error: ", connectionError));
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "site-info"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "connection-status success"
  }, "Connected"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("table", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("tbody", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("tr", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, "Site:"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, title)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("tr", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, "URL:"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, url)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("tr", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, "Collection count:"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, collectionCount)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("tr", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, "Object count:"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("td", null, objectCount)))));
};

var RemoteAdminPage = function RemoteAdminPage() {
  var wpmRestBase = '/wp-json/wp-museum/v1';
  var mrRestBase = '/museum-remote/v1';

  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(null),
      _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),
      remoteURL = _useState2[0],
      setRemoteURL = _useState2[1];

  var _useState3 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])({}),
      _useState4 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState3, 2),
      siteData = _useState4[0],
      setSiteData = _useState4[1];

  var _useState5 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(false),
      _useState6 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState5, 2),
      currentlyConnecting = _useState6[0],
      setCurrentlyConnecting = _useState6[1];

  var _useState7 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(null),
      _useState8 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState7, 2),
      connectionError = _useState8[0],
      setConnectionError = _useState8[1];

  var textInput = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useRef"])(null);
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    return textInput.current.focus();
  }, []);
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
      path: "".concat(mrRestBase, "/remote_url")
    }).then(function (result) {
      if (result) {
        setRemoteURL(result);
        doConnect(result);
      }
    });
  }, []);

  var updateRemoteUrlOption = function updateRemoteUrlOption() {
    var newUrl = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var data = newUrl != null ? newUrl : remoteURL;
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
      path: "".concat(mrRestBase, "/remote_url"),
      method: 'POST',
      data: data
    }).then(function (result) {
      return console.log(result);
    });
  };

  var onUrlChange = function onUrlChange(event) {
    setRemoteURL(event.target.value);
  };

  var onUrlBlur = function onUrlBlur() {
    var newUrl = cleanUrl();
    updateRemoteUrlOption(newUrl);
  };

  var cleanUrl = function cleanUrl() {
    var newUrl = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var cleanedUrl = newUrl ? newUrl : remoteURL;
    cleanedUrl = cleanedUrl.trim();
    cleanedUrl = cleanedUrl.endsWith('/') ? cleanedUrl.slice(0, -1) : cleanedUrl;
    cleanedUrl = cleanedUrl.startsWith('http://') || cleanedUrl.startsWith('https://') ? cleanedUrl : 'http://' + cleanedUrl;
    setRemoteURL(cleanedUrl);
    return cleanedUrl;
  };
  /**
   * Connects to remote site, checks response, and if everything is ok update the site data.
   *
   * @see https://developers.google.com/web/ilt/pwa/working-with-the-fetch-api
   */


  var doConnect = function doConnect() {
    var newUrl = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    setCurrentlyConnecting(true);
    setConnectionError(null);
    var cleanedUrl = cleanUrl(newUrl);
    updateRemoteUrlOption(cleanedUrl);

    var validateResponse = function validateResponse(response) {
      if (!response.ok) {
        throw Error(response.statusText);
      }

      return response;
    };

    var readJSONResponse = function readJSONResponse(response) {
      response.json().then(function (data) {
        return setSiteData(data);
      });
    };

    var stopConnecting = function stopConnecting() {
      setCurrentlyConnecting(false);
    };

    var catchError = function catchError(error) {
      stopConnecting();
      setConnectionError(error.message);
      console.log('Error fetching: ' + error);
    };

    fetch("".concat(cleanedUrl).concat(wpmRestBase, "/site_data")).then(validateResponse).then(readJSONResponse).then(stopConnecting).catch(catchError);
  };

  var maybeConnect = function maybeConnect(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      event.stopPropagation();
      doConnect();
    }
  };

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "remote-admin-page"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("h2", null, "Museum Remote Configuration"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("label", null, "Remote Museum URL:", Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("input", {
    type: "url",
    ref: textInput,
    placeholder: "http://example.com",
    pattern: "https?:\\/\\/.*",
    onChange: onUrlChange,
    onBlur: onUrlBlur,
    onKeyDown: maybeConnect,
    value: remoteURL
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
    isLarge: true,
    isPrimary: true,
    onClick: doConnect
  }, "Connect"), (currentlyConnecting || connectionError || Object.keys(siteData).length > 0) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(SiteInfo, {
    currentlyConnecting: currentlyConnecting,
    connectionError: connectionError,
    siteData: siteData
  }));
};

if (!!document.getElementById('museum-remote-admin-container')) {
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["render"])(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(RemoteAdminPage, null), document.getElementById('museum-remote-admin-container'));
}

/***/ }),

/***/ "./src/admin.scss":
/*!************************!*\
  !*** ./src/admin.scss ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_page__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin-page */ "./src/admin-page/index.js");
/* harmony import */ var _admin_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./admin.scss */ "./src/admin.scss");
/* harmony import */ var _admin_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_admin_scss__WEBPACK_IMPORTED_MODULE_1__);



/***/ }),

/***/ "@wordpress/api-fetch":
/*!*******************************************!*\
  !*** external {"this":["wp","apiFetch"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map