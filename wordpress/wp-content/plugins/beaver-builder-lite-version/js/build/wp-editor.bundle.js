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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 27);
/******/ })
/************************************************************************/
/******/ ({

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(28);

__webpack_require__(29);

__webpack_require__(33);

/***/ }),

/***/ 28:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var registerStore = wp.data.registerStore;


var DEFAULT_STATE = {
	launching: false
};

var actions = {
	setLaunching: function setLaunching(launching) {
		return {
			type: 'SET_LAUNCHING',
			launching: launching
		};
	}
};

var selectors = {
	isLaunching: function isLaunching(state) {
		return state.launching;
	}
};

registerStore('fl-builder', {
	reducer: function reducer() {
		var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
		var action = arguments[1];

		switch (action.type) {
			case 'SET_LAUNCHING':
				state.launching = action.launching;
		}

		return state;
	},

	actions: actions,
	selectors: selectors
});

/***/ }),

/***/ 29:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _edit = __webpack_require__(30);

var _editPre = __webpack_require__(31);

__webpack_require__(32);

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings;
var version = FLBuilderConfig.wp.version;
var registerBlockType = wp.blocks.registerBlockType;
var RawHTML = wp.element.RawHTML;


var getBlockEdit = function getBlockEdit() {
	if (parseFloat(version) < 5.3) {
		return _editPre.LayoutBlockEditConnectedPre_5_3;
	}
	return _edit.LayoutBlockEditConnected;
};

/**
 * Register the block.
 */
if (builder.access && builder.unrestricted || builder.enabled) {
	registerBlockType('fl-builder/layout', {
		title: strings.title,
		description: strings.description,
		icon: 'welcome-widgets-menus',
		category: 'layout',
		useOnce: true,
		supports: {
			customClassName: false,
			className: false,
			html: false
		},
		attributes: {
			content: {
				type: 'string',
				source: 'html'
			}
		},
		edit: getBlockEdit(),
		save: function save(_ref) {
			var attributes = _ref.attributes;

			return React.createElement(
				RawHTML,
				null,
				attributes.content
			);
		}
	});
}

/***/ }),

/***/ 30:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings,
    urls = _FLBuilderConfig.urls;
var _wp$blocks = wp.blocks,
    rawHandler = _wp$blocks.rawHandler,
    serialize = _wp$blocks.serialize;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Placeholder = _wp$components.Placeholder,
    Spinner = _wp$components.Spinner;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var Component = wp.element.Component;

/**
 * Edit Component
 */

var LayoutBlockEdit = function (_Component) {
	_inherits(LayoutBlockEdit, _Component);

	function LayoutBlockEdit() {
		_classCallCheck(this, LayoutBlockEdit);

		return _possibleConstructorReturn(this, (LayoutBlockEdit.__proto__ || Object.getPrototypeOf(LayoutBlockEdit)).apply(this, arguments));
	}

	_createClass(LayoutBlockEdit, [{
		key: 'componentDidMount',
		value: function componentDidMount() {
			var blockCount = this.props.blockCount;

			if (1 === blockCount) {
				this.toggleEditor('disable');
			}
		}
	}, {
		key: 'componentWillUnmount',
		value: function componentWillUnmount() {
			this.toggleEditor('enable');
		}
	}, {
		key: 'render',
		value: function render() {
			var _props = this.props,
			    blockCount = _props.blockCount,
			    onReplace = _props.onReplace,
			    isLaunching = _props.isLaunching;

			var label = void 0,
			    callback = void 0,
			    description = void 0;

			if (1 === blockCount) {
				label = builder.access ? strings.launch : strings.view;
				callback = this.launchBuilder.bind(this);
			} else {
				label = strings.convert;
				callback = this.convertToBuilder.bind(this);
			}

			if (builder.enabled) {
				description = strings.active;
			} else {
				description = strings.description;
			}

			if (false === builder.showui) {
				return '';
			}

			return React.createElement(
				Placeholder,
				{
					key: 'placeholder',
					instructions: description,
					icon: 'welcome-widgets-menus',
					label: strings.title,
					className: 'fl-builder-layout-launch-view'
				},
				isLaunching && React.createElement(Spinner, null),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						isPrimary: true,
						type: 'submit',
						onClick: callback
					},
					label
				),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						type: 'submit',
						onClick: this.convertToBlocks.bind(this)
					},
					strings.editor
				)
			);
		}
	}, {
		key: 'toggleEditor',
		value: function toggleEditor() {
			var method = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'enable';
			var classList = document.body.classList;

			var enabledClass = 'fl-builder-layout-enabled';

			if ('enable' === method) {
				if (classList.contains(enabledClass)) {
					classList.remove(enabledClass);
				}
			} else {
				if (!classList.contains(enabledClass)) {
					classList.add(enabledClass);
				}
			}
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props2 = this.props,
			    savePost = _props2.savePost,
			    setLaunching = _props2.setLaunching;

			setLaunching(true);
			savePost().then(function () {
				setTimeout(function () {
					window.location.href = builder.access ? urls.edit : urls.view;
				}, 2000);
			});
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props3 = this.props,
			    clientId = _props3.clientId,
			    blocks = _props3.blocks,
			    setAttributes = _props3.setAttributes,
			    removeBlocks = _props3.removeBlocks;

			var content = serialize(blocks);
			var clientIds = blocks.map(function (block) {
				return block.clientId;
			}).filter(function (id) {
				return id !== clientId;
			});

			setAttributes({ content: content.replace(/<!--(.*?)-->/g, '') });
			removeBlocks(clientIds);
			this.launchBuilder();
		}
	}, {
		key: 'convertToBlocks',
		value: function convertToBlocks() {
			var _props4 = this.props,
			    attributes = _props4.attributes,
			    clientId = _props4.clientId,
			    replaceBlocks = _props4.replaceBlocks,
			    onReplace = _props4.onReplace;


			if (attributes.content && !confirm(strings.warning)) {
				return;
			} else if (attributes.content) {
				replaceBlocks([clientId], rawHandler({
					HTML: attributes.content,
					mode: 'BLOCKS'
				}));
			} else {
				onReplace([]);
			}
		}
	}]);

	return LayoutBlockEdit;
}(Component);

/**
 * Connect the edit component to editor data.
 */


var LayoutBlockEditConnected = exports.LayoutBlockEditConnected = compose(withDispatch(function (dispatch, ownProps) {
	var blockEditor = dispatch('core/block-editor');
	var editor = dispatch('core/editor');
	var builder = dispatch('fl-builder');
	return {
		removeBlocks: blockEditor.removeBlocks,
		replaceBlocks: blockEditor.replaceBlocks,
		savePost: editor.savePost,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var blockEditor = select('core/block-editor');
	var editor = select('core/editor');
	var builder = select('fl-builder');
	return {
		blockCount: blockEditor.getBlockCount(),
		blocks: blockEditor.getBlocks(),
		isLaunching: builder.isLaunching()
	};
}))(LayoutBlockEdit);

/***/ }),

/***/ 31:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings,
    urls = _FLBuilderConfig.urls;
var _wp$blocks = wp.blocks,
    rawHandler = _wp$blocks.rawHandler,
    serialize = _wp$blocks.serialize;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Placeholder = _wp$components.Placeholder,
    Spinner = _wp$components.Spinner;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    subscribe = _wp$data.subscribe,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var Component = wp.element.Component;

/**
 * Edit component for WordPress versions before 5.3.
 */

var LayoutBlockEditPre_5_3 = function (_Component) {
	_inherits(LayoutBlockEditPre_5_3, _Component);

	function LayoutBlockEditPre_5_3() {
		_classCallCheck(this, LayoutBlockEditPre_5_3);

		var _this = _possibleConstructorReturn(this, (LayoutBlockEditPre_5_3.__proto__ || Object.getPrototypeOf(LayoutBlockEditPre_5_3)).apply(this, arguments));

		_this.unsubscribe = subscribe(_this.storeDidUpdate.bind(_this));
		return _this;
	}

	_createClass(LayoutBlockEditPre_5_3, [{
		key: 'storeDidUpdate',
		value: function storeDidUpdate() {
			var _props = this.props,
			    isLaunching = _props.isLaunching,
			    isSavingPost = _props.isSavingPost;

			if (isLaunching && !isSavingPost) {
				this.unsubscribe();
				this.redirectToBuilder();
			}
		}
	}, {
		key: 'componentDidMount',
		value: function componentDidMount() {
			var blockCount = this.props.blockCount;

			if (1 === blockCount) {
				this.toggleEditor('disable');
			}
		}
	}, {
		key: 'componentWillUnmount',
		value: function componentWillUnmount() {
			this.unsubscribe();
			this.toggleEditor('enable');
		}
	}, {
		key: 'render',
		value: function render() {
			var _props2 = this.props,
			    blockCount = _props2.blockCount,
			    onReplace = _props2.onReplace,
			    isLaunching = _props2.isLaunching;

			var label = void 0,
			    callback = void 0,
			    description = void 0;

			if (1 === blockCount) {
				label = builder.access ? strings.launch : strings.view;
				callback = this.launchBuilder.bind(this);
			} else {
				label = strings.convert;
				callback = this.convertToBuilder.bind(this);
			}

			if (builder.enabled) {
				description = strings.active;
			} else {
				description = strings.description;
			}

			if (false === builder.showui) {
				return '';
			}

			return React.createElement(
				Placeholder,
				{
					key: 'placeholder',
					instructions: description,
					icon: 'welcome-widgets-menus',
					label: strings.title,
					className: 'fl-builder-layout-launch-view'
				},
				isLaunching && React.createElement(Spinner, null),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						isPrimary: true,
						type: 'submit',
						onClick: callback
					},
					label
				),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						type: 'submit',
						onClick: this.convertToBlocks.bind(this)
					},
					strings.editor
				)
			);
		}
	}, {
		key: 'toggleEditor',
		value: function toggleEditor() {
			var method = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'enable';
			var classList = document.body.classList;

			var enabledClass = 'fl-builder-layout-enabled';

			if ('enable' === method) {
				if (classList.contains(enabledClass)) {
					classList.remove(enabledClass);
				}
			} else {
				if (!classList.contains(enabledClass)) {
					classList.add(enabledClass);
				}
			}
		}
	}, {
		key: 'redirectToBuilder',
		value: function redirectToBuilder() {
			window.location.href = builder.access ? urls.edit : urls.view;
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props3 = this.props,
			    savePost = _props3.savePost,
			    setLaunching = _props3.setLaunching;

			setLaunching(true);
			savePost();
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props4 = this.props,
			    clientId = _props4.clientId,
			    blocks = _props4.blocks,
			    setAttributes = _props4.setAttributes,
			    removeBlocks = _props4.removeBlocks;

			var content = serialize(blocks);
			var clientIds = blocks.map(function (block) {
				return block.clientId;
			}).filter(function (id) {
				return id !== clientId;
			});

			setAttributes({ content: content.replace(/<!--(.*?)-->/g, '') });
			removeBlocks(clientIds);
			this.launchBuilder();
		}
	}, {
		key: 'convertToBlocks',
		value: function convertToBlocks() {
			var _props5 = this.props,
			    attributes = _props5.attributes,
			    clientId = _props5.clientId,
			    replaceBlocks = _props5.replaceBlocks,
			    onReplace = _props5.onReplace;


			if (attributes.content && !confirm(strings.warning)) {
				return;
			} else if (attributes.content) {
				replaceBlocks([clientId], rawHandler({
					HTML: attributes.content,
					mode: 'BLOCKS'
				}));
			} else {
				onReplace([]);
			}
		}
	}]);

	return LayoutBlockEditPre_5_3;
}(Component);

/**
 * Connect the edit component to editor data.
 */


var LayoutBlockEditConnectedPre_5_3 = exports.LayoutBlockEditConnectedPre_5_3 = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	var builder = dispatch('fl-builder');
	return {
		savePost: editor.savePost,
		removeBlocks: editor.removeBlocks,
		replaceBlocks: editor.replaceBlocks,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	var builder = select('fl-builder');
	return {
		blockCount: editor.getBlockCount(),
		blocks: editor.getBlocks(),
		isSavingPost: editor.isSavingPost(),
		isLaunching: builder.isLaunching()
	};
}))(LayoutBlockEditPre_5_3);

/***/ }),

/***/ 32:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _menuItem = __webpack_require__(34);

var _menuItemPre = __webpack_require__(35);

var version = FLBuilderConfig.wp.version;
var registerPlugin = wp.plugins.registerPlugin;


var getMenuItemComponent = function getMenuItemComponent() {
	if (parseFloat(version) < 5.3) {
		return _menuItemPre.BuilderMoreMenuItemConnectedPre_5_3;
	}
	return _menuItem.BuilderMoreMenuItemConnected;
};

/**
 * Register the builder more menu plugin.
 */
registerPlugin('fl-builder-plugin-sidebar', {
	icon: 'welcome-widgets-menus',
	render: getMenuItemComponent()
});

/***/ }),

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings,
    urls = _FLBuilderConfig.urls;
var _wp$blocks = wp.blocks,
    createBlock = _wp$blocks.createBlock,
    serialize = _wp$blocks.serialize;
var Button = wp.components.Button;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
var Component = wp.element.Component;

/**
 * Builder menu item for the more menu.
 */

var BuilderMoreMenuItem = function (_Component) {
	_inherits(BuilderMoreMenuItem, _Component);

	function BuilderMoreMenuItem() {
		_classCallCheck(this, BuilderMoreMenuItem);

		return _possibleConstructorReturn(this, (BuilderMoreMenuItem.__proto__ || Object.getPrototypeOf(BuilderMoreMenuItem)).apply(this, arguments));
	}

	_createClass(BuilderMoreMenuItem, [{
		key: 'render',
		value: function render() {
			return React.createElement(
				PluginMoreMenuItem,
				{ onClick: this.menuItemClicked.bind(this) },
				this.hasBuilderBlock() ? strings.launch : strings.convert
			);
		}
	}, {
		key: 'hasBuilderBlock',
		value: function hasBuilderBlock() {
			var blocks = this.props.blocks;

			var builder = blocks.filter(function (block) {
				return 'fl-builder/layout' === block.name;
			});

			return !!builder.length;
		}
	}, {
		key: 'menuItemClicked',
		value: function menuItemClicked() {
			if (this.hasBuilderBlock()) {
				this.launchBuilder();
			} else {
				this.convertToBuilder();
			}
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props = this.props,
			    blocks = _props.blocks,
			    insertBlock = _props.insertBlock,
			    removeBlocks = _props.removeBlocks;

			var clientIds = blocks.map(function (block) {
				return block.clientId;
			});
			var content = serialize(blocks).replace(/<!--(.*?)-->/g, '');
			var block = createBlock('fl-builder/layout', { content: content });

			insertBlock(block, 0);
			removeBlocks(clientIds);
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props2 = this.props,
			    savePost = _props2.savePost,
			    setLaunching = _props2.setLaunching;

			setLaunching(true);
			savePost().then(function () {
				setTimeout(function () {
					window.location.href = builder.access ? urls.edit : urls.view;
				}, 2000);
			});
		}
	}]);

	return BuilderMoreMenuItem;
}(Component);

/**
 * Connect the menu item to editor data.
 */


var BuilderMoreMenuItemConnected = exports.BuilderMoreMenuItemConnected = compose(withDispatch(function (dispatch, ownProps) {
	var blockEditor = dispatch('core/block-editor');
	var editor = dispatch('core/editor');
	var builder = dispatch('fl-builder');
	return {
		insertBlock: blockEditor.insertBlock,
		removeBlocks: blockEditor.removeBlocks,
		savePost: editor.savePost,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var blockEditor = select('core/block-editor');
	return {
		blocks: blockEditor.getBlocks()
	};
}))(BuilderMoreMenuItem);

/***/ }),

/***/ 35:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.BuilderMoreMenuItemConnectedPre_5_3 = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(36);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    strings = _FLBuilderConfig.strings;
var _wp$blocks = wp.blocks,
    createBlock = _wp$blocks.createBlock,
    serialize = _wp$blocks.serialize;
var Button = wp.components.Button;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
var Component = wp.element.Component;

/**
 * Builder menu item for the more menu pre WordPress 5.3.
 *
 * More menu items currently only support opening a sidebar.
 * However, we need a click event. For now, that is done in a
 * hacky manner with an absolute div that contains the event.
 * This should be reworked in the future when API supports it.
 */

var BuilderMoreMenuItemPre_5_3 = function (_Component) {
	_inherits(BuilderMoreMenuItemPre_5_3, _Component);

	function BuilderMoreMenuItemPre_5_3() {
		_classCallCheck(this, BuilderMoreMenuItemPre_5_3);

		return _possibleConstructorReturn(this, (BuilderMoreMenuItemPre_5_3.__proto__ || Object.getPrototypeOf(BuilderMoreMenuItemPre_5_3)).apply(this, arguments));
	}

	_createClass(BuilderMoreMenuItemPre_5_3, [{
		key: 'render',
		value: function render() {
			return React.createElement(
				PluginSidebarMoreMenuItem,
				null,
				React.createElement('div', {
					className: 'fl-builder-plugin-sidebar-button',
					onClick: this.menuItemClicked.bind(this) }),
				this.hasBuilderBlock() ? strings.launch : strings.convert
			);
		}
	}, {
		key: 'hasBuilderBlock',
		value: function hasBuilderBlock() {
			var blocks = this.props.blocks;

			var builder = blocks.filter(function (block) {
				return 'fl-builder/layout' === block.name;
			});

			return !!builder.length;
		}
	}, {
		key: 'menuItemClicked',
		value: function menuItemClicked() {
			var closeGeneralSidebar = this.props.closeGeneralSidebar;


			if (this.hasBuilderBlock()) {
				this.launchBuilder();
			} else {
				this.convertToBuilder();
			}

			// Another hack because we can't have click events yet :(
			setTimeout(closeGeneralSidebar, 100);
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props = this.props,
			    blocks = _props.blocks,
			    insertBlock = _props.insertBlock,
			    removeBlocks = _props.removeBlocks;

			var clientIds = blocks.map(function (block) {
				return block.clientId;
			});
			var content = serialize(blocks).replace(/<!--(.*?)-->/g, '');
			var block = createBlock('fl-builder/layout', { content: content });

			insertBlock(block, 0);
			removeBlocks(clientIds);
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props2 = this.props,
			    savePost = _props2.savePost,
			    setLaunching = _props2.setLaunching;

			setLaunching(true);
			savePost();
		}
	}]);

	return BuilderMoreMenuItemPre_5_3;
}(Component);

/**
 * Connect the menu item to editor data.
 */


var BuilderMoreMenuItemConnectedPre_5_3 = exports.BuilderMoreMenuItemConnectedPre_5_3 = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	var editPost = dispatch('core/edit-post');
	var builder = dispatch('fl-builder');
	return {
		savePost: editor.savePost,
		insertBlock: editor.insertBlock,
		removeBlocks: editor.removeBlocks,
		closeGeneralSidebar: editPost.closeGeneralSidebar,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	return {
		blocks: editor.getBlocks()
	};
}))(BuilderMoreMenuItemPre_5_3);

/***/ }),

/***/ 36:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });