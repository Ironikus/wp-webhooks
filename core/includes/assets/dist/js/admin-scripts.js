/*!
  * Bootstrap v4.5.0 (https://getbootstrap.com/)
  * Copyright 2011-2020 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
  */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery'), require('popper.js')) :
  typeof define === 'function' && define.amd ? define(['exports', 'jquery', 'popper.js'], factory) :
  (global = global || self, factory(global.bootstrap = {}, global.jQuery, global.Popper));
}(this, (function (exports, $, Popper) { 'use strict';

  $ = $ && Object.prototype.hasOwnProperty.call($, 'default') ? $['default'] : $;
  Popper = Popper && Object.prototype.hasOwnProperty.call(Popper, 'default') ? Popper['default'] : Popper;

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

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);
      if (enumerableOnly) symbols = symbols.filter(function (sym) {
        return Object.getOwnPropertyDescriptor(object, sym).enumerable;
      });
      keys.push.apply(keys, symbols);
    }

    return keys;
  }

  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};

      if (i % 2) {
        ownKeys(Object(source), true).forEach(function (key) {
          _defineProperty(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys(Object(source)).forEach(function (key) {
          Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
        });
      }
    }

    return target;
  }

  function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    subClass.__proto__ = superClass;
  }

  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v4.5.0): util.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * --------------------------------------------------------------------------
   */
  /**
   * ------------------------------------------------------------------------
   * Private TransitionEnd Helpers
   * ------------------------------------------------------------------------
   */

  var TRANSITION_END = 'transitionend';
  var MAX_UID = 1000000;
  var MILLISECONDS_MULTIPLIER = 1000; // Shoutout AngusCroll (https://goo.gl/pxwQGp)

  function toType(obj) {
    if (obj === null || typeof obj === 'undefined') {
      return "" + obj;
    }

    return {}.toString.call(obj).match(/\s([a-z]+)/i)[1].toLowerCase();
  }

  function getSpecialTransitionEndEvent() {
    return {
      bindType: TRANSITION_END,
      delegateType: TRANSITION_END,
      handle: function handle(event) {
        if ($(event.target).is(this)) {
          return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
        }

        return undefined;
      }
    };
  }

  function transitionEndEmulator(duration) {
    var _this = this;

    var called = false;
    $(this).one(Util.TRANSITION_END, function () {
      called = true;
    });
    setTimeout(function () {
      if (!called) {
        Util.triggerTransitionEnd(_this);
      }
    }, duration);
    return this;
  }

  function setTransitionEndSupport() {
    $.fn.emulateTransitionEnd = transitionEndEmulator;
    $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
  }
  /**
   * --------------------------------------------------------------------------
   * Public Util Api
   * --------------------------------------------------------------------------
   */


  var Util = {
    TRANSITION_END: 'bsTransitionEnd',
    getUID: function getUID(prefix) {
      do {
        // eslint-disable-next-line no-bitwise
        prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
      } while (document.getElementById(prefix));

      return prefix;
    },
    getSelectorFromElement: function getSelectorFromElement(element) {
      var selector = element.getAttribute('data-target');

      if (!selector || selector === '#') {
        var hrefAttr = element.getAttribute('href');
        selector = hrefAttr && hrefAttr !== '#' ? hrefAttr.trim() : '';
      }

      try {
        return document.querySelector(selector) ? selector : null;
      } catch (err) {
        return null;
      }
    },
    getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
      if (!element) {
        return 0;
      } // Get transition-duration of the element


      var transitionDuration = $(element).css('transition-duration');
      var transitionDelay = $(element).css('transition-delay');
      var floatTransitionDuration = parseFloat(transitionDuration);
      var floatTransitionDelay = parseFloat(transitionDelay); // Return 0 if element or transition duration is not found

      if (!floatTransitionDuration && !floatTransitionDelay) {
        return 0;
      } // If multiple durations are defined, take the first


      transitionDuration = transitionDuration.split(',')[0];
      transitionDelay = transitionDelay.split(',')[0];
      return (parseFloat(transitionDuration) + parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
    },
    reflow: function reflow(element) {
      return element.offsetHeight;
    },
    triggerTransitionEnd: function triggerTransitionEnd(element) {
      $(element).trigger(TRANSITION_END);
    },
    // TODO: Remove in v5
    supportsTransitionEnd: function supportsTransitionEnd() {
      return Boolean(TRANSITION_END);
    },
    isElement: function isElement(obj) {
      return (obj[0] || obj).nodeType;
    },
    typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
      for (var property in configTypes) {
        if (Object.prototype.hasOwnProperty.call(configTypes, property)) {
          var expectedTypes = configTypes[property];
          var value = config[property];
          var valueType = value && Util.isElement(value) ? 'element' : toType(value);

          if (!new RegExp(expectedTypes).test(valueType)) {
            throw new Error(componentName.toUpperCase() + ": " + ("Option \"" + property + "\" provided type \"" + valueType + "\" ") + ("but expected type \"" + expectedTypes + "\"."));
          }
        }
      }
    },
    findShadowRoot: function findShadowRoot(element) {
      if (!document.documentElement.attachShadow) {
        return null;
      } // Can find the shadow root otherwise it'll return the document


      if (typeof element.getRootNode === 'function') {
        var root = element.getRootNode();
        return root instanceof ShadowRoot ? root : null;
      }

      if (element instanceof ShadowRoot) {
        return element;
      } // when we don't find a shadow root


      if (!element.parentNode) {
        return null;
      }

      return Util.findShadowRoot(element.parentNode);
    },
    jQueryDetection: function jQueryDetection() {
      if (typeof $ === 'undefined') {
        throw new TypeError('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.');
      }

      var version = $.fn.jquery.split(' ')[0].split('.');
      var minMajor = 1;
      var ltMajor = 2;
      var minMinor = 9;
      var minPatch = 1;
      var maxMajor = 4;

      if (version[0] < ltMajor && version[1] < minMinor || version[0] === minMajor && version[1] === minMinor && version[2] < minPatch || version[0] >= maxMajor) {
        throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0');
      }
    }
  };
  Util.jQueryDetection();
  setTransitionEndSupport();

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME = 'alert';
  var VERSION = '4.5.0';
  var DATA_KEY = 'bs.alert';
  var EVENT_KEY = "." + DATA_KEY;
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = $.fn[NAME];
  var SELECTOR_DISMISS = '[data-dismiss="alert"]';
  var EVENT_CLOSE = "close" + EVENT_KEY;
  var EVENT_CLOSED = "closed" + EVENT_KEY;
  var EVENT_CLICK_DATA_API = "click" + EVENT_KEY + DATA_API_KEY;
  var CLASS_NAME_ALERT = 'alert';
  var CLASS_NAME_FADE = 'fade';
  var CLASS_NAME_SHOW = 'show';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Alert = /*#__PURE__*/function () {
    function Alert(element) {
      this._element = element;
    } // Getters


    var _proto = Alert.prototype;

    // Public
    _proto.close = function close(element) {
      var rootElement = this._element;

      if (element) {
        rootElement = this._getRootElement(element);
      }

      var customEvent = this._triggerCloseEvent(rootElement);

      if (customEvent.isDefaultPrevented()) {
        return;
      }

      this._removeElement(rootElement);
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY);
      this._element = null;
    } // Private
    ;

    _proto._getRootElement = function _getRootElement(element) {
      var selector = Util.getSelectorFromElement(element);
      var parent = false;

      if (selector) {
        parent = document.querySelector(selector);
      }

      if (!parent) {
        parent = $(element).closest("." + CLASS_NAME_ALERT)[0];
      }

      return parent;
    };

    _proto._triggerCloseEvent = function _triggerCloseEvent(element) {
      var closeEvent = $.Event(EVENT_CLOSE);
      $(element).trigger(closeEvent);
      return closeEvent;
    };

    _proto._removeElement = function _removeElement(element) {
      var _this = this;

      $(element).removeClass(CLASS_NAME_SHOW);

      if (!$(element).hasClass(CLASS_NAME_FADE)) {
        this._destroyElement(element);

        return;
      }

      var transitionDuration = Util.getTransitionDurationFromElement(element);
      $(element).one(Util.TRANSITION_END, function (event) {
        return _this._destroyElement(element, event);
      }).emulateTransitionEnd(transitionDuration);
    };

    _proto._destroyElement = function _destroyElement(element) {
      $(element).detach().trigger(EVENT_CLOSED).remove();
    } // Static
    ;

    Alert._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $element = $(this);
        var data = $element.data(DATA_KEY);

        if (!data) {
          data = new Alert(this);
          $element.data(DATA_KEY, data);
        }

        if (config === 'close') {
          data[config](this);
        }
      });
    };

    Alert._handleDismiss = function _handleDismiss(alertInstance) {
      return function (event) {
        if (event) {
          event.preventDefault();
        }

        alertInstance.close(this);
      };
    };

    _createClass(Alert, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION;
      }
    }]);

    return Alert;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API, SELECTOR_DISMISS, Alert._handleDismiss(new Alert()));
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME] = Alert._jQueryInterface;
  $.fn[NAME].Constructor = Alert;

  $.fn[NAME].noConflict = function () {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Alert._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$1 = 'button';
  var VERSION$1 = '4.5.0';
  var DATA_KEY$1 = 'bs.button';
  var EVENT_KEY$1 = "." + DATA_KEY$1;
  var DATA_API_KEY$1 = '.data-api';
  var JQUERY_NO_CONFLICT$1 = $.fn[NAME$1];
  var CLASS_NAME_ACTIVE = 'active';
  var CLASS_NAME_BUTTON = 'btn';
  var CLASS_NAME_FOCUS = 'focus';
  var SELECTOR_DATA_TOGGLE_CARROT = '[data-toggle^="button"]';
  var SELECTOR_DATA_TOGGLES = '[data-toggle="buttons"]';
  var SELECTOR_DATA_TOGGLE = '[data-toggle="button"]';
  var SELECTOR_DATA_TOGGLES_BUTTONS = '[data-toggle="buttons"] .btn';
  var SELECTOR_INPUT = 'input:not([type="hidden"])';
  var SELECTOR_ACTIVE = '.active';
  var SELECTOR_BUTTON = '.btn';
  var EVENT_CLICK_DATA_API$1 = "click" + EVENT_KEY$1 + DATA_API_KEY$1;
  var EVENT_FOCUS_BLUR_DATA_API = "focus" + EVENT_KEY$1 + DATA_API_KEY$1 + " " + ("blur" + EVENT_KEY$1 + DATA_API_KEY$1);
  var EVENT_LOAD_DATA_API = "load" + EVENT_KEY$1 + DATA_API_KEY$1;
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Button = /*#__PURE__*/function () {
    function Button(element) {
      this._element = element;
    } // Getters


    var _proto = Button.prototype;

    // Public
    _proto.toggle = function toggle() {
      var triggerChangeEvent = true;
      var addAriaPressed = true;
      var rootElement = $(this._element).closest(SELECTOR_DATA_TOGGLES)[0];

      if (rootElement) {
        var input = this._element.querySelector(SELECTOR_INPUT);

        if (input) {
          if (input.type === 'radio') {
            if (input.checked && this._element.classList.contains(CLASS_NAME_ACTIVE)) {
              triggerChangeEvent = false;
            } else {
              var activeElement = rootElement.querySelector(SELECTOR_ACTIVE);

              if (activeElement) {
                $(activeElement).removeClass(CLASS_NAME_ACTIVE);
              }
            }
          }

          if (triggerChangeEvent) {
            // if it's not a radio button or checkbox don't add a pointless/invalid checked property to the input
            if (input.type === 'checkbox' || input.type === 'radio') {
              input.checked = !this._element.classList.contains(CLASS_NAME_ACTIVE);
            }

            $(input).trigger('change');
          }

          input.focus();
          addAriaPressed = false;
        }
      }

      if (!(this._element.hasAttribute('disabled') || this._element.classList.contains('disabled'))) {
        if (addAriaPressed) {
          this._element.setAttribute('aria-pressed', !this._element.classList.contains(CLASS_NAME_ACTIVE));
        }

        if (triggerChangeEvent) {
          $(this._element).toggleClass(CLASS_NAME_ACTIVE);
        }
      }
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$1);
      this._element = null;
    } // Static
    ;

    Button._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$1);

        if (!data) {
          data = new Button(this);
          $(this).data(DATA_KEY$1, data);
        }

        if (config === 'toggle') {
          data[config]();
        }
      });
    };

    _createClass(Button, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$1;
      }
    }]);

    return Button;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$1, SELECTOR_DATA_TOGGLE_CARROT, function (event) {
    var button = event.target;
    var initialButton = button;

    if (!$(button).hasClass(CLASS_NAME_BUTTON)) {
      button = $(button).closest(SELECTOR_BUTTON)[0];
    }

    if (!button || button.hasAttribute('disabled') || button.classList.contains('disabled')) {
      event.preventDefault(); // work around Firefox bug #1540995
    } else {
      var inputBtn = button.querySelector(SELECTOR_INPUT);

      if (inputBtn && (inputBtn.hasAttribute('disabled') || inputBtn.classList.contains('disabled'))) {
        event.preventDefault(); // work around Firefox bug #1540995

        return;
      }

      if (initialButton.tagName === 'LABEL' && inputBtn && inputBtn.type === 'checkbox') {
        event.preventDefault(); // work around event sent to label and input
      }

      Button._jQueryInterface.call($(button), 'toggle');
    }
  }).on(EVENT_FOCUS_BLUR_DATA_API, SELECTOR_DATA_TOGGLE_CARROT, function (event) {
    var button = $(event.target).closest(SELECTOR_BUTTON)[0];
    $(button).toggleClass(CLASS_NAME_FOCUS, /^focus(in)?$/.test(event.type));
  });
  $(window).on(EVENT_LOAD_DATA_API, function () {
    // ensure correct active class is set to match the controls' actual values/states
    // find all checkboxes/readio buttons inside data-toggle groups
    var buttons = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLES_BUTTONS));

    for (var i = 0, len = buttons.length; i < len; i++) {
      var button = buttons[i];
      var input = button.querySelector(SELECTOR_INPUT);

      if (input.checked || input.hasAttribute('checked')) {
        button.classList.add(CLASS_NAME_ACTIVE);
      } else {
        button.classList.remove(CLASS_NAME_ACTIVE);
      }
    } // find all button toggles


    buttons = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE));

    for (var _i = 0, _len = buttons.length; _i < _len; _i++) {
      var _button = buttons[_i];

      if (_button.getAttribute('aria-pressed') === 'true') {
        _button.classList.add(CLASS_NAME_ACTIVE);
      } else {
        _button.classList.remove(CLASS_NAME_ACTIVE);
      }
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$1] = Button._jQueryInterface;
  $.fn[NAME$1].Constructor = Button;

  $.fn[NAME$1].noConflict = function () {
    $.fn[NAME$1] = JQUERY_NO_CONFLICT$1;
    return Button._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$2 = 'carousel';
  var VERSION$2 = '4.5.0';
  var DATA_KEY$2 = 'bs.carousel';
  var EVENT_KEY$2 = "." + DATA_KEY$2;
  var DATA_API_KEY$2 = '.data-api';
  var JQUERY_NO_CONFLICT$2 = $.fn[NAME$2];
  var ARROW_LEFT_KEYCODE = 37; // KeyboardEvent.which value for left arrow key

  var ARROW_RIGHT_KEYCODE = 39; // KeyboardEvent.which value for right arrow key

  var TOUCHEVENT_COMPAT_WAIT = 500; // Time for mouse compat events to fire after touch

  var SWIPE_THRESHOLD = 40;
  var Default = {
    interval: 5000,
    keyboard: true,
    slide: false,
    pause: 'hover',
    wrap: true,
    touch: true
  };
  var DefaultType = {
    interval: '(number|boolean)',
    keyboard: 'boolean',
    slide: '(boolean|string)',
    pause: '(string|boolean)',
    wrap: 'boolean',
    touch: 'boolean'
  };
  var DIRECTION_NEXT = 'next';
  var DIRECTION_PREV = 'prev';
  var DIRECTION_LEFT = 'left';
  var DIRECTION_RIGHT = 'right';
  var EVENT_SLIDE = "slide" + EVENT_KEY$2;
  var EVENT_SLID = "slid" + EVENT_KEY$2;
  var EVENT_KEYDOWN = "keydown" + EVENT_KEY$2;
  var EVENT_MOUSEENTER = "mouseenter" + EVENT_KEY$2;
  var EVENT_MOUSELEAVE = "mouseleave" + EVENT_KEY$2;
  var EVENT_TOUCHSTART = "touchstart" + EVENT_KEY$2;
  var EVENT_TOUCHMOVE = "touchmove" + EVENT_KEY$2;
  var EVENT_TOUCHEND = "touchend" + EVENT_KEY$2;
  var EVENT_POINTERDOWN = "pointerdown" + EVENT_KEY$2;
  var EVENT_POINTERUP = "pointerup" + EVENT_KEY$2;
  var EVENT_DRAG_START = "dragstart" + EVENT_KEY$2;
  var EVENT_LOAD_DATA_API$1 = "load" + EVENT_KEY$2 + DATA_API_KEY$2;
  var EVENT_CLICK_DATA_API$2 = "click" + EVENT_KEY$2 + DATA_API_KEY$2;
  var CLASS_NAME_CAROUSEL = 'carousel';
  var CLASS_NAME_ACTIVE$1 = 'active';
  var CLASS_NAME_SLIDE = 'slide';
  var CLASS_NAME_RIGHT = 'carousel-item-right';
  var CLASS_NAME_LEFT = 'carousel-item-left';
  var CLASS_NAME_NEXT = 'carousel-item-next';
  var CLASS_NAME_PREV = 'carousel-item-prev';
  var CLASS_NAME_POINTER_EVENT = 'pointer-event';
  var SELECTOR_ACTIVE$1 = '.active';
  var SELECTOR_ACTIVE_ITEM = '.active.carousel-item';
  var SELECTOR_ITEM = '.carousel-item';
  var SELECTOR_ITEM_IMG = '.carousel-item img';
  var SELECTOR_NEXT_PREV = '.carousel-item-next, .carousel-item-prev';
  var SELECTOR_INDICATORS = '.carousel-indicators';
  var SELECTOR_DATA_SLIDE = '[data-slide], [data-slide-to]';
  var SELECTOR_DATA_RIDE = '[data-ride="carousel"]';
  var PointerType = {
    TOUCH: 'touch',
    PEN: 'pen'
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Carousel = /*#__PURE__*/function () {
    function Carousel(element, config) {
      this._items = null;
      this._interval = null;
      this._activeElement = null;
      this._isPaused = false;
      this._isSliding = false;
      this.touchTimeout = null;
      this.touchStartX = 0;
      this.touchDeltaX = 0;
      this._config = this._getConfig(config);
      this._element = element;
      this._indicatorsElement = this._element.querySelector(SELECTOR_INDICATORS);
      this._touchSupported = 'ontouchstart' in document.documentElement || navigator.maxTouchPoints > 0;
      this._pointerEvent = Boolean(window.PointerEvent || window.MSPointerEvent);

      this._addEventListeners();
    } // Getters


    var _proto = Carousel.prototype;

    // Public
    _proto.next = function next() {
      if (!this._isSliding) {
        this._slide(DIRECTION_NEXT);
      }
    };

    _proto.nextWhenVisible = function nextWhenVisible() {
      // Don't call next when the page isn't visible
      // or the carousel or its parent isn't visible
      if (!document.hidden && $(this._element).is(':visible') && $(this._element).css('visibility') !== 'hidden') {
        this.next();
      }
    };

    _proto.prev = function prev() {
      if (!this._isSliding) {
        this._slide(DIRECTION_PREV);
      }
    };

    _proto.pause = function pause(event) {
      if (!event) {
        this._isPaused = true;
      }

      if (this._element.querySelector(SELECTOR_NEXT_PREV)) {
        Util.triggerTransitionEnd(this._element);
        this.cycle(true);
      }

      clearInterval(this._interval);
      this._interval = null;
    };

    _proto.cycle = function cycle(event) {
      if (!event) {
        this._isPaused = false;
      }

      if (this._interval) {
        clearInterval(this._interval);
        this._interval = null;
      }

      if (this._config.interval && !this._isPaused) {
        this._interval = setInterval((document.visibilityState ? this.nextWhenVisible : this.next).bind(this), this._config.interval);
      }
    };

    _proto.to = function to(index) {
      var _this = this;

      this._activeElement = this._element.querySelector(SELECTOR_ACTIVE_ITEM);

      var activeIndex = this._getItemIndex(this._activeElement);

      if (index > this._items.length - 1 || index < 0) {
        return;
      }

      if (this._isSliding) {
        $(this._element).one(EVENT_SLID, function () {
          return _this.to(index);
        });
        return;
      }

      if (activeIndex === index) {
        this.pause();
        this.cycle();
        return;
      }

      var direction = index > activeIndex ? DIRECTION_NEXT : DIRECTION_PREV;

      this._slide(direction, this._items[index]);
    };

    _proto.dispose = function dispose() {
      $(this._element).off(EVENT_KEY$2);
      $.removeData(this._element, DATA_KEY$2);
      this._items = null;
      this._config = null;
      this._element = null;
      this._interval = null;
      this._isPaused = null;
      this._isSliding = null;
      this._activeElement = null;
      this._indicatorsElement = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2({}, Default), config);
      Util.typeCheckConfig(NAME$2, config, DefaultType);
      return config;
    };

    _proto._handleSwipe = function _handleSwipe() {
      var absDeltax = Math.abs(this.touchDeltaX);

      if (absDeltax <= SWIPE_THRESHOLD) {
        return;
      }

      var direction = absDeltax / this.touchDeltaX;
      this.touchDeltaX = 0; // swipe left

      if (direction > 0) {
        this.prev();
      } // swipe right


      if (direction < 0) {
        this.next();
      }
    };

    _proto._addEventListeners = function _addEventListeners() {
      var _this2 = this;

      if (this._config.keyboard) {
        $(this._element).on(EVENT_KEYDOWN, function (event) {
          return _this2._keydown(event);
        });
      }

      if (this._config.pause === 'hover') {
        $(this._element).on(EVENT_MOUSEENTER, function (event) {
          return _this2.pause(event);
        }).on(EVENT_MOUSELEAVE, function (event) {
          return _this2.cycle(event);
        });
      }

      if (this._config.touch) {
        this._addTouchEventListeners();
      }
    };

    _proto._addTouchEventListeners = function _addTouchEventListeners() {
      var _this3 = this;

      if (!this._touchSupported) {
        return;
      }

      var start = function start(event) {
        if (_this3._pointerEvent && PointerType[event.originalEvent.pointerType.toUpperCase()]) {
          _this3.touchStartX = event.originalEvent.clientX;
        } else if (!_this3._pointerEvent) {
          _this3.touchStartX = event.originalEvent.touches[0].clientX;
        }
      };

      var move = function move(event) {
        // ensure swiping with one touch and not pinching
        if (event.originalEvent.touches && event.originalEvent.touches.length > 1) {
          _this3.touchDeltaX = 0;
        } else {
          _this3.touchDeltaX = event.originalEvent.touches[0].clientX - _this3.touchStartX;
        }
      };

      var end = function end(event) {
        if (_this3._pointerEvent && PointerType[event.originalEvent.pointerType.toUpperCase()]) {
          _this3.touchDeltaX = event.originalEvent.clientX - _this3.touchStartX;
        }

        _this3._handleSwipe();

        if (_this3._config.pause === 'hover') {
          // If it's a touch-enabled device, mouseenter/leave are fired as
          // part of the mouse compatibility events on first tap - the carousel
          // would stop cycling until user tapped out of it;
          // here, we listen for touchend, explicitly pause the carousel
          // (as if it's the second time we tap on it, mouseenter compat event
          // is NOT fired) and after a timeout (to allow for mouse compatibility
          // events to fire) we explicitly restart cycling
          _this3.pause();

          if (_this3.touchTimeout) {
            clearTimeout(_this3.touchTimeout);
          }

          _this3.touchTimeout = setTimeout(function (event) {
            return _this3.cycle(event);
          }, TOUCHEVENT_COMPAT_WAIT + _this3._config.interval);
        }
      };

      $(this._element.querySelectorAll(SELECTOR_ITEM_IMG)).on(EVENT_DRAG_START, function (e) {
        return e.preventDefault();
      });

      if (this._pointerEvent) {
        $(this._element).on(EVENT_POINTERDOWN, function (event) {
          return start(event);
        });
        $(this._element).on(EVENT_POINTERUP, function (event) {
          return end(event);
        });

        this._element.classList.add(CLASS_NAME_POINTER_EVENT);
      } else {
        $(this._element).on(EVENT_TOUCHSTART, function (event) {
          return start(event);
        });
        $(this._element).on(EVENT_TOUCHMOVE, function (event) {
          return move(event);
        });
        $(this._element).on(EVENT_TOUCHEND, function (event) {
          return end(event);
        });
      }
    };

    _proto._keydown = function _keydown(event) {
      if (/input|textarea/i.test(event.target.tagName)) {
        return;
      }

      switch (event.which) {
        case ARROW_LEFT_KEYCODE:
          event.preventDefault();
          this.prev();
          break;

        case ARROW_RIGHT_KEYCODE:
          event.preventDefault();
          this.next();
          break;
      }
    };

    _proto._getItemIndex = function _getItemIndex(element) {
      this._items = element && element.parentNode ? [].slice.call(element.parentNode.querySelectorAll(SELECTOR_ITEM)) : [];
      return this._items.indexOf(element);
    };

    _proto._getItemByDirection = function _getItemByDirection(direction, activeElement) {
      var isNextDirection = direction === DIRECTION_NEXT;
      var isPrevDirection = direction === DIRECTION_PREV;

      var activeIndex = this._getItemIndex(activeElement);

      var lastItemIndex = this._items.length - 1;
      var isGoingToWrap = isPrevDirection && activeIndex === 0 || isNextDirection && activeIndex === lastItemIndex;

      if (isGoingToWrap && !this._config.wrap) {
        return activeElement;
      }

      var delta = direction === DIRECTION_PREV ? -1 : 1;
      var itemIndex = (activeIndex + delta) % this._items.length;
      return itemIndex === -1 ? this._items[this._items.length - 1] : this._items[itemIndex];
    };

    _proto._triggerSlideEvent = function _triggerSlideEvent(relatedTarget, eventDirectionName) {
      var targetIndex = this._getItemIndex(relatedTarget);

      var fromIndex = this._getItemIndex(this._element.querySelector(SELECTOR_ACTIVE_ITEM));

      var slideEvent = $.Event(EVENT_SLIDE, {
        relatedTarget: relatedTarget,
        direction: eventDirectionName,
        from: fromIndex,
        to: targetIndex
      });
      $(this._element).trigger(slideEvent);
      return slideEvent;
    };

    _proto._setActiveIndicatorElement = function _setActiveIndicatorElement(element) {
      if (this._indicatorsElement) {
        var indicators = [].slice.call(this._indicatorsElement.querySelectorAll(SELECTOR_ACTIVE$1));
        $(indicators).removeClass(CLASS_NAME_ACTIVE$1);

        var nextIndicator = this._indicatorsElement.children[this._getItemIndex(element)];

        if (nextIndicator) {
          $(nextIndicator).addClass(CLASS_NAME_ACTIVE$1);
        }
      }
    };

    _proto._slide = function _slide(direction, element) {
      var _this4 = this;

      var activeElement = this._element.querySelector(SELECTOR_ACTIVE_ITEM);

      var activeElementIndex = this._getItemIndex(activeElement);

      var nextElement = element || activeElement && this._getItemByDirection(direction, activeElement);

      var nextElementIndex = this._getItemIndex(nextElement);

      var isCycling = Boolean(this._interval);
      var directionalClassName;
      var orderClassName;
      var eventDirectionName;

      if (direction === DIRECTION_NEXT) {
        directionalClassName = CLASS_NAME_LEFT;
        orderClassName = CLASS_NAME_NEXT;
        eventDirectionName = DIRECTION_LEFT;
      } else {
        directionalClassName = CLASS_NAME_RIGHT;
        orderClassName = CLASS_NAME_PREV;
        eventDirectionName = DIRECTION_RIGHT;
      }

      if (nextElement && $(nextElement).hasClass(CLASS_NAME_ACTIVE$1)) {
        this._isSliding = false;
        return;
      }

      var slideEvent = this._triggerSlideEvent(nextElement, eventDirectionName);

      if (slideEvent.isDefaultPrevented()) {
        return;
      }

      if (!activeElement || !nextElement) {
        // Some weirdness is happening, so we bail
        return;
      }

      this._isSliding = true;

      if (isCycling) {
        this.pause();
      }

      this._setActiveIndicatorElement(nextElement);

      var slidEvent = $.Event(EVENT_SLID, {
        relatedTarget: nextElement,
        direction: eventDirectionName,
        from: activeElementIndex,
        to: nextElementIndex
      });

      if ($(this._element).hasClass(CLASS_NAME_SLIDE)) {
        $(nextElement).addClass(orderClassName);
        Util.reflow(nextElement);
        $(activeElement).addClass(directionalClassName);
        $(nextElement).addClass(directionalClassName);
        var nextElementInterval = parseInt(nextElement.getAttribute('data-interval'), 10);

        if (nextElementInterval) {
          this._config.defaultInterval = this._config.defaultInterval || this._config.interval;
          this._config.interval = nextElementInterval;
        } else {
          this._config.interval = this._config.defaultInterval || this._config.interval;
        }

        var transitionDuration = Util.getTransitionDurationFromElement(activeElement);
        $(activeElement).one(Util.TRANSITION_END, function () {
          $(nextElement).removeClass(directionalClassName + " " + orderClassName).addClass(CLASS_NAME_ACTIVE$1);
          $(activeElement).removeClass(CLASS_NAME_ACTIVE$1 + " " + orderClassName + " " + directionalClassName);
          _this4._isSliding = false;
          setTimeout(function () {
            return $(_this4._element).trigger(slidEvent);
          }, 0);
        }).emulateTransitionEnd(transitionDuration);
      } else {
        $(activeElement).removeClass(CLASS_NAME_ACTIVE$1);
        $(nextElement).addClass(CLASS_NAME_ACTIVE$1);
        this._isSliding = false;
        $(this._element).trigger(slidEvent);
      }

      if (isCycling) {
        this.cycle();
      }
    } // Static
    ;

    Carousel._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$2);

        var _config = _objectSpread2(_objectSpread2({}, Default), $(this).data());

        if (typeof config === 'object') {
          _config = _objectSpread2(_objectSpread2({}, _config), config);
        }

        var action = typeof config === 'string' ? config : _config.slide;

        if (!data) {
          data = new Carousel(this, _config);
          $(this).data(DATA_KEY$2, data);
        }

        if (typeof config === 'number') {
          data.to(config);
        } else if (typeof action === 'string') {
          if (typeof data[action] === 'undefined') {
            throw new TypeError("No method named \"" + action + "\"");
          }

          data[action]();
        } else if (_config.interval && _config.ride) {
          data.pause();
          data.cycle();
        }
      });
    };

    Carousel._dataApiClickHandler = function _dataApiClickHandler(event) {
      var selector = Util.getSelectorFromElement(this);

      if (!selector) {
        return;
      }

      var target = $(selector)[0];

      if (!target || !$(target).hasClass(CLASS_NAME_CAROUSEL)) {
        return;
      }

      var config = _objectSpread2(_objectSpread2({}, $(target).data()), $(this).data());

      var slideIndex = this.getAttribute('data-slide-to');

      if (slideIndex) {
        config.interval = false;
      }

      Carousel._jQueryInterface.call($(target), config);

      if (slideIndex) {
        $(target).data(DATA_KEY$2).to(slideIndex);
      }

      event.preventDefault();
    };

    _createClass(Carousel, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$2;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default;
      }
    }]);

    return Carousel;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$2, SELECTOR_DATA_SLIDE, Carousel._dataApiClickHandler);
  $(window).on(EVENT_LOAD_DATA_API$1, function () {
    var carousels = [].slice.call(document.querySelectorAll(SELECTOR_DATA_RIDE));

    for (var i = 0, len = carousels.length; i < len; i++) {
      var $carousel = $(carousels[i]);

      Carousel._jQueryInterface.call($carousel, $carousel.data());
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$2] = Carousel._jQueryInterface;
  $.fn[NAME$2].Constructor = Carousel;

  $.fn[NAME$2].noConflict = function () {
    $.fn[NAME$2] = JQUERY_NO_CONFLICT$2;
    return Carousel._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$3 = 'collapse';
  var VERSION$3 = '4.5.0';
  var DATA_KEY$3 = 'bs.collapse';
  var EVENT_KEY$3 = "." + DATA_KEY$3;
  var DATA_API_KEY$3 = '.data-api';
  var JQUERY_NO_CONFLICT$3 = $.fn[NAME$3];
  var Default$1 = {
    toggle: true,
    parent: ''
  };
  var DefaultType$1 = {
    toggle: 'boolean',
    parent: '(string|element)'
  };
  var EVENT_SHOW = "show" + EVENT_KEY$3;
  var EVENT_SHOWN = "shown" + EVENT_KEY$3;
  var EVENT_HIDE = "hide" + EVENT_KEY$3;
  var EVENT_HIDDEN = "hidden" + EVENT_KEY$3;
  var EVENT_CLICK_DATA_API$3 = "click" + EVENT_KEY$3 + DATA_API_KEY$3;
  var CLASS_NAME_SHOW$1 = 'show';
  var CLASS_NAME_COLLAPSE = 'collapse';
  var CLASS_NAME_COLLAPSING = 'collapsing';
  var CLASS_NAME_COLLAPSED = 'collapsed';
  var DIMENSION_WIDTH = 'width';
  var DIMENSION_HEIGHT = 'height';
  var SELECTOR_ACTIVES = '.show, .collapsing';
  var SELECTOR_DATA_TOGGLE$1 = '[data-toggle="collapse"]';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Collapse = /*#__PURE__*/function () {
    function Collapse(element, config) {
      this._isTransitioning = false;
      this._element = element;
      this._config = this._getConfig(config);
      this._triggerArray = [].slice.call(document.querySelectorAll("[data-toggle=\"collapse\"][href=\"#" + element.id + "\"]," + ("[data-toggle=\"collapse\"][data-target=\"#" + element.id + "\"]")));
      var toggleList = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE$1));

      for (var i = 0, len = toggleList.length; i < len; i++) {
        var elem = toggleList[i];
        var selector = Util.getSelectorFromElement(elem);
        var filterElement = [].slice.call(document.querySelectorAll(selector)).filter(function (foundElem) {
          return foundElem === element;
        });

        if (selector !== null && filterElement.length > 0) {
          this._selector = selector;

          this._triggerArray.push(elem);
        }
      }

      this._parent = this._config.parent ? this._getParent() : null;

      if (!this._config.parent) {
        this._addAriaAndCollapsedClass(this._element, this._triggerArray);
      }

      if (this._config.toggle) {
        this.toggle();
      }
    } // Getters


    var _proto = Collapse.prototype;

    // Public
    _proto.toggle = function toggle() {
      if ($(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        this.hide();
      } else {
        this.show();
      }
    };

    _proto.show = function show() {
      var _this = this;

      if (this._isTransitioning || $(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        return;
      }

      var actives;
      var activesData;

      if (this._parent) {
        actives = [].slice.call(this._parent.querySelectorAll(SELECTOR_ACTIVES)).filter(function (elem) {
          if (typeof _this._config.parent === 'string') {
            return elem.getAttribute('data-parent') === _this._config.parent;
          }

          return elem.classList.contains(CLASS_NAME_COLLAPSE);
        });

        if (actives.length === 0) {
          actives = null;
        }
      }

      if (actives) {
        activesData = $(actives).not(this._selector).data(DATA_KEY$3);

        if (activesData && activesData._isTransitioning) {
          return;
        }
      }

      var startEvent = $.Event(EVENT_SHOW);
      $(this._element).trigger(startEvent);

      if (startEvent.isDefaultPrevented()) {
        return;
      }

      if (actives) {
        Collapse._jQueryInterface.call($(actives).not(this._selector), 'hide');

        if (!activesData) {
          $(actives).data(DATA_KEY$3, null);
        }
      }

      var dimension = this._getDimension();

      $(this._element).removeClass(CLASS_NAME_COLLAPSE).addClass(CLASS_NAME_COLLAPSING);
      this._element.style[dimension] = 0;

      if (this._triggerArray.length) {
        $(this._triggerArray).removeClass(CLASS_NAME_COLLAPSED).attr('aria-expanded', true);
      }

      this.setTransitioning(true);

      var complete = function complete() {
        $(_this._element).removeClass(CLASS_NAME_COLLAPSING).addClass(CLASS_NAME_COLLAPSE + " " + CLASS_NAME_SHOW$1);
        _this._element.style[dimension] = '';

        _this.setTransitioning(false);

        $(_this._element).trigger(EVENT_SHOWN);
      };

      var capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
      var scrollSize = "scroll" + capitalizedDimension;
      var transitionDuration = Util.getTransitionDurationFromElement(this._element);
      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      this._element.style[dimension] = this._element[scrollSize] + "px";
    };

    _proto.hide = function hide() {
      var _this2 = this;

      if (this._isTransitioning || !$(this._element).hasClass(CLASS_NAME_SHOW$1)) {
        return;
      }

      var startEvent = $.Event(EVENT_HIDE);
      $(this._element).trigger(startEvent);

      if (startEvent.isDefaultPrevented()) {
        return;
      }

      var dimension = this._getDimension();

      this._element.style[dimension] = this._element.getBoundingClientRect()[dimension] + "px";
      Util.reflow(this._element);
      $(this._element).addClass(CLASS_NAME_COLLAPSING).removeClass(CLASS_NAME_COLLAPSE + " " + CLASS_NAME_SHOW$1);
      var triggerArrayLength = this._triggerArray.length;

      if (triggerArrayLength > 0) {
        for (var i = 0; i < triggerArrayLength; i++) {
          var trigger = this._triggerArray[i];
          var selector = Util.getSelectorFromElement(trigger);

          if (selector !== null) {
            var $elem = $([].slice.call(document.querySelectorAll(selector)));

            if (!$elem.hasClass(CLASS_NAME_SHOW$1)) {
              $(trigger).addClass(CLASS_NAME_COLLAPSED).attr('aria-expanded', false);
            }
          }
        }
      }

      this.setTransitioning(true);

      var complete = function complete() {
        _this2.setTransitioning(false);

        $(_this2._element).removeClass(CLASS_NAME_COLLAPSING).addClass(CLASS_NAME_COLLAPSE).trigger(EVENT_HIDDEN);
      };

      this._element.style[dimension] = '';
      var transitionDuration = Util.getTransitionDurationFromElement(this._element);
      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
    };

    _proto.setTransitioning = function setTransitioning(isTransitioning) {
      this._isTransitioning = isTransitioning;
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$3);
      this._config = null;
      this._parent = null;
      this._element = null;
      this._triggerArray = null;
      this._isTransitioning = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2({}, Default$1), config);
      config.toggle = Boolean(config.toggle); // Coerce string values

      Util.typeCheckConfig(NAME$3, config, DefaultType$1);
      return config;
    };

    _proto._getDimension = function _getDimension() {
      var hasWidth = $(this._element).hasClass(DIMENSION_WIDTH);
      return hasWidth ? DIMENSION_WIDTH : DIMENSION_HEIGHT;
    };

    _proto._getParent = function _getParent() {
      var _this3 = this;

      var parent;

      if (Util.isElement(this._config.parent)) {
        parent = this._config.parent; // It's a jQuery object

        if (typeof this._config.parent.jquery !== 'undefined') {
          parent = this._config.parent[0];
        }
      } else {
        parent = document.querySelector(this._config.parent);
      }

      var selector = "[data-toggle=\"collapse\"][data-parent=\"" + this._config.parent + "\"]";
      var children = [].slice.call(parent.querySelectorAll(selector));
      $(children).each(function (i, element) {
        _this3._addAriaAndCollapsedClass(Collapse._getTargetFromElement(element), [element]);
      });
      return parent;
    };

    _proto._addAriaAndCollapsedClass = function _addAriaAndCollapsedClass(element, triggerArray) {
      var isOpen = $(element).hasClass(CLASS_NAME_SHOW$1);

      if (triggerArray.length) {
        $(triggerArray).toggleClass(CLASS_NAME_COLLAPSED, !isOpen).attr('aria-expanded', isOpen);
      }
    } // Static
    ;

    Collapse._getTargetFromElement = function _getTargetFromElement(element) {
      var selector = Util.getSelectorFromElement(element);
      return selector ? document.querySelector(selector) : null;
    };

    Collapse._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY$3);

        var _config = _objectSpread2(_objectSpread2(_objectSpread2({}, Default$1), $this.data()), typeof config === 'object' && config ? config : {});

        if (!data && _config.toggle && typeof config === 'string' && /show|hide/.test(config)) {
          _config.toggle = false;
        }

        if (!data) {
          data = new Collapse(this, _config);
          $this.data(DATA_KEY$3, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Collapse, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$3;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$1;
      }
    }]);

    return Collapse;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$3, SELECTOR_DATA_TOGGLE$1, function (event) {
    // preventDefault only for <a> elements (which change the URL) not inside the collapsible element
    if (event.currentTarget.tagName === 'A') {
      event.preventDefault();
    }

    var $trigger = $(this);
    var selector = Util.getSelectorFromElement(this);
    var selectors = [].slice.call(document.querySelectorAll(selector));
    $(selectors).each(function () {
      var $target = $(this);
      var data = $target.data(DATA_KEY$3);
      var config = data ? 'toggle' : $trigger.data();

      Collapse._jQueryInterface.call($target, config);
    });
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$3] = Collapse._jQueryInterface;
  $.fn[NAME$3].Constructor = Collapse;

  $.fn[NAME$3].noConflict = function () {
    $.fn[NAME$3] = JQUERY_NO_CONFLICT$3;
    return Collapse._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$4 = 'dropdown';
  var VERSION$4 = '4.5.0';
  var DATA_KEY$4 = 'bs.dropdown';
  var EVENT_KEY$4 = "." + DATA_KEY$4;
  var DATA_API_KEY$4 = '.data-api';
  var JQUERY_NO_CONFLICT$4 = $.fn[NAME$4];
  var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key

  var SPACE_KEYCODE = 32; // KeyboardEvent.which value for space key

  var TAB_KEYCODE = 9; // KeyboardEvent.which value for tab key

  var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key

  var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key

  var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)

  var REGEXP_KEYDOWN = new RegExp(ARROW_UP_KEYCODE + "|" + ARROW_DOWN_KEYCODE + "|" + ESCAPE_KEYCODE);
  var EVENT_HIDE$1 = "hide" + EVENT_KEY$4;
  var EVENT_HIDDEN$1 = "hidden" + EVENT_KEY$4;
  var EVENT_SHOW$1 = "show" + EVENT_KEY$4;
  var EVENT_SHOWN$1 = "shown" + EVENT_KEY$4;
  var EVENT_CLICK = "click" + EVENT_KEY$4;
  var EVENT_CLICK_DATA_API$4 = "click" + EVENT_KEY$4 + DATA_API_KEY$4;
  var EVENT_KEYDOWN_DATA_API = "keydown" + EVENT_KEY$4 + DATA_API_KEY$4;
  var EVENT_KEYUP_DATA_API = "keyup" + EVENT_KEY$4 + DATA_API_KEY$4;
  var CLASS_NAME_DISABLED = 'disabled';
  var CLASS_NAME_SHOW$2 = 'show';
  var CLASS_NAME_DROPUP = 'dropup';
  var CLASS_NAME_DROPRIGHT = 'dropright';
  var CLASS_NAME_DROPLEFT = 'dropleft';
  var CLASS_NAME_MENURIGHT = 'dropdown-menu-right';
  var CLASS_NAME_POSITION_STATIC = 'position-static';
  var SELECTOR_DATA_TOGGLE$2 = '[data-toggle="dropdown"]';
  var SELECTOR_FORM_CHILD = '.dropdown form';
  var SELECTOR_MENU = '.dropdown-menu';
  var SELECTOR_NAVBAR_NAV = '.navbar-nav';
  var SELECTOR_VISIBLE_ITEMS = '.dropdown-menu .dropdown-item:not(.disabled):not(:disabled)';
  var PLACEMENT_TOP = 'top-start';
  var PLACEMENT_TOPEND = 'top-end';
  var PLACEMENT_BOTTOM = 'bottom-start';
  var PLACEMENT_BOTTOMEND = 'bottom-end';
  var PLACEMENT_RIGHT = 'right-start';
  var PLACEMENT_LEFT = 'left-start';
  var Default$2 = {
    offset: 0,
    flip: true,
    boundary: 'scrollParent',
    reference: 'toggle',
    display: 'dynamic',
    popperConfig: null
  };
  var DefaultType$2 = {
    offset: '(number|string|function)',
    flip: 'boolean',
    boundary: '(string|element)',
    reference: '(string|element)',
    display: 'string',
    popperConfig: '(null|object)'
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Dropdown = /*#__PURE__*/function () {
    function Dropdown(element, config) {
      this._element = element;
      this._popper = null;
      this._config = this._getConfig(config);
      this._menu = this._getMenuElement();
      this._inNavbar = this._detectNavbar();

      this._addEventListeners();
    } // Getters


    var _proto = Dropdown.prototype;

    // Public
    _proto.toggle = function toggle() {
      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED)) {
        return;
      }

      var isActive = $(this._menu).hasClass(CLASS_NAME_SHOW$2);

      Dropdown._clearMenus();

      if (isActive) {
        return;
      }

      this.show(true);
    };

    _proto.show = function show(usePopper) {
      if (usePopper === void 0) {
        usePopper = false;
      }

      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED) || $(this._menu).hasClass(CLASS_NAME_SHOW$2)) {
        return;
      }

      var relatedTarget = {
        relatedTarget: this._element
      };
      var showEvent = $.Event(EVENT_SHOW$1, relatedTarget);

      var parent = Dropdown._getParentFromElement(this._element);

      $(parent).trigger(showEvent);

      if (showEvent.isDefaultPrevented()) {
        return;
      } // Disable totally Popper.js for Dropdown in Navbar


      if (!this._inNavbar && usePopper) {
        /**
         * Check for Popper dependency
         * Popper - https://popper.js.org
         */
        if (typeof Popper === 'undefined') {
          throw new TypeError('Bootstrap\'s dropdowns require Popper.js (https://popper.js.org/)');
        }

        var referenceElement = this._element;

        if (this._config.reference === 'parent') {
          referenceElement = parent;
        } else if (Util.isElement(this._config.reference)) {
          referenceElement = this._config.reference; // Check if it's jQuery element

          if (typeof this._config.reference.jquery !== 'undefined') {
            referenceElement = this._config.reference[0];
          }
        } // If boundary is not `scrollParent`, then set position to `static`
        // to allow the menu to "escape" the scroll parent's boundaries
        // https://github.com/twbs/bootstrap/issues/24251


        if (this._config.boundary !== 'scrollParent') {
          $(parent).addClass(CLASS_NAME_POSITION_STATIC);
        }

        this._popper = new Popper(referenceElement, this._menu, this._getPopperConfig());
      } // If this is a touch-enabled device we add extra
      // empty mouseover listeners to the body's immediate children;
      // only needed because of broken event delegation on iOS
      // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html


      if ('ontouchstart' in document.documentElement && $(parent).closest(SELECTOR_NAVBAR_NAV).length === 0) {
        $(document.body).children().on('mouseover', null, $.noop);
      }

      this._element.focus();

      this._element.setAttribute('aria-expanded', true);

      $(this._menu).toggleClass(CLASS_NAME_SHOW$2);
      $(parent).toggleClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_SHOWN$1, relatedTarget));
    };

    _proto.hide = function hide() {
      if (this._element.disabled || $(this._element).hasClass(CLASS_NAME_DISABLED) || !$(this._menu).hasClass(CLASS_NAME_SHOW$2)) {
        return;
      }

      var relatedTarget = {
        relatedTarget: this._element
      };
      var hideEvent = $.Event(EVENT_HIDE$1, relatedTarget);

      var parent = Dropdown._getParentFromElement(this._element);

      $(parent).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      if (this._popper) {
        this._popper.destroy();
      }

      $(this._menu).toggleClass(CLASS_NAME_SHOW$2);
      $(parent).toggleClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_HIDDEN$1, relatedTarget));
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$4);
      $(this._element).off(EVENT_KEY$4);
      this._element = null;
      this._menu = null;

      if (this._popper !== null) {
        this._popper.destroy();

        this._popper = null;
      }
    };

    _proto.update = function update() {
      this._inNavbar = this._detectNavbar();

      if (this._popper !== null) {
        this._popper.scheduleUpdate();
      }
    } // Private
    ;

    _proto._addEventListeners = function _addEventListeners() {
      var _this = this;

      $(this._element).on(EVENT_CLICK, function (event) {
        event.preventDefault();
        event.stopPropagation();

        _this.toggle();
      });
    };

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2(_objectSpread2({}, this.constructor.Default), $(this._element).data()), config);
      Util.typeCheckConfig(NAME$4, config, this.constructor.DefaultType);
      return config;
    };

    _proto._getMenuElement = function _getMenuElement() {
      if (!this._menu) {
        var parent = Dropdown._getParentFromElement(this._element);

        if (parent) {
          this._menu = parent.querySelector(SELECTOR_MENU);
        }
      }

      return this._menu;
    };

    _proto._getPlacement = function _getPlacement() {
      var $parentDropdown = $(this._element.parentNode);
      var placement = PLACEMENT_BOTTOM; // Handle dropup

      if ($parentDropdown.hasClass(CLASS_NAME_DROPUP)) {
        placement = $(this._menu).hasClass(CLASS_NAME_MENURIGHT) ? PLACEMENT_TOPEND : PLACEMENT_TOP;
      } else if ($parentDropdown.hasClass(CLASS_NAME_DROPRIGHT)) {
        placement = PLACEMENT_RIGHT;
      } else if ($parentDropdown.hasClass(CLASS_NAME_DROPLEFT)) {
        placement = PLACEMENT_LEFT;
      } else if ($(this._menu).hasClass(CLASS_NAME_MENURIGHT)) {
        placement = PLACEMENT_BOTTOMEND;
      }

      return placement;
    };

    _proto._detectNavbar = function _detectNavbar() {
      return $(this._element).closest('.navbar').length > 0;
    };

    _proto._getOffset = function _getOffset() {
      var _this2 = this;

      var offset = {};

      if (typeof this._config.offset === 'function') {
        offset.fn = function (data) {
          data.offsets = _objectSpread2(_objectSpread2({}, data.offsets), _this2._config.offset(data.offsets, _this2._element) || {});
          return data;
        };
      } else {
        offset.offset = this._config.offset;
      }

      return offset;
    };

    _proto._getPopperConfig = function _getPopperConfig() {
      var popperConfig = {
        placement: this._getPlacement(),
        modifiers: {
          offset: this._getOffset(),
          flip: {
            enabled: this._config.flip
          },
          preventOverflow: {
            boundariesElement: this._config.boundary
          }
        }
      }; // Disable Popper.js if we have a static display

      if (this._config.display === 'static') {
        popperConfig.modifiers.applyStyle = {
          enabled: false
        };
      }

      return _objectSpread2(_objectSpread2({}, popperConfig), this._config.popperConfig);
    } // Static
    ;

    Dropdown._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$4);

        var _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new Dropdown(this, _config);
          $(this).data(DATA_KEY$4, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    Dropdown._clearMenus = function _clearMenus(event) {
      if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH || event.type === 'keyup' && event.which !== TAB_KEYCODE)) {
        return;
      }

      var toggles = [].slice.call(document.querySelectorAll(SELECTOR_DATA_TOGGLE$2));

      for (var i = 0, len = toggles.length; i < len; i++) {
        var parent = Dropdown._getParentFromElement(toggles[i]);

        var context = $(toggles[i]).data(DATA_KEY$4);
        var relatedTarget = {
          relatedTarget: toggles[i]
        };

        if (event && event.type === 'click') {
          relatedTarget.clickEvent = event;
        }

        if (!context) {
          continue;
        }

        var dropdownMenu = context._menu;

        if (!$(parent).hasClass(CLASS_NAME_SHOW$2)) {
          continue;
        }

        if (event && (event.type === 'click' && /input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) && $.contains(parent, event.target)) {
          continue;
        }

        var hideEvent = $.Event(EVENT_HIDE$1, relatedTarget);
        $(parent).trigger(hideEvent);

        if (hideEvent.isDefaultPrevented()) {
          continue;
        } // If this is a touch-enabled device we remove the extra
        // empty mouseover listeners we added for iOS support


        if ('ontouchstart' in document.documentElement) {
          $(document.body).children().off('mouseover', null, $.noop);
        }

        toggles[i].setAttribute('aria-expanded', 'false');

        if (context._popper) {
          context._popper.destroy();
        }

        $(dropdownMenu).removeClass(CLASS_NAME_SHOW$2);
        $(parent).removeClass(CLASS_NAME_SHOW$2).trigger($.Event(EVENT_HIDDEN$1, relatedTarget));
      }
    };

    Dropdown._getParentFromElement = function _getParentFromElement(element) {
      var parent;
      var selector = Util.getSelectorFromElement(element);

      if (selector) {
        parent = document.querySelector(selector);
      }

      return parent || element.parentNode;
    } // eslint-disable-next-line complexity
    ;

    Dropdown._dataApiKeydownHandler = function _dataApiKeydownHandler(event) {
      // If not input/textarea:
      //  - And not a key in REGEXP_KEYDOWN => not a dropdown command
      // If input/textarea:
      //  - If space key => not a dropdown command
      //  - If key is other than escape
      //    - If key is not up or down => not a dropdown command
      //    - If trigger inside the menu => not a dropdown command
      if (/input|textarea/i.test(event.target.tagName) ? event.which === SPACE_KEYCODE || event.which !== ESCAPE_KEYCODE && (event.which !== ARROW_DOWN_KEYCODE && event.which !== ARROW_UP_KEYCODE || $(event.target).closest(SELECTOR_MENU).length) : !REGEXP_KEYDOWN.test(event.which)) {
        return;
      }

      if (this.disabled || $(this).hasClass(CLASS_NAME_DISABLED)) {
        return;
      }

      var parent = Dropdown._getParentFromElement(this);

      var isActive = $(parent).hasClass(CLASS_NAME_SHOW$2);

      if (!isActive && event.which === ESCAPE_KEYCODE) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      if (!isActive || isActive && (event.which === ESCAPE_KEYCODE || event.which === SPACE_KEYCODE)) {
        if (event.which === ESCAPE_KEYCODE) {
          $(parent.querySelector(SELECTOR_DATA_TOGGLE$2)).trigger('focus');
        }

        $(this).trigger('click');
        return;
      }

      var items = [].slice.call(parent.querySelectorAll(SELECTOR_VISIBLE_ITEMS)).filter(function (item) {
        return $(item).is(':visible');
      });

      if (items.length === 0) {
        return;
      }

      var index = items.indexOf(event.target);

      if (event.which === ARROW_UP_KEYCODE && index > 0) {
        // Up
        index--;
      }

      if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
        // Down
        index++;
      }

      if (index < 0) {
        index = 0;
      }

      items[index].focus();
    };

    _createClass(Dropdown, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$4;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$2;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$2;
      }
    }]);

    return Dropdown;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_KEYDOWN_DATA_API, SELECTOR_DATA_TOGGLE$2, Dropdown._dataApiKeydownHandler).on(EVENT_KEYDOWN_DATA_API, SELECTOR_MENU, Dropdown._dataApiKeydownHandler).on(EVENT_CLICK_DATA_API$4 + " " + EVENT_KEYUP_DATA_API, Dropdown._clearMenus).on(EVENT_CLICK_DATA_API$4, SELECTOR_DATA_TOGGLE$2, function (event) {
    event.preventDefault();
    event.stopPropagation();

    Dropdown._jQueryInterface.call($(this), 'toggle');
  }).on(EVENT_CLICK_DATA_API$4, SELECTOR_FORM_CHILD, function (e) {
    e.stopPropagation();
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$4] = Dropdown._jQueryInterface;
  $.fn[NAME$4].Constructor = Dropdown;

  $.fn[NAME$4].noConflict = function () {
    $.fn[NAME$4] = JQUERY_NO_CONFLICT$4;
    return Dropdown._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$5 = 'modal';
  var VERSION$5 = '4.5.0';
  var DATA_KEY$5 = 'bs.modal';
  var EVENT_KEY$5 = "." + DATA_KEY$5;
  var DATA_API_KEY$5 = '.data-api';
  var JQUERY_NO_CONFLICT$5 = $.fn[NAME$5];
  var ESCAPE_KEYCODE$1 = 27; // KeyboardEvent.which value for Escape (Esc) key

  var Default$3 = {
    backdrop: true,
    keyboard: true,
    focus: true,
    show: true
  };
  var DefaultType$3 = {
    backdrop: '(boolean|string)',
    keyboard: 'boolean',
    focus: 'boolean',
    show: 'boolean'
  };
  var EVENT_HIDE$2 = "hide" + EVENT_KEY$5;
  var EVENT_HIDE_PREVENTED = "hidePrevented" + EVENT_KEY$5;
  var EVENT_HIDDEN$2 = "hidden" + EVENT_KEY$5;
  var EVENT_SHOW$2 = "show" + EVENT_KEY$5;
  var EVENT_SHOWN$2 = "shown" + EVENT_KEY$5;
  var EVENT_FOCUSIN = "focusin" + EVENT_KEY$5;
  var EVENT_RESIZE = "resize" + EVENT_KEY$5;
  var EVENT_CLICK_DISMISS = "click.dismiss" + EVENT_KEY$5;
  var EVENT_KEYDOWN_DISMISS = "keydown.dismiss" + EVENT_KEY$5;
  var EVENT_MOUSEUP_DISMISS = "mouseup.dismiss" + EVENT_KEY$5;
  var EVENT_MOUSEDOWN_DISMISS = "mousedown.dismiss" + EVENT_KEY$5;
  var EVENT_CLICK_DATA_API$5 = "click" + EVENT_KEY$5 + DATA_API_KEY$5;
  var CLASS_NAME_SCROLLABLE = 'modal-dialog-scrollable';
  var CLASS_NAME_SCROLLBAR_MEASURER = 'modal-scrollbar-measure';
  var CLASS_NAME_BACKDROP = 'modal-backdrop';
  var CLASS_NAME_OPEN = 'modal-open';
  var CLASS_NAME_FADE$1 = 'fade';
  var CLASS_NAME_SHOW$3 = 'show';
  var CLASS_NAME_STATIC = 'modal-static';
  var SELECTOR_DIALOG = '.modal-dialog';
  var SELECTOR_MODAL_BODY = '.modal-body';
  var SELECTOR_DATA_TOGGLE$3 = '[data-toggle="modal"]';
  var SELECTOR_DATA_DISMISS = '[data-dismiss="modal"]';
  var SELECTOR_FIXED_CONTENT = '.fixed-top, .fixed-bottom, .is-fixed, .sticky-top';
  var SELECTOR_STICKY_CONTENT = '.sticky-top';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Modal = /*#__PURE__*/function () {
    function Modal(element, config) {
      this._config = this._getConfig(config);
      this._element = element;
      this._dialog = element.querySelector(SELECTOR_DIALOG);
      this._backdrop = null;
      this._isShown = false;
      this._isBodyOverflowing = false;
      this._ignoreBackdropClick = false;
      this._isTransitioning = false;
      this._scrollbarWidth = 0;
    } // Getters


    var _proto = Modal.prototype;

    // Public
    _proto.toggle = function toggle(relatedTarget) {
      return this._isShown ? this.hide() : this.show(relatedTarget);
    };

    _proto.show = function show(relatedTarget) {
      var _this = this;

      if (this._isShown || this._isTransitioning) {
        return;
      }

      if ($(this._element).hasClass(CLASS_NAME_FADE$1)) {
        this._isTransitioning = true;
      }

      var showEvent = $.Event(EVENT_SHOW$2, {
        relatedTarget: relatedTarget
      });
      $(this._element).trigger(showEvent);

      if (this._isShown || showEvent.isDefaultPrevented()) {
        return;
      }

      this._isShown = true;

      this._checkScrollbar();

      this._setScrollbar();

      this._adjustDialog();

      this._setEscapeEvent();

      this._setResizeEvent();

      $(this._element).on(EVENT_CLICK_DISMISS, SELECTOR_DATA_DISMISS, function (event) {
        return _this.hide(event);
      });
      $(this._dialog).on(EVENT_MOUSEDOWN_DISMISS, function () {
        $(_this._element).one(EVENT_MOUSEUP_DISMISS, function (event) {
          if ($(event.target).is(_this._element)) {
            _this._ignoreBackdropClick = true;
          }
        });
      });

      this._showBackdrop(function () {
        return _this._showElement(relatedTarget);
      });
    };

    _proto.hide = function hide(event) {
      var _this2 = this;

      if (event) {
        event.preventDefault();
      }

      if (!this._isShown || this._isTransitioning) {
        return;
      }

      var hideEvent = $.Event(EVENT_HIDE$2);
      $(this._element).trigger(hideEvent);

      if (!this._isShown || hideEvent.isDefaultPrevented()) {
        return;
      }

      this._isShown = false;
      var transition = $(this._element).hasClass(CLASS_NAME_FADE$1);

      if (transition) {
        this._isTransitioning = true;
      }

      this._setEscapeEvent();

      this._setResizeEvent();

      $(document).off(EVENT_FOCUSIN);
      $(this._element).removeClass(CLASS_NAME_SHOW$3);
      $(this._element).off(EVENT_CLICK_DISMISS);
      $(this._dialog).off(EVENT_MOUSEDOWN_DISMISS);

      if (transition) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, function (event) {
          return _this2._hideModal(event);
        }).emulateTransitionEnd(transitionDuration);
      } else {
        this._hideModal();
      }
    };

    _proto.dispose = function dispose() {
      [window, this._element, this._dialog].forEach(function (htmlElement) {
        return $(htmlElement).off(EVENT_KEY$5);
      });
      /**
       * `document` has 2 events `EVENT_FOCUSIN` and `EVENT_CLICK_DATA_API`
       * Do not move `document` in `htmlElements` array
       * It will remove `EVENT_CLICK_DATA_API` event that should remain
       */

      $(document).off(EVENT_FOCUSIN);
      $.removeData(this._element, DATA_KEY$5);
      this._config = null;
      this._element = null;
      this._dialog = null;
      this._backdrop = null;
      this._isShown = null;
      this._isBodyOverflowing = null;
      this._ignoreBackdropClick = null;
      this._isTransitioning = null;
      this._scrollbarWidth = null;
    };

    _proto.handleUpdate = function handleUpdate() {
      this._adjustDialog();
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2({}, Default$3), config);
      Util.typeCheckConfig(NAME$5, config, DefaultType$3);
      return config;
    };

    _proto._triggerBackdropTransition = function _triggerBackdropTransition() {
      var _this3 = this;

      if (this._config.backdrop === 'static') {
        var hideEventPrevented = $.Event(EVENT_HIDE_PREVENTED);
        $(this._element).trigger(hideEventPrevented);

        if (hideEventPrevented.defaultPrevented) {
          return;
        }

        this._element.classList.add(CLASS_NAME_STATIC);

        var modalTransitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, function () {
          _this3._element.classList.remove(CLASS_NAME_STATIC);
        }).emulateTransitionEnd(modalTransitionDuration);

        this._element.focus();
      } else {
        this.hide();
      }
    };

    _proto._showElement = function _showElement(relatedTarget) {
      var _this4 = this;

      var transition = $(this._element).hasClass(CLASS_NAME_FADE$1);
      var modalBody = this._dialog ? this._dialog.querySelector(SELECTOR_MODAL_BODY) : null;

      if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE) {
        // Don't move modal's DOM position
        document.body.appendChild(this._element);
      }

      this._element.style.display = 'block';

      this._element.removeAttribute('aria-hidden');

      this._element.setAttribute('aria-modal', true);

      if ($(this._dialog).hasClass(CLASS_NAME_SCROLLABLE) && modalBody) {
        modalBody.scrollTop = 0;
      } else {
        this._element.scrollTop = 0;
      }

      if (transition) {
        Util.reflow(this._element);
      }

      $(this._element).addClass(CLASS_NAME_SHOW$3);

      if (this._config.focus) {
        this._enforceFocus();
      }

      var shownEvent = $.Event(EVENT_SHOWN$2, {
        relatedTarget: relatedTarget
      });

      var transitionComplete = function transitionComplete() {
        if (_this4._config.focus) {
          _this4._element.focus();
        }

        _this4._isTransitioning = false;
        $(_this4._element).trigger(shownEvent);
      };

      if (transition) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._dialog);
        $(this._dialog).one(Util.TRANSITION_END, transitionComplete).emulateTransitionEnd(transitionDuration);
      } else {
        transitionComplete();
      }
    };

    _proto._enforceFocus = function _enforceFocus() {
      var _this5 = this;

      $(document).off(EVENT_FOCUSIN) // Guard against infinite focus loop
      .on(EVENT_FOCUSIN, function (event) {
        if (document !== event.target && _this5._element !== event.target && $(_this5._element).has(event.target).length === 0) {
          _this5._element.focus();
        }
      });
    };

    _proto._setEscapeEvent = function _setEscapeEvent() {
      var _this6 = this;

      if (this._isShown) {
        $(this._element).on(EVENT_KEYDOWN_DISMISS, function (event) {
          if (_this6._config.keyboard && event.which === ESCAPE_KEYCODE$1) {
            event.preventDefault();

            _this6.hide();
          } else if (!_this6._config.keyboard && event.which === ESCAPE_KEYCODE$1) {
            _this6._triggerBackdropTransition();
          }
        });
      } else if (!this._isShown) {
        $(this._element).off(EVENT_KEYDOWN_DISMISS);
      }
    };

    _proto._setResizeEvent = function _setResizeEvent() {
      var _this7 = this;

      if (this._isShown) {
        $(window).on(EVENT_RESIZE, function (event) {
          return _this7.handleUpdate(event);
        });
      } else {
        $(window).off(EVENT_RESIZE);
      }
    };

    _proto._hideModal = function _hideModal() {
      var _this8 = this;

      this._element.style.display = 'none';

      this._element.setAttribute('aria-hidden', true);

      this._element.removeAttribute('aria-modal');

      this._isTransitioning = false;

      this._showBackdrop(function () {
        $(document.body).removeClass(CLASS_NAME_OPEN);

        _this8._resetAdjustments();

        _this8._resetScrollbar();

        $(_this8._element).trigger(EVENT_HIDDEN$2);
      });
    };

    _proto._removeBackdrop = function _removeBackdrop() {
      if (this._backdrop) {
        $(this._backdrop).remove();
        this._backdrop = null;
      }
    };

    _proto._showBackdrop = function _showBackdrop(callback) {
      var _this9 = this;

      var animate = $(this._element).hasClass(CLASS_NAME_FADE$1) ? CLASS_NAME_FADE$1 : '';

      if (this._isShown && this._config.backdrop) {
        this._backdrop = document.createElement('div');
        this._backdrop.className = CLASS_NAME_BACKDROP;

        if (animate) {
          this._backdrop.classList.add(animate);
        }

        $(this._backdrop).appendTo(document.body);
        $(this._element).on(EVENT_CLICK_DISMISS, function (event) {
          if (_this9._ignoreBackdropClick) {
            _this9._ignoreBackdropClick = false;
            return;
          }

          if (event.target !== event.currentTarget) {
            return;
          }

          _this9._triggerBackdropTransition();
        });

        if (animate) {
          Util.reflow(this._backdrop);
        }

        $(this._backdrop).addClass(CLASS_NAME_SHOW$3);

        if (!callback) {
          return;
        }

        if (!animate) {
          callback();
          return;
        }

        var backdropTransitionDuration = Util.getTransitionDurationFromElement(this._backdrop);
        $(this._backdrop).one(Util.TRANSITION_END, callback).emulateTransitionEnd(backdropTransitionDuration);
      } else if (!this._isShown && this._backdrop) {
        $(this._backdrop).removeClass(CLASS_NAME_SHOW$3);

        var callbackRemove = function callbackRemove() {
          _this9._removeBackdrop();

          if (callback) {
            callback();
          }
        };

        if ($(this._element).hasClass(CLASS_NAME_FADE$1)) {
          var _backdropTransitionDuration = Util.getTransitionDurationFromElement(this._backdrop);

          $(this._backdrop).one(Util.TRANSITION_END, callbackRemove).emulateTransitionEnd(_backdropTransitionDuration);
        } else {
          callbackRemove();
        }
      } else if (callback) {
        callback();
      }
    } // ----------------------------------------------------------------------
    // the following methods are used to handle overflowing modals
    // todo (fat): these should probably be refactored out of modal.js
    // ----------------------------------------------------------------------
    ;

    _proto._adjustDialog = function _adjustDialog() {
      var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

      if (!this._isBodyOverflowing && isModalOverflowing) {
        this._element.style.paddingLeft = this._scrollbarWidth + "px";
      }

      if (this._isBodyOverflowing && !isModalOverflowing) {
        this._element.style.paddingRight = this._scrollbarWidth + "px";
      }
    };

    _proto._resetAdjustments = function _resetAdjustments() {
      this._element.style.paddingLeft = '';
      this._element.style.paddingRight = '';
    };

    _proto._checkScrollbar = function _checkScrollbar() {
      var rect = document.body.getBoundingClientRect();
      this._isBodyOverflowing = Math.round(rect.left + rect.right) < window.innerWidth;
      this._scrollbarWidth = this._getScrollbarWidth();
    };

    _proto._setScrollbar = function _setScrollbar() {
      var _this10 = this;

      if (this._isBodyOverflowing) {
        // Note: DOMNode.style.paddingRight returns the actual value or '' if not set
        //   while $(DOMNode).css('padding-right') returns the calculated value or 0 if not set
        var fixedContent = [].slice.call(document.querySelectorAll(SELECTOR_FIXED_CONTENT));
        var stickyContent = [].slice.call(document.querySelectorAll(SELECTOR_STICKY_CONTENT)); // Adjust fixed content padding

        $(fixedContent).each(function (index, element) {
          var actualPadding = element.style.paddingRight;
          var calculatedPadding = $(element).css('padding-right');
          $(element).data('padding-right', actualPadding).css('padding-right', parseFloat(calculatedPadding) + _this10._scrollbarWidth + "px");
        }); // Adjust sticky content margin

        $(stickyContent).each(function (index, element) {
          var actualMargin = element.style.marginRight;
          var calculatedMargin = $(element).css('margin-right');
          $(element).data('margin-right', actualMargin).css('margin-right', parseFloat(calculatedMargin) - _this10._scrollbarWidth + "px");
        }); // Adjust body padding

        var actualPadding = document.body.style.paddingRight;
        var calculatedPadding = $(document.body).css('padding-right');
        $(document.body).data('padding-right', actualPadding).css('padding-right', parseFloat(calculatedPadding) + this._scrollbarWidth + "px");
      }

      $(document.body).addClass(CLASS_NAME_OPEN);
    };

    _proto._resetScrollbar = function _resetScrollbar() {
      // Restore fixed content padding
      var fixedContent = [].slice.call(document.querySelectorAll(SELECTOR_FIXED_CONTENT));
      $(fixedContent).each(function (index, element) {
        var padding = $(element).data('padding-right');
        $(element).removeData('padding-right');
        element.style.paddingRight = padding ? padding : '';
      }); // Restore sticky content

      var elements = [].slice.call(document.querySelectorAll("" + SELECTOR_STICKY_CONTENT));
      $(elements).each(function (index, element) {
        var margin = $(element).data('margin-right');

        if (typeof margin !== 'undefined') {
          $(element).css('margin-right', margin).removeData('margin-right');
        }
      }); // Restore body padding

      var padding = $(document.body).data('padding-right');
      $(document.body).removeData('padding-right');
      document.body.style.paddingRight = padding ? padding : '';
    };

    _proto._getScrollbarWidth = function _getScrollbarWidth() {
      // thx d.walsh
      var scrollDiv = document.createElement('div');
      scrollDiv.className = CLASS_NAME_SCROLLBAR_MEASURER;
      document.body.appendChild(scrollDiv);
      var scrollbarWidth = scrollDiv.getBoundingClientRect().width - scrollDiv.clientWidth;
      document.body.removeChild(scrollDiv);
      return scrollbarWidth;
    } // Static
    ;

    Modal._jQueryInterface = function _jQueryInterface(config, relatedTarget) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$5);

        var _config = _objectSpread2(_objectSpread2(_objectSpread2({}, Default$3), $(this).data()), typeof config === 'object' && config ? config : {});

        if (!data) {
          data = new Modal(this, _config);
          $(this).data(DATA_KEY$5, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config](relatedTarget);
        } else if (_config.show) {
          data.show(relatedTarget);
        }
      });
    };

    _createClass(Modal, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$5;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$3;
      }
    }]);

    return Modal;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$5, SELECTOR_DATA_TOGGLE$3, function (event) {
    var _this11 = this;

    var target;
    var selector = Util.getSelectorFromElement(this);

    if (selector) {
      target = document.querySelector(selector);
    }

    var config = $(target).data(DATA_KEY$5) ? 'toggle' : _objectSpread2(_objectSpread2({}, $(target).data()), $(this).data());

    if (this.tagName === 'A' || this.tagName === 'AREA') {
      event.preventDefault();
    }

    var $target = $(target).one(EVENT_SHOW$2, function (showEvent) {
      if (showEvent.isDefaultPrevented()) {
        // Only register focus restorer if modal will actually get shown
        return;
      }

      $target.one(EVENT_HIDDEN$2, function () {
        if ($(_this11).is(':visible')) {
          _this11.focus();
        }
      });
    });

    Modal._jQueryInterface.call($(target), config, this);
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$5] = Modal._jQueryInterface;
  $.fn[NAME$5].Constructor = Modal;

  $.fn[NAME$5].noConflict = function () {
    $.fn[NAME$5] = JQUERY_NO_CONFLICT$5;
    return Modal._jQueryInterface;
  };

  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v4.5.0): tools/sanitizer.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * --------------------------------------------------------------------------
   */
  var uriAttrs = ['background', 'cite', 'href', 'itemtype', 'longdesc', 'poster', 'src', 'xlink:href'];
  var ARIA_ATTRIBUTE_PATTERN = /^aria-[\w-]*$/i;
  var DefaultWhitelist = {
    // Global attributes allowed on any supplied element below.
    '*': ['class', 'dir', 'id', 'lang', 'role', ARIA_ATTRIBUTE_PATTERN],
    a: ['target', 'href', 'title', 'rel'],
    area: [],
    b: [],
    br: [],
    col: [],
    code: [],
    div: [],
    em: [],
    hr: [],
    h1: [],
    h2: [],
    h3: [],
    h4: [],
    h5: [],
    h6: [],
    i: [],
    img: ['src', 'srcset', 'alt', 'title', 'width', 'height'],
    li: [],
    ol: [],
    p: [],
    pre: [],
    s: [],
    small: [],
    span: [],
    sub: [],
    sup: [],
    strong: [],
    u: [],
    ul: []
  };
  /**
   * A pattern that recognizes a commonly useful subset of URLs that are safe.
   *
   * Shoutout to Angular 7 https://github.com/angular/angular/blob/7.2.4/packages/core/src/sanitization/url_sanitizer.ts
   */

  var SAFE_URL_PATTERN = /^(?:(?:https?|mailto|ftp|tel|file):|[^#&/:?]*(?:[#/?]|$))/gi;
  /**
   * A pattern that matches safe data URLs. Only matches image, video and audio types.
   *
   * Shoutout to Angular 7 https://github.com/angular/angular/blob/7.2.4/packages/core/src/sanitization/url_sanitizer.ts
   */

  var DATA_URL_PATTERN = /^data:(?:image\/(?:bmp|gif|jpeg|jpg|png|tiff|webp)|video\/(?:mpeg|mp4|ogg|webm)|audio\/(?:mp3|oga|ogg|opus));base64,[\d+/a-z]+=*$/i;

  function allowedAttribute(attr, allowedAttributeList) {
    var attrName = attr.nodeName.toLowerCase();

    if (allowedAttributeList.indexOf(attrName) !== -1) {
      if (uriAttrs.indexOf(attrName) !== -1) {
        return Boolean(attr.nodeValue.match(SAFE_URL_PATTERN) || attr.nodeValue.match(DATA_URL_PATTERN));
      }

      return true;
    }

    var regExp = allowedAttributeList.filter(function (attrRegex) {
      return attrRegex instanceof RegExp;
    }); // Check if a regular expression validates the attribute.

    for (var i = 0, len = regExp.length; i < len; i++) {
      if (attrName.match(regExp[i])) {
        return true;
      }
    }

    return false;
  }

  function sanitizeHtml(unsafeHtml, whiteList, sanitizeFn) {
    if (unsafeHtml.length === 0) {
      return unsafeHtml;
    }

    if (sanitizeFn && typeof sanitizeFn === 'function') {
      return sanitizeFn(unsafeHtml);
    }

    var domParser = new window.DOMParser();
    var createdDocument = domParser.parseFromString(unsafeHtml, 'text/html');
    var whitelistKeys = Object.keys(whiteList);
    var elements = [].slice.call(createdDocument.body.querySelectorAll('*'));

    var _loop = function _loop(i, len) {
      var el = elements[i];
      var elName = el.nodeName.toLowerCase();

      if (whitelistKeys.indexOf(el.nodeName.toLowerCase()) === -1) {
        el.parentNode.removeChild(el);
        return "continue";
      }

      var attributeList = [].slice.call(el.attributes);
      var whitelistedAttributes = [].concat(whiteList['*'] || [], whiteList[elName] || []);
      attributeList.forEach(function (attr) {
        if (!allowedAttribute(attr, whitelistedAttributes)) {
          el.removeAttribute(attr.nodeName);
        }
      });
    };

    for (var i = 0, len = elements.length; i < len; i++) {
      var _ret = _loop(i);

      if (_ret === "continue") continue;
    }

    return createdDocument.body.innerHTML;
  }

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$6 = 'tooltip';
  var VERSION$6 = '4.5.0';
  var DATA_KEY$6 = 'bs.tooltip';
  var EVENT_KEY$6 = "." + DATA_KEY$6;
  var JQUERY_NO_CONFLICT$6 = $.fn[NAME$6];
  var CLASS_PREFIX = 'bs-tooltip';
  var BSCLS_PREFIX_REGEX = new RegExp("(^|\\s)" + CLASS_PREFIX + "\\S+", 'g');
  var DISALLOWED_ATTRIBUTES = ['sanitize', 'whiteList', 'sanitizeFn'];
  var DefaultType$4 = {
    animation: 'boolean',
    template: 'string',
    title: '(string|element|function)',
    trigger: 'string',
    delay: '(number|object)',
    html: 'boolean',
    selector: '(string|boolean)',
    placement: '(string|function)',
    offset: '(number|string|function)',
    container: '(string|element|boolean)',
    fallbackPlacement: '(string|array)',
    boundary: '(string|element)',
    sanitize: 'boolean',
    sanitizeFn: '(null|function)',
    whiteList: 'object',
    popperConfig: '(null|object)'
  };
  var AttachmentMap = {
    AUTO: 'auto',
    TOP: 'top',
    RIGHT: 'right',
    BOTTOM: 'bottom',
    LEFT: 'left'
  };
  var Default$4 = {
    animation: true,
    template: '<div class="tooltip" role="tooltip">' + '<div class="arrow"></div>' + '<div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    selector: false,
    placement: 'top',
    offset: 0,
    container: false,
    fallbackPlacement: 'flip',
    boundary: 'scrollParent',
    sanitize: true,
    sanitizeFn: null,
    whiteList: DefaultWhitelist,
    popperConfig: null
  };
  var HOVER_STATE_SHOW = 'show';
  var HOVER_STATE_OUT = 'out';
  var Event = {
    HIDE: "hide" + EVENT_KEY$6,
    HIDDEN: "hidden" + EVENT_KEY$6,
    SHOW: "show" + EVENT_KEY$6,
    SHOWN: "shown" + EVENT_KEY$6,
    INSERTED: "inserted" + EVENT_KEY$6,
    CLICK: "click" + EVENT_KEY$6,
    FOCUSIN: "focusin" + EVENT_KEY$6,
    FOCUSOUT: "focusout" + EVENT_KEY$6,
    MOUSEENTER: "mouseenter" + EVENT_KEY$6,
    MOUSELEAVE: "mouseleave" + EVENT_KEY$6
  };
  var CLASS_NAME_FADE$2 = 'fade';
  var CLASS_NAME_SHOW$4 = 'show';
  var SELECTOR_TOOLTIP_INNER = '.tooltip-inner';
  var SELECTOR_ARROW = '.arrow';
  var TRIGGER_HOVER = 'hover';
  var TRIGGER_FOCUS = 'focus';
  var TRIGGER_CLICK = 'click';
  var TRIGGER_MANUAL = 'manual';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Tooltip = /*#__PURE__*/function () {
    function Tooltip(element, config) {
      if (typeof Popper === 'undefined') {
        throw new TypeError('Bootstrap\'s tooltips require Popper.js (https://popper.js.org/)');
      } // private


      this._isEnabled = true;
      this._timeout = 0;
      this._hoverState = '';
      this._activeTrigger = {};
      this._popper = null; // Protected

      this.element = element;
      this.config = this._getConfig(config);
      this.tip = null;

      this._setListeners();
    } // Getters


    var _proto = Tooltip.prototype;

    // Public
    _proto.enable = function enable() {
      this._isEnabled = true;
    };

    _proto.disable = function disable() {
      this._isEnabled = false;
    };

    _proto.toggleEnabled = function toggleEnabled() {
      this._isEnabled = !this._isEnabled;
    };

    _proto.toggle = function toggle(event) {
      if (!this._isEnabled) {
        return;
      }

      if (event) {
        var dataKey = this.constructor.DATA_KEY;
        var context = $(event.currentTarget).data(dataKey);

        if (!context) {
          context = new this.constructor(event.currentTarget, this._getDelegateConfig());
          $(event.currentTarget).data(dataKey, context);
        }

        context._activeTrigger.click = !context._activeTrigger.click;

        if (context._isWithActiveTrigger()) {
          context._enter(null, context);
        } else {
          context._leave(null, context);
        }
      } else {
        if ($(this.getTipElement()).hasClass(CLASS_NAME_SHOW$4)) {
          this._leave(null, this);

          return;
        }

        this._enter(null, this);
      }
    };

    _proto.dispose = function dispose() {
      clearTimeout(this._timeout);
      $.removeData(this.element, this.constructor.DATA_KEY);
      $(this.element).off(this.constructor.EVENT_KEY);
      $(this.element).closest('.modal').off('hide.bs.modal', this._hideModalHandler);

      if (this.tip) {
        $(this.tip).remove();
      }

      this._isEnabled = null;
      this._timeout = null;
      this._hoverState = null;
      this._activeTrigger = null;

      if (this._popper) {
        this._popper.destroy();
      }

      this._popper = null;
      this.element = null;
      this.config = null;
      this.tip = null;
    };

    _proto.show = function show() {
      var _this = this;

      if ($(this.element).css('display') === 'none') {
        throw new Error('Please use show on visible elements');
      }

      var showEvent = $.Event(this.constructor.Event.SHOW);

      if (this.isWithContent() && this._isEnabled) {
        $(this.element).trigger(showEvent);
        var shadowRoot = Util.findShadowRoot(this.element);
        var isInTheDom = $.contains(shadowRoot !== null ? shadowRoot : this.element.ownerDocument.documentElement, this.element);

        if (showEvent.isDefaultPrevented() || !isInTheDom) {
          return;
        }

        var tip = this.getTipElement();
        var tipId = Util.getUID(this.constructor.NAME);
        tip.setAttribute('id', tipId);
        this.element.setAttribute('aria-describedby', tipId);
        this.setContent();

        if (this.config.animation) {
          $(tip).addClass(CLASS_NAME_FADE$2);
        }

        var placement = typeof this.config.placement === 'function' ? this.config.placement.call(this, tip, this.element) : this.config.placement;

        var attachment = this._getAttachment(placement);

        this.addAttachmentClass(attachment);

        var container = this._getContainer();

        $(tip).data(this.constructor.DATA_KEY, this);

        if (!$.contains(this.element.ownerDocument.documentElement, this.tip)) {
          $(tip).appendTo(container);
        }

        $(this.element).trigger(this.constructor.Event.INSERTED);
        this._popper = new Popper(this.element, tip, this._getPopperConfig(attachment));
        $(tip).addClass(CLASS_NAME_SHOW$4); // If this is a touch-enabled device we add extra
        // empty mouseover listeners to the body's immediate children;
        // only needed because of broken event delegation on iOS
        // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html

        if ('ontouchstart' in document.documentElement) {
          $(document.body).children().on('mouseover', null, $.noop);
        }

        var complete = function complete() {
          if (_this.config.animation) {
            _this._fixTransition();
          }

          var prevHoverState = _this._hoverState;
          _this._hoverState = null;
          $(_this.element).trigger(_this.constructor.Event.SHOWN);

          if (prevHoverState === HOVER_STATE_OUT) {
            _this._leave(null, _this);
          }
        };

        if ($(this.tip).hasClass(CLASS_NAME_FADE$2)) {
          var transitionDuration = Util.getTransitionDurationFromElement(this.tip);
          $(this.tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
        } else {
          complete();
        }
      }
    };

    _proto.hide = function hide(callback) {
      var _this2 = this;

      var tip = this.getTipElement();
      var hideEvent = $.Event(this.constructor.Event.HIDE);

      var complete = function complete() {
        if (_this2._hoverState !== HOVER_STATE_SHOW && tip.parentNode) {
          tip.parentNode.removeChild(tip);
        }

        _this2._cleanTipClass();

        _this2.element.removeAttribute('aria-describedby');

        $(_this2.element).trigger(_this2.constructor.Event.HIDDEN);

        if (_this2._popper !== null) {
          _this2._popper.destroy();
        }

        if (callback) {
          callback();
        }
      };

      $(this.element).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      $(tip).removeClass(CLASS_NAME_SHOW$4); // If this is a touch-enabled device we remove the extra
      // empty mouseover listeners we added for iOS support

      if ('ontouchstart' in document.documentElement) {
        $(document.body).children().off('mouseover', null, $.noop);
      }

      this._activeTrigger[TRIGGER_CLICK] = false;
      this._activeTrigger[TRIGGER_FOCUS] = false;
      this._activeTrigger[TRIGGER_HOVER] = false;

      if ($(this.tip).hasClass(CLASS_NAME_FADE$2)) {
        var transitionDuration = Util.getTransitionDurationFromElement(tip);
        $(tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }

      this._hoverState = '';
    };

    _proto.update = function update() {
      if (this._popper !== null) {
        this._popper.scheduleUpdate();
      }
    } // Protected
    ;

    _proto.isWithContent = function isWithContent() {
      return Boolean(this.getTitle());
    };

    _proto.addAttachmentClass = function addAttachmentClass(attachment) {
      $(this.getTipElement()).addClass(CLASS_PREFIX + "-" + attachment);
    };

    _proto.getTipElement = function getTipElement() {
      this.tip = this.tip || $(this.config.template)[0];
      return this.tip;
    };

    _proto.setContent = function setContent() {
      var tip = this.getTipElement();
      this.setElementContent($(tip.querySelectorAll(SELECTOR_TOOLTIP_INNER)), this.getTitle());
      $(tip).removeClass(CLASS_NAME_FADE$2 + " " + CLASS_NAME_SHOW$4);
    };

    _proto.setElementContent = function setElementContent($element, content) {
      if (typeof content === 'object' && (content.nodeType || content.jquery)) {
        // Content is a DOM node or a jQuery
        if (this.config.html) {
          if (!$(content).parent().is($element)) {
            $element.empty().append(content);
          }
        } else {
          $element.text($(content).text());
        }

        return;
      }

      if (this.config.html) {
        if (this.config.sanitize) {
          content = sanitizeHtml(content, this.config.whiteList, this.config.sanitizeFn);
        }

        $element.html(content);
      } else {
        $element.text(content);
      }
    };

    _proto.getTitle = function getTitle() {
      var title = this.element.getAttribute('data-original-title');

      if (!title) {
        title = typeof this.config.title === 'function' ? this.config.title.call(this.element) : this.config.title;
      }

      return title;
    } // Private
    ;

    _proto._getPopperConfig = function _getPopperConfig(attachment) {
      var _this3 = this;

      var defaultBsConfig = {
        placement: attachment,
        modifiers: {
          offset: this._getOffset(),
          flip: {
            behavior: this.config.fallbackPlacement
          },
          arrow: {
            element: SELECTOR_ARROW
          },
          preventOverflow: {
            boundariesElement: this.config.boundary
          }
        },
        onCreate: function onCreate(data) {
          if (data.originalPlacement !== data.placement) {
            _this3._handlePopperPlacementChange(data);
          }
        },
        onUpdate: function onUpdate(data) {
          return _this3._handlePopperPlacementChange(data);
        }
      };
      return _objectSpread2(_objectSpread2({}, defaultBsConfig), this.config.popperConfig);
    };

    _proto._getOffset = function _getOffset() {
      var _this4 = this;

      var offset = {};

      if (typeof this.config.offset === 'function') {
        offset.fn = function (data) {
          data.offsets = _objectSpread2(_objectSpread2({}, data.offsets), _this4.config.offset(data.offsets, _this4.element) || {});
          return data;
        };
      } else {
        offset.offset = this.config.offset;
      }

      return offset;
    };

    _proto._getContainer = function _getContainer() {
      if (this.config.container === false) {
        return document.body;
      }

      if (Util.isElement(this.config.container)) {
        return $(this.config.container);
      }

      return $(document).find(this.config.container);
    };

    _proto._getAttachment = function _getAttachment(placement) {
      return AttachmentMap[placement.toUpperCase()];
    };

    _proto._setListeners = function _setListeners() {
      var _this5 = this;

      var triggers = this.config.trigger.split(' ');
      triggers.forEach(function (trigger) {
        if (trigger === 'click') {
          $(_this5.element).on(_this5.constructor.Event.CLICK, _this5.config.selector, function (event) {
            return _this5.toggle(event);
          });
        } else if (trigger !== TRIGGER_MANUAL) {
          var eventIn = trigger === TRIGGER_HOVER ? _this5.constructor.Event.MOUSEENTER : _this5.constructor.Event.FOCUSIN;
          var eventOut = trigger === TRIGGER_HOVER ? _this5.constructor.Event.MOUSELEAVE : _this5.constructor.Event.FOCUSOUT;
          $(_this5.element).on(eventIn, _this5.config.selector, function (event) {
            return _this5._enter(event);
          }).on(eventOut, _this5.config.selector, function (event) {
            return _this5._leave(event);
          });
        }
      });

      this._hideModalHandler = function () {
        if (_this5.element) {
          _this5.hide();
        }
      };

      $(this.element).closest('.modal').on('hide.bs.modal', this._hideModalHandler);

      if (this.config.selector) {
        this.config = _objectSpread2(_objectSpread2({}, this.config), {}, {
          trigger: 'manual',
          selector: ''
        });
      } else {
        this._fixTitle();
      }
    };

    _proto._fixTitle = function _fixTitle() {
      var titleType = typeof this.element.getAttribute('data-original-title');

      if (this.element.getAttribute('title') || titleType !== 'string') {
        this.element.setAttribute('data-original-title', this.element.getAttribute('title') || '');
        this.element.setAttribute('title', '');
      }
    };

    _proto._enter = function _enter(event, context) {
      var dataKey = this.constructor.DATA_KEY;
      context = context || $(event.currentTarget).data(dataKey);

      if (!context) {
        context = new this.constructor(event.currentTarget, this._getDelegateConfig());
        $(event.currentTarget).data(dataKey, context);
      }

      if (event) {
        context._activeTrigger[event.type === 'focusin' ? TRIGGER_FOCUS : TRIGGER_HOVER] = true;
      }

      if ($(context.getTipElement()).hasClass(CLASS_NAME_SHOW$4) || context._hoverState === HOVER_STATE_SHOW) {
        context._hoverState = HOVER_STATE_SHOW;
        return;
      }

      clearTimeout(context._timeout);
      context._hoverState = HOVER_STATE_SHOW;

      if (!context.config.delay || !context.config.delay.show) {
        context.show();
        return;
      }

      context._timeout = setTimeout(function () {
        if (context._hoverState === HOVER_STATE_SHOW) {
          context.show();
        }
      }, context.config.delay.show);
    };

    _proto._leave = function _leave(event, context) {
      var dataKey = this.constructor.DATA_KEY;
      context = context || $(event.currentTarget).data(dataKey);

      if (!context) {
        context = new this.constructor(event.currentTarget, this._getDelegateConfig());
        $(event.currentTarget).data(dataKey, context);
      }

      if (event) {
        context._activeTrigger[event.type === 'focusout' ? TRIGGER_FOCUS : TRIGGER_HOVER] = false;
      }

      if (context._isWithActiveTrigger()) {
        return;
      }

      clearTimeout(context._timeout);
      context._hoverState = HOVER_STATE_OUT;

      if (!context.config.delay || !context.config.delay.hide) {
        context.hide();
        return;
      }

      context._timeout = setTimeout(function () {
        if (context._hoverState === HOVER_STATE_OUT) {
          context.hide();
        }
      }, context.config.delay.hide);
    };

    _proto._isWithActiveTrigger = function _isWithActiveTrigger() {
      for (var trigger in this._activeTrigger) {
        if (this._activeTrigger[trigger]) {
          return true;
        }
      }

      return false;
    };

    _proto._getConfig = function _getConfig(config) {
      var dataAttributes = $(this.element).data();
      Object.keys(dataAttributes).forEach(function (dataAttr) {
        if (DISALLOWED_ATTRIBUTES.indexOf(dataAttr) !== -1) {
          delete dataAttributes[dataAttr];
        }
      });
      config = _objectSpread2(_objectSpread2(_objectSpread2({}, this.constructor.Default), dataAttributes), typeof config === 'object' && config ? config : {});

      if (typeof config.delay === 'number') {
        config.delay = {
          show: config.delay,
          hide: config.delay
        };
      }

      if (typeof config.title === 'number') {
        config.title = config.title.toString();
      }

      if (typeof config.content === 'number') {
        config.content = config.content.toString();
      }

      Util.typeCheckConfig(NAME$6, config, this.constructor.DefaultType);

      if (config.sanitize) {
        config.template = sanitizeHtml(config.template, config.whiteList, config.sanitizeFn);
      }

      return config;
    };

    _proto._getDelegateConfig = function _getDelegateConfig() {
      var config = {};

      if (this.config) {
        for (var key in this.config) {
          if (this.constructor.Default[key] !== this.config[key]) {
            config[key] = this.config[key];
          }
        }
      }

      return config;
    };

    _proto._cleanTipClass = function _cleanTipClass() {
      var $tip = $(this.getTipElement());
      var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX);

      if (tabClass !== null && tabClass.length) {
        $tip.removeClass(tabClass.join(''));
      }
    };

    _proto._handlePopperPlacementChange = function _handlePopperPlacementChange(popperData) {
      this.tip = popperData.instance.popper;

      this._cleanTipClass();

      this.addAttachmentClass(this._getAttachment(popperData.placement));
    };

    _proto._fixTransition = function _fixTransition() {
      var tip = this.getTipElement();
      var initConfigAnimation = this.config.animation;

      if (tip.getAttribute('x-placement') !== null) {
        return;
      }

      $(tip).removeClass(CLASS_NAME_FADE$2);
      this.config.animation = false;
      this.hide();
      this.show();
      this.config.animation = initConfigAnimation;
    } // Static
    ;

    Tooltip._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$6);

        var _config = typeof config === 'object' && config;

        if (!data && /dispose|hide/.test(config)) {
          return;
        }

        if (!data) {
          data = new Tooltip(this, _config);
          $(this).data(DATA_KEY$6, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Tooltip, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$6;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$4;
      }
    }, {
      key: "NAME",
      get: function get() {
        return NAME$6;
      }
    }, {
      key: "DATA_KEY",
      get: function get() {
        return DATA_KEY$6;
      }
    }, {
      key: "Event",
      get: function get() {
        return Event;
      }
    }, {
      key: "EVENT_KEY",
      get: function get() {
        return EVENT_KEY$6;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$4;
      }
    }]);

    return Tooltip;
  }();
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$6] = Tooltip._jQueryInterface;
  $.fn[NAME$6].Constructor = Tooltip;

  $.fn[NAME$6].noConflict = function () {
    $.fn[NAME$6] = JQUERY_NO_CONFLICT$6;
    return Tooltip._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$7 = 'popover';
  var VERSION$7 = '4.5.0';
  var DATA_KEY$7 = 'bs.popover';
  var EVENT_KEY$7 = "." + DATA_KEY$7;
  var JQUERY_NO_CONFLICT$7 = $.fn[NAME$7];
  var CLASS_PREFIX$1 = 'bs-popover';
  var BSCLS_PREFIX_REGEX$1 = new RegExp("(^|\\s)" + CLASS_PREFIX$1 + "\\S+", 'g');

  var Default$5 = _objectSpread2(_objectSpread2({}, Tooltip.Default), {}, {
    placement: 'right',
    trigger: 'click',
    content: '',
    template: '<div class="popover" role="tooltip">' + '<div class="arrow"></div>' + '<h3 class="popover-header"></h3>' + '<div class="popover-body"></div></div>'
  });

  var DefaultType$5 = _objectSpread2(_objectSpread2({}, Tooltip.DefaultType), {}, {
    content: '(string|element|function)'
  });

  var CLASS_NAME_FADE$3 = 'fade';
  var CLASS_NAME_SHOW$5 = 'show';
  var SELECTOR_TITLE = '.popover-header';
  var SELECTOR_CONTENT = '.popover-body';
  var Event$1 = {
    HIDE: "hide" + EVENT_KEY$7,
    HIDDEN: "hidden" + EVENT_KEY$7,
    SHOW: "show" + EVENT_KEY$7,
    SHOWN: "shown" + EVENT_KEY$7,
    INSERTED: "inserted" + EVENT_KEY$7,
    CLICK: "click" + EVENT_KEY$7,
    FOCUSIN: "focusin" + EVENT_KEY$7,
    FOCUSOUT: "focusout" + EVENT_KEY$7,
    MOUSEENTER: "mouseenter" + EVENT_KEY$7,
    MOUSELEAVE: "mouseleave" + EVENT_KEY$7
  };
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Popover = /*#__PURE__*/function (_Tooltip) {
    _inheritsLoose(Popover, _Tooltip);

    function Popover() {
      return _Tooltip.apply(this, arguments) || this;
    }

    var _proto = Popover.prototype;

    // Overrides
    _proto.isWithContent = function isWithContent() {
      return this.getTitle() || this._getContent();
    };

    _proto.addAttachmentClass = function addAttachmentClass(attachment) {
      $(this.getTipElement()).addClass(CLASS_PREFIX$1 + "-" + attachment);
    };

    _proto.getTipElement = function getTipElement() {
      this.tip = this.tip || $(this.config.template)[0];
      return this.tip;
    };

    _proto.setContent = function setContent() {
      var $tip = $(this.getTipElement()); // We use append for html objects to maintain js events

      this.setElementContent($tip.find(SELECTOR_TITLE), this.getTitle());

      var content = this._getContent();

      if (typeof content === 'function') {
        content = content.call(this.element);
      }

      this.setElementContent($tip.find(SELECTOR_CONTENT), content);
      $tip.removeClass(CLASS_NAME_FADE$3 + " " + CLASS_NAME_SHOW$5);
    } // Private
    ;

    _proto._getContent = function _getContent() {
      return this.element.getAttribute('data-content') || this.config.content;
    };

    _proto._cleanTipClass = function _cleanTipClass() {
      var $tip = $(this.getTipElement());
      var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX$1);

      if (tabClass !== null && tabClass.length > 0) {
        $tip.removeClass(tabClass.join(''));
      }
    } // Static
    ;

    Popover._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$7);

        var _config = typeof config === 'object' ? config : null;

        if (!data && /dispose|hide/.test(config)) {
          return;
        }

        if (!data) {
          data = new Popover(this, _config);
          $(this).data(DATA_KEY$7, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Popover, null, [{
      key: "VERSION",
      // Getters
      get: function get() {
        return VERSION$7;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$5;
      }
    }, {
      key: "NAME",
      get: function get() {
        return NAME$7;
      }
    }, {
      key: "DATA_KEY",
      get: function get() {
        return DATA_KEY$7;
      }
    }, {
      key: "Event",
      get: function get() {
        return Event$1;
      }
    }, {
      key: "EVENT_KEY",
      get: function get() {
        return EVENT_KEY$7;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$5;
      }
    }]);

    return Popover;
  }(Tooltip);
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$7] = Popover._jQueryInterface;
  $.fn[NAME$7].Constructor = Popover;

  $.fn[NAME$7].noConflict = function () {
    $.fn[NAME$7] = JQUERY_NO_CONFLICT$7;
    return Popover._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$8 = 'scrollspy';
  var VERSION$8 = '4.5.0';
  var DATA_KEY$8 = 'bs.scrollspy';
  var EVENT_KEY$8 = "." + DATA_KEY$8;
  var DATA_API_KEY$6 = '.data-api';
  var JQUERY_NO_CONFLICT$8 = $.fn[NAME$8];
  var Default$6 = {
    offset: 10,
    method: 'auto',
    target: ''
  };
  var DefaultType$6 = {
    offset: 'number',
    method: 'string',
    target: '(string|element)'
  };
  var EVENT_ACTIVATE = "activate" + EVENT_KEY$8;
  var EVENT_SCROLL = "scroll" + EVENT_KEY$8;
  var EVENT_LOAD_DATA_API$2 = "load" + EVENT_KEY$8 + DATA_API_KEY$6;
  var CLASS_NAME_DROPDOWN_ITEM = 'dropdown-item';
  var CLASS_NAME_ACTIVE$2 = 'active';
  var SELECTOR_DATA_SPY = '[data-spy="scroll"]';
  var SELECTOR_NAV_LIST_GROUP = '.nav, .list-group';
  var SELECTOR_NAV_LINKS = '.nav-link';
  var SELECTOR_NAV_ITEMS = '.nav-item';
  var SELECTOR_LIST_ITEMS = '.list-group-item';
  var SELECTOR_DROPDOWN = '.dropdown';
  var SELECTOR_DROPDOWN_ITEMS = '.dropdown-item';
  var SELECTOR_DROPDOWN_TOGGLE = '.dropdown-toggle';
  var METHOD_OFFSET = 'offset';
  var METHOD_POSITION = 'position';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var ScrollSpy = /*#__PURE__*/function () {
    function ScrollSpy(element, config) {
      var _this = this;

      this._element = element;
      this._scrollElement = element.tagName === 'BODY' ? window : element;
      this._config = this._getConfig(config);
      this._selector = this._config.target + " " + SELECTOR_NAV_LINKS + "," + (this._config.target + " " + SELECTOR_LIST_ITEMS + ",") + (this._config.target + " " + SELECTOR_DROPDOWN_ITEMS);
      this._offsets = [];
      this._targets = [];
      this._activeTarget = null;
      this._scrollHeight = 0;
      $(this._scrollElement).on(EVENT_SCROLL, function (event) {
        return _this._process(event);
      });
      this.refresh();

      this._process();
    } // Getters


    var _proto = ScrollSpy.prototype;

    // Public
    _proto.refresh = function refresh() {
      var _this2 = this;

      var autoMethod = this._scrollElement === this._scrollElement.window ? METHOD_OFFSET : METHOD_POSITION;
      var offsetMethod = this._config.method === 'auto' ? autoMethod : this._config.method;
      var offsetBase = offsetMethod === METHOD_POSITION ? this._getScrollTop() : 0;
      this._offsets = [];
      this._targets = [];
      this._scrollHeight = this._getScrollHeight();
      var targets = [].slice.call(document.querySelectorAll(this._selector));
      targets.map(function (element) {
        var target;
        var targetSelector = Util.getSelectorFromElement(element);

        if (targetSelector) {
          target = document.querySelector(targetSelector);
        }

        if (target) {
          var targetBCR = target.getBoundingClientRect();

          if (targetBCR.width || targetBCR.height) {
            // TODO (fat): remove sketch reliance on jQuery position/offset
            return [$(target)[offsetMethod]().top + offsetBase, targetSelector];
          }
        }

        return null;
      }).filter(function (item) {
        return item;
      }).sort(function (a, b) {
        return a[0] - b[0];
      }).forEach(function (item) {
        _this2._offsets.push(item[0]);

        _this2._targets.push(item[1]);
      });
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$8);
      $(this._scrollElement).off(EVENT_KEY$8);
      this._element = null;
      this._scrollElement = null;
      this._config = null;
      this._selector = null;
      this._offsets = null;
      this._targets = null;
      this._activeTarget = null;
      this._scrollHeight = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2({}, Default$6), typeof config === 'object' && config ? config : {});

      if (typeof config.target !== 'string' && Util.isElement(config.target)) {
        var id = $(config.target).attr('id');

        if (!id) {
          id = Util.getUID(NAME$8);
          $(config.target).attr('id', id);
        }

        config.target = "#" + id;
      }

      Util.typeCheckConfig(NAME$8, config, DefaultType$6);
      return config;
    };

    _proto._getScrollTop = function _getScrollTop() {
      return this._scrollElement === window ? this._scrollElement.pageYOffset : this._scrollElement.scrollTop;
    };

    _proto._getScrollHeight = function _getScrollHeight() {
      return this._scrollElement.scrollHeight || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    };

    _proto._getOffsetHeight = function _getOffsetHeight() {
      return this._scrollElement === window ? window.innerHeight : this._scrollElement.getBoundingClientRect().height;
    };

    _proto._process = function _process() {
      var scrollTop = this._getScrollTop() + this._config.offset;

      var scrollHeight = this._getScrollHeight();

      var maxScroll = this._config.offset + scrollHeight - this._getOffsetHeight();

      if (this._scrollHeight !== scrollHeight) {
        this.refresh();
      }

      if (scrollTop >= maxScroll) {
        var target = this._targets[this._targets.length - 1];

        if (this._activeTarget !== target) {
          this._activate(target);
        }

        return;
      }

      if (this._activeTarget && scrollTop < this._offsets[0] && this._offsets[0] > 0) {
        this._activeTarget = null;

        this._clear();

        return;
      }

      for (var i = this._offsets.length; i--;) {
        var isActiveTarget = this._activeTarget !== this._targets[i] && scrollTop >= this._offsets[i] && (typeof this._offsets[i + 1] === 'undefined' || scrollTop < this._offsets[i + 1]);

        if (isActiveTarget) {
          this._activate(this._targets[i]);
        }
      }
    };

    _proto._activate = function _activate(target) {
      this._activeTarget = target;

      this._clear();

      var queries = this._selector.split(',').map(function (selector) {
        return selector + "[data-target=\"" + target + "\"]," + selector + "[href=\"" + target + "\"]";
      });

      var $link = $([].slice.call(document.querySelectorAll(queries.join(','))));

      if ($link.hasClass(CLASS_NAME_DROPDOWN_ITEM)) {
        $link.closest(SELECTOR_DROPDOWN).find(SELECTOR_DROPDOWN_TOGGLE).addClass(CLASS_NAME_ACTIVE$2);
        $link.addClass(CLASS_NAME_ACTIVE$2);
      } else {
        // Set triggered link as active
        $link.addClass(CLASS_NAME_ACTIVE$2); // Set triggered links parents as active
        // With both <ul> and <nav> markup a parent is the previous sibling of any nav ancestor

        $link.parents(SELECTOR_NAV_LIST_GROUP).prev(SELECTOR_NAV_LINKS + ", " + SELECTOR_LIST_ITEMS).addClass(CLASS_NAME_ACTIVE$2); // Handle special case when .nav-link is inside .nav-item

        $link.parents(SELECTOR_NAV_LIST_GROUP).prev(SELECTOR_NAV_ITEMS).children(SELECTOR_NAV_LINKS).addClass(CLASS_NAME_ACTIVE$2);
      }

      $(this._scrollElement).trigger(EVENT_ACTIVATE, {
        relatedTarget: target
      });
    };

    _proto._clear = function _clear() {
      [].slice.call(document.querySelectorAll(this._selector)).filter(function (node) {
        return node.classList.contains(CLASS_NAME_ACTIVE$2);
      }).forEach(function (node) {
        return node.classList.remove(CLASS_NAME_ACTIVE$2);
      });
    } // Static
    ;

    ScrollSpy._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var data = $(this).data(DATA_KEY$8);

        var _config = typeof config === 'object' && config;

        if (!data) {
          data = new ScrollSpy(this, _config);
          $(this).data(DATA_KEY$8, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(ScrollSpy, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$8;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$6;
      }
    }]);

    return ScrollSpy;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(window).on(EVENT_LOAD_DATA_API$2, function () {
    var scrollSpys = [].slice.call(document.querySelectorAll(SELECTOR_DATA_SPY));
    var scrollSpysLength = scrollSpys.length;

    for (var i = scrollSpysLength; i--;) {
      var $spy = $(scrollSpys[i]);

      ScrollSpy._jQueryInterface.call($spy, $spy.data());
    }
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$8] = ScrollSpy._jQueryInterface;
  $.fn[NAME$8].Constructor = ScrollSpy;

  $.fn[NAME$8].noConflict = function () {
    $.fn[NAME$8] = JQUERY_NO_CONFLICT$8;
    return ScrollSpy._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$9 = 'tab';
  var VERSION$9 = '4.5.0';
  var DATA_KEY$9 = 'bs.tab';
  var EVENT_KEY$9 = "." + DATA_KEY$9;
  var DATA_API_KEY$7 = '.data-api';
  var JQUERY_NO_CONFLICT$9 = $.fn[NAME$9];
  var EVENT_HIDE$3 = "hide" + EVENT_KEY$9;
  var EVENT_HIDDEN$3 = "hidden" + EVENT_KEY$9;
  var EVENT_SHOW$3 = "show" + EVENT_KEY$9;
  var EVENT_SHOWN$3 = "shown" + EVENT_KEY$9;
  var EVENT_CLICK_DATA_API$6 = "click" + EVENT_KEY$9 + DATA_API_KEY$7;
  var CLASS_NAME_DROPDOWN_MENU = 'dropdown-menu';
  var CLASS_NAME_ACTIVE$3 = 'active';
  var CLASS_NAME_DISABLED$1 = 'disabled';
  var CLASS_NAME_FADE$4 = 'fade';
  var CLASS_NAME_SHOW$6 = 'show';
  var SELECTOR_DROPDOWN$1 = '.dropdown';
  var SELECTOR_NAV_LIST_GROUP$1 = '.nav, .list-group';
  var SELECTOR_ACTIVE$2 = '.active';
  var SELECTOR_ACTIVE_UL = '> li > .active';
  var SELECTOR_DATA_TOGGLE$4 = '[data-toggle="tab"], [data-toggle="pill"], [data-toggle="list"]';
  var SELECTOR_DROPDOWN_TOGGLE$1 = '.dropdown-toggle';
  var SELECTOR_DROPDOWN_ACTIVE_CHILD = '> .dropdown-menu .active';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Tab = /*#__PURE__*/function () {
    function Tab(element) {
      this._element = element;
    } // Getters


    var _proto = Tab.prototype;

    // Public
    _proto.show = function show() {
      var _this = this;

      if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && $(this._element).hasClass(CLASS_NAME_ACTIVE$3) || $(this._element).hasClass(CLASS_NAME_DISABLED$1)) {
        return;
      }

      var target;
      var previous;
      var listElement = $(this._element).closest(SELECTOR_NAV_LIST_GROUP$1)[0];
      var selector = Util.getSelectorFromElement(this._element);

      if (listElement) {
        var itemSelector = listElement.nodeName === 'UL' || listElement.nodeName === 'OL' ? SELECTOR_ACTIVE_UL : SELECTOR_ACTIVE$2;
        previous = $.makeArray($(listElement).find(itemSelector));
        previous = previous[previous.length - 1];
      }

      var hideEvent = $.Event(EVENT_HIDE$3, {
        relatedTarget: this._element
      });
      var showEvent = $.Event(EVENT_SHOW$3, {
        relatedTarget: previous
      });

      if (previous) {
        $(previous).trigger(hideEvent);
      }

      $(this._element).trigger(showEvent);

      if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
        return;
      }

      if (selector) {
        target = document.querySelector(selector);
      }

      this._activate(this._element, listElement);

      var complete = function complete() {
        var hiddenEvent = $.Event(EVENT_HIDDEN$3, {
          relatedTarget: _this._element
        });
        var shownEvent = $.Event(EVENT_SHOWN$3, {
          relatedTarget: previous
        });
        $(previous).trigger(hiddenEvent);
        $(_this._element).trigger(shownEvent);
      };

      if (target) {
        this._activate(target, target.parentNode, complete);
      } else {
        complete();
      }
    };

    _proto.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY$9);
      this._element = null;
    } // Private
    ;

    _proto._activate = function _activate(element, container, callback) {
      var _this2 = this;

      var activeElements = container && (container.nodeName === 'UL' || container.nodeName === 'OL') ? $(container).find(SELECTOR_ACTIVE_UL) : $(container).children(SELECTOR_ACTIVE$2);
      var active = activeElements[0];
      var isTransitioning = callback && active && $(active).hasClass(CLASS_NAME_FADE$4);

      var complete = function complete() {
        return _this2._transitionComplete(element, active, callback);
      };

      if (active && isTransitioning) {
        var transitionDuration = Util.getTransitionDurationFromElement(active);
        $(active).removeClass(CLASS_NAME_SHOW$6).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    };

    _proto._transitionComplete = function _transitionComplete(element, active, callback) {
      if (active) {
        $(active).removeClass(CLASS_NAME_ACTIVE$3);
        var dropdownChild = $(active.parentNode).find(SELECTOR_DROPDOWN_ACTIVE_CHILD)[0];

        if (dropdownChild) {
          $(dropdownChild).removeClass(CLASS_NAME_ACTIVE$3);
        }

        if (active.getAttribute('role') === 'tab') {
          active.setAttribute('aria-selected', false);
        }
      }

      $(element).addClass(CLASS_NAME_ACTIVE$3);

      if (element.getAttribute('role') === 'tab') {
        element.setAttribute('aria-selected', true);
      }

      Util.reflow(element);

      if (element.classList.contains(CLASS_NAME_FADE$4)) {
        element.classList.add(CLASS_NAME_SHOW$6);
      }

      if (element.parentNode && $(element.parentNode).hasClass(CLASS_NAME_DROPDOWN_MENU)) {
        var dropdownElement = $(element).closest(SELECTOR_DROPDOWN$1)[0];

        if (dropdownElement) {
          var dropdownToggleList = [].slice.call(dropdownElement.querySelectorAll(SELECTOR_DROPDOWN_TOGGLE$1));
          $(dropdownToggleList).addClass(CLASS_NAME_ACTIVE$3);
        }

        element.setAttribute('aria-expanded', true);
      }

      if (callback) {
        callback();
      }
    } // Static
    ;

    Tab._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY$9);

        if (!data) {
          data = new Tab(this);
          $this.data(DATA_KEY$9, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config]();
        }
      });
    };

    _createClass(Tab, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$9;
      }
    }]);

    return Tab;
  }();
  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */


  $(document).on(EVENT_CLICK_DATA_API$6, SELECTOR_DATA_TOGGLE$4, function (event) {
    event.preventDefault();

    Tab._jQueryInterface.call($(this), 'show');
  });
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME$9] = Tab._jQueryInterface;
  $.fn[NAME$9].Constructor = Tab;

  $.fn[NAME$9].noConflict = function () {
    $.fn[NAME$9] = JQUERY_NO_CONFLICT$9;
    return Tab._jQueryInterface;
  };

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  var NAME$a = 'toast';
  var VERSION$a = '4.5.0';
  var DATA_KEY$a = 'bs.toast';
  var EVENT_KEY$a = "." + DATA_KEY$a;
  var JQUERY_NO_CONFLICT$a = $.fn[NAME$a];
  var EVENT_CLICK_DISMISS$1 = "click.dismiss" + EVENT_KEY$a;
  var EVENT_HIDE$4 = "hide" + EVENT_KEY$a;
  var EVENT_HIDDEN$4 = "hidden" + EVENT_KEY$a;
  var EVENT_SHOW$4 = "show" + EVENT_KEY$a;
  var EVENT_SHOWN$4 = "shown" + EVENT_KEY$a;
  var CLASS_NAME_FADE$5 = 'fade';
  var CLASS_NAME_HIDE = 'hide';
  var CLASS_NAME_SHOW$7 = 'show';
  var CLASS_NAME_SHOWING = 'showing';
  var DefaultType$7 = {
    animation: 'boolean',
    autohide: 'boolean',
    delay: 'number'
  };
  var Default$7 = {
    animation: true,
    autohide: true,
    delay: 500
  };
  var SELECTOR_DATA_DISMISS$1 = '[data-dismiss="toast"]';
  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  var Toast = /*#__PURE__*/function () {
    function Toast(element, config) {
      this._element = element;
      this._config = this._getConfig(config);
      this._timeout = null;

      this._setListeners();
    } // Getters


    var _proto = Toast.prototype;

    // Public
    _proto.show = function show() {
      var _this = this;

      var showEvent = $.Event(EVENT_SHOW$4);
      $(this._element).trigger(showEvent);

      if (showEvent.isDefaultPrevented()) {
        return;
      }

      if (this._config.animation) {
        this._element.classList.add(CLASS_NAME_FADE$5);
      }

      var complete = function complete() {
        _this._element.classList.remove(CLASS_NAME_SHOWING);

        _this._element.classList.add(CLASS_NAME_SHOW$7);

        $(_this._element).trigger(EVENT_SHOWN$4);

        if (_this._config.autohide) {
          _this._timeout = setTimeout(function () {
            _this.hide();
          }, _this._config.delay);
        }
      };

      this._element.classList.remove(CLASS_NAME_HIDE);

      Util.reflow(this._element);

      this._element.classList.add(CLASS_NAME_SHOWING);

      if (this._config.animation) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    };

    _proto.hide = function hide() {
      if (!this._element.classList.contains(CLASS_NAME_SHOW$7)) {
        return;
      }

      var hideEvent = $.Event(EVENT_HIDE$4);
      $(this._element).trigger(hideEvent);

      if (hideEvent.isDefaultPrevented()) {
        return;
      }

      this._close();
    };

    _proto.dispose = function dispose() {
      clearTimeout(this._timeout);
      this._timeout = null;

      if (this._element.classList.contains(CLASS_NAME_SHOW$7)) {
        this._element.classList.remove(CLASS_NAME_SHOW$7);
      }

      $(this._element).off(EVENT_CLICK_DISMISS$1);
      $.removeData(this._element, DATA_KEY$a);
      this._element = null;
      this._config = null;
    } // Private
    ;

    _proto._getConfig = function _getConfig(config) {
      config = _objectSpread2(_objectSpread2(_objectSpread2({}, Default$7), $(this._element).data()), typeof config === 'object' && config ? config : {});
      Util.typeCheckConfig(NAME$a, config, this.constructor.DefaultType);
      return config;
    };

    _proto._setListeners = function _setListeners() {
      var _this2 = this;

      $(this._element).on(EVENT_CLICK_DISMISS$1, SELECTOR_DATA_DISMISS$1, function () {
        return _this2.hide();
      });
    };

    _proto._close = function _close() {
      var _this3 = this;

      var complete = function complete() {
        _this3._element.classList.add(CLASS_NAME_HIDE);

        $(_this3._element).trigger(EVENT_HIDDEN$4);
      };

      this._element.classList.remove(CLASS_NAME_SHOW$7);

      if (this._config.animation) {
        var transitionDuration = Util.getTransitionDurationFromElement(this._element);
        $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(transitionDuration);
      } else {
        complete();
      }
    } // Static
    ;

    Toast._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $element = $(this);
        var data = $element.data(DATA_KEY$a);

        var _config = typeof config === 'object' && config;

        if (!data) {
          data = new Toast(this, _config);
          $element.data(DATA_KEY$a, data);
        }

        if (typeof config === 'string') {
          if (typeof data[config] === 'undefined') {
            throw new TypeError("No method named \"" + config + "\"");
          }

          data[config](this);
        }
      });
    };

    _createClass(Toast, null, [{
      key: "VERSION",
      get: function get() {
        return VERSION$a;
      }
    }, {
      key: "DefaultType",
      get: function get() {
        return DefaultType$7;
      }
    }, {
      key: "Default",
      get: function get() {
        return Default$7;
      }
    }]);

    return Toast;
  }();
  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */


  $.fn[NAME$a] = Toast._jQueryInterface;
  $.fn[NAME$a].Constructor = Toast;

  $.fn[NAME$a].noConflict = function () {
    $.fn[NAME$a] = JQUERY_NO_CONFLICT$a;
    return Toast._jQueryInterface;
  };

  exports.Alert = Alert;
  exports.Button = Button;
  exports.Carousel = Carousel;
  exports.Collapse = Collapse;
  exports.Dropdown = Dropdown;
  exports.Modal = Modal;
  exports.Popover = Popover;
  exports.Scrollspy = ScrollSpy;
  exports.Tab = Tab;
  exports.Toast = Toast;
  exports.Tooltip = Tooltip;
  exports.Util = Util;

  Object.defineProperty(exports, '__esModule', { value: true });

})));
//# sourceMappingURL=bootstrap.js.map

(function( $ ) {
    'use strict';

    $( document ).on( "click", ".ironikus-refresh", function() {
        location.reload();
    });

    var accordion = (function(){

        var $accordion = $('.irnks-accordion');
        var $accordion_header = $accordion.find('.irnks-accordion-header');
        var $accordion_item = $('.irnks-accordion-item');

        // default settings
        var settings = {
            speed: 400,
            oneOpen: false
        };

        return {
            init: function($settings) {
                $accordion_header.on('click', function() {
                    accordion.toggle($(this));
                });

                $.extend(settings, $settings);

                if(settings.oneOpen && $('.irnks-accordion-item.active').length > 1) {
                    $('.irnks-accordion-item.active:not(:first)').removeClass('active');
                }

                $('.irnks-accordion-item.active').find('> .irnks-accordion-body').show();
            },
            toggle: function($this) {
                $this.closest('.irnks-accordion-item').toggleClass('active');
                $this.next().stop().slideToggle(settings.speed);
            }
        }
    })();

    $(document).ready(function(){
        accordion.init({ speed: 300, oneOpen: true });
    });

    $( ".ironikus-save" ).on( "click", function() {

        var $this = $( this );

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        var webhook_id = $( $this ).attr( 'ironikus-webhook-trigger' );
        var $webhook_callback = $( $this ).attr( 'ironikus-webhook-callback' );
        var webhook_url_val = $( '#ironikus-webhook-url-' + webhook_id ).val();
        var webhook_slug_val = $( '#ironikus-webhook-slug-' + webhook_id ).val();
        var webhook_current_url = $( '#ironikus-webhook-current-url' ).val();

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_add_webhook_trigger',
                webhook_url : webhook_url_val,
                webhook_slug : webhook_slug_val,
                webhook_group : webhook_id,
                webhook_callback : $webhook_callback,
                current_url : webhook_current_url,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );
                    $( '#ironikus-webhook-url-' + webhook_id ).val( '' );
                    $( '#ironikus-webhook-slug-' + webhook_id ).val( '' );

                    if( $webhook['success'] != 'false' && $webhook['success'] != false ){
                        $( $this ).css( { 'background': '#00a73f' } );

                        var $webhook_html = '<tr id="ironikus-webhook-id-' + webhook_id + '-' + $webhook['webhook'] + '"><td>' + $webhook['webhook'] + '</td><td>';
                        $webhook_html += '<input class="ironikus-webhook-input" type="text" name="ironikus_wp_webhooks_pro_webhook_url" value="' + $webhook['webhook_url'] + '" readonly /><br>';
                        $webhook_html += '</td><td><div class="ironikus-element-actions">';
                        $webhook_html += '<span class="ironikus-delete" ironikus-delete="' + $webhook['webhook'] + '" ironikus-group="' + $webhook['webhook_group'] + '" >Delete</span><br>';
                        $webhook_html += '<span class="ironikus-status-action active" ironikus-webhook-status="active" ironikus-webhook-group="' + $webhook['webhook_group'] + '" ironikus-webhook-slug="' + $webhook['webhook'] + '">' + 'Deactivate' + '</span><br>';
                        $webhook_html += '<span class="ironikus-refresh">Refresh for Settings</span>';

                        if( $webhook['webhook_callback'] != '' ){
                            $webhook_html += '<br><span class="ironikus-send-demo" ironikus-demo-data-callback="' + $webhook['webhook_callback'] + '" ironikus-webhook="' + $webhook['webhook'] + '" ironikus-group="' + $webhook['webhook_group'] + '" >Send demo</span>';
                        }

                        $webhook_html += '</div></td></tr>';

                        $( '.ironikus-webhook-table.ironikus-group-' + webhook_id + ' > tbody' ).append( $webhook_html );
                    } else {
                        $( $this ).css( { 'background': '#a70000' } );
                        confirm( $webhook['msg'] );
                        
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );
                    $( $this ).css( { 'background': '#a70000' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            }
        } );

    });

    $( ".ironikus-action-save" ).on( "click", function() {

        var $this = $( this );
        var $webhook_slug = $( '#ironikus-webhook-action-name' ).val();

        if( ! $webhook_slug ){
            return;
        }

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_add_webhook_action',
                webhook_slug : $webhook_slug,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                console.log($webhook);

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    if( $webhook['success'] != 'false' && $webhook['success'] != false ){
                        $( $this ).css( { 'background': '#00a73f' } );

                        var $webhook_html = '<tr id="webhook-action-' + $webhook['webhook'] + '"><td>' + $webhook['webhook'] + '</td>';
                        $webhook_html += '<td>';
                        $webhook_html += '<input class="ironikus-webhook-input" type="text" name="ironikus_wp_webhooks_pro_webhook_url" value="' + $webhook['webhook_url'] + '" readonly /><br>';
                        $webhook_html += '</td>';
                        $webhook_html += '<td>';
                        $webhook_html += '<input class="ironikus-webhook-input" type="text" name="ironikus_wp_webhooks_pro_webhook_api_key" value="' + $webhook['webhook_api_key'] + '" readonly="">';
                        $webhook_html += '</td>';
                        $webhook_html += '<td>';
                        $webhook_html += '<div class="ironikus-element-actions">';

                        $webhook_html += '<span class="ironikus-delete-action" ironikus-webhook-slug="' + $webhook['webhook'] + '">' + $webhook['webhook_action_delete_name'] + '</span><br>';
                        $webhook_html += '<span class="ironikus-status-action active" ironikus-webhook-status="active" ironikus-webhook-slug="' + $webhook['webhook'] + '">' + 'Deactivate' + '</span><br>';
                        $webhook_html += '<span class="ironikus-refresh">Refresh for Settings</span>';

                        $webhook_html += '</div>';
                        $webhook_html += '</td>';
                        $webhook_html += '</tr>';

                        $( '.ironikus-webhook-table.ironikus-webhook-action-table > tbody' ).append( $webhook_html );
                        $( '#ironikus-webhook-action-name' ).val( '' );
                    } else {
                        $( $this ).css( { 'background': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );
                    $( $this ).css( { 'background': '#a70000' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            }
        } );

    });

    //Prefill action testing form
    $( document ).on( "change", ".wpwhpro-webhook-actions-webhook-select", function(e) {
        
        var $this = this;
        var $webhook_url = $( $this ).val();
        var $identkey = $( $this ).attr( 'wpwh-identkey' );

        if( $webhook_url == 'empty' ){
            $( '#wpwh-action-testing-form-'+$identkey ).css( { 'display': 'none' } );
        } else {
            $( '#wpwh-action-testing-form-'+$identkey ).css( { 'display': 'block' } );
        }

        $( '#wpwh-action-testing-form-'+$identkey ).attr( 'action', $webhook_url );

    });

    $( document ).on( "click", ".ironikus-delete", function() {

        if (confirm("Are you sure you want to delete this webhook?")){

            var $this = this;
            var $webhook = $( $this ).attr( 'ironikus-delete' );
            var $webhook_group = $( $this ).attr( 'ironikus-group' );

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_remove_webhook_trigger',
                    webhook : $webhook,
                    webhook_group : $webhook_group,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $webhook_response = $.parseJSON( $response );

                    if( $webhook_response['success'] != 'false' ){
                        $( '#ironikus-webhook-id-' + $webhook_group + '-' + $webhook ).remove();
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });

        }

    });

    $( document ).on( "click", ".ironikus-delete-action", function() {

        if (confirm("Are you sure you want to delete this webhook?")){

            var $this = this;
            var $webhook = $( $this ).attr( 'ironikus-webhook-slug' );

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_remove_webhook_action',
                    webhook : $webhook,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $webhook_response = $.parseJSON( $response );

                    console.log( $response );

                    if( $webhook_response['success'] != 'false' ){
                        $( '#webhook-action-' + $webhook ).remove();
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });

        }

    });

    $( document ).on( "click", ".ironikus-status-action", function() {

        var $this = this;
        var $all_status_actions = $( '.ironikus-status-action' );

        //Prevent from clicking again
        if( $all_status_actions.hasClass( 'loading' ) ){
            return;
        } else {
            $all_status_actions.addClass( 'loading' );
        }

        var $webhook = $( $this ).attr( 'ironikus-webhook-slug' );
        var $webhook_group = $( $this ).attr( 'ironikus-webhook-group' );
        var $webhook_status = $( $this ).attr( 'ironikus-webhook-status' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_change_status_webhook_action',
                webhook : $webhook,
                webhook_status : $webhook_status,
                webhook_group : $webhook_group,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook_response = $.parseJSON( $response );

                $all_status_actions.removeClass( 'loading' );

                if( $webhook_response['success'] != 'false' && $webhook_response['success'] != false ){
                    setTimeout(function(){
                        $( $this ).text( $webhook_response['new_status_name'] );
                        $( $this ).attr( 'ironikus-webhook-status', $webhook_response['new_status'] )
                        $( $this ).toggleClass( $webhook_status, $webhook_response['new_status'] );

                        if( $webhook_response['success'] != 'false' ){
                            $( $this ).css( { 'color': '#00a73f' } );
                        } else {
                            $( $this ).css( { 'color': '#a70000' } );
                        }
    
                    }, 200);
                    setTimeout(function(){
                        $( $this ).css( { 'color': '' } );
                    }, 2700);
                }
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

    $( document ).on( "click", ".ironikus-send-demo", function() {
        var $this = this;
        var $webhook = $( $this ).attr( 'ironikus-webhook' );
        var $webhook_group = $( $this ).attr( 'ironikus-group' );
        var $webhook_callback = $( $this ).attr( 'ironikus-demo-data-callback' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_test_webhook_trigger',
                webhook : $webhook,
                webhook_group : $webhook_group,
                webhook_callback : $webhook_callback,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook_response = $.parseJSON( $response );

                console.log( $webhook_response );

                setTimeout(function(){

                    if( $webhook_response['success'] != 'false' ){
                        $( $this ).css( { 'color': '#00a73f' } );
                    } else {
                        $( $this ).css( { 'color': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'color': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        } );
    });

    //Save the settings via Ajax
    $( document ).on( "click", ".ironikus-settings-wrapper .ironikus-submit-settings-data", function(e) {
        e.preventDefault();

        var $this = this;
        var $datastring = $("#ironikus-main-settings-form").serialize();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }
//todo change button HTML within the settings page to the one in the thickbox
        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_save_main_settings',
                main_settings : $datastring,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                // var $settings_response = $.parseJSON( $response );

                window.location = window.location.href;location.reload();

                // if( $settings_response['success'] != 'false' ){
                //    $( '#ironikus-webhook-id-' + $webhook ).remove();
                // }

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '', 'border-color': '' } );
                }, 2700);
               
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

    //Save whitelable settings via Ajax
    $( document ).on( "click", ".ironikus-whitelabel-settings-wrapper .ironikus-submit-whitelabel-settings-data", function(e) {
        e.preventDefault();

        var $this = this;
        var $datastring = $("#ironikus-whitelabel-settings-form").serialize();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }
//todo change button HTML within the settings page to the one in the thickbox
        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_save_whitelabel_settings',
                whitelabel_settings : $datastring,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                // var $settings_response = $.parseJSON( $response );

                window.location = window.location.href;location.reload();

                // if( $settings_response['success'] != 'false' ){
                //    $( '#ironikus-webhook-id-' + $webhook ).remove();
                // }

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '', 'border-color': '' } );
                }, 2700);
               
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

    //New TB logic for trigger settings
    $( document ).on( "click", "#TB_ajaxContent .ironikus-submit-settings-form", function(e) {
        e.preventDefault();

        var $this = this;
        var $webhook = $( $this ).attr( 'webhook-id' );
        var $webhook_group = $( $this ).attr( 'webhook-group' );
        var $datastring = $("#ironikus-webhook-form-"+$webhook_group+'-'+$webhook).serialize();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_save_webhook_trigger_settings',
                webhook_id : $webhook,
                webhook_group : $webhook_group,
                trigger_settings : $datastring,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                //var $webhook_response = $.parseJSON( $response );

                //if( $webhook_response['success'] != 'false' ){
                //    $( '#ironikus-webhook-id-' + $webhook ).remove();
                //}

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '', 'border-color': '' } );
                }, 2700);
                console.log($response);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });
    //New TB logic for trigger settings
    $( document ).on( "click", "#TB_ajaxContent .ironikus-actions-submit-settings-form", function(e) {
        e.preventDefault();

        var $this = this;
        var $webhook = $( $this ).attr( 'webhook-id' );
        var $datastring = $("#ironikus-webhook-action-form-"+$webhook).serialize();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_save_webhook_action_settings',
                webhook_id : $webhook,
                action_settings : $datastring,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                //var $webhook_response = $.parseJSON( $response );

                //if( $webhook_response['success'] != 'false' ){
                //    $( '#ironikus-webhook-id-' + $webhook ).remove();
                //}

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '', 'border-color': '#00a73f' } );
                }, 2700);
                console.log($response);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

    //Choose template file
    $( document ).on( "change", "#wpwhpro-data-mapping-template-select", function(e) {

        //Prevent from clicking again
        if( $( "#wpwhpro-data-mapper-template-loader-img" ).hasClass( 'active' ) ){
            return;
        }

        $( "#wpwhpro-data-mapper-template-loader-img" ).addClass( 'active' );
        
        var $this = this;
        var $data_mapping_id = $( $this ).val();
        var $wrapper_html = '';

        if( $data_mapping_id && $data_mapping_id !== 'empty' ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_load_data_mapping_data',
                    data_mapping_id : $data_mapping_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $mapping_response = $.parseJSON( $response );
                    var $mapping_html = '';
                    console.log($mapping_response);

                    $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );

                    //Add logic for delete and save button
                    $( "#wpwhpro-delete-template-button" ).addClass( 'active' ).attr( 'wpwhpro-mapping-id', $data_mapping_id );
                    $( "#wpwhpro-save-template-button" ).addClass( 'active' ).attr( 'wpwhpro-mapping-id', $data_mapping_id );
                    $( "#wpwhpro-settings-template-button" ).addClass( 'active' ).attr( 'wpwhpro-mapping-id', $data_mapping_id );
    
                    if( $mapping_response['success'] === 'true' || $mapping_response['success'] === true ){
                        
                        $mapping_html = create_data_mapping_table( $mapping_response['data']['template'], $mapping_response );

                        //assign settings
                        $( '#wpwhpro-data-mapping-template-settings' ).html( get_data_mapping_template_settings_html( $mapping_response['template_settings'], $mapping_response['data']['template'] ) );

                        $( '#wpwhpro-data-mapping-wrapper' ).html( $mapping_html );
                        reload_sortable();
                    }
                },
                error: function( errorThrown ){
                    $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );
                    console.log(errorThrown);
                }
            });
        } else {
            $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );
            $( "#wpwhpro-delete-template-button" ).removeClass( 'active' );
            $( "#wpwhpro-save-template-button" ).removeClass( 'active' );
            $( "#wpwhpro-settings-template-button" ).removeClass( 'active' );

            $wrapper_html += '<div class="wpwhpro-empty">';
            $wrapper_html += 'Please choose a template first.';
            $wrapper_html += '</div>';

            $( '#wpwhpro-data-mapping-wrapper' ).html( $wrapper_html );
        }

    });

    //New TB logic for trigger settings
    $( document ).on( "click", ".ironikus-data-mapping-preview-submit-button", function(e) {
        e.preventDefault();

        var $this = this;
        var $original_data = $("#wpwhpro-data-mapping-preview-input").val();
        var $mapping_type = $( $this ).attr( 'mapping-type' );
        var $current_mapping_template = create_template_json();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_data_mapping_create_preview',
                original_data : $original_data,
                current_mapping_template : $current_mapping_template,
                mapping_type : $mapping_type,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook_response = $.parseJSON( $response );

                if( $webhook_response['success'] != 'false' ){
                    $('#wpwhpro-data-mapping-preview-output').jsonBrowse( $webhook_response['payload'] );
                }

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '', 'border-color': '' } );
                }, 2700);
                console.log($response);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

    //New TB logic for trigger settings
    $( document ).on( "click", "#TB_ajaxContent .ironikus-submit-data-mapping-settings", function(e) {
        e.preventDefault();

        var $this = this;
        var $settings_id = $( $this ).attr( 'mapping-settings-id' );

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        update_data_mapping_settings_input( $settings_id );

        setTimeout(function(){
            $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
            $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

            $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
        }, 200);
        setTimeout(function(){
            $( $this ).css( { 'background': '', 'border-color': '' } );
        }, 2700);

        //Close the thickbox
        setTimeout(function(){
            $('#TB_closeWindowButton').click();
        }, 800);
    });

    function create_data_mapping_table( $data, $args ){

        var $html_table = '';
        var $html_action = '';
        var $json_obj = $.parseJSON( $data );

        if ( typeof $json_obj !== 'object' || $.isEmptyObject( $json_obj ) ){
            $json_obj = {};
        }

        $html_table += '<div id="wpwhpro-data-editor">';

        //backwards compatibility
        if( typeof $json_obj.template_data == 'undefined' ){
            var $temp_data = {};
            $temp_data.template_data = $json_obj;

            $json_obj = $temp_data;
        }

        if ( ! $.isEmptyObject( $json_obj.template_data ) ) {

            $.each( $json_obj.template_data, function( index, value ) {
                $html_table += get_table_single_row_layout( value );
            });

        } else {
            $html_table += '<div class="wpwhpro-empty">';
            $html_table += $args['text']['add_first_row_text'];
            $html_table += '</div>';
        }

        $html_table += '</div>';

        $html_action += '<div class="wpwhpro-data-mapping-actions">';
        $html_action += '<div class="btn btn-primary wpwhpro-add-row-button-text wpwhpro-button btn-blue">' + $args['text']['add_button_text'] + '</div>';
        $html_action += '<div class="wpwhpro-data-mapping-imexport">';
        $html_action += '<div class="btn btn-primary wpwhpro-import-data wpwhpro-button btn-blue">' + $args['text']['import_button_text'] + '</div>';
        $html_action += '<div class="btn btn-primary wpwhpro-export-data wpwhpro-button btn-blue">' + $args['text']['export_button_text'] + '</div><p id="wpwhpro-export-data-dialogue" style="display:none !important;"></p>';
        $html_action += '</div>';
        $html_action += '</div>';

        //Map settings
        $html_table += $html_action;

        return $html_table;

    }

    function get_table_single_row_layout( $data ){
        var $html = '';
        var $new_key_placeholder = 'Add new key';
        $html += '<div class="single-data-row">';

        //Add sortable button
        $html += '<div alt="f182" class="data-delete-icon dashicons dashicons-trash"></div>';
        $html += '<div alt="f545" class="data-move-icon dashicons dashicons-move"></div>';

        if( $data === 'empty' ){

            //setup new key
            $html += '<div class="data-new-key-wrapper">';
            $html += '<input class="data-new-key" name="data-new-key" placeholder="' + $new_key_placeholder + '" />';
            $html += '</div>';

            //Setup connector
            $html += '<div class="data-connector">' + get_connector() + '</div>';

            //setup current data keys
            $html += '<ul class="data-income-keys"></ul>';

        } else {

            //setup new key
            $html += '<div class="data-new-key-wrapper">';
            $html += '<input class="data-new-key" name="data-new-key" value="' + $data.new_key + '" placeholder="' + $new_key_placeholder + '" />';
            $html += '</div>';

            //Setup connector
            $html += '<div class="data-connector">' + get_connector() + '</div>';

            //setup current data keys
            $html += '<ul class="data-income-keys">';

            $.each( $data.singles, function( index, value ) {
                $html += get_single_key_html( value );
            });

            $html += '';
            $html += '</ul>';

        }

        //add new data key button @todo - translate text
        $html += '<div class="btn btn-primary wpwhpro-add-key-button-text wpwhpro-button btn-blue">' + 'Add Key' + '</div>';

        $html += '</div>';

        return $html;

    }

    function get_connector(){
        return '<?xml version="1.0" encoding="UTF-8"?>' +
        '<!DOCTYPE svg  PUBLIC \'-//W3C//DTD SVG 1.1//EN\'  \'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\'>' + 
        '<svg version="1.1" viewBox="0 0 640 640" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' +
        '<defs>' +
        '<path id="a" d="m34.98 270.03h428.25c-80.77-80.95-125.65-125.93-134.62-134.92-6.6-6.59-10.21-15.52-10.21-24.88 0-9.37 3.61-18.23 10.21-24.83 2.09-2.1 18.85-18.86 20.94-20.96 6.59-6.59 15.37-10.23 24.73-10.23 9.37 0 18.15 3.62 24.74 10.2 23.07 23.08 207.7 207.69 230.78 230.76 6.61 6.62 10.23 15.43 10.2 24.8 0.03 9.42-3.59 18.23-10.2 24.84-23.08 23.07-207.71 207.7-230.78 230.78-6.59 6.58-15.37 10.2-24.74 10.2-9.36 0-18.14-3.63-24.73-10.2-2.09-2.1-18.85-18.87-20.94-20.96-6.6-6.58-10.21-15.36-10.21-24.73 0-9.36 3.61-17.68 10.21-24.26 9.07-9.05 54.46-54.27 136.14-135.68h-429.25c-19.29 0-35.5-16.63-35.5-35.9v-29.65c0-19.27 16.69-34.6 35.98-34.6-0.14 0.03-0.47 0.1-1 0.22z"/>' +
        '</defs>' +
        '<use fill="#000000" xlink:href="#a"/>' +
        '<use fill-opacity="0" stroke="#000000" stroke-opacity="0" xlink:href="#a"/>' +
        '</svg>';
    }

    function reload_sortable(){
        $( ".data-income-keys" ).sortable({connectWith: ".wpwhpro-single-row", delay: 150});
        $( "#wpwhpro-data-editor" ).sortable({connectWith: ".single-data-row", delay: 150});
    }

    function get_add_new_html(){
        var $html = '';

        $html += '<div class="wpwhpro-data-mapping-add-template-wrapper">';
        $html += '<input id="wpwhpro-data-mapping-add-template-name" type="text" placeholder="' + 'my-template-name' + '" />';
        $html += '<div class="btn btn-primary wpwhpro-add-template-button wpwhpro-button btn-blue">' + 'Add Template' + '</div>';
        $html += '';
        $html += '';
        $html += '</div>';

        return $html;
    }

    function reload_wnd(){
        window.location = window.location.href;
        location.reload();
    }
    
    function is_json( str ){
        try {
            $.parseJSON( str );
        } catch (e){
            return false;
        }

        return true;
    }

    function generate_unique_page_id(){
        return '_' + Math.random().toString(36).substr(2, 9);
    }

    function get_single_key_html( $value ){
        var $html = '';
        var $map_key_placeholder = 'Add old key';
        var $new_key_action_button = 'Edit';
        var $unique_id = generate_unique_page_id();
        var $settings_json = get_data_mapping_settings_json();
        var $settings = '';
        var $real_value = $value;
        var $key_data = [];

        //Add new logic for data mapping 2.0
        if( is_json( $value ) ){
            $key_data = $.parseJSON( $value );
            $real_value = $key_data.value;
        }

        console.log( $key_data );
        $settings = get_data_mapping_settings_html( $unique_id, $settings_json, $key_data );

        

        $html += '<li id="wpwhpro-data-mapping-settings-single-row-' + $unique_id + '" class="wpwhpro-single-row">';
        $html += '<a id="wpwhpro-data-mapping-settings-single-row-link-' + $unique_id + '" title="' + $real_value + '" href="#TB_inline?height=330&width=800&inlineId=wpwhpro-data-mapping-settings-' + $unique_id + '" class="thickbox" tabindex="-1" role="button">';
        
        if( $real_value == 'ironikus-empty' ){
            $html += '<input id="wpwhpro-single-row-preview-input-' + $unique_id + '" class="wpwhpro-single-row-preview-input" name="data-preview-key" placeholder="' + $map_key_placeholder + '" disabled />';
        } else {
            $html += '<input id="wpwhpro-single-row-preview-input-' + $unique_id + '" class="wpwhpro-single-row-preview-input" name="data-preview-key" value=\'' + $real_value + '\' placeholder="' + $map_key_placeholder + '" disabled />';
        }

        $html += '</a>';
        $html += '<div id="wpwhpro-data-mapping-settings-' + $unique_id + '" style="display:none;">';
        $html += '<div class="wpwhpro-data-mapping-settings-popup-wrapper">';

        //add value inout type logic
        
        $html += '<div class="row">';
        $html +=    '<div class="col-9 wpwh-data-mapping-input-wrapper">';
        $html +=        '<div class="input-group mb-3 mt-3">';
        $html +=            '<div class="input-group-prepend">';
        $html +=                '<span class="input-group-text" id="inputGroup-sizing-default">Value</span>';
        $html +=            '</div>';

        if( $real_value == 'ironikus-empty' ){
            $html +=            '<input id="wpwh-data-mapping-key-value-' + $unique_id + '" type="text" class="form-control" aria-label="Include your value here">';
        } else {
            $html +=            '<input id="wpwh-data-mapping-key-value-' + $unique_id + '" type="text" class="form-control" aria-label="Include your value here" value="' + $real_value + '">';
        }

        $html +=        '</div>';
        $html +=    '</div>';
        $html +=    '<div class="col wpwh-data-mapping-submit-button-wrapper">';

        //Add safe button
        $html += '<div class="ironikus-single-webhook-data-mapping-settings">';
        $html += '<p class="btn btn-primary h30 ironikus-submit-data-mapping-settings" mapping-settings-id="' + $unique_id + '">';
        $html += '<span class="ironikus-save-text active">Apply Settings</span>';
        $html += '<img class="ironikus-loader" src="' + ironikus.plugin_url + 'core/includes/assets/img/loader.gif" />';
        $html += '</p>';
        $html += '</div>';
        $html += '';

        $html +=    '</div>';
        $html += '</div>';

        $html += '<form id="wpwhpro-data-mapping-settings-form-' + $unique_id + '">';
        $html += $settings;
        $html += '</form>';

        if( $real_value == 'ironikus-empty' ){
            $html += '<input id="wpwh-data-mapping-data-' + $unique_id + '" type="hidden" class="wpwhpro-single-row-input" name="data-income-key" placeholder="' + $map_key_placeholder + '" />';
        } else {
            $html += '<input id="wpwh-data-mapping-data-' + $unique_id + '" type="hidden" class="wpwhpro-single-row-input" name="data-income-key" value="' + $value.replace(/"/g, '&quot;') + '" placeholder="' + $map_key_placeholder + '" />';
        }

        $html += '</div>';
        $html += '</div>'; // END wpwhpro-data-mapping-settings-popup-wrapper
        $html += '<div class="wpwhpro-single-row-key-actions">';
        $html += '<div class="wpwhpro-delete-single-row-key"><div alt="f182" class="dashicons dashicons-trash"></div></div>';
        $html += '<div class="wpwhpro-move-single-row-key"><div alt="f545" class="dashicons dashicons-move"></div></div>';
        $html += '</div>';
        $html += '</li>';

        return $html;
    }

    function get_data_mapping_settings_json(){
        return $( '#wpwhpro-data-mapping-key-settings' ).html();
    }

    function get_data_mapping_template_settings_html( $data, $values ){

        var $html = '';
        var $settings_data = $data;
        var $settings_values_json = $.parseJSON( $values );

        if ( typeof $settings_values_json !== 'object' || $.isEmptyObject( $settings_values_json ) ){
            $settings_values_json = {};
        }

        var $settings_values = ( typeof $settings_values_json.template_settings !== 'undefined' ) ? $settings_values_json.template_settings : [];
        var $selected = '';

        $html += '<form id="wpwhpro-data-mapping-template-settings-form" class="table wpwhpro-settings-table form-table">';
        $html += '<table id="wpwhpro-data-mapping-template-settings" class="table wpwhpro-settings-table form-table">';
        $html += '<tbody>';

        $.each( $settings_data, function( $setting_name, $setting ){

            var $is_checked = ( $setting.type == 'checkbox' && $setting.default_value == 'yes' ) ? 'checked' : '';
            var $value = ( $setting.type != 'checkbox' && typeof $setting.default_value !== 'undefined' ) ? $setting.default_value : '1';
            var $placeholder = ( $setting.type != 'checkbox' && typeof $setting.placeholder !== 'undefined' ) ? $setting.placeholder : '';
            var $is_multiple_keys = ( typeof $setting.multiple !== 'undefined' && $setting.multiple ) ? '[]' : '';
            var $is_multiple_attr = ( typeof $setting.multiple !== 'undefined' && $setting.multiple ) ? 'multiple' : '';
      
            if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
              $value = $settings_values[ $setting_name ];
              $is_checked = ( $setting.type == 'checkbox' && $value == 1 ) ? 'checked' : '';
            }

            $html += '<tr valign="top">';
            $html += '<td class="tb-settings-input">';
            $html += '<label for="iroikus-data-mapping-input-id-' + $setting_name + '">';
            $html += '<strong>' + $setting.label + '</strong>';
            $html += '</label>';

            if( $.inArray( $setting.type, ['text'] ) !== -1 ){
                $html += '<input id="iroikus-data-mapping-input-id-' + $setting_name + '" name="' + $setting_name + '" type="' + $setting.type + '" placeholder="' + $placeholder + '" value="' + $value + '" ' + $is_checked + ' />';
            } else if( $.inArray( $setting.type, ['checkbox'] ) !== -1 ){
                $html += '<label class="switch ">';
                $html += '<input id="iroikus-data-mapping-input-id-' + $setting_name + '" class="default primary" name="' + $setting_name + '" type="' + $setting.type + '" placeholder="' + $placeholder + '" value="' + $value + '" ' + $is_checked + ' />';
                $html += '<span class="slider round"></span>';
                $html += '</label>';
            } else if( $setting.type == 'select' && typeof $setting.choices !== 'undefined' ){
                $html += '<select name="' + $setting_name + $is_multiple_keys + '" ' + $is_multiple_attr + '>';

                if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
                    $settings_values[ $setting_name ] = ( $.isArray( $settings_values[ $setting_name ] ) ) ? $settings_values[ $setting_name ].reverse() : $settings_values[ $setting_name ];
                }

                $.each( $setting.choices, function( $choice_name, $choice_label ){
                    $selected = '';
                    if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
    
                      if( $.isArray( $settings_values[ $setting_name ] ) ){
                        if( typeof $settings_values[ $setting_name ][ $choice_name ] !== 'undefined' ){
                          $selected = 'selected="selected"';
                        }
                      } else {
                        if( $settings_values[ $setting_name ] == $choice_name ){
                          $selected = 'selected="selected"';
                        }
                      }
    
                    }

                    $html += '<option value="' + $choice_name + '" ' + $selected + '>' + $choice_label + '</option>';
                });

                $html += '</select>';
            }
            
            $html += '</td>';
            $html += '<td>';
            $html += '<p class="description">';
            $html += $setting.description;
            $html += '</p>';
            $html += '</td>';
            $html += '</tr>';

        });

        $html += '</tbody>';
        $html += '</table>';
        $html += '</form>';

        return $html;
    }

    function get_data_mapping_settings_html( $unique_id, $data, $values ){
        var $html = '';
        var $settings_data = $.parseJSON( $data );
        var $settings_values = ( typeof $values.settings !== 'undefined' ) ? $values.settings : [];
        var $selected = '';

        $html += '<table id="wpwhpro-settings-table-' + $unique_id + '" class="table wpwhpro-settings-table form-table">';
        $html += '<tbody>';

        $.each( $settings_data, function( $setting_name, $setting ){

            var $is_checked = ( $setting.type == 'checkbox' && $setting.default_value == 'yes' ) ? 'checked' : '';
            var $value = ( $setting.type != 'checkbox' && typeof $setting.default_value !== 'undefined' ) ? $setting.default_value : '1';
            var $placeholder = ( $setting.type != 'checkbox' && typeof $setting.placeholder !== 'undefined' ) ? $setting.placeholder : '';
            var $is_multiple_keys = ( typeof $setting.multiple !== 'undefined' && $setting.multiple ) ? '[]' : '';
            var $is_multiple_attr = ( typeof $setting.multiple !== 'undefined' && $setting.multiple ) ? 'multiple' : '';
      
            if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
              $value = $settings_values[ $setting_name ];
              $is_checked = ( $setting.type == 'checkbox' && $value == 1 ) ? 'checked' : '';
            }

            $html += '<tr valign="top">';
            $html += '<td class="tb-settings-input">';
            $html += '<label for="iroikus-data-mapping-input-id-' + $setting_name + '-' + $unique_id + '">';
            $html += '<strong>' + $setting.label + '</strong>';
            $html += '</label>';

            if( $.inArray( $setting.type, ['text'] ) !== -1 ){
                $html += '<input id="iroikus-data-mapping-input-id-' + $setting_name + '-' + $unique_id + '" name="' + $setting_name + '" type="' + $setting.type + '" placeholder="' + $placeholder + '" value="' + $value + '" ' + $is_checked + ' />';
            } else if( $.inArray( $setting.type, ['checkbox'] ) !== -1 ){
                $html += '<label class="switch ">';
                $html += '<input id="iroikus-data-mapping-input-id-' + $setting_name + '-' + $unique_id + '" class="default primary" name="' + $setting_name + '" type="' + $setting.type + '" placeholder="' + $placeholder + '" value="' + $value + '" ' + $is_checked + ' />';
                $html += '<span class="slider round"></span>';
                $html += '</label>';
            } else if( $setting.type == 'select' && typeof $setting.choices !== 'undefined' ){
                $html += '<select name="' + $setting_name + $is_multiple_keys + '" ' + $is_multiple_attr + '>';

                if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
                    $settings_values[ $setting_name ] = ( $.isArray( $settings_values[ $setting_name ] ) ) ? $settings_values[ $setting_name ].reverse() : $settings_values[ $setting_name ];
                }

                $.each( $setting.choices, function( $choice_name, $choice_label ){
                    $selected = '';
                    if( typeof $settings_values[ $setting_name ] !== 'undefined' ){
    
                      if( $.isArray( $settings_values[ $setting_name ] ) ){
                        if( typeof $settings_values[ $setting_name ][ $choice_name ] !== 'undefined' ){
                          $selected = 'selected="selected"';
                        }
                      } else {
                        if( $settings_values[ $setting_name ] == $choice_name ){
                          $selected = 'selected="selected"';
                        }
                      }
    
                    }

                    $html += '<option value="' + $choice_name + '" ' + $selected + '>' + $choice_label + '</option>';
                });

                $html += '</select>';
            }
            
            $html += '</td>';
            $html += '<td>';
            $html += '<p class="description">';
            $html += $setting.description;
            $html += '</p>';
            $html += '</td>';
            $html += '</tr>';

        });

        $html += '</tbody>';
        $html += '</table>';

        return $html;
    }

    function update_data_mapping_settings_input( $unique_id ){
        var $main_mapping_key_data = $( "#wpwh-data-mapping-data-" + $unique_id ).val();

        //Fallbback for older versions
        if( ! is_json( $main_mapping_key_data ) ){
            $main_mapping_key_data = '{"value":"' + $main_mapping_key_data + '"}';
        }

        var $main_mapping_key_value = $( "#wpwh-data-mapping-key-value-" + $unique_id ).val();
        var $main_mapping_key_data_json = $.parseJSON( $main_mapping_key_data );
        var $main_mapping_settings_data = $("#wpwhpro-data-mapping-settings-form-"+$unique_id).serializeArray();
        var $validated_mapping_values = {};

        //Update the value data
        $( "#TB_ajaxWindowTitle" ).html( $main_mapping_key_value );
        $( "#wpwhpro-single-row-preview-input-" + $unique_id ).val( $main_mapping_key_value );
        $( "#wpwhpro-data-mapping-settings-single-row-link-" + $unique_id ).attr( 'title', $main_mapping_key_value );

        $.each( $main_mapping_settings_data, function( $setting_key, $setting ){
            $validated_mapping_values[$setting.name] = $setting.value;
        });

        //backwards compatibility to convert simple values to JSON
        if( typeof response !== 'object' ){
            $main_mapping_key_data_json = {};
        }

        $main_mapping_key_data_json.settings = $.extend( $main_mapping_key_data_json.settings, $validated_mapping_values );
        $main_mapping_key_data_json.value = $main_mapping_key_value;
        
        $( "#wpwh-data-mapping-data-" + $unique_id ).val( JSON.stringify( $main_mapping_key_data_json ) );
    }

    function create_template_json(){
        var $template_settings = $("#wpwhpro-data-mapping-template-settings-form").serializeArray();
        var $validated_mapping_settings = {};
        var $mapping_template = {};
        var $data = $('#wpwhpro-data-editor .data-new-key').map(function() {
            return {
                new_key: $(this).val(),
                singles: $(this).parent().parent().find('.wpwhpro-single-row-input').map(function() {
                    return $(this).val();
                }).get()
            };
        }).get();

        //Add the global template settings
        $.each( $template_settings, function( $setting_key, $setting ){
            $validated_mapping_settings[$setting.name] = $setting.value;
        });

        $mapping_template.template_settings = $validated_mapping_settings;
        $mapping_template.template_data = $data;
        
        return $mapping_template;
    }

    $(document).ready(function(){
        var $html = '';

        $html += get_add_new_html();
        $html += '<div id="wpwhpro-save-template-button" class="btn btn-primary wpwhpro-button btn-blue">' + 'Save Template' + '</div>';
        $html += '<a id="wpwhpro-settings-template-button" title="' + 'Template Settings' + '" href="#TB_inline?height=330&width=800&inlineId=wpwhpro-data-mapping-template-settings" class="btn btn-primary wpwhpro-button btn-blue thickbox" tabindex="-1">' + 'Template Settings' + '</a>';
        $html += '<div id="wpwhpro-delete-template-button" class="btn btn-danger wpwhpro-button btn-red">' + 'Delete Template' + '</div>';
        $html += '';
        $html += '<div id="wpwhpro-data-mapping-template-settings" style="display:none;">';
        $html += '</div>';

        $( '#wpwhpro-data-mapping-actions' ).html( $html );
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-add-row-button-text", function() {
        var $this = this;

        //Clear empty text ares
        if( $("#wpwhpro-data-editor .wpwhpro-empty").length ){
            $( '#wpwhpro-data-editor' ).html( '' );
        }

        var $single_row = get_table_single_row_layout( 'empty' );

        $( '#wpwhpro-data-editor' ).append( $single_row );
        
        reload_sortable();
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-import-data", function() {
        var $this = this;
        var $json = prompt("Include the exported JSON here. (This will append the new fields to the existing mapping template.)", '{"myjson": "myvalue"}');
        var $mapping_response = $.parseJSON( $json );
        var $html_table = '';

        if ( typeof $mapping_response !== 'object' || $.isEmptyObject( $mapping_response ) ){
            $mapping_response = {};
        }
        
        //Backwards compatibility
        if( typeof $mapping_response.template_data == 'undefined' ){
            var $temp_data = {};

            $temp_data.template_data = $mapping_response;
            $mapping_response = $temp_data;
        }

        if ( ! $.isEmptyObject( $mapping_response.template_data ) ) {

            //Clear empty text ares
            if( $("#wpwhpro-data-editor .wpwhpro-empty").length ){
                $( '#wpwhpro-data-editor' ).html( '' );
            }

            $.each( $mapping_response.template_data, function( index, value ) {
                $html_table += get_table_single_row_layout( value );
            });

        }

        $( '#wpwhpro-data-editor' ).append( $html_table );
        reload_sortable();
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-export-data", function() {
        var $this = this;
        var $template_json = create_template_json();
        var $button_text = $( $this ).html();
        
        $( '#wpwhpro-export-data-dialogue' ).text( JSON.stringify( $template_json ) );
        wpwhpro_copy_to_clipboard( '#wpwhpro-export-data-dialogue' );

        $( $this ).html( 'Copied!' );
        alert( 'Copied!' );

        setTimeout(function(){
            $( $this ).html( $button_text );
        }, 2700);
    });

    // delete single key logic
    $( document ).on( "click", ".wpwhpro-delete-single-row-key", function() {
        var $this = this;
        $( $this ).parent().parent(".wpwhpro-single-row").remove();
    });

    // delete single key logic
    $( document ).on( "click", ".data-delete-icon", function() {
        if (confirm("Are you sure you want to delete this row?")){
            var $this = this;
            $( $this ).parent(".single-data-row").remove();
        }
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-add-key-button-text", function() {
        var $this = this;
        var $html = get_single_key_html( 'ironikus-empty' );

        $( $this ).prev( ".data-income-keys" ).append( $html );
        
        reload_sortable();
    });

    // Delete Template logic
    $( document ).on( "click", "#wpwhpro-save-template-button", function() {

        var $this = this;
        var $data_mapping_id = $( $this ).attr( 'wpwhpro-mapping-id' );
        var $template_json = create_template_json();

        if( $data_mapping_id ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_save_data_mapping_template',
                    data_mapping_id : $data_mapping_id,
                    data_mapping_json : $template_json,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $saving_response = $.parseJSON( $response );
    
                    if( $saving_response['success'] === 'true' || $saving_response['success'] === true ){
                        
                        setTimeout(function(){
                            $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                        }, 200);
                        setTimeout(function(){
                            $( $this ).css( { 'background': '', 'border-color': '' } );
                        }, 2700);
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Delete Template logic
    $( document ).on( "click", "#wpwhpro-delete-template-button", function() {

        var $this = this;
        var $data_mapping_id = $( $this ).attr( 'wpwhpro-mapping-id' );
        var $wrapper_html = '';

        if( $data_mapping_id && $data_mapping_id !== 'empty' && confirm( "Are you sure you want to delete this template?" ) ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_delete_data_mapping_template',
                    data_mapping_id : $data_mapping_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $deleting_response = $.parseJSON( $response );
    
                    if( $deleting_response['success'] === 'true' || $deleting_response['success'] === true ){
                        
                        $( "#wpwhpro-delete-template-button" ).removeClass( 'active' );
                        $( "#wpwhpro-save-template-button" ).removeClass( 'active' );
                        $( "#wpwhpro-settings-template-button" ).removeClass( 'active' );
                        $("#wpwhpro-data-mapping-template-select option[value='" + $data_mapping_id + "']").remove();

                        $wrapper_html += '<div class="wpwhpro-empty">';
                        $wrapper_html += 'Please choose a template first.';
                        $wrapper_html += '</div>';

                        $( '#wpwhpro-data-mapping-wrapper' ).html( $wrapper_html );
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Add Template logic
    $( document ).on( "click", ".wpwhpro-add-template-button", function() {

        var $this = this;
        var $data_mapping_name = $( "#wpwhpro-data-mapping-add-template-name" ).val();

        if( $data_mapping_name ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_add_data_mapping_template',
                    data_mapping_name : $data_mapping_name,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $add_response = $.parseJSON( $response );
    
                    if( $add_response['success'] === 'true' || $add_response['success'] === true ){
                        
                        if( confirm( 'Reload required. Want to reload now?' ) ){
                            reload_wnd();
                        }
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Add authentication template
    $( document ).on( "click", ".ironikus-submit-auth-data", function() {

        var $this = this;
        var $auth_template = $( "#wpwh-authentication-template" ).val();
        var $auth_type = $( "#wpwh-authentication-type" ).val();

        if( $auth_template && $auth_type && $auth_type !== 'empty' ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_add_authentication_template',
                    auth_template : $auth_template,
                    auth_type : $auth_type,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $add_response = $.parseJSON( $response );
    
                    if( $add_response['success'] === 'true' || $add_response['success'] === true ){
                        
                        if( confirm( 'Reload required. Want to reload now?' ) ){
                            reload_wnd();
                        }
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        } else {
            alert( 'Please set a template name, as well as a auth type.' );
        }

    });

    //Load authentication template
    $( document ).on( "change", "#wpwhpro-authentication-template-select", function(e) {

        //Prevent from clicking again
        if( $( "#wpwhpro-authentication-template-loader-img" ).hasClass( 'active' ) ){
            return;
        }

        $( "#wpwhpro-authentication-template-loader-img" ).addClass( 'active' );
        
        var $this = this;
        var $auth_template_id = $( $this ).val();
        var $wrapper_html = '';

        if( $auth_template_id && $auth_template_id !== 'empty' ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_load_authentication_template_data',
                    auth_template_id : $auth_template_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $auth_response = $.parseJSON( $response );
                    var $mapping_html = '';
                    console.log($auth_response);

                    $( "#wpwhpro-authentication-template-loader-img" ).removeClass( 'active' );
    
                    if( $auth_response['success'] === 'true' || $auth_response['success'] === true ){
                        
                        $mapping_html = $auth_response['content'];
                        $mapping_html += '<div class="wpwh-authentication-inline-actions">';
                        $mapping_html += '<div id="wpwhpro-save-auth-template-button" class="btn btn-primary wpwhpro-button" wpwhpro-auth-id="' + $auth_response['id'] + '">Save Template</div>';
                        $mapping_html += '<div id="wpwhpro-delete-auth-template-button" class="btn btn-primary wpwhpro-button" wpwhpro-auth-id="' + $auth_response['id'] + '">Delete Template</div>';
                        $mapping_html += '</div>';

                        $( '#wpwhpro-authentication-content-wrapper' ).html( $mapping_html );
                        reload_sortable();
                    }
                },
                error: function( errorThrown ){
                    $( "#wpwhpro-authentication-template-loader-img" ).removeClass( 'active' );
                    console.log(errorThrown);
                }
            });
        } else {
            $( "#wpwhpro-authentication-template-loader-img" ).removeClass( 'active' );

            $wrapper_html += '<div class="wpwhpro-empty">';
            $wrapper_html += 'Please choose a template first.';
            $wrapper_html += '</div>';

            $( '#wpwhpro-authentication-content-wrapper' ).html( $wrapper_html );
        }

    });

    // Save authentication template
    $( document ).on( "click", "#wpwhpro-save-auth-template-button", function() {

        var $this = this;
        var $data_auth_id = $( $this ).attr( 'wpwhpro-auth-id' );
        var $datastring = $("#ironikus-authentication-template-form").serialize();

        if( $data_auth_id && $datastring ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_save_authentication_template',
                    data_auth_id : $data_auth_id,
                    datastring : $datastring,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $saving_response = $.parseJSON( $response );
    
                    if( $saving_response['success'] === 'true' || $saving_response['success'] === true ){
                        
                        setTimeout(function(){
                            $( $this ).css( { 'background': '#00a73f', 'border-color': '#00a73f' } );
                        }, 200);
                        setTimeout(function(){
                            $( $this ).css( { 'background': '', 'border-color': '' } );
                        }, 2700);
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Delete authentication template
    $( document ).on( "click", "#wpwhpro-delete-auth-template-button", function() {

        var $this = this;
        var $data_auth_id = $( $this ).attr( 'wpwhpro-auth-id' );
        var $wrapper_html = '';

        if( $data_auth_id && confirm( "Are you sure you want to delete this template?" ) ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_delete_authentication_template',
                    data_auth_id : $data_auth_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $deleting_response = $.parseJSON( $response );
    
                    if( $deleting_response['success'] === 'true' || $deleting_response['success'] === true ){
                        
                        $("#wpwhpro-authentication-template-select option[value='" + $data_auth_id + "']").remove();

                        $wrapper_html += '<div class="wpwhpro-empty">';
                        $wrapper_html += 'Please choose a template first.';
                        $wrapper_html += '</div>';

                        $( '#wpwhpro-authentication-content-wrapper' ).html( $wrapper_html );
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    $( document ).on({
        mouseover: function () {
            var $this = this;
            $( $this ).find( ".wpwhpro-single-row-key-actions" ).addClass( 'active' );
        },
        mouseleave: function () {
            var $this = this;
            $( $this ).find( ".wpwhpro-single-row-key-actions" ).removeClass( "active" );
        }
    }, ".wpwhpro-single-row");

    //Whitelist logic
    $( document ).on( "click", ".wpwh-whitelist-card-header", function() {
        var $this = this;
        var $request_id = $( $this ).attr( 'whitelist-request-id' );
        var $log_json = $( "#preloader-json-"+$request_id ).text();
        $('#wpwhpro-whitelist-json-'+$request_id).jsonBrowse( $.parseJSON( $log_json ) );

     });

    //Log logic
     $( document ).on( "click", ".log-element", function() {
        var $this = this;
        var $log_id = $( $this ).attr( 'wpwhpro-log-id' );

        var $log_json = $( "#wpwhpro-log-json-"+$log_id ).text();
        if( $log_json !== '' ){
            $('#wpwhpro-log-json-output-' + $log_id).jsonBrowse( $.parseJSON( $log_json ) );
        }

        var $log_json_1 = $( "#wpwhpro-log-json-endpoint-"+$log_id ).text();
        if( $log_json_1 !== '' ){
            $('#wpwhpro-log-json-endpoint-output-' + $log_id).jsonBrowse( $.parseJSON( $log_json_1 ) );
        }

        var $log_content = $( "#wpwhpro-log-content-"+$log_id ).html();
        $( "#wpwhpro-log-content" ).html( $log_content );

     });

     function wpwhpro_copy_to_clipboard( element ) {
        var $temp = $("<input>");
        var $json = $(element).text();
        $("body").append($temp);
        $temp.val($json).select();
        document.execCommand("copy");
        $temp.remove();
        console.log($json);
      }

      /* EXTENSION VIEW */
      $( ".ironikus-extension-manage" ).on( "click", function() {

        var $this = $( this );
        var $append_delete = '';
        var $extension_slug = $( $this ).attr( 'webhook-extension-slug' );
        var $extension_status = $( $this ).attr( 'webhook-extension-status' );
        var $extension_download = $( $this ).attr( 'webhook-extension-dl' );
        var $extension_id = $( $this ).attr( 'webhook-extension-id' );
        var $extension_version = $( $this ).attr( 'webhook-extension-version' );

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_manage_extensions',
                extension_slug : $extension_slug,
                extension_status : $extension_status,
                extension_download : $extension_download,
                extension_id : $extension_id,
                extension_version : $extension_version,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    if( $webhook['success'] != 'false' && $webhook['success'] != false ){
                        $( $this ).addClass( $webhook['new_class'] ).removeClass( $webhook['old_class'] );
                        $( $this ).children( '.ironikus-save-text' ).html( $webhook['new_label'] );
                        $( $this ).attr( 'webhook-extension-status', $webhook['new_status'] );

                        if( $extension_status == 'uninstalled' ){
                            $append_delete = '<div class="ironikus-extension-delete" webhook-extension-status="delete" webhook-extension-slug="' + $extension_slug + '" webhook-extension-dl="' + $extension_download + '">';
                            $append_delete += '<small>' + $webhook['delete_name'] + '</small>';
                            $append_delete += '</div>';
                            
                            $( $this ).next( '.bottom-action-wrapper' ).html( $append_delete );
                        }

                    } else {
                        $( $this ).css( { 'background': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );
                    $( $this ).css( { 'background': '#a70000' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            }
        } );

    });

    $( document ).on( "click", ".ironikus-extension-delete", function() {

        var $this = $( this );
        var $extension_slug = $( $this ).attr( 'webhook-extension-slug' );
        var $extension_status = $( $this ).attr( 'webhook-extension-status' );
        var $extension_download = $( $this ).attr( 'webhook-extension-dl' );
        var $extension_id = $( $this ).attr( 'webhook-extension-id' );
        var $extension_version = $( $this ).attr( 'webhook-extension-version' );

        //Prevent from clicking again
        if( ! confirm( 'Are you sure?' ) ){
            return;
        }

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_manage_extensions',
                extension_slug : $extension_slug,
                extension_status : $extension_status,
                extension_download : $extension_download,
                extension_id : $extension_id,
                extension_version : $extension_version,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                setTimeout(function(){

                    if( $webhook['success'] != 'false' && $webhook['success'] != false ){
                        if( confirm( 'Reload required. Want to reload now?' ) ){
                            reload_wnd();
                        }
                    } else {
                        $( $this ).css( { 'background': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                setTimeout(function(){
                    $( $this ).css( { 'background': '#a70000' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            }
        } );

    });

})( jQuery );

(function($) {
	$.formalist = function(form, options) {
		
		// default plugin options
		var defaults = {
			selector: ':radio,:checkbox,select',
			event: 'change',
			box: 'div.box',
			cascade: ':radio:checked:visible,:checkbox:checked:visible,select:visible option:selected',
			classwhenhidden: 'hidden',
			classwhenvisible: 'visible',
			hide: function(box) {hide(box);},
			show: function(box) {show(box);},
			correlate: function(box, field, type, value, name, id) {return correlate(box, field, type, value, name, id);}
		};
		
		// extend default plugin options with user's custom options
		var plugin = this;
		plugin.settings = $.extend({}, defaults, options);
		
		// override of the hide method
		if (!$.isFunction(plugin.settings.hide)) {
			plugin.settings.hide = hide;
		}
		
		// override of the show method
		if (!$.isFunction(plugin.settings.show)) {
			plugin.settings.show = show;
		}
		
		// override of the correlation method
		if (!$.isFunction(plugin.settings.correlate)) {
			plugin.settings.correlate = correlate;
		}
		
		// initializes the plugin
		function init() {
			
			// hides all box except first and hides all box that contains a "*" in the data-hide attribute
			plugin.settings.hide($(form).find(plugin.settings.box + ':not(:first),' + plugin.settings.box + '[data-hide~="*"]'));
			
			// shows all box that contains a "*" in the data-show attribute
			plugin.settings.show($(form).find(plugin.settings.box + '[data-show~="*"]'));
			
			// runs through all input fields that already matched the cascade constraint
			$(form).find(plugin.settings.cascade).each(function(){
				run(this, form);
			});
			
			// binds all input fields on a specific event
			$(form).find(plugin.settings.selector).bind(plugin.settings.event, function(){
				run(this, form);
			});
		}
		
		// runs input fields through specific tests
		function run(field, form) {
			
			// grabs properties from the input field
			var type = $(field).is('input') ? $(field).prop('type') : $(field).prop('tagName').toLowerCase();
			var value = $(field).val();
			var name = $(field).prop('name');
			var id = $(field).prop('id');
			
			// hides all input fields when the data-hide attribute contains a "*", or equals a specific value, or contains a specific name, or contains a specific identifier
			plugin.settings.hide($(form).find(plugin.settings.box + '[data-hide~="*"],' + plugin.settings.box + '[data-hide="' + value + '"],' + plugin.settings.box + '[data-hide~="' + name + '"],' + plugin.settings.box + '[data-hide~="' + id + '"]'));
			
			// shows all input fields when the data-show attribute contains a "*"
			plugin.settings.show($(form).find(plugin.settings.box + '[data-show~="*"]'));
			
			// shows all input fields when the data-show attribute equals a specific value, or contains a specific name, or contains a specific identifier
			$(form).find(plugin.settings.box + '[data-show="' + value + '"],' + plugin.settings.box + '[data-show~="' + name + '"],' + plugin.settings.box + '[data-show~="' + id + '"]').each(function(){
				
				// correlates the box with the captured input field
				if (plugin.settings.correlate(this, field, type, value, name, id)) {
					plugin.settings.show(this);
				} else {
					plugin.settings.hide(this);
				}
				
				// runs again through all input fields that matched the cascade constraint
				$(this).find(plugin.settings.cascade).each(function(){
					run(this, form);
				});
			});
		}
		
		// hides an box
		function hide(box) {
			$(box).removeClass(plugin.settings.classwhenvisible).addClass(plugin.settings.classwhenhidden);
		}
		
		// shows an box
		function show(box) {
			$(box).removeClass(plugin.settings.classwhenhidden).addClass(plugin.settings.classwhenvisible);
		}
		
		// correlation between box and field
		function correlate(box, field, type, value, name, id) {
			return type == 'select' ? $(field).has('option:selected') : $(field).is(':checked');
		}
		
		// initializes the plugin
		init();
	};
	
	// declares and instanciates the plugin
	$.fn.formalist = function(options) {
		return this.each(function() {
			if (undefined === $(this).data('formalist')) {
				var plugin = new $.formalist(this, options);
				$(this).data('formalist', plugin);
			}
		});
	};
})(jQuery);
/**
 * jQuery json-viewer
 * @author: Kevin Olson <acidjazz@gmail.com>
 */
(function($){

  /**
   * Check if arg is either an array with at least 1 element, or a dict with at least 1 key
   * @return boolean
   */
  function isCollapsable(arg) {
    return arg instanceof Object && Object.keys(arg).length > 0;
  }

  /**
   * Check if a string represents a valid url
   * @return boolean
   */
  function isUrl(string) {
     var regexp = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
     return regexp.test(string);
  }

  /**
   * Transform a json object into html representation
   * @return string
   */
  function json2html(json, options) {
    html = '';
    if (typeof json === 'string') {
      // Escape tags
      json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      if (isUrl(json))
        html += '<a href="' + json + '" class="json-string">' + json + '</a>';
      else
        html += '<span class="json-string">"' + json + '"</span>';
    }
    else if (typeof json === 'number') {
      html += '<span class="json-literal">' + json + '</span>';
    }
    else if (typeof json === 'boolean') {
      html += '<span class="json-literal">' + json + '</span>';
    }
    else if (json === null) {
      html += '<span class="json-literal">null</span>';
    }
    else if (json instanceof Array) {
      if (json.length > 0) {
        html += '[<ol class="json-array">';
        for (var i = 0; i < json.length; ++i) {
          html += '<li>'
          // Add toggle button if item is collapsable
          if (isCollapsable(json[i])) {
            html += '<a href class="json-toggle"></a>';
          }
          html += json2html(json[i], options);
          // Add comma if item is not last
          if (i < json.length - 1) {
            html += ',';
          }
          html += '</li>';
        }
        html += '</ol>]';
      }
      else {
        html += '[]';
      }
    }
    else if (typeof json === 'object') {
      var key_count = Object.keys(json).length;
      if (key_count > 0) {
        html += '{<ul class="json-dict">';
        for (var key in json) {
          if (json.hasOwnProperty(key)) {
            html += '<li>';
            var keyRepr = options.withQuotes ?
              '<span class="json-string">"' + key + '"</span>' : key;
            // Add toggle button if item is collapsable
            if (isCollapsable(json[key])) {
              html += '<a href class="json-toggle">' + keyRepr + '</a>';
            }
            else {
              html += keyRepr;
            }
            html += ': ' + json2html(json[key], options);
            // Add comma if item is not last
            if (--key_count > 0)
              html += ',';
            html += '</li>';
          }
        }
        html += '</ul>}';
      }
      else {
        html += '{}';
      }
    }
    return html;
  }

  /**
   * jQuery plugin method
   * @param json: a javascript object
   * @param options: an optional options hash
   */
  $.fn.jsonBrowse = function(json, options) {
    options = options || {};

    // jQuery chaining
    return this.each(function() {

      // Transform to HTML
      var html = json2html(json, options)
      if (isCollapsable(json))
        html = '<a href class="json-toggle"></a>' + html;

      // Insert HTML in target DOM element
      $(this).html(html);

      // Bind click on toggle buttons
      $(this).off('click');
      $(this).on('click', 'a.json-toggle', function() {
        var target = $(this).toggleClass('collapsed').siblings('ul.json-dict, ol.json-array');
        target.toggle();
        if (target.is(':visible')) {
          target.siblings('.json-placeholder').remove();
        }
        else {
          var count = target.children('li').length;
          var placeholder = count + (count > 1 ? ' items' : ' item');
          target.after('<a href class="json-placeholder">' + placeholder + '</a>');
        }
        return false;
      });

      // Simulate click on toggle button when placeholder is clicked
      $(this).on('click', 'a.json-placeholder', function() {
        $(this).siblings('a.json-toggle').click();
        return false;
      });

      if (options.collapsed == true) {
        // Trigger click to collapse all nodes
        $(this).find('a.json-toggle').click();
      }
    });
  };
})(jQuery);
