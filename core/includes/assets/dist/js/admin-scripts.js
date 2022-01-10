/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"admin-scripts": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./core/includes/assets/js/main.js","admin-vendor"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./core/includes/assets/js/custom/ajax-scripts.js":
/*!********************************************************!*\
  !*** ./core/includes/assets/js/custom/ajax-scripts.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function () {

  /**
  * Save the settings via Ajax
  *
  * Jannis' code
  */
  $(document).on("click", ".wpwh-submit-settings-data", function (e) {
    e.preventDefault();

    var $this = $(this);
    var datastring = $("#wpwh-main-settings-form").serialize();
    var $loader = $this.find('.wpwh-loader');
    var $saveText = $this.find('.wpwh-save-text');

    // Prevent from clicking again
    if ($loader.hasClass('active')) {
      return;
    }
    //todo change button HTML within the settings page to the one in the thickbox
    $saveText.toggleClass('active');
    $loader.toggleClass('active');

    $.ajax({
      url: ironikus.ajax_url,
      type: 'post',
      data: {
        action: 'ironikus_save_main_settings',
        main_settings: datastring,
        ironikus_nonce: ironikus.ajax_nonce
      },
      success: function success($response) {
        window.location = window.location.href;
        location.reload();

        setTimeout(function () {
          $saveText.toggleClass('active');
          $loader.toggleClass('active');

          $this.css({ 'background': '#00a73f', 'border-color': '#00a73f' });
        }, 200);
        setTimeout(function () {
          $this.css({ 'background': '', 'border-color': '' });
        }, 2700);
      },
      error: function error(errorThrown) {
        console.log(errorThrown);
      }
    });
  });

  /**
  * Ironikus Delete Action
  *
  * Jannis' code
  */
  $(document).on("click", ".wpwh-delete-action", function (e) {
    e.preventDefault();

    if (confirm("Are you sure you want to delete this webhook?")) {

      var $this = $(this);
      var $webhook = $this.attr('wpwh-webhook-slug');

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_remove_webhook_action',
          webhook: $webhook,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success($response) {
          var $webhook_response = JSON.parse($response);

          console.log($response);

          if ($webhook_response['success'] != 'false') {
            $('#webhook-action-' + $webhook).remove();
          }
        },
        error: function error(errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  });

  /**
  * Status Action
  *
  * Jannis' code
  */
  $(document).on("click", ".wpwh-status-action", function () {

    var $this = $(this);
    var $all_status_actions = $('.wpwh-status-action');

    //Prevent from clicking again
    if ($all_status_actions.hasClass('loading')) {
      return;
    } else {
      $all_status_actions.addClass('loading');
    }

    var $webhook = $this.attr('wpwh-webhook-slug');
    var $webhook_group = $this.attr('wpwh-webhook-group');
    var $webhook_status = $this.attr('wpwh-webhook-status');

    $.ajax({
      url: ironikus.ajax_url,
      type: 'post',
      data: {
        action: 'ironikus_change_status_webhook_action',
        webhook: $webhook,
        webhook_status: $webhook_status,
        webhook_group: $webhook_group,
        ironikus_nonce: ironikus.ajax_nonce
      },
      success: function success($response) {
        var $webhook_response = JSON.parse($response);

        $all_status_actions.removeClass('loading');

        if ($webhook_response['success'] != 'false' && $webhook_response['success'] != false) {
          setTimeout(function () {
            $this.text($webhook_response['new_status_name']);
            $this.attr('wpwh-webhook-status', $webhook_response['new_status']);
            $this.toggleClass($webhook_status, $webhook_response['new_status']);

            if ($webhook_response['success'] != 'false') {
              $this.css({
                'color': '#00a73f'
              });
            } else {
              $this.css({
                'color': '#a70000'
              });
            }
          }, 200);
          setTimeout(function () {
            $this.css({
              'color': ''
            });
          }, 2700);
        }
      },
      error: function error(errorThrown) {
        console.log(errorThrown);
      }
    });
  });

  /**
  * Prefill action testing form
  */
  $(document).on("change", ".wpwh-webhook-receive-test-action", function (e) {
    var $this = $(this);
    var webhookUrl = $this.val();
    var identkey = $this.data('wpwh-identkey');
    var $form = $($this.data('wpwh-target'));

    console.log('asdfasdfasdf');
    console.log($this.data('wpwh-target'));
    console.log($form);

    if (webhookUrl === 'empty') {
      $form.hide();
    } else {
      $form.show();
    }

    $form.attr('action', webhookUrl);
  });

  /**
  * Extension Manage
  *
  * Jannis' code
  */
  $(document).on('click', '.wpwh-extension-manage', function (e) {
    e.preventDefault();

    var $this = $(this);
    var appendDelete = '';
    var extensionType = $this.data('wpwh-extension');
    var extensionSlug = $this.data('wpwh-extension-slug');
    var extStatus = $this.data('wpwh-extension-status');
    var extDownload = $this.data('wpwh-extension-dl');
    var extId = $this.data('wpwh-extension-id');
    var extVersion = $this.data('wpwh-extension-version');

    // Prevent if status is to be deleted
    if (extStatus === 'delete') {
      if (!confirm('Are you sure?')) {
        return;
      }
    }

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    $.ajax({
      url: ironikus.ajax_url,
      type: 'post',
      data: {
        action: 'ironikus_manage_extensions',
        extension_slug: extensionSlug,
        extension_status: extStatus,
        extension_download: extDownload,
        extension_id: extId,
        extension_version: extVersion,
        ironikus_nonce: ironikus.ajax_nonce
      },
      success: function success(res) {
        res = JSON.parse(res);

        setTimeout(function () {
          $this.removeClass('is-loading');

          if (res['success'] != 'false' && res['success'] != false) {
            $this.attr('class', '').addClass('wpwh-extension-manage');
            // $this.removeClass( 'text-secondary text-primary text-success text-danger text-warning text-green' );
            $this.addClass(res['new_class']);
            $this.find('span').text(res['new_label']);
            $this.data('wpwh-extension-status', res['new_status']);

            if (extStatus == 'uninstalled') {
              appendDelete = '<a href="#" class="wpwh-text-danger wpwh-extension-manage" data-wpwh-extension="delete" data-wpwh-extension-status="delete" data-wpwh-extension-slug="' + extensionSlug + '" data-wpwh-extension-dl="' + extDownload + '">';
              appendDelete += '<span>' + res['delete_name'] + '</span>';
              appendDelete += '</a>';

              $(appendDelete).appendTo($this.parent());
            } else if (extStatus === 'delete') {
              $this.siblings('.wpwh-extension-manage').attr('class', '').addClass('wpwh-extension-manage');
              $this.siblings('.wpwh-extension-manage').addClass(res['new_class']);
              $this.siblings('.wpwh-extension-manage').find('span').text(res['new_label']);
              $this.siblings('.wpwh-extension-manage').data('wpwh-extension-status', res['new_status']);
              $this.remove();
            }
          }
        }, 200);
      },
      error: function error(errorThrown) {
        setTimeout(function () {
          $this.removeClass('is-loading');
        }, 200);
      }
    });
  });

  /**
  * New TB logic for trigger settings
  *
  * Jannis' code (Zeshan modified)
  */
  $(document).on("click", ".dm-preview__submit-btn", function (e) {
    e.preventDefault();

    var $this = $(this);
    var originalData = $("#wpwh-data-mapping-preview-input").val();
    var mappingType = $this.data('mapping-type');
    var currentMappingTemplate = createTemplateJson();

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    $.ajax({
      url: ironikus.ajax_url,
      type: 'post',
      data: {
        action: 'ironikus_data_mapping_create_preview',
        original_data: originalData,
        current_mapping_template: currentMappingTemplate,
        mapping_type: mappingType,
        ironikus_nonce: ironikus.ajax_nonce
      },
      success: function success(res) {
        res = JSON.parse(res);

        if (res['success'] != 'false') {
          $('#wpwh-data-mapping-preview-output').jsonBrowse(res['payload']);
        }

        setTimeout(function () {
          $this.removeClass('is-loading');
        }, 200);
      },
      error: function error(err) {
        console.log(err);
      }
    });
  });

  /**
  * Create Template JSON
  */
  function createTemplateJson() {
    var templateSettings = $("#wpwhpro-data-mapping-template-settings-form").serializeArray();
    var validatedMappingSettings = {};
    var mappingTemplate = {};
    var data = $('#wpwhpro-data-editor .data-new-key').map(function () {
      return {
        new_key: $(this).val(),
        singles: $(this).parent().parent().find('.wpwhpro-single-row-input').map(function () {
          return $(this).val();
        }).get()
      };
    }).get();

    //Add the global template settings
    $.each(templateSettings, function ($setting_key, $setting) {
      validatedMappingSettings[$setting.name] = $setting.value;
    });

    mappingTemplate.template_settings = validatedMappingSettings;
    mappingTemplate.template_data = data;

    return mappingTemplate;
  }

  /**
   * Authentication:
   *
   * Load authentication template
   */
  $(document).on("click", ".wpwh-edit-auth-template", function (e) {
    e.preventDefault();

    var $this = $(this);
    var authTemplateId = $this.data('wpwh-auth-id');
    var templateName = $this.data('wpwh-template-name');
    var modalId = $this.data('modal-id');
    var wrapperHtml = '';

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    if ($this.closest('.dropdown').length) {
      $this.closest('.dropdown').addClass('dropdown-is-loading');
    }

    if (authTemplateId && authTemplateId !== 'empty') {

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_load_authentication_template_data',
          auth_template_id: authTemplateId,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          var mappingHtml = '';
          res = JSON.parse(res);
          console.log(res);

          $this.removeClass('is-loading');

          if ($this.closest('.dropdown').length) {
            $this.closest('.dropdown').removeClass('dropdown-is-loading');
          }

          if (res['success'] === 'true' || res['success'] === true) {
            $('#wpwh-authentication-content-wrapper').html(res['content']);
            $("#wpwh-save-auth-template-button").data('wpwh-auth-id', res['id']);
            $("#editAuthTemplateModal .modal-title span").text(templateName);
            $(modalId).modal('show');
          }
        },
        error: function error(errorThrown) {
          $this.removeClass('is-loading');

          if ($this.closest('.dropdown').length) {
            $this.closest('.dropdown').removeClass('dropdown-is-loading');
          }
          console.log(errorThrown);
        }
      });
    }
  });

  /**
   * Authentication:
   *
   * Save authentication template
   */
  $(document).on("click", "#wpwh-save-auth-template-button", function () {

    var $this = $(this);
    var dataAuthId = $this.data('wpwh-auth-id');
    var datastring = $("#wpwh-authentication-template-form").serialize();

    console.log(dataAuthId);
    console.log(datastring);

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    if (dataAuthId && datastring) {

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_save_authentication_template',
          data_auth_id: dataAuthId,
          datastring: datastring,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          res = JSON.parse(res);

          if (res['success'] === 'true' || res['success'] === true) {
            $this.removeClass('is-loading');
            $this.closest('.modal').modal('hide');
          }
        },
        error: function error(errorThrown) {
          $this.removeClass('is-loading');
          console.log(errorThrown);
        }
      });
    } else {
      $this.removeClass('is-loading');
    }
  });

  /**
   * Authentication:
   *
   * Delete authentication template
   */
  $(document).on("click", ".wpwh-delete-auth-template", function () {

    var $this = $(this);
    var dataAuthId = $this.data('wpwh-auth-id');
    var wrapperHtml = '';

    if (dataAuthId && confirm("Are you sure you want to delete this template?")) {

      // Prevent from clicking again
      if ($this.hasClass('is-loading')) {
        return;
      }

      $this.addClass('is-loading');

      if ($this.closest('.dropdown').length) {
        $this.closest('.dropdown').addClass('dropdown-is-loading');
      }

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_delete_authentication_template',
          data_auth_id: dataAuthId,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          res = JSON.parse(res);

          if (res['success'] === 'true' || res['success'] === true) {

            $this.removeClass('is-loading');

            if ($this.closest('.dropdown').length) {
              $this.closest('.dropdown').removeClass('dropdown-is-loading');
            }

            $this.closest('tr').remove();

            $('#wpwh-authentication-content-wrapper').html('');
          }
        },
        error: function error(errorThrown) {
          console.log(errorThrown);
        }
      });
    }
  });
};
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/custom/data-mapping.js":
/*!********************************************************!*\
  !*** ./core/includes/assets/js/custom/data-mapping.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

exports.default = function () {

  /**
   * Fix for Multiple Bootstrap Modals Backdrops
   *
   * reference: https:// stackoverflow.com/questions/19305821/multiple-modals-overlay
   */
  $(document).on('show.bs.modal', '.wpwh-mapping-modal', function () {
    var zIndex = 9999 + 10 * $('.wpwh-mapping-modal:visible').length;
    $(this).css('z-index', zIndex);
    setTimeout(function () {
      $('.modal-backdrop').not('.wpwh-mapping-modal-stack').css('z-index', zIndex - 1).addClass('wpwh-mapping-modal-stack');
    }, 0);
  });

  $(document).on('hidden.bs.modal', '.wpwh-mapping-modal', function () {
    $('.wpwh-mapping-modal:visible').length && $(document.body).addClass('modal-open');
  });

  $(document).on('click', '[data-dismiss="this-modal"]', function () {
    $(this).closest('.modal').modal('hide');
  });

  // Traversing: Run through each item
  var $dataEditor = $('.wpwh-data-editor');
  var $modal = $('#wpwhDataMappingModal');
  var $exportDataDialogue = $('.wpwh-dm-export-data-dialogue');
  var $keySettings = $('.wpwh-data-mapping-key-settings');
  var $addRowBtn = $('.wpwh-add-row-button-text');

  // New TB logic for trigger settings
  $(document).on('click', '.wpwh-dm-preview__submit-btn', function (e) {
    e.preventDefault();

    var $this = $(this);
    var originalData = $('#wpwh-data-mapping-preview-input').val();
    var mappingType = $this.data('mapping-type');
    var currentMappingTemplate = createTemplateJson();

    //Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    $.ajax({
      url: ironikus.ajax_url,
      type: 'post',
      data: {
        action: 'ironikus_data_mapping_create_preview',
        original_data: originalData,
        current_mapping_template: currentMappingTemplate,
        mapping_type: mappingType,
        ironikus_nonce: ironikus.ajax_nonce
      },
      success: function success(res) {
        res = JSON.parse(res);

        if (res.success != 'false') {
          $('#wpwh-data-mapping-preview-output').jsonBrowse(res.payload);
        }

        setTimeout(function () {
          $this.removeClass('is-loading');
        }, 200);

        console.log(res);
      },
      error: function error(err) {
        console.log(err);
      }
    });
  });

  /**
   * Action: Save Template Button
   */
  $(document).on('click', '.wpwh-dm-save-template-btn', function (el) {

    var $this = $(this);
    var dataMappingId = $this.data('wpwh-mapping-id');
    var templateJson = createTemplateJson();

    console.log('=============================');
    console.log(dataMappingId);
    console.log(templateJson);
    console.log('=============================');

    if (dataMappingId) {

      if ($this.hasClass('is-loading')) {
        return;
      }

      $this.addClass('is-loading');

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_save_data_mapping_template',
          data_mapping_id: dataMappingId,
          data_mapping_json: templateJson,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          res = JSON.parse(res);

          $this.removeClass('is-loading');

          if (res['success'] === 'true' || res['success'] === true) {
            $('#wpwhDataMappingModal').modal('hide');
          }
        },
        error: function error(errorThrown) {
          $this.removeClass('is-loading');
          console.log(errorThrown);
        }
      });
    }
  });

  /**
   * Action: Delete Template Button
   */
  $(document).on('click', '.wpwh-dm-delete-template-btn', function (e) {
    e.preventDefault();

    var $this = $(this);
    var dataMappingId = $this.data('wpwh-mapping-id');

    if (dataMappingId && dataMappingId !== 'empty' && confirm('Are you sure you want to delete this template?')) {

      if ($this.hasClass('is-loading')) {
        return;
      }

      $this.addClass('is-loading');

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_delete_data_mapping_template',
          data_mapping_id: dataMappingId,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          res = JSON.parse(res);

          $this.removeClass('is-loading');

          if (res['success'] === 'true' || res['success'] === true) {
            $('#wpwhDataMappingModal').modal('hide');
            $('#data-mapping-' + dataMappingId).remove();
          }
        },
        error: function error(errorThrown) {
          $this.removeClass('is-loading');
          console.log(errorThrown);
        }
      });
    }
  });

  /**
   * Action: View Template Button
   */
  $(document).on('click', '.wpwh-dm-view-template-btn', function (e) {

    var $this = $(this);
    var dataMappingId = $this.data('wpwh-mapping-id');
    var wrapperHtml = '';

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    if ($this.closest('.dropdown').length) {
      $this.closest('.dropdown').addClass('dropdown-is-loading');
    }

    if (dataMappingId && dataMappingId !== 'empty') {

      $.ajax({
        url: ironikus.ajax_url,
        type: 'post',
        data: {
          action: 'ironikus_load_data_mapping_data',
          data_mapping_id: dataMappingId,
          ironikus_nonce: ironikus.ajax_nonce
        },
        success: function success(res) {
          res = JSON.parse(res);
          var mappingHtml = '';
          console.log(res);

          $modal.modal({
            backdrop: 'static',
            keyboard: false
          });

          $this.removeClass('is-loading');

          if ($this.closest('.dropdown').length) {
            $this.closest('.dropdown').removeClass('dropdown-is-loading');
          }

          // Add logic for delete and save button
          $('.wpwh-dm-delete-template-btn').data('wpwh-mapping-id', dataMappingId);
          $('.wpwh-dm-save-template-btn').data('wpwh-mapping-id', dataMappingId);
          $('.wpwh-settings-template-button').data('wpwh-mapping-id', dataMappingId);

          if (res.success === 'true' || res.success === true) {

            // Update template name input field
            $('#wpwh_data_mapping_template_name').val(res.data.name);

            mappingHtml = createDataMappingTable(res.data.template, res);

            // Assign settings
            $('#wpwh-data-mapping-template-settings-form .modal-body').html(getDataMappingTemplateSettingsHtml(res.template_settings, res.data.template));

            $dataEditor.html(mappingHtml);
            reloadSortable();
          }
        },
        error: function error(errorThrown) {
          $this.removeClass('is-loading');
          console.log(errorThrown);
        }
      });
    } else {
      $('.wpwh-dm-delete-template-btn').data('wpwh-mapping-id', '');
      $('.wpwh-dm-save-template-btn').data('wpwh-mapping-id', '');
      $('.wpwh-settings-template-button').data('wpwh-mapping-id', '');

      wrapperHtml += '<div class="wpwh-empty ui-sortable-handle">Add a row to get started!</div>';

      $dataEditor.html(wrapperHtml);
    }
  });

  /**
   * On Click: Import Data
   */
  $(document).on('click', '.wpwh-dm-import-data', function () {
    var json = prompt('Include the exported JSON here. (This will append the new fields to the existing mapping template.)', '{"myjson": "myvalue"}');
    var mappingResponse = JSON.parse(json);
    var htmlTable = '';

    if ((typeof mappingResponse === 'undefined' ? 'undefined' : _typeof(mappingResponse)) !== 'object' || $.isEmptyObject(mappingResponse)) {
      mappingResponse = {};
    }

    // Backwards compatibility
    if (typeof mappingResponse.template_data == 'undefined') {
      var tempData = {};

      tempData.template_data = mappingResponse;
      mappingResponse = tempData;
    }

    if (!$.isEmptyObject(mappingResponse.template_data)) {

      // Clear empty text ares
      if ($dataEditor.find('.wpwh-empty').length) {
        $dataEditor.html('');
      }

      $.each(mappingResponse.template_data, function (index, value) {
        htmlTable += getTableSingleRowLayout(value);
      });
    }

    $dataEditor.append(htmlTable);
    reloadSortable();
  });

  /**
   * On Click: Export Data
   */
  $(document).on('click', '.wpwh-dm-export-data', function () {
    var $this = $(this);
    var templateJson = createTemplateJson();
    var buttonText = $this.html();

    $('.wpwh-dm-export-data-dialogue').text(JSON.stringify(templateJson));
    console.log($('.wpwh-dm-export-data-dialogue'), $('.wpwh-dm-export-data-dialogue').text());
    copyToClipboard('.wpwh-dm-export-data-dialogue');

    $this.html('Copied!');
    alert('Copied!');

    setTimeout(function () {
      $this.html(buttonText);
    }, 2700);
  });

  /**
   * On Click: Delete Single Key
   */
  $(document).on('click', '.wpwh-dm-data-cell-delete', function () {
    var $this = $(this);
    $this.parent().parent('.wpwh-dm-data-cell').remove();
  });

  /**
   * On Click: Delete Data Row
   */
  $(document).on('click', '.wpwh-dm-data-editor-delete-key', function () {
    if (confirm('Are you sure you want to delete this row?')) {
      var $this = $(this);
      $this.closest('.wpwh-dm-single-data-row').remove();
    }
  });

  /**
   * Add Key Button Text
   */
  $(document).on('click', '.wpwh-dm-add-key-btn', function () {
    var $this = $(this);
    var html = getSingleKeyHtml('ironikus-empty');

    $this.prev(".wpwh-dm-data-incoming-keys").append(html);

    reloadSortable();
  });

  // JSON editor logic
  $(document).on('click', '.wpwh-add-row-button-text', function () {
    var $this = $(this);

    console.log('asdfasdf');

    // Clear empty text ares
    if ($dataEditor.find('.wpwh-empty').length) {
      $dataEditor.html('');
    }

    var singleRow = getTableSingleRowLayout('empty');

    $dataEditor.append(singleRow);

    reloadSortable();
  });

  // Open Old Key modal
  $(document).on('click', '.wpwh-dm-data-cell-preview-input', function () {

    var $this = $(this),
        target = $this.data('wpwh-modal'),
        $target = $(target);

    console.log('asdfasdfasdf');

    if ($target.length) {
      // First close all the existing modals.
      $target.modal({
        backdrop: 'static',
        keyboard: false
      });

      setTimeout(function () {
        $target.find('.wpwh-form-input').eq(0).trigger('focus');
      }, 500);
    }
  });

  // Trigger Settings Submit Button
  $(document).on('click', '.wpwh-submit-data-mapping-settings', function (e) {
    e.preventDefault();

    var $this = $(this);
    var settingId = $this.data('mapping-settings-id');

    // Prevent from clicking again
    if ($this.hasClass('is-loading')) {
      return;
    }

    $this.addClass('is-loading');

    if ($this.closest('.dropdown').length) {
      $this.closest('.dropdown').addClass('dropdown-is-loading');
    }

    updateDataMappingSettingsInput(settingId);

    // Close the modal
    setTimeout(function () {
      $this.removeClass('is-loading');

      if ($this.closest('.dropdown').length) {
        $this.closest('.dropdown').removeClass('dropdown-is-loading');
      }
      $('#wpwhDataMappingOldKeyModal-' + settingId).modal('hide');
    }, 800);
  });

  $(document).on({
    mouseover: function mouseover() {
      $(this).find('.wpwh-dm-data-cell-actions').addClass('active');
    },
    mouseleave: function mouseleave() {
      $(this).find('.wpwh-dm-data-cell-actions').removeClass('active');
    }
  }, '.wpwh-dm-data-cell');

  /**
   * Create Data Mapping Table
   *
   * @param {object} data
   * @param {object} args
   */
  function createDataMappingTable(data, args) {

    var htmlTable = '';
    var htmlAction = '';
    var jsonObj = JSON.parse(data);

    if ((typeof jsonObj === 'undefined' ? 'undefined' : _typeof(jsonObj)) !== 'object' || $.isEmptyObject(jsonObj)) {
      jsonObj = {};
    }

    // backwards compatibility
    if (typeof jsonObj.template_data == 'undefined') {
      var tempData = {};
      tempData.template_data = jsonObj;

      jsonObj = tempData;
    }

    if (!$.isEmptyObject(jsonObj.template_data)) {

      $.each(jsonObj.template_data, function (index, value) {
        htmlTable += getTableSingleRowLayout(value);
      });
    } else {
      htmlTable += '<div class="wpwh-empty">';
      htmlTable += args['text']['add_first_row_text'];
      htmlTable += '</div>';
    }

    // Map settings
    htmlTable += htmlAction;

    return htmlTable;
  }

  /**
   * Get Data Mapping Template Settings HTML
   *
   * @param {object} data
   * @param {string} values
   */
  function getDataMappingTemplateSettingsHtml(data, values) {

    var html = '';
    var settingsData = data;
    var settingsValuesJson = JSON.parse(values);

    if ((typeof settingsValuesJson === 'undefined' ? 'undefined' : _typeof(settingsValuesJson)) !== 'object' || $.isEmptyObject(settingsValuesJson)) {
      settingsValuesJson = {};
    }

    var settingsValues = typeof settingsValuesJson.template_settings !== 'undefined' ? settingsValuesJson.template_settings : [];
    var $selected = '';

    html += '<table id="wpwh-data-mapping-template-settings" class="wpwh-table wpwh-table--sm wpwh-table--no-style">';
    html += '<tbody>';

    $.each(settingsData, function (settingName, setting) {

      var isChecked = setting.type == 'checkbox' && setting.default_value == 'yes' ? 'checked' : '';
      var value = setting.type != 'checkbox' && typeof setting.default_value !== 'undefined' ? setting.default_value : '1';
      var placeholder = setting.type != 'checkbox' && typeof setting.placeholder !== 'undefined' ? setting.placeholder : '';
      var isMultipleKeys = typeof setting.multiple !== 'undefined' && setting.multiple ? '[]' : '';
      var isMultipleAttr = typeof setting.multiple !== 'undefined' && setting.multiple ? 'multiple' : '';

      if (typeof settingsValues[settingName] !== 'undefined') {
        value = settingsValues[settingName];
        isChecked = setting.type == 'checkbox' && value == 1 ? 'checked' : '';
      }

      html += '<tr valign="top">';
      html += '<td>';
      html += '<label for="iroikus-data-mapping-input-id-' + settingName + '">';
      html += '<strong>' + setting.label + '</strong>';
      html += '</label>';

      if ($.inArray(setting.type, ['text']) !== -1) {
        html += '<input class="wpwh-form-input" id="iroikus-data-mapping-input-id-' + settingName + '" name="' + settingName + '" type="' + setting.type + '" placeholder="' + placeholder + '" value="' + value + '" ' + isChecked + ' />';
      } else if ($.inArray(setting.type, ['checkbox']) !== -1) {
        html += '<div class="wpwh-toggle wpwh-toggle--on-off">';
        html += '<input id="iroikus-data-mapping-input-id-' + settingName + '" class="wpwh-toggle__input" name="' + settingName + '" type="' + setting.type + '" placeholder="' + placeholder + '" value="' + value + '" ' + isChecked + '>';
        html += '<label class="wpwh-toggle__btn" for="iroikus-data-mapping-input-id-' + settingName + '"></label>';
        html += '</div>';
      } else if (setting.type == 'select' && typeof setting.choices !== 'undefined') {
        html += '<select class="wpwh-form-input" name="' + settingName + isMultipleKeys + '" ' + isMultipleAttr + '>';

        if (typeof settingsValues[settingName] !== 'undefined') {
          settingsValues[settingName] = $.isArray(settingsValues[settingName]) ? settingsValues[settingName].reverse() : settingsValues[settingName];
        }

        $.each(setting.choices, function (choiceName, choice) {
          $selected = '';

          if (typeof settingsValues[settingName] !== 'undefined') {

            if ($.isArray(settingsValues[settingName])) {
              if (typeof settingsValues[settingName][choiceName] !== 'undefined') {
                $selected = 'selected="selected"';
              }
            } else {
              if (settingsValues[settingName] == choiceName) {
                $selected = 'selected="selected"';
              }
            }
          }

          html += '<option value="' + choiceName + '" ' + $selected + '>' + (choice.label ? choice.label : choice) + '</option>';
        });

        html += '</select>';
      }

      html += '</td>';
      html += '<td>' + setting.description + '</td>';
      html += '</tr>';
    });

    html += '</tbody>';
    html += '</table>';

    return html;
  }

  /**
   * Update Data Mapping Settings Input
   *
   * @param {int} uniqueId
   */
  function updateDataMappingSettingsInput(uniqueId) {
    var mainMappingKeyData = $('#wpwh-dm-cell-data-input-' + uniqueId).val();

    // Fallbback for older versions
    if (!isJson(mainMappingKeyData)) {
      mainMappingKeyData = '{"value":"' + mainMappingKeyData + '"}';
    }

    var mainMappingKeyValue = $('#wpwh-data-mapping-key-value-' + uniqueId).val();
    var mainMappingKeyDataJson = JSON.parse(mainMappingKeyData);
    var mainMappingSettingsData = $('#wpwh-data-mapping-template-settings-form-' + uniqueId).serializeArray();
    var validatedMappingValues = {};

    // Update the value data
    $('#wpwh-dm-data-cell-preview-input-' + uniqueId).val(mainMappingKeyValue);

    $.each(mainMappingSettingsData, function (settingKey, setting) {
      validatedMappingValues[setting.name] = setting.value;
    });

    // backwards compatibility to convert simple values to JSON
    if ((typeof response === 'undefined' ? 'undefined' : _typeof(response)) !== 'object') {
      mainMappingKeyDataJson = {};
    }

    mainMappingKeyDataJson.settings = $.extend(mainMappingKeyDataJson.settings, validatedMappingValues);
    mainMappingKeyDataJson.value = mainMappingKeyValue;

    $('#wpwh-dm-cell-data-input-' + uniqueId).val(JSON.stringify(mainMappingKeyDataJson));
  }

  /**
   * Get Table Single Row Layout
   *
   * @param {object} data
   */
  function getTableSingleRowLayout(data) {
    var html = '';
    var $new_key_placeholder = 'Add new key';
    html += '<div class="wpwh-dm-single-data-row">';

    // Add sortable button
    html += '<button class="wpwh-btn wpwh-btn--link px-0 wpwh-dm-data-editor-delete-key">' + deleteSvg() + '</button>';
    html += '<div class="wpwh-dm-data-cell-move">' + moveSvg() + '</div>';

    if (data === 'empty') {

      // setup new key
      html += '<div class="wpwh-dm-data-new-key-wrapper">';
      html += '<input class="wpwh-form-input" name="data-new-key" placeholder="' + $new_key_placeholder + '" />';
      html += '</div>';

      // Setup connector
      html += '<div class="wpwh-dm-data-connector">' + connectorSvg() + '</div>';

      // setup current data keys
      html += '<ul class="wpwh-dm-data-incoming-keys"></ul>';
    } else {

      // setup new key
      html += '<div class="wpwh-dm-data-new-key-wrapper">';
      html += '<input class="wpwh-form-input" name="data-new-key" value=\'' + data.new_key + '\' placeholder="' + $new_key_placeholder + '" />';
      html += '</div>';

      // Setup connector
      html += '<div class="wpwh-dm-data-connector">' + connectorSvg() + '</div>';

      // setup current data keys
      html += '<ul class="wpwh-dm-data-incoming-keys">';

      $.each(data.singles, function (index, value) {
        html += getSingleKeyHtml(value);
      });

      html += '';
      html += '</ul>';
    }

    // add new data key button @todo - translate text
    html += '<button type="button" class="wpwh-btn wpwh-btn--sm wpwh-btn--outline-secondary wpwh-dm-add-key-btn">Add Key</button>';

    html += '</div>';

    return html;
  }

  /**
   * Reload Sortable
   */
  function reloadSortable() {
    $('.wpwh-dm-data-incoming-keys').sortable({
      connectWith: '.wpwh-dm-data-cell',
      delay: 150
    });

    $dataEditor.sortable({
      connectWith: '.wpwh-dm-single-data-row',
      delay: 150
    });
  }

  /**
   * Is JSON
   *
   * Check if string is a JSON or not.
   *
   * @param {string} str
   */
  function isJson(str) {
    try {
      JSON.parse(str);
    } catch (e) {
      return false;
    }

    return true;
  }

  /**
   * Generate Uniquite Page ID
   */
  function generateUniquePageId() {
    return '_' + Math.random().toString(36).substr(2, 9);
  }

  /**
   * Get Single Key HTML
   *
   * @param {string} value
   */
  function getSingleKeyHtml(value) {
    var html = '';
    var mapKeyPlaceholder = 'Add old key';
    var newKeyActionButton = 'Edit';
    var uniqueId = generateUniquePageId();
    var settingsJson = getDataMappingSettingsJson();
    var settings = '';
    var realValue = value;
    var keyData = [];

    // Add new logic for data mapping 2.0
    if (isJson(value)) {
      keyData = JSON.parse(value);
      realValue = keyData.value;
    }

    console.log(settingsJson);
    settings = getDataMappingSettingsHtml(uniqueId, settingsJson, keyData);

    html += '<li id="wpwh-dm-data-cell-' + uniqueId + '" class="wpwh-dm-data-cell">';
    // html += '<a id="wpwh-dm-data-cell-link-' + uniqueId + '" title="' + realValue + '" href="#TB_inline?height=330&width=800&inlineId=wpwh-data-mapping-settings-' + uniqueId + '" class="thickbox" tabindex="-1" role="button">';

    if (realValue == 'ironikus-empty') {
      html += '<input id="wpwh-dm-data-cell-preview-input-' + uniqueId + '" class="wpwh-form-input wpwh-dm-data-cell-preview-input" data-wpwh-modal="#wpwhDataMappingOldKeyModal-' + uniqueId + '" name="data-preview-key" placeholder="' + mapKeyPlaceholder + '" readonly />';
    } else {
      html += '<input id="wpwh-dm-data-cell-preview-input-' + uniqueId + '" class="wpwh-form-input wpwh-dm-data-cell-preview-input" data-wpwh-modal="#wpwhDataMappingOldKeyModal-' + uniqueId + '" name="data-preview-key" value=\'' + realValue + '\' placeholder="' + mapKeyPlaceholder + '" readonly />';
    }

    // html += '</a>';

    var modalHtml = '';

    var modalHtml3 = '<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingOldKeyModal-' + uniqueId + '" tabindex="-1" role="dialog">\n      <div class="modal-dialog" role="document">\n        <div class="modal-content">\n          <div class="modal-header">\n            <h3 class="modal-title">Add Old Key</h3>\n            <button type="button" class="close" data-dismiss="this-modal" aria-label="Close">\n              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http:// www.w3.org/2000/svg">\n                <path d="M13 1L1 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />\n                <path d="M1 1L13 13" stroke="#264653" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />\n              </svg>\n            </button>\n          </div>\n\n          <div class="modal-body">\n            <div class="wpwh-form-field">\n              <label class="wpwh-form-label" for="wpwh-data-mapping-key-value-' + uniqueId + '">Value</label>';

    if (realValue == 'ironikus-empty') {
      modalHtml3 += '<input class="wpwh-form-input w-100" type="text" id="wpwh-data-mapping-key-value-' + uniqueId + '" aria-label="Add value here" placeholder="Add value here">';
    } else {
      modalHtml3 += '<input class="wpwh-form-input w-100" type="text" id="wpwh-data-mapping-key-value-' + uniqueId + '" aria-label="Add value here" placeholder="Add value here" value=\'' + realValue + '\'>';
    }

    modalHtml3 += '</div>\n            <form id="wpwh-data-mapping-template-settings-form-' + uniqueId + '">\n              ' + settings + '\n            </form>';

    if (realValue == 'ironikus-empty') {
      modalHtml3 += '<input id="wpwh-dm-cell-data-input-' + uniqueId + '" type="hidden" class="wpwh-dm-data-cell-data-input" name="data-income-key" placeholder="' + mapKeyPlaceholder + '" />';
    } else {
      modalHtml3 += '<input id="wpwh-dm-cell-data-input-' + uniqueId + '" type="hidden" class="wpwh-dm-data-cell-data-input" name="data-income-key" value="' + value.replace(/"/g, '&quot;') + '" placeholder="' + mapKeyPlaceholder + '" />';
    }

    modalHtml3 += '</div>\n          <div class="modal-footer text-center">\n            <button type="submit" class="wpwh-btn wpwh-btn--secondary wpwh-submit-data-mapping-settings" data-mapping-settings-id="' + uniqueId + '"><span>Apply Settings</span></button>\n          </div>\n        </div>\n      </div>\n    </div>';

    // Start Data Mapping Modal HTML
    modalHtml += '<div id="wpwh-data-mapping-settings-' + uniqueId + '" style="display:none;">';
    modalHtml += '<div class="wpwh-data-mapping-settings-popup-wrapper">';

    // add value inout type logic
    modalHtml += '<div class="row">';
    modalHtml += '<div class="col-9 wpwh-data-mapping-input-wrapper">';
    modalHtml += '<div class="input-group mb-3 mt-3">';
    modalHtml += '<div class="input-group-prepend">';
    modalHtml += '<span class="input-group-text" id="inputGroup-sizing-default">Value</span>';
    modalHtml += '</div>';

    if (realValue == 'ironikus-empty') {
      modalHtml += '<input id="wpwh-data-mapping-key-value-' + uniqueId + '" type="text" class="form-control" aria-label="Include your value here">';
    } else {
      modalHtml += '<input id="wpwh-data-mapping-key-value-' + uniqueId + '" type="text" class="form-control" aria-label="Include your value here" value=\'' + realValue + '\'>';
    }

    modalHtml += '</div>';
    modalHtml += '</div>';
    modalHtml += '<div class="col wpwh-data-mapping-submit-button-wrapper">';

    // Add safe button
    modalHtml += '<div class="ironikus-single-webhook-data-mapping-settings">';
    modalHtml += '<p class="btn btn-primary h30 ironikus-submit-data-mapping-settings" mapping-settings-id="' + uniqueId + '">';
    modalHtml += '<span class="ironikus-save-text active">Apply Settings</span>';
    modalHtml += '<img class="ironikus-loader" src="' + ironikus.plugin_url + 'core/includes/assets/img/loader.gif" />';
    modalHtml += '</p>';
    modalHtml += '</div>';
    modalHtml += '';

    modalHtml += '</div>';
    modalHtml += '</div>';

    modalHtml += '<form id="wpwh-data-mapping-template-settings-form-' + uniqueId + '">';
    modalHtml += settings;
    modalHtml += '</form>';

    if (realValue == 'ironikus-empty') {
      modalHtml += '<input id="wpwh-dm-cell-data-input-' + uniqueId + '" type="hidden" class="wpwh-dm-data-cell-data-input" name="data-income-key" placeholder="' + mapKeyPlaceholder + '" />';
    } else {
      modalHtml += '<input id="wpwh-dm-cell-data-input-' + uniqueId + '" type="hidden" class="wpwh-dm-data-cell-data-input" name="data-income-key" value="' + value.replace(/"/g, '&quot;') + '" placeholder="' + mapKeyPlaceholder + '" />';
    }

    modalHtml += '</div>';
    modalHtml += '</div>';
    // END Data Mapping Modal HTML

    // html += modalHtml;
    html += modalHtml3;

    html += '<div class="wpwh-dm-data-cell-actions">';
    html += '<button class="wpwh-btn wpwh-btn--link p-0 wpwh-dm-data-cell-delete">' + deleteSvg() + '</button>';
    html += '<div class="wpwh-dm-data-cell-move m-0">' + moveSvg() + '</div>';
    html += '</div>';
    html += '</li>';

    return html;
  }

  /**
   * Get Data Mapping Settings JSON
   */
  function getDataMappingSettingsJson() {
    console.log(wpwhDataMappingSettings);
    return wpwhDataMappingSettings ? wpwhDataMappingSettings : $keySettings.html();
  }

  /**
   * Get Data Mapping Settings HTML
   *
   * @param {int} uniqueId
   * @param {string} data
   * @param {string} values
   */
  function getDataMappingSettingsHtml(uniqueId, data, values) {
    var html = '';
    var settingsData = data;
    var settingsValues = typeof values.settings !== 'undefined' ? values.settings : [];
    var selected = '';

    console.log(settingsData);

    html += '<table id="wpwh-settings-table-' + uniqueId + '" class="wpwh-table wpwh-table--sm wpwh-table--no-style">';
    html += '<tbody>';

    $.each(settingsData, function (settingName, setting) {

      var isChecked = setting.type == 'checkbox' && setting.default_value == 'yes' ? 'checked' : '';
      var value = setting.type != 'checkbox' && typeof setting.default_value !== 'undefined' ? setting.default_value : '1';
      var placeholder = setting.type != 'checkbox' && typeof setting.placeholder !== 'undefined' ? setting.placeholder : '';
      var isMultipleKeys = typeof setting.multiple !== 'undefined' && setting.multiple ? '[]' : '';
      var isMultipleAttr = typeof setting.multiple !== 'undefined' && setting.multiple ? 'multiple' : '';

      if (typeof settingsValues[settingName] !== 'undefined') {
        value = settingsValues[settingName];
        isChecked = setting.type == 'checkbox' && value == 1 ? 'checked' : '';
      }

      html += '<tr>';
      html += '<td>';
      html += '<label class="wpwh-form-label" for="iroikus-data-mapping-input-id-' + settingName + '-' + uniqueId + '">';
      html += '<strong>' + setting.label + '</strong>';
      html += '</label>';

      if ($.inArray(setting.type, ['text']) !== -1) {
        html += '<input class="wpwh-form-input" id="iroikus-data-mapping-input-id-' + settingName + '-' + uniqueId + '" name="' + settingName + '" type="' + setting.type + '" placeholder="' + placeholder + '" value="' + value + '" ' + isChecked + ' />';
      } else if ($.inArray(setting.type, ['checkbox']) !== -1) {

        html += '<div class="wpwh-toggle wpwh-toggle--on-off">';
        html += '<input type="' + setting.type + '" id="iroikus-data-mapping-input-id-' + settingName + '-' + uniqueId + '" name="' + settingName + '" class="wpwh-toggle__input" placeholder="' + placeholder + '" value="' + value + '" ' + isChecked + '>';
        html += '<label class="wpwh-toggle__btn" for="iroikus-data-mapping-input-id-' + settingName + '-' + uniqueId + '"></label>';
        html += '</div>';
      } else if (setting.type == 'select' && typeof setting.choices !== 'undefined') {
        html += '<select class="wpwh-form-input" name="' + settingName + isMultipleKeys + '" ' + isMultipleAttr + '>';

        if (typeof settingsValues[settingName] !== 'undefined') {
          settingsValues[settingName] = $.isArray(settingsValues[settingName]) ? settingsValues[settingName].reverse() : settingsValues[settingName];
        }

        $.each(setting.choices, function (choiceName, $choice_label) {
          selected = '';
          if (typeof settingsValues[settingName] !== 'undefined') {

            if ($.isArray(settingsValues[settingName])) {
              if (typeof settingsValues[settingName][choiceName] !== 'undefined') {
                selected = 'selected="selected"';
              }
            } else {
              if (settingsValues[settingName] == choiceName) {
                selected = 'selected="selected"';
              }
            }
          }

          html += '<option value="' + choiceName + '" ' + selected + '>' + $choice_label + '</option>';
        });

        html += '</select>';
      }
      html += '</td>';
      html += '<td>' + setting.description + '</td>';
      html += '</tr>';
    });

    html += '</tbody>';
    html += '</table>';

    return html;
  }

  /**
   * Copy To Clipboard
   *
   * @param {string} element
   */
  function copyToClipboard(element) {
    var $temp = $('<input>');
    var json = $(element).text();
    $(element).after($temp);
    $temp.val(json).select();
    document.execCommand('copy');
    $temp.remove();
    console.log(json);
  }

  /**
   * Create Template JSON
   */
  function createTemplateJson() {
    var templateSettings = $('#wpwh-data-mapping-template-settings-form').serializeArray();
    var validatedMappingSettings = {};
    var mappingTemplate = {};

    var data = $dataEditor.find('[name="data-new-key"]').map(function () {
      var $thisKey = $(this);
      var output = {};

      // Return new key as output
      output.new_key = $thisKey.val();

      // Return mapped keys values
      output.singles = $thisKey.closest('.wpwh-dm-single-data-row').find('.wpwh-dm-data-cell-data-input').map(function () {
        return $(this).val();
      }).get();

      return output;
    }).get();

    // Add the global template settings
    $.each(templateSettings, function (settingKey, setting) {
      validatedMappingSettings[setting.name] = setting.value;
    });

    // Add validated mapping template to the final return array.
    mappingTemplate.template_settings = validatedMappingSettings;
    mappingTemplate.template_data = data;

    return mappingTemplate;
  }

  /**
   * Get Connector SVG
   */
  function connectorSvg() {
    return '<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http:// www.w3.org/2000/svg">\n      <path d="M1 13L7 7L1 1" stroke="#DFDFDF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>\n    </svg>';
  }

  /**
   * Delete SVG
   */
  function deleteSvg() {
    return '<svg xmlns="http:// www.w3.org/2000/svg" width="24" height="24" fill="none">\n      <defs/>\n      <path stroke="red" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M8 6V4c0-.53043.21071-1.03914.58579-1.41421C8.96086 2.21071 9.46957 2 10 2h4c.5304 0 1.0391.21071 1.4142.58579C15.7893 2.96086 16 3.46957 16 4v2m3 0v14c0 .5304-.2107 1.0391-.5858 1.4142S17.5304 22 17 22H7c-.53043 0-1.03914-.2107-1.41421-.5858C5.21071 21.0391 5 20.5304 5 20V6h14zM10 11v6M14 11v6"/>\n    </svg>';
  }

  /**
   * Move SVG
   */
  function moveSvg() {
    return '<svg xmlns="http:// www.w3.org/2000/svg" width="11" height="18" fill="none">\n      <defs/>\n      <path stroke="#264653" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10c.55228 0 1-.44772 1-1s-.44772-1-1-1-1 .44772-1 1 .44772 1 1 1zM2 10c.55228 0 1-.44772 1-1s-.44772-1-1-1-1 .44772-1 1 .44772 1 1 1zM9 3c.55228 0 1-.44772 1-1s-.44772-1-1-1-1 .44772-1 1 .44772 1 1 1zM2 3c.55228 0 1-.44772 1-1s-.44772-1-1-1-1 .44772-1 1 .44772 1 1 1zM9 17c.55228 0 1-.4477 1-1s-.44772-1-1-1-1 .4477-1 1 .44772 1 1 1zM2 17c.55228 0 1-.4477 1-1s-.44772-1-1-1-1 .4477-1 1 .44772 1 1 1z"/>\n    </svg>';
  }
};
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/custom/triggers.js":
/*!****************************************************!*\
  !*** ./core/includes/assets/js/custom/triggers.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});
var Triggers = function Triggers() {

  var $window = $(window),
      $body = $('body');

  var $triggerEls = $('[data-wpwh-trigger]');

  // Run through all triggers instances
  $triggerEls.each(function (i, el) {

    var $triggerEl = $(el);
    var $search = $triggerEl.find('[data-wpwh-trigger-search]'),
        $searchItems = $triggerEl.find('.wpwh-trigger-search__items'),
        $triggerItems = $triggerEl.find('.wpwh-trigger-item'),
        $triggerIds = $triggerEl.find('[data-wpwh-trigger-id]'),
        $groups = $triggerEl.find('.wpwh-trigger-search__item--group'),
        $fallbackMsg = $('<div class="wpwh-trigger-search__item wpwh-trigger-search__item--default">No trigger available based on your search</div>'),
        $content = $triggerEl.find('[data-wpwh-trigger-content]'),
        $name = $triggerEl.find('[data-wpwh-trigger-name]'),
        $tbody = $triggerEl.find('tbody');

    var pageType = '';

    // Append fallback message to the items and hide it.
    $searchItems.append($fallbackMsg);
    $fallbackMsg.hide();

    // Set page type, e.g., is it 'send-data' or 'receive-data'
    pageType = getUrlParam('wpwhprovrs');

    // When you start typing in the $search, filter items
    $search.on('keyup', function (e) {
      searchInput($(this));
    });

    $search[0].addEventListener('search', function (e) {
      searchInput($(this));
    });

    /**
     * Search Input
     *
     * @param {object} $el
     */
    function searchInput($el) {
      var thisVal = $el.val().toLowerCase();

      // Filter the items that need to be hidden and hide them
      var $toHide = $triggerIds.show().filter(function (i, el) {

        var triggerId = $(this).data('wpwh-trigger-id');
        var triggerText = $(this).text().toLowerCase();

        /**
         * Trigger text conditionals.
         */
        // Check if the text includes searched string.
        var hasText = triggerText.includes(thisVal);
        // Check if the text (after removing spaces) includes searched string
        var hasTextIgnoreSpace = triggerText.replace(/\s+/g, '').includes(thisVal);
        // Check if the text includes searched string (after replacing _ with spaces)
        var hasTextNoSpace = triggerText.includes(thisVal.replace('_', ' '));

        /**
        * Trigger id conditionals.
        */
        // Check if the trigger id includes searched string.
        var hasTrigger = triggerId && triggerId.includes(thisVal);
        // Check if the trigger id includes searched string (after replacing spaces with _).
        var hasTriggerIgnoreSpace = triggerId && triggerId.includes(thisVal.replace(/\s+/g, '_'));
        // Check if the trigger id (after replacing _ with spaces) includes searched string.
        var hasTriggerNoSpace = triggerId && triggerId.replace('_', '').includes(thisVal);

        if (hasText || hasTextIgnoreSpace || hasTextNoSpace || hasTrigger || hasTriggerIgnoreSpace || hasTriggerNoSpace) {
          return false;
        }

        return true;
      }).hide();

      // Show hide groups
      $groups.show().filter(function (i, el) {

        var $nextVisible = $(this).nextAll().filter(':visible').eq(0);

        if (!$nextVisible.length || $nextVisible.hasClass('wpwh-trigger-search__item--group') || $nextVisible.hasClass('wpwh-trigger-search__item--default')) {
          return true;
        }

        return false;
      }).hide();

      // Show the fallback message if search doesn't find anything
      // Hide it otherwise
      if ($toHide.length === $triggerIds.length) {
        $fallbackMsg.show();
      } else {
        $fallbackMsg.hide();
      }
    }

    // Event: On click
    $triggerIds.on('click', function (e) {
      e.preventDefault();

      var $this = $(this);
      var id = $this.attr('href');
      var triggerId = $this.data('wpwh-trigger-id');

      if ($(id).length > 0) {
        $this.addClass('wpwh-trigger-search__item--active').siblings().removeClass('wpwh-trigger-search__item--active');

        $(id).show().addClass('wpwh-trigger-item--active').siblings().hide().removeClass('wpwh-trigger-item--active');

        $('html,body').animate({
          scrollTop: $(id).offset().top - ($('#wpadminbar').outerHeight() + 10)
        });

        if (pageType == 'receive-data') {
          insertParamToUrl('wpwh-action', triggerId);
        } else {
          insertParamToUrl('wpwh-trigger', triggerId);
        }
      }
    });

    // Select the trigger if URL has a #hash that matches the trigger ID
    if (window.location.hash) {
      $('[data-wpwh-trigger-id][href="' + window.location.hash + '"]').trigger('click');
    } else if (!$triggerIds.find('.wpwh-trigger-search__item--active')) {
      $triggerIds.first().trigger('click');
    } else {
      $triggerIds.find('.wpwh-trigger-search__item--active').first().trigger('click');
    }
  });
};

exports.default = Triggers;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/custom/webhook-search.js":
/*!**********************************************************!*\
  !*** ./core/includes/assets/js/custom/webhook-search.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});
var WebhookSearch = function WebhookSearch() {

  var $window = $(window),
      $body = $('body');

  var $instances = $('[data-wpwh-webook-search]');

  // Run through all triggers instances
  $instances.each(function (i, el) {

    var $instance = $(el);
    var $search = $instance.find('[data-wpwh-webhook-search]'),
        $items = $instance.find('tbody tr'),
        $fallbackMsg = $('<tr class="wpwh-text-center"><td>No webhook is available based on your search</tr>');

    if ($search.length) {

      /**
       * Search Input
       *
       * @param {object} $el
       */
      var searchInput = function searchInput($el) {
        var thisVal = $el.val().toLowerCase();

        // Filter the items that need to be hidden and hide them
        var $toHide = $items.show().filter(function (i, el) {

          var name = $(this).data('wpwh-webhook-search-name') || '';
          var url = $(this).data('wpwh-webhook-search-url') || '';

          console.log(name, url);
          console.log(name.toLowerCase().includes(thisVal));
          console.log(url.toLowerCase().includes(thisVal));

          if (name.toLowerCase().includes(thisVal) || url.toLowerCase().includes(thisVal)) {
            return false;
          }

          return true;
        }).hide();

        // Show the fallback message if search doesn't find anything
        // Hide it otherwise
        if ($toHide.length === $items.length) {
          $fallbackMsg.show();
        } else {
          $fallbackMsg.hide();
        }
      };

      var pageType = '';

      $fallbackMsg.find('td').attr('colspan', $instance.find('thead td, thead th').length);

      // Append fallback message to the items and hide it.
      $instance.find('tbody').append($fallbackMsg);
      $fallbackMsg.hide();

      // Set page type, e.g., is it 'send-data' or 'receive-data'
      pageType = getUrlParam('wpwhprovrs');

      // On $search input
      $search.on('keydown', function (e) {

        if (e.code === 'Tab' || e.code === 'Enter') {
          e.preventDefault();
        }

        searchInput($(this));
      });

      $search[0].addEventListener('search', function (e) {
        searchInput($(this));
      });
    }
  });
};

exports.default = WebhookSearch;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/custom/wpwh-events.js":
/*!*******************************************************!*\
  !*** ./core/includes/assets/js/custom/wpwh-events.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function () {

  // Traversing: Run through each item
  $(document).on('click', '[data-wpwh-event]', function (e) {
    e.preventDefault();

    console.log('asdfasdfasdf');

    var $thisEl = $(this);
    var event = $thisEl.data('wpwh-event');
    var eventType = $thisEl.data('wpwh-event-type');
    var eventElement = $thisEl.data('wpwh-event-element');
    var $eventElement = $(eventElement);
    var $dropdown = $thisEl.closest('.dropdown');

    // Add loader class to the dropdown.
    $dropdown.addClass('dropdown-is-loading');

    /**
     * Event: Delete
     */
    if (event === 'delete') {

      if (confirm("Are you sure you want to delete this webhook?")) {

        if (eventType === 'receive') {

          var webhookSlug = $thisEl.data('wpwh-webhook-slug');

          $.ajax({
            url: ironikus.ajax_url,
            type: 'post',
            data: {
              action: 'ironikus_remove_webhook_action',
              webhook: webhookSlug,
              ironikus_nonce: ironikus.ajax_nonce
            },
            success: function success(res) {
              res = JSON.parse(res);

              $dropdown.removeClass('dropdown-is-loading');

              if (res['success'] != 'false') {
                $eventElement.remove();
              }
            },
            error: function error(err) {
              console.log(err);

              // Remove loader.
              $dropdown.removeClass('dropdown-is-loading');
            }
          });
        } else if (eventType === 'send') {

          var webhook = $thisEl.data('wpwh-delete');
          var webhookGroup = $thisEl.data('wpwh-group');

          $.ajax({
            url: ironikus.ajax_url,
            type: 'post',
            data: {
              action: 'ironikus_remove_webhook_trigger',
              webhook: webhook,
              webhook_group: webhookGroup,
              ironikus_nonce: ironikus.ajax_nonce
            },
            success: function success(res) {
              res = JSON.parse(res);

              $dropdown.removeClass('dropdown-is-loading');

              if (res['success'] != 'false') {
                $eventElement.remove();
              }
            },
            error: function error(err) {
              console.log(err);

              // Remove loader.
              $dropdown.removeClass('dropdown-is-loading');
            }
          });
        }
      } else {
        $dropdown.removeClass('dropdown-is-loading');
      }
    }

    /**
     * Event: Deactivate
     */
    else if (event === 'deactivate') {

        $eventElement.addClass('performing-event');

        var _webhookSlug = $thisEl.data('wpwh-webhook-slug');
        var _webhookGroup = $thisEl.data('wpwh-webhook-group');
        var webhookStatus = $thisEl.data('wpwh-webhook-status');
        var $statusCell = $thisEl.closest('tr').find('.wpwh-status-cell');
        var $statusCellTip = $statusCell.find('[data-tippy-content]');

        $.ajax({
          url: ironikus.ajax_url,
          type: 'post',
          data: {
            action: 'ironikus_change_status_webhook_action',
            webhook: _webhookSlug,
            webhook_status: webhookStatus,
            webhook_group: _webhookGroup,
            ironikus_nonce: ironikus.ajax_nonce
          },
          success: function success(res) {

            // Parse JSON.
            res = JSON.parse(res);

            console.log(res);

            // Remove loader.
            $dropdown.removeClass('dropdown-is-loading');

            // Remove class from all the events.
            $eventElement.removeClass('performing-event');

            // Check if response is successful
            if (res['success'] != 'false' && res['success'] != false) {
              setTimeout(function () {

                // Update text.
                $thisEl.find('span').text(res['new_status_name']);

                // Update status.
                $thisEl.data('wpwh-webhook-status', res['new_status']);

                // Add classes to the parent element based on the status.
                if (res['new_status'] == 'active') {
                  $eventElement.addClass('is-active').removeClass('is-inactive');
                  $thisEl.find('.img-deactivate').show();
                  $thisEl.find('.img-activate').hide();

                  // Update Status Cell's state.
                  $statusCell.toggleClass('wpwh-status-cell--active wpwh-status-cell--inactive');

                  // Update Status Cell Tooltip's content
                  if ($statusCellTip.length) {
                    $statusCellTip[0]._tippy.setContent('inactive');
                  }
                } else {
                  $eventElement.addClass('is-inactive').removeClass('is-active');
                  $thisEl.find('.img-deactivate').hide();
                  $thisEl.find('.img-activate').show();

                  // Update Status Cell's state.
                  $statusCell.toggleClass('wpwh-status-cell--inactive wpwh-status-cell--active');

                  // Update Status Cell Tooltip's content
                  if ($statusCellTip.length) {
                    $statusCellTip[0]._tippy.setContent('active');
                  }
                }

                // Based on success, add classes to the parent element.
                if (res['success'] != 'false') {
                  $eventElement.addClass('is-success');
                } else {
                  $eventElement.addClass('is-failure');
                }
              }, 200);

              // Remove all status classes after 2.7 seconds
              setTimeout(function () {
                $eventElement.removeClass('is-success is-failure');
              }, 2700);
            }
          },
          error: function error(err) {
            console.log(err);

            // Remove loader.
            $dropdown.removeClass('dropdown-is-loading');
          }
        });
      }

      /**
       * Event: Save
       */
      else if (event === 'save') {

          if (eventType === 'receive') {

            var _webhook = $thisEl.data('webhook-id');
            var formData = $thisEl.closest('form').serialize();

            // Prevent from clicking again
            if ($thisEl.hasClass('is-loading')) {
              return;
            }

            $thisEl.addClass('is-loading');

            $.ajax({
              url: ironikus.ajax_url,
              type: 'post',
              data: {
                action: 'ironikus_save_webhook_action_settings',
                webhook_id: _webhook,
                action_settings: formData,
                ironikus_nonce: ironikus.ajax_nonce
              },
              success: function success(res) {
                console.log(res);

                $thisEl.removeClass('is-loading').after('<span class="btn-msg wpwh-text-success ml-2 wpwh-text-small">Settings saved</span>');

                setTimeout(function () {
                  $thisEl.next('.btn-msg').remove();
                }, 3000);
              },
              error: function error(err) {
                console.log(err);
              }
            });
          } else if (eventType === 'send') {

            var _webhook2 = $thisEl.data('webhook-id');
            var _webhookGroup2 = $thisEl.data('webhook-group');
            var _formData = $thisEl.closest('form').serialize();

            // Prevent from clicking again
            if ($thisEl.hasClass('is-loading')) {
              return;
            }

            $thisEl.addClass('is-loading');

            $.ajax({
              url: ironikus.ajax_url,
              type: 'post',
              data: {
                action: 'ironikus_save_webhook_trigger_settings',
                webhook_id: _webhook2,
                webhook_group: _webhookGroup2,
                trigger_settings: _formData,
                ironikus_nonce: ironikus.ajax_nonce
              },
              success: function success(res) {
                console.log(res);
                $thisEl.removeClass('is-loading').after('<span class="btn-msg wpwh-text-success ml-2 wpwh-text-small">Settings saved</span>');

                setTimeout(function () {
                  $thisEl.next('.btn-msg').remove();
                }, 3000);
              },
              error: function error(err) {
                console.log(err);
              }
            });
          }
        }

        /**
         * Event: Demo
         */
        else if (event === 'demo') {

            if (eventType === 'send') {

              var _webhook3 = $thisEl.data('wpwh-webhook');
              var _webhookGroup3 = $thisEl.data('wpwh-group');
              var webhookCallback = $thisEl.data('wpwh-demo-data-callback');

              $.ajax({
                url: ironikus.ajax_url,
                type: 'post',
                data: {
                  action: 'ironikus_test_webhook_trigger',
                  webhook: _webhook3,
                  webhook_group: _webhookGroup3,
                  webhook_callback: webhookCallback,
                  ironikus_nonce: ironikus.ajax_nonce
                },
                success: function success(res) {
                  res = JSON.parse(res);

                  $dropdown.removeClass('dropdown-is-loading');

                  if (res['success'] != 'false') {
                    // show success message
                  } else {
                      // show failure message
                    }
                },
                error: function error(err) {
                  console.log(err);
                }
              });
            }
          }
  });
};
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/main.js":
/*!*****************************************!*\
  !*** ./core/includes/assets/js/main.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(jQuery) {

var _tippy = __webpack_require__(/*! tippy.js */ "./node_modules/tippy.js/dist/tippy.esm.js");

var _tippy2 = _interopRequireDefault(_tippy);

__webpack_require__(/*! bootstrap/js/dist/modal */ "./node_modules/bootstrap/js/dist/modal.js");

__webpack_require__(/*! bootstrap/js/dist/tab */ "./node_modules/bootstrap/js/dist/tab.js");

__webpack_require__(/*! bootstrap/js/dist/scrollspy */ "./node_modules/bootstrap/js/dist/scrollspy.js");

__webpack_require__(/*! bootstrap/js/dist/collapse */ "./node_modules/bootstrap/js/dist/collapse.js");

__webpack_require__(/*! bootstrap/js/dist/dropdown */ "./node_modules/bootstrap/js/dist/dropdown.js");

__webpack_require__(/*! bootstrap/js/dist/alert */ "./node_modules/bootstrap/js/dist/alert.js");

__webpack_require__(/*! simplebar */ "./node_modules/simplebar/dist/simplebar.esm.js");

__webpack_require__(/*! ./vendor/jsonviewer */ "./core/includes/assets/js/vendor/jsonviewer.js");

__webpack_require__(/*! ./vendor/jquery.matchHeight-min */ "./core/includes/assets/js/vendor/jquery.matchHeight-min.js");

var _insertParamToURL = __webpack_require__(/*! ./vendor/insertParamToURL */ "./core/includes/assets/js/vendor/insertParamToURL.js");

var _insertParamToURL2 = _interopRequireDefault(_insertParamToURL);

var _getUrlParam = __webpack_require__(/*! ./vendor/getUrlParam */ "./core/includes/assets/js/vendor/getUrlParam.js");

var _getUrlParam2 = _interopRequireDefault(_getUrlParam);

var _triggers = __webpack_require__(/*! ./custom/triggers */ "./core/includes/assets/js/custom/triggers.js");

var _triggers2 = _interopRequireDefault(_triggers);

var _ajaxScripts = __webpack_require__(/*! ./custom/ajax-scripts */ "./core/includes/assets/js/custom/ajax-scripts.js");

var _ajaxScripts2 = _interopRequireDefault(_ajaxScripts);

var _wpwhEvents = __webpack_require__(/*! ./custom/wpwh-events */ "./core/includes/assets/js/custom/wpwh-events.js");

var _wpwhEvents2 = _interopRequireDefault(_wpwhEvents);

var _dataMapping = __webpack_require__(/*! ./custom/data-mapping */ "./core/includes/assets/js/custom/data-mapping.js");

var _dataMapping2 = _interopRequireDefault(_dataMapping);

var _webhookSearch = __webpack_require__(/*! ./custom/webhook-search */ "./core/includes/assets/js/custom/webhook-search.js");

var _webhookSearch2 = _interopRequireDefault(_webhookSearch);

var _aos = __webpack_require__(/*! aos */ "./node_modules/aos/dist/aos.js");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * ---------> assets/js/src/app.js
 */

// Vendor Plugins

window.$ = jQuery;
window.tippy = _tippy2.default;
window.insertParamToUrl = _insertParamToURL2.default;
window.getUrlParam = _getUrlParam2.default;

// Custom Imports


// Flows
// import Flows from './custom/flows'


/**
 * Custom jQuery Code
 */
jQuery(document).ready(function ($) {

  // Initialize scripts from sub files jQuery ready.
  (0, _triggers2.default)();
  (0, _ajaxScripts2.default)();
  (0, _wpwhEvents2.default)();
  (0, _dataMapping2.default)();
  (0, _webhookSearch2.default)();

  // const flows = new Flows();
  // Flows.init();

  // Tippy
  (0, _tippy2.default)('[data-tippy-content]', {
    allowHTML: true,
    popperOptions: {
      strategy: 'fixed',
      modifiers: [{
        name: 'flip',
        options: {
          fallbackPlacements: ['bottom', 'right']
        }
      }, {
        name: 'preventOverflow',
        options: {
          altAxis: true,
          tether: false
        }
      }]
    }
  });

  // MatchHeight
  $('.wpwh-card__title').matchHeight();
  $('.wpwh-card__text').matchHeight();

  // Switch to Bootstrap tab automatically if HASH matches
  var hash = window.location.hash;
  hash && $('.wpwh .nav a[href="' + hash + '"]').tab('show');

  // Append hash to URL when bootstrap tab is switched
  $('.wpwh .nav-tabs a').on('click', function (e) {
    $(this).tab('show');
    var scrollmem = $('body').scrollTop() || $('html').scrollTop();
    window.location.hash = this.hash;
    $('html,body').scrollTop(scrollmem);
  });

  // Copy to clipboard input
  $('.wpwh-copy-wrapper').each(function (i, el) {
    var $thisEl = $(el);

    $thisEl.find('input').on('click', function (e) {
      $(this).trigger('select');
      document.execCommand('copy');
    });

    (0, _tippy2.default)($thisEl[0], {
      arrow: true,
      animation: 'fade',
      trigger: 'click',
      content: $thisEl.data('wpwh-tippy-content') || 'copied!',
      offset: [0, 15],
      onShow: function onShow(instance) {
        setTimeout(function () {
          instance.hide();
        }, 1500);
      }
    });
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./core/includes/assets/js/vendor/getUrlParam.js":
/*!*******************************************************!*\
  !*** ./core/includes/assets/js/vendor/getUrlParam.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
/**
 * Get URL Parameter
 *
 * Usage:
 * var searchText = getUrlParam( 'search' );
 *
 * @param  {string} sParam    parameter name, e.g., "search"
 * @param  {string} link      (optional) if you want to get the parameter from
 *                            a different URL then current page url.
 * @return {multiple}         returns the 'value' of parameter
 */
var getUrlParam = function getUrlParam(sParam, link) {
    var sPageURL = link ? link : decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

exports.default = getUrlParam;

/***/ }),

/***/ "./core/includes/assets/js/vendor/insertParamToURL.js":
/*!************************************************************!*\
  !*** ./core/includes/assets/js/vendor/insertParamToURL.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Insert Parameter in URL
 *
 * Dynamically insert or update a parameter in the URL.
 *
 * @param {string} key parameter name
 * @param {string} value parameter value
 * @param {string} base set a custom URL base.
 */
var insertParamToUrl = function insertParamToUrl(key, value) {
  var base = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';

  // Check if both key exists.
  var kvp = document.location.search.substr(1).split('&');

  if (key) {
    key = encodeURI(key);
    value = value ? encodeURI(value) : '';

    var i = kvp.length;var x;
    while (i--) {
      x = kvp[i].split('=');

      if (x[0] == key) {
        x[1] = value;
        kvp[i] = x.join('=');
        break;
      }
    }

    if (i < 0) {
      kvp[kvp.length] = [key, value].join('=');
    }
  }

  // this will reload the page, it's likely better to store this until finished
  // document.location.search = kvp.join( '&' );
  var urlBase = window.location.origin + (base ? base : window.location.pathname);
  var newUrl = urlBase + '?' + kvp.join('&') + window.location.hash;
  window.history.replaceState(null, null, newUrl);
};

exports.default = insertParamToUrl;

/***/ }),

/***/ "./core/includes/assets/js/vendor/jquery.matchHeight-min.js":
/*!******************************************************************!*\
  !*** ./core/includes/assets/js/vendor/jquery.matchHeight-min.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*
* jquery-match-height 0.7.2 by @liabru
* http://brm.io/jquery-match-height/
* License MIT
*/
!function (t) {
  "use strict";
   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_FACTORY__ = (t),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
}(function (t) {
  var e = -1,
      o = -1,
      n = function n(t) {
    return parseFloat(t) || 0;
  },
      a = function a(e) {
    var o = 1,
        a = t(e),
        i = null,
        r = [];return a.each(function () {
      var e = t(this),
          a = e.offset().top - n(e.css("margin-top")),
          s = r.length > 0 ? r[r.length - 1] : null;null === s ? r.push(e) : Math.floor(Math.abs(i - a)) <= o ? r[r.length - 1] = s.add(e) : r.push(e), i = a;
    }), r;
  },
      i = function i(e) {
    var o = {
      byRow: !0, property: "height", target: null, remove: !1 };return "object" == (typeof e === "undefined" ? "undefined" : _typeof(e)) ? t.extend(o, e) : ("boolean" == typeof e ? o.byRow = e : "remove" === e && (o.remove = !0), o);
  },
      r = t.fn.matchHeight = function (e) {
    var o = i(e);if (o.remove) {
      var n = this;return this.css(o.property, ""), t.each(r._groups, function (t, e) {
        e.elements = e.elements.not(n);
      }), this;
    }return this.length <= 1 && !o.target ? this : (r._groups.push({ elements: this, options: o }), r._apply(this, o), this);
  };r.version = "0.7.2", r._groups = [], r._throttle = 80, r._maintainScroll = !1, r._beforeUpdate = null, r._afterUpdate = null, r._rows = a, r._parse = n, r._parseOptions = i, r._apply = function (e, o) {
    var s = i(o),
        h = t(e),
        l = [h],
        c = t(window).scrollTop(),
        p = t("html").outerHeight(!0),
        u = h.parents().filter(":hidden");return u.each(function () {
      var e = t(this);e.data("style-cache", e.attr("style"));
    }), u.css("display", "block"), s.byRow && !s.target && (h.each(function () {
      var e = t(this),
          o = e.css("display");"inline-block" !== o && "flex" !== o && "inline-flex" !== o && (o = "block"), e.data("style-cache", e.attr("style")), e.css({ display: o, "padding-top": "0",
        "padding-bottom": "0", "margin-top": "0", "margin-bottom": "0", "border-top-width": "0", "border-bottom-width": "0", height: "100px", overflow: "hidden" });
    }), l = a(h), h.each(function () {
      var e = t(this);e.attr("style", e.data("style-cache") || "");
    })), t.each(l, function (e, o) {
      var a = t(o),
          i = 0;if (s.target) i = s.target.outerHeight(!1);else {
        if (s.byRow && a.length <= 1) return void a.css(s.property, "");a.each(function () {
          var e = t(this),
              o = e.attr("style"),
              n = e.css("display");"inline-block" !== n && "flex" !== n && "inline-flex" !== n && (n = "block");var a = {
            display: n };a[s.property] = "", e.css(a), e.outerHeight(!1) > i && (i = e.outerHeight(!1)), o ? e.attr("style", o) : e.css("display", "");
        });
      }a.each(function () {
        var e = t(this),
            o = 0;s.target && e.is(s.target) || ("border-box" !== e.css("box-sizing") && (o += n(e.css("border-top-width")) + n(e.css("border-bottom-width")), o += n(e.css("padding-top")) + n(e.css("padding-bottom"))), e.css(s.property, i - o + "px"));
      });
    }), u.each(function () {
      var e = t(this);e.attr("style", e.data("style-cache") || null);
    }), r._maintainScroll && t(window).scrollTop(c / p * t("html").outerHeight(!0)), this;
  }, r._applyDataApi = function () {
    var e = {};t("[data-match-height], [data-mh]").each(function () {
      var o = t(this),
          n = o.attr("data-mh") || o.attr("data-match-height");n in e ? e[n] = e[n].add(o) : e[n] = o;
    }), t.each(e, function () {
      this.matchHeight(!0);
    });
  };var s = function s(e) {
    r._beforeUpdate && r._beforeUpdate(e, r._groups), t.each(r._groups, function () {
      r._apply(this.elements, this.options);
    }), r._afterUpdate && r._afterUpdate(e, r._groups);
  };r._update = function (n, a) {
    if (a && "resize" === a.type) {
      var i = t(window).width();if (i === e) return;e = i;
    }n ? o === -1 && (o = setTimeout(function () {
      s(a), o = -1;
    }, r._throttle)) : s(a);
  }, t(r._applyDataApi);var h = t.fn.on ? "on" : "bind";t(window)[h]("load", function (t) {
    r._update(!1, t);
  }), t(window)[h]("resize orientationchange", function (t) {
    r._update(!0, t);
  });
});

/***/ }),

/***/ "./core/includes/assets/js/vendor/jsonviewer.js":
/*!******************************************************!*\
  !*** ./core/includes/assets/js/vendor/jsonviewer.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(jQuery) {

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/**
 * jQuery json-viewer
 * @author: Kevin Olson <acidjazz@gmail.com>
 */
(function ($) {

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
    var regexp = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(string);
  }

  /**
   * Transform a json object into html representation
   * @return string
   */
  function json2html(json, options) {
    var html = '';
    if (typeof json === 'string') {
      // Escape tags
      json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      if (isUrl(json)) html += '<a href="' + json + '" class="json-string">' + json + '</a>';else html += '<span class="json-string">"' + json + '"</span>';
    } else if (typeof json === 'number') {
      html += '<span class="json-literal">' + json + '</span>';
    } else if (typeof json === 'boolean') {
      html += '<span class="json-literal">' + json + '</span>';
    } else if (json === null) {
      html += '<span class="json-literal">null</span>';
    } else if (json instanceof Array) {
      if (json.length > 0) {
        html += '[<ol class="json-array">';
        for (var i = 0; i < json.length; ++i) {
          html += '<li>';
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
      } else {
        html += '[]';
      }
    } else if ((typeof json === 'undefined' ? 'undefined' : _typeof(json)) === 'object') {
      var key_count = Object.keys(json).length;
      if (key_count > 0) {
        html += '{<ul class="json-dict">';
        for (var key in json) {
          if (json.hasOwnProperty(key)) {
            html += '<li>';
            var keyRepr = options.withQuotes ? '<span class="json-string">"' + key + '"</span>' : key;
            // Add toggle button if item is collapsable
            if (isCollapsable(json[key])) {
              html += '<a href class="json-toggle">' + keyRepr + '</a>';
            } else {
              html += keyRepr;
            }
            html += ': ' + json2html(json[key], options);
            // Add comma if item is not last
            if (--key_count > 0) html += ',';
            html += '</li>';
          }
        }
        html += '</ul>}';
      } else {
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
  $.fn.jsonBrowse = function (json, options) {
    options = options || {};

    // jQuery chaining
    return this.each(function () {

      // Transform to HTML
      var html = json2html(json, options);
      if (isCollapsable(json)) html = '<a href class="json-toggle"></a>' + html;

      // Insert HTML in target DOM element
      $(this).html(html);

      // Bind click on toggle buttons
      $(this).off('click');
      $(this).on('click', 'a.json-toggle', function () {
        var target = $(this).toggleClass('collapsed').siblings('ul.json-dict, ol.json-array');
        target.toggle();
        if (target.is(':visible')) {
          target.siblings('.json-placeholder').remove();
        } else {
          var count = target.children('li').length;
          var placeholder = count + (count > 1 ? ' items' : ' item');
          target.after('<a href class="json-placeholder">' + placeholder + '</a>');
        }
        return false;
      });

      // Simulate click on toggle button when placeholder is clicked
      $(this).on('click', 'a.json-placeholder', function () {
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
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=admin-scripts.js.map