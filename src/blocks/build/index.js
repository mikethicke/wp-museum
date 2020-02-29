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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/blocks/src/index.js");
/******/ })
/************************************************************************/
/******/ ({

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

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

module.exports = _classCallCheck;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

module.exports = _createClass;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/extends.js":
/*!********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/extends.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _extends() {
  module.exports = _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

module.exports = _extends;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

module.exports = _getPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}

module.exports = _inherits;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArrayLimit(arr, i) {
  if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) {
    return;
  }

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
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}

module.exports = _nonIterableRest;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ../helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");

var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return assertThisInitialized(self);
}

module.exports = _possibleConstructorReturn;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

module.exports = _setPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles */ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js");

var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit */ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");

var nonIterableRest = __webpack_require__(/*! ./nonIterableRest */ "./node_modules/@babel/runtime/helpers/nonIterableRest.js");

function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || nonIterableRest();
}

module.exports = _slicedToArray;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

module.exports = _typeof;

/***/ }),

/***/ "./src/blocks/src/components/object-search-box.js":
/*!********************************************************!*\
  !*** ./src/blocks/src/components/object-search-box.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);



/**
 * Modal dialog box allowing user to search for a museum object post.
 */



var ObjectSearchButton = function ObjectSearchButton(props) {
  var children = props.children;

  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(false),
      _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),
      isOpen = _useState2[0],
      setOpen = _useState2[1];

  var openModal = function openModal() {
    return setOpen(true);
  };

  var closeModal = function closeModal() {
    return setOpen(false);
  };

  return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
    isSecondary: true,
    onClick: openModal
  }, children), isOpen && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Modal"], {
    title: "This is my modal",
    onRequestClose: closeModal
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Button"], {
    isSecondary: true,
    onClick: closeModal
  }, "My custom close button")))];
};

/* harmony default export */ __webpack_exports__["default"] = (ObjectSearchButton);

/***/ }),

/***/ "./src/blocks/src/index.js":
/*!*********************************!*\
  !*** ./src/blocks/src/index.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _test_block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./test-block */ "./src/blocks/src/test-block/index.js");
/* harmony import */ var _object_info_box__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./object-info-box */ "./src/blocks/src/object-info-box/index.js");



/***/ }),

/***/ "./src/blocks/src/object-info-box/edit.js":
/*!************************************************!*\
  !*** ./src/blocks/src/object-info-box/edit.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/extends */ "./node_modules/@babel/runtime/helpers/extends.js");
/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/blockEditor */ "@wordpress/blockEditor");
/* harmony import */ var _wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _components_object_search_box__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../components/object-search-box */ "./src/blocks/src/components/object-search-box.js");
/* harmony import */ var _info_content__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./info-content */ "./src/blocks/src/object-info-box/info-content.js");
/* harmony import */ var _info_placeholder__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./info-placeholder */ "./src/blocks/src/object-info-box/info-placeholder.js");
















var imageSizes = {
  thumbnail: {
    height: 150,
    width: 150
  },
  medium: {
    height: 300,
    width: 300
  },
  large: {
    height: 1024,
    width: 1024
  },
  full: {
    height: null,
    width: null
  }
};

var AppearancePanel =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(AppearancePanel, _Component);

  function AppearancePanel(props) {
    var _this;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, AppearancePanel);

    _this = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(AppearancePanel).call(this, props));
    _this.setAppearance = _this.setAppearance.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this));
    _this.render = _this.render.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this));
    return _this;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(AppearancePanel, [{
    key: "setAppearance",
    value: function setAppearance(field, val) {
      var _this$props = this.props,
          appearance = _this$props.appearance,
          setAttributes = _this$props.setAttributes;
      var newVal;
      val ? newVal = val : newVal = 0;
      var newAppearance = Object.assign({}, appearance);

      if (field === 'borderColor' || field === 'backgroundColor') {
        newVal = newVal.hex;
      }

      newAppearance[field] = newVal;
      setAttributes({
        appearance: newAppearance
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props2 = this.props,
          appearance = _this$props2.appearance,
          setAttributes = _this$props2.setAttributes;
      var borderWidth = appearance.borderWidth,
          borderColor = appearance.borderColor,
          backgroundColor = appearance.backgroundColor,
          backgroundOpacity = appearance.backgroundOpacity;
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
        title: "Appearance",
        initialOpen: false
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["RangeControl"], {
        label: "Border Width",
        allowReset: true,
        initialPosition: "0",
        onChange: function onChange(val) {
          return _this2.setAppearance('borderWidth', val);
        },
        min: "0",
        max: "5",
        step: "0.5",
        value: borderWidth
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("p", null, "Border Color"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["ColorPicker"], {
        color: borderColor,
        onChangeComplete: function onChangeComplete(val) {
          return _this2.setAppearance('borderColor', val);
        },
        disableAlpha: true
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("p", null, "Background Color"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["ColorPicker"], {
        color: backgroundColor,
        onChangeComplete: function onChangeComplete(val) {
          return _this2.setAppearance('backgroundColor', val);
        },
        disableAlpha: true
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["RangeControl"], {
        label: "Background Opacity",
        allowReset: true,
        initialPosition: "0",
        onChange: function onChange(val) {
          return _this2.setAppearance('backgroundOpacity', val);
        },
        min: "0",
        max: "1",
        step: "0.01",
        value: backgroundOpacity
      })))];
    }
  }]);

  return AppearancePanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

var FieldsPanel =
/*#__PURE__*/
function (_Component2) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(FieldsPanel, _Component2);

  function FieldsPanel() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, FieldsPanel);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(FieldsPanel).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(FieldsPanel, [{
    key: "updateField",
    value: function updateField(key, val) {
      var _this$props3 = this.props,
          setAttributes = _this$props3.setAttributes,
          toggle = _this$props3.toggle,
          fields = _this$props3.fields;
      fields[key] = val;
      setAttributes({
        fields: fields,
        toggle: !toggle
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this3 = this;

      var _this$props4 = this.props,
          fieldData = _this$props4.fieldData,
          fields = _this$props4.fields;

      if (Object.keys(fields).length > 0 && Object.keys(fieldData).length === Object.keys(fields).length) {
        var items = [];

        var _loop = function _loop(key) {
          items.push( //Use map instead
          Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["CheckboxControl"], {
            key: key.toString(),
            label: fieldData[key]['name'],
            checked: fields[key],
            onChange: function onChange(val) {
              _this3.updateField(key, val);
            }
          }));
        };

        for (var key in fields) {
          _loop(key);
        }

        return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
          title: "Custom Fields",
          initialOpen: false
        }, items)];
      } else {
        return null;
      }
    }
  }]);

  return FieldsPanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

var OptionsPanel =
/*#__PURE__*/
function (_Component3) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(OptionsPanel, _Component3);

  function OptionsPanel() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, OptionsPanel);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(OptionsPanel).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(OptionsPanel, [{
    key: "render",
    value: function render() {
      var _this$props5 = this.props,
          attributes = _this$props5.attributes,
          setAttributes = _this$props5.setAttributes;
      var displayTitle = attributes.displayTitle,
          displayExcerpt = attributes.displayExcerpt,
          displayThumbnail = attributes.displayThumbnail,
          linkToObject = attributes.linkToObject;
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
        title: "Options",
        initialOpen: true
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["CheckboxControl"], {
        label: "Display Title",
        checked: displayTitle,
        onChange: function onChange(val) {
          setAttributes({
            displayTitle: val
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["CheckboxControl"], {
        label: "Display Excerpt",
        checked: displayExcerpt,
        onChange: function onChange(val) {
          setAttributes({
            displayExcerpt: val
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["CheckboxControl"], {
        label: "Display Thumbnail",
        checked: displayThumbnail,
        onChange: function onChange(val) {
          setAttributes({
            displayThumbnail: val
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["CheckboxControl"], {
        label: "Link to Object",
        checked: linkToObject,
        onChange: function onChange(val) {
          setAttributes({
            linkToObject: val
          });
        }
      }))];
    }
  }]);

  return OptionsPanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

var ImageSizePanel =
/*#__PURE__*/
function (_Component4) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(ImageSizePanel, _Component4);

  function ImageSizePanel(props) {
    var _this4;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, ImageSizePanel);

    _this4 = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(ImageSizePanel).call(this, props));
    _this4.updateImage = _this4.updateImage.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this4));
    _this4.updateHeight = _this4.updateHeight.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this4));
    _this4.updateWidth = _this4.updateWidth.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this4));
    _this4.updateImageAlignment = _this4.updateImageAlignment.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this4));
    return _this4;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(ImageSizePanel, [{
    key: "updateImage",
    value: function updateImage(sizeSlug) {
      var _this$props6 = this.props,
          setAttributes = _this$props6.setAttributes,
          state = _this$props6.state;
      var imgHeight = state.imgHeight,
          imgWidth = state.imgWidth,
          imgReady = state.imgReady;

      if (imgReady) {
        var targetSize = imageSizes[sizeSlug].width; //width == height

        var scaleFactor;

        if (targetSize === null) {
          scaleFactor = 1;
        } else {
          scaleFactor = targetSize / Math.max(imgWidth, imgHeight);
        }

        var newImageDimensions = {
          height: Math.round(scaleFactor * imgHeight),
          width: Math.round(scaleFactor * imgWidth),
          size: sizeSlug
        };
        setAttributes({
          imageDimensions: newImageDimensions
        });
      }
    }
  }, {
    key: "updateHeight",
    value: function updateHeight(newHeight) {
      var _this$props7 = this.props,
          setAttributes = _this$props7.setAttributes,
          state = _this$props7.state;
      var imgHeight = state.imgHeight,
          imgWidth = state.imgWidth,
          imgReady = state.imgReady;

      if (imgReady) {
        var setHeight = Math.min(newHeight, imgHeight);
        var setWidth = Math.round(setHeight / imgHeight * imgWidth);
        var newImageDimensions = {
          height: setHeight,
          width: setWidth,
          size: null
        };
        setAttributes({
          imageDimensions: newImageDimensions
        });
      }
    }
  }, {
    key: "updateWidth",
    value: function updateWidth(newWidth) {
      var _this$props8 = this.props,
          setAttributes = _this$props8.setAttributes,
          state = _this$props8.state;
      var imgHeight = state.imgHeight,
          imgWidth = state.imgWidth,
          imgReady = state.imgReady;

      if (imgReady) {
        var setWidth = Math.min(newWidth, imgWidth);
        var setHeight = Math.round(setWidth / imgWidth * imgHeight);
        var newImageDimensions = {
          height: setHeight,
          width: setWidth,
          size: null
        };
        setAttributes({
          imageDimensions: newImageDimensions
        });
      }
    }
  }, {
    key: "updateImageAlignment",
    value: function updateImageAlignment(newAlignment) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        imageAlignment: newAlignment
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this5 = this;

      var attributes = this.props.attributes;
      var imageDimensions = attributes.imageDimensions,
          imageAlignment = attributes.imageAlignment;
      var width = imageDimensions.width,
          height = imageDimensions.height,
          sizeSlug = imageDimensions.sizeSlug;
      var imageSizeOptions = [{
        value: 'thumbnail',
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Thumbnail')
      }, {
        value: 'medium',
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Medium')
      }, {
        value: 'large',
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Large')
      }, {
        value: 'full',
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Full Size')
      }];
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Image Settings'),
        initialOpen: true
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Image Size'),
        value: sizeSlug,
        options: imageSizeOptions,
        onChange: this.updateImage
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Image Dimensions')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["TextControl"], {
        type: "number",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Width'),
        value: width || '',
        min: 1,
        onChange: this.updateWidth
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["TextControl"], {
        type: "number",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Height'),
        value: height || '',
        min: 1,
        onChange: this.updateHeight
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__["__"])('Image Alignment')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["ButtonGroup"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Button"], {
        isPrimary: imageAlignment === 'left',
        onClick: function onClick() {
          _this5.updateImageAlignment('left');
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Dashicon"], {
        icon: "align-left"
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Button"], {
        isPrimary: imageAlignment === 'center',
        onClick: function onClick() {
          _this5.updateImageAlignment('center');
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Dashicon"], {
        icon: "align-center"
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Button"], {
        isPrimary: imageAlignment === 'right',
        onClick: function onClick() {
          _this5.updateImageAlignment('right');
        }
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Dashicon"], {
        icon: "align-right"
      })))))];
    }
  }]);

  return ImageSizePanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

var FontSizePanel =
/*#__PURE__*/
function (_Component5) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(FontSizePanel, _Component5);

  function FontSizePanel() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, FontSizePanel);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(FontSizePanel).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(FontSizePanel, [{
    key: "render",
    value: function render() {
      var _this$props9 = this.props,
          setAttributes = _this$props9.setAttributes,
          titleTag = _this$props9.titleTag,
          fontSize = _this$props9.fontSize;
      var titleTagOptions = [{
        label: 'Heading 2',
        value: 'h2'
      }, {
        label: 'Heading 3',
        value: 'h3'
      }, {
        label: 'Heading 4',
        value: 'h4'
      }, {
        label: 'Heading 5',
        value: 'h5'
      }, {
        label: 'Heading 6',
        value: 'h6'
      }, {
        label: 'Paragraph',
        value: 'p'
      }];
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
        title: "Font Size",
        initialOpen: false
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["SelectControl"], {
        label: "Title Style",
        value: titleTag,
        options: titleTagOptions,
        onChange: function onChange(val) {
          return setAttributes({
            titleTag: val
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["RangeControl"], {
        label: "Text (em)",
        onChange: function onChange(val) {
          return val ? setAttributes({
            fontSize: val
          }) : setAttributes({
            fontSize: 1
          });
        },
        min: "0.25",
        max: "2",
        step: "0.05",
        value: fontSize,
        initialPosition: "1",
        withInputField: true,
        allowReset: true
      })))];
    }
  }]);

  return FontSizePanel;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

function EditContent(props) {
  var attributes = props.attributes,
      state = props.state,
      onChangeObjectID = props.onChangeObjectID,
      onUpdateButton = props.onUpdateButton,
      imageSizes = props.imageSizes;
  var objectID = attributes.objectID,
      title = attributes.title,
      excerpt = attributes.excerpt,
      thumbnailURL = attributes.thumbnailURL,
      objectURL = attributes.objectURL,
      fields = attributes.fields,
      fieldData = attributes.fieldData,
      imageDimensions = attributes.imageDimensions,
      imageAlignment = attributes.imageAlignment,
      fontSize = attributes.fontSize,
      displayTitle = attributes.displayTitle,
      displayThumbnail = attributes.displayThumbnail,
      displayExcerpt = attributes.displayExcerpt,
      linkToObject = attributes.linkToObject,
      appearance = attributes.appearance,
      titleTag = attributes.titleTag;
  var object_fetched = state.object_fetched;

  if (object_fetched) {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_info_content__WEBPACK_IMPORTED_MODULE_13__["default"], {
      objectID: objectID,
      title: displayTitle ? title : null,
      excerpt: displayExcerpt ? excerpt : null,
      thumbnailURL: displayThumbnail ? thumbnailURL : null,
      objectURL: linkToObject ? objectURL : null,
      fields: fields,
      fieldData: fieldData,
      imageDimensions: imageDimensions,
      imageSizes: imageSizes,
      state: state,
      imageAlignment: imageAlignment,
      fontSize: fontSize,
      appearance: appearance,
      titleTag: titleTag
    })];
  } else {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_info_placeholder__WEBPACK_IMPORTED_MODULE_14__["default"], {
      objectID: objectID,
      onChangeObjectID: onChangeObjectID,
      onUpdateButton: onUpdateButton
    })];
  }
}

var ObjectInfoEdit =
/*#__PURE__*/
function (_Component6) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_6___default()(ObjectInfoEdit, _Component6);

  function ObjectInfoEdit(props) {
    var _this6;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1___default()(this, ObjectInfoEdit);

    _this6 = _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(ObjectInfoEdit).call(this, props));
    _this6.onUpdateButton = _this6.onUpdateButton.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this6));
    _this6.onChangeObjectID = _this6.onChangeObjectID.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this6));
    _this6.fetchFieldData = _this6.fetchFieldData.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_5___default()(_this6));
    _this6.state = {
      object_fetched: false,
      object_data: {},
      imgHeight: null,
      imgWidth: null,
      imgReady: false
    };
    return _this6;
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2___default()(ObjectInfoEdit, [{
    key: "getImageDimensions",
    value: function getImageDimensions() {
      var thumbnailURL = this.props.attributes.thumbnailURL;
      var that = this; // https://stackoverflow.com/questions/52059596/loading-an-image-on-web-browser-using-promise

      function loadImage(src) {
        return new Promise(function (resolve, reject) {
          var img = new Image();
          img.addEventListener("load", function () {
            return resolve(img);
          });
          img.addEventListener("error", function (err) {
            return reject(err);
          });
          img.src = src;
        });
      }

      ;
      loadImage(thumbnailURL).then(function (img) {
        that.setState({
          imgHeight: img.height,
          imgWidth: img.width,
          imgReady: true
        });
      });
    }
  }, {
    key: "fetchFieldData",
    value: function fetchFieldData() {
      var setAttributes = this.props.setAttributes;
      var objectID = this.props.attributes.objectID;
      var base_rest_path = '/wp-museum/v1/';

      if (objectID != null) {
        var object_path = base_rest_path + 'all/' + objectID;
        var that = this;
        _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
          path: object_path
        }).then(function (result) {
          that.setState({
            object_data: result
          });
          setAttributes({
            title: result['post_title'],
            excerpt: result['excerpt'],
            thumbnailURL: result['thumbnail'][0],
            objectURL: result['link']
          });

          if (that.props.attributes.thumbnailURL != null) {
            that.setState({
              imgReady: false
            });
            that.getImageDimensions();
          }

          _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
            path: base_rest_path + result.post_type + '/custom'
          }).then(function (result) {
            var fields = that.props.attributes.fields;
            var object_data = that.state.object_data;
            var newFields = {};
            var fieldData = {};

            for (var key in result) {
              if (typeof fields[key] === 'undefined') {
                newFields[key] = result[key]['info_default'];
              } else {
                newFields[key] = fields[key];
              }

              var content = '';

              if (result[key]['type'] === 'tinyint') {
                if (object_data[result[key]['slug']] === 1) {
                  content = 'Yes';
                } else {
                  content = 'No';
                }
              } else {
                content = object_data[result[key]['slug']];
              }

              fieldData[key] = {
                name: result[key]['name'],
                content: content
              };
            }

            setAttributes({
              fields: newFields,
              fieldData: fieldData
            });
            that.setState({
              object_fetched: true
            });
          });
        });
      }
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      this.fetchFieldData();
    }
  }, {
    key: "onChangeObjectID",
    value: function onChangeObjectID(content) {
      var setAttributes = this.props.setAttributes;
      setAttributes({
        objectID: content
      });
    }
  }, {
    key: "onUpdateButton",
    value: function onUpdateButton() {
      this.fetchFieldData();
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props10 = this.props,
          setAttributes = _this$props10.setAttributes,
          attributes = _this$props10.attributes;
      var fontSize = attributes.fontSize,
          appearance = attributes.appearance,
          titleTag = attributes.titleTag;
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])("div", {
        className: "wp-museum-object-info-edit"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_8__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelBody"], {
        title: "Embed Object",
        initialOpen: true
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["TextControl"], {
        label: "Object ID",
        onChange: this.onChangeObjectID,
        value: this.objectID
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["PanelRow"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_9__["Button"], {
        isDefault: true,
        isPrimary: true,
        onClick: this.onUpdateButton
      }, "Update"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(_components_object_search_box__WEBPACK_IMPORTED_MODULE_12__["default"], null, "Search"))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(OptionsPanel, this.props), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(ImageSizePanel, _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0___default()({}, this.props, {
        state: this.state
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(AppearancePanel, {
        setAttributes: setAttributes,
        appearance: appearance
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(FontSizePanel, {
        setAttributes: setAttributes,
        titleTag: titleTag,
        fontSize: fontSize
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(FieldsPanel, {
        setAttributes: this.props.setAttributes,
        fields: this.props.attributes.fields,
        fieldData: this.props.attributes.fieldData,
        toggle: this.props.attributes.toggle
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["createElement"])(EditContent, _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0___default()({}, this.props, {
        onUpdateButton: this.onUpdateButton,
        onChangeObjectID: this.onChangeObjectID,
        state: this.state,
        imageSizes: imageSizes
      })))];
    }
  }]);

  return ObjectInfoEdit;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (ObjectInfoEdit);

/***/ }),

/***/ "./src/blocks/src/object-info-box/index.js":
/*!*************************************************!*\
  !*** ./src/blocks/src/object-info-box/index.js ***!
  \*************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/blocks/src/object-info-box/edit.js");



Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__["registerBlockType"])('wp-museum/object-info-box', {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Museum Object Infobox'),
  icon: 'archive',
  category: 'widgets',
  supports: {
    align: ['left', 'right', 'center']
  },
  attributes: {
    objectID: {
      type: 'string',
      default: null
    },
    title: {
      type: 'string',
      default: null
    },
    excerpt: {
      type: 'string',
      default: null
    },
    thumbnailURL: {
      type: 'string',
      default: null
    },
    objectURL: {
      type: 'string',
      default: null
    },
    displayTitle: {
      type: 'boolean',
      default: true
    },
    displayExcerpt: {
      type: 'boolean',
      default: true
    },
    displayThumbnail: {
      type: 'boolean',
      default: true
    },
    linkToObject: {
      type: 'boolean',
      default: true
    },
    fields: {
      type: 'object',
      default: {}
    },
    fieldData: {
      type: 'object',
      default: {}
    },
    toggle: {
      type: 'boolean',
      default: false
    },
    imageDimensions: {
      type: 'object',
      default: {
        width: null,
        height: null,
        size: 'large' //options: thumbnail, medium, large, full

      }
    },
    imageAlignment: {
      type: 'string',
      default: 'center' //options: left, center, right

    },
    fontSize: {
      type: 'float',
      default: 0.7
    },
    titleTag: {
      type: 'string',
      default: 'h6' //options: h2, h3, h, h5, h6, p

    },
    appearance: {
      type: 'object',
      default: {
        borderWidth: 1,
        borderColor: '#000',
        backgroundColor: '#fff',
        backgroundOpacity: 0
      }
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save(props) {
    return null;
  }
});

/***/ }),

/***/ "./src/blocks/src/object-info-box/info-content.js":
/*!********************************************************!*\
  !*** ./src/blocks/src/object-info-box/info-content.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);






 // https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb

function hexToRgb(hex) {
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function (m, r, g, b) {
    return r + r + g + g + b + b;
  });
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

var InfoContent =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(InfoContent, _Component);

  function InfoContent() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, InfoContent);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(InfoContent).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(InfoContent, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          objectID = _this$props.objectID,
          title = _this$props.title,
          excerpt = _this$props.excerpt,
          thumbnailURL = _this$props.thumbnailURL,
          fields = _this$props.fields,
          fieldData = _this$props.fieldData,
          imageDimensions = _this$props.imageDimensions,
          imageSizes = _this$props.imageSizes,
          state = _this$props.state,
          imageAlignment = _this$props.imageAlignment,
          fontSize = _this$props.fontSize,
          appearance = _this$props.appearance,
          titleTag = _this$props.titleTag;
      var width = imageDimensions.width,
          height = imageDimensions.height,
          size = imageDimensions.size;
      var imgHeight = state.imgHeight,
          imgWidth = state.imgWidth,
          imgReady = state.imgReady;
      var borderWidth = appearance.borderWidth,
          borderColor = appearance.borderColor,
          backgroundColor = appearance.backgroundColor,
          backgroundOpacity = appearance.backgroundOpacity;
      var imgRenderHeight, imgRenderWidth;

      if (imgReady) {
        if (width != null && height != null) {
          imgRenderWidth = width;
          imgRenderHeight = height;
        } else {
          var targetSize = imageSizes[size].width; //width == height

          var scaleFactor = targetSize / Math.max(imgHeight, imgWidth);
          imgRenderWidth = Math.round(imgWidth * scaleFactor);
          imgRenderHeight = Math.round(imgWidth * scaleFactor);
        }
      }

      var field_list = [];

      if (Object.keys(fieldData).length === Object.keys(fields).length) {
        for (var key in fields) {
          if (fields[key]) {
            field_list.push(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("li", {
              key: key,
              style: {
                fontSize: fontSize + 'em'
              }
            }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("span", {
              className: "field-name"
            }, fieldData[key]['name'], ": "), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("span", {
              className: "field-data"
            }, fieldData[key]['content'])));
          }
        }
      }

      var TitleTag = titleTag;
      var body = [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Fragment"], null, imgReady && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("img", {
        className: 'img_info_' + imageAlignment,
        src: thumbnailURL,
        height: imgRenderHeight,
        width: imgRenderWidth
      }), title === null || Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(TitleTag, null, title), excerpt === null || Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("p", {
        style: {
          fontSize: fontSize + 'em'
        }
      }, excerpt, " "))];
      var bRGB = hexToRgb(backgroundColor.toString(16));
      var divStyle = {
        borderWidth: borderWidth,
        borderStyle: 'solid',
        padding: '5px',
        borderColor: borderColor,
        backgroundColor: "rgba( ".concat(bRGB.r, ", ").concat(bRGB.g, ", ").concat(bRGB.b, ", ").concat(backgroundOpacity, " )")
      };

      if (objectID !== null) {
        return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", {
          style: divStyle
        }, body, field_list.length === 0 || Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("ul", null, field_list))];
      } else {
        return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", null)];
      }
    }
  }]);

  return InfoContent;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (InfoContent);

/***/ }),

/***/ "./src/blocks/src/object-info-box/info-placeholder.js":
/*!************************************************************!*\
  !*** ./src/blocks/src/object-info-box/info-placeholder.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _components_object_search_box_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../components/object-search-box.js */ "./src/blocks/src/components/object-search-box.js");










var InfoPlaceholder =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(InfoPlaceholder, _Component);

  function InfoPlaceholder() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, InfoPlaceholder);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(InfoPlaceholder).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(InfoPlaceholder, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          objectID = _this$props.objectID,
          onChangeObjectID = _this$props.onChangeObjectID,
          onUpdateButton = _this$props.onUpdateButton;
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("p", null, "Enter the Wordpress ID of the object you wish to display."), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__["TextControl"], {
        label: "Object ID",
        onChange: onChangeObjectID,
        value: objectID
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__["Button"], {
        isDefault: true,
        isPrimary: true,
        onClick: onUpdateButton
      }, "Update"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_components_object_search_box_js__WEBPACK_IMPORTED_MODULE_7__["default"], null, "Search"))];
    }
  }]);

  return InfoPlaceholder;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

/* harmony default export */ __webpack_exports__["default"] = (InfoPlaceholder);

/***/ }),

/***/ "./src/blocks/src/test-block/index.js":
/*!********************************************!*\
  !*** ./src/blocks/src/test-block/index.js ***!
  \********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/blockEditor */ "@wordpress/blockEditor");
/* harmony import */ var _wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__);











var edit =
/*#__PURE__*/
function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(EditComponent, _Component);

  function EditComponent() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, EditComponent);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(EditComponent).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(EditComponent, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var an_array = attributes.an_array,
          a_boolean = attributes.a_boolean;
      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_blockEditor__WEBPACK_IMPORTED_MODULE_6__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["CheckboxControl"], {
        label: "Checkbox",
        checked: an_array[0] === 1,
        onChange: function onChange(val) {
          an_array.push(an_array[0]);
          val ? an_array[0] = 1 : an_array[0] = 0;
          setAttributes({
            an_array: an_array // a_boolean: ! a_boolean

          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])("div", null, "Array: ", an_array.toString()))];
    }
  }]);

  return EditComponent;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["Component"]);

Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_8__["registerBlockType"])('test/array-not-update', {
  title: 'Array Attribute Change Issue',
  icon: 'universal-access-alt',
  category: 'layout',
  attributes: {
    an_array: {
      type: 'object',
      default: []
    },
    a_boolean: {
      type: 'boolean',
      default: false
    }
  },
  edit: edit,
  save: function save() {
    return null;
  }
});

/***/ }),

/***/ "@wordpress/api-fetch":
/*!*******************************************!*\
  !*** external {"this":["wp","apiFetch"]} ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/blockEditor":
/*!**********************************************!*\
  !*** external {"this":["wp","blockEditor"]} ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blockEditor"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

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

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map