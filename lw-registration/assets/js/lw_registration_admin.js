jQuery(document).ready(function () {
  try {
    if (jQuery.fn.select2) {
      jQuery(".multiple-select").not(".lw-pronouns-select").select2();
    }
    jQuery(".panel-collapse").on("show.bs.collapse", function () {
      jQuery(this).siblings(".panel-heading").addClass("active");
    });

    jQuery(".panel-collapse").on("hide.bs.collapse", function () {
      jQuery(this).siblings(".panel-heading").removeClass("active");
    });
  } catch(e) { /* prevent breaking other scripts */ }
});

function LW_show_loader() {
  jQuery("#lw_loader_info_data").hide(0);
  jQuery("#lw_loader_data").show(0);
  jQuery("#lw_loader").fadeIn(300);
}

function LW_hide_loader() {
  jQuery("#lw_loader").hide(0);
}

function LW_show_loader_output(text, status) {
  var fa = '<i class="fa fa-times"></i>';
  var Popup = "LwFailedPopup";
  if (status == 1) {
    var fa = '<i class="fa fa-check"></i> ';
    Popup = "LwSuccessPopup";
  }

  jQuery("#lw_loader_data").hide(0);
  jQuery("#lw_loader_info_data").html(
    "<div class=" +
      Popup +
      ">" +
      fa +
      '<div id="lw_loader_info_data_text">' +
      text +
      "<div></div>"
  );
  jQuery("#lw_loader_info_data").fadeIn(300);
}

function LW_redirect_to(url) {
  setTimeout(function () {
    window.location.href = url;
  }, 3000);
}

jQuery(document).ready(function () {
  try {
    if (jQuery.fn.DataTable) {
      jQuery("#invitation_table").DataTable();
    }
  } catch(e) { /* prevent breaking other scripts */ }
  jQuery("body").append(
    '<div id="lw_loader" style="display:none"><div id="lw_loader_container"> <div id="lw_loader_container_inner"> <div id="lw_loader_data"><div id="DM_css_loader"></div><div id="lw_css_loader_text">>We\'re working on it... </div></div><div id="lw_loader_info_data" style="display:none"></div></div></div></div>'
  );

  jQuery(document).on("submit", "#LwInvitationForm", function (event) {
    event.preventDefault();
    try {
      if (jQuery("#LwInvitationForm").valid && jQuery("#LwInvitationForm").valid()) {
        LW_show_loader();
        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url: lw_registration.ajaxUrl,
          data: {
            action: "lw_invitation_admin_action",
            data: jQuery("#LwInvitationForm").serialize(),
          },
          error: function (jqXHR, exception) {
          },
          success: function (resp) {
            if (resp.status == "1") {
              LW_show_loader_output(resp.message, 1);
              setTimeout(function () {
                LW_hide_loader();
                window.location.reload();
              }, 3000);
            } else {
              LW_show_loader_output(resp.message, 0);
              setTimeout(function () {
                LW_hide_loader();
              }, 3000);
            }
          },
        });
      }
    } catch(err) {
      LW_show_loader();
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: lw_registration.ajaxUrl,
        data: {
          action: "lw_invitation_admin_action",
          data: jQuery("#LwInvitationForm").serialize(),
        },
        success: function (resp) {
          if (resp.status == "1") {
            LW_show_loader_output(resp.message, 1);
            setTimeout(function () {
              LW_hide_loader();
              window.location.reload();
            }, 3000);
          } else {
            LW_show_loader_output(resp.message, 0);
            setTimeout(function () {
              LW_hide_loader();
            }, 3000);
          }
        }
      });
    }
  });
});
jQuery(document).on("click", ".resend_invitation", function (event) {
  var id = jQuery(this).attr("data-id");
  if (confirm("Are you sure you want to resend invitation?")) {
    LW_show_loader();
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: lw_registration.ajaxUrl,
      data: {
        action: "lw_invitation_admin_resend",
        id: id,
      },
      error: function (jqXHR, exception) {
      },
      success: function (resp) {
        if (resp.status == "1") {
          LW_show_loader_output(resp.message, 1);
          setTimeout(function () {
            LW_hide_loader();
            window.location.reload();
          }, 3000);
        } else {
          LW_show_loader_output(resp.message, 0);
          setTimeout(function () {
            LW_hide_loader();
          }, 3000);
        }
      },
    });
  }
});

jQuery(function($){
  var dbg = (window.lwPronounsDebug !== undefined) ? !!window.lwPronounsDebug : true;
  var log = function(){ if(dbg){ try{ console.log.apply(console, arguments); }catch(e){} } };
  var select2Available = !!$.fn.select2;
  if(!select2Available){ log('Select2 not available'); }

  (function injectSafeStyles(){
    if(document.getElementById('lw-pronouns-select2-safety')){ return; }
    var css = [
      '.select2-container--open{display:block !important;visibility:visible !important;}',
      'body > .select2-container .select2-dropdown{position:absolute !important;}',
      '.select2-container-multi .select2-choices{border: 1px solid #8c8f94; max-height: 28px;}',
      '.select2-container, .select2{display:inline-block !important;}',
      '.select2-container-multi.select2-container-active .select2-choices {box-shadow: none;}',
      'body .select2-drop{position:fixed !important; border: 1px solid #8c8f94; border-bottom: 0; border-top: 0;}',
      '.select2, .select2-container{pointer-events:auto !important; max-width: 190px;}',
      '.select2-selection--multiple .select2-selection__choice{position:relative; padding-right:18px !important;}',
      '.select2-selection__choice__remove{display:inline-block !important; position:absolute !important; right:6px !important; top:50% !important; transform:translateY(-50%); line-height:1; cursor:pointer !important; opacity:1 !important; visibility:visible !important;}',
      '.select2-selection__choice__remove::after{content:"x"; font-weight:bold;}',
      '.select2-search-choice{position:relative; padding-right:18px !important;}',
      '.select2-search-choice >div{color: #2c3338}',
      '.select2-search-choice-close{display:block !important; position:absolute !important; right:2px !important; top:50% !important; transform:translateY(-50%); width:12px; height:12px; cursor:pointer !important;}',
      '.select2-search-choice-close::after{content:"x"; font-weight:bold;}',
      '.select2-results{max-height:260px;overflow-y:auto;}',
      '.select2-search__field{width:100% !important;}',
      '.select2-search input{width:100% !important;}',
      '.select2-results__message{display:none !important;}',
      '.select2-no-results{display:none !important;}',
      'li.select2-no-results{display:none !important;}',
      '#your-profile .field_pronouns #field_21{display: none},'
    ].join('');
    var style = document.createElement('style');
    style.type = 'text/css';
    style.id = 'lw-pronouns-select2-safety';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  })();

  function ensureDropdownVisibleForSelect($el){
    var $cont = $el.next('.select2, .select2-container');
    if(!$cont.length){
      try { $cont = $el.select2('container'); } catch(e){}
    }
    if(!$cont || !$cont.length){ return; }
    var rect;
    try { rect = $cont[0].getBoundingClientRect(); } catch(e){ var off = $cont.offset(); rect = { left: off.left, top: off.top, bottom: off.top + $cont.outerHeight(), width: $cont.outerWidth(), right: off.left + ($cont.outerWidth()||0) }; }
    var w = rect.width || $cont.outerWidth();
    var $open = jQuery('.select2-container--open');
    var isV4 = !!$open.length;
    if(!isV4){
      $open = jQuery('#select2-drop.select2-drop-active');
      if(!$open.length){ $open = jQuery('.select2-drop.select2-drop-active'); }
    }
    if(!$open.length){ return; }
    var $dd = isV4 ? $open.find('.select2-dropdown') : $open;
    var rtl = (document.documentElement.dir === 'rtl') || (jQuery('body').css('direction') === 'rtl');
    if(rtl){
      var rightPx = Math.max(0, window.innerWidth - rect.right);
      $open.css({ position: 'fixed', right: rightPx + 'px', left: 'auto', top: (rect.bottom) + 'px', display: 'block', visibility: 'visible' });
    } else {
      $open.css({ position: 'fixed', left: (rect.left) + 'px', right: 'auto', top: (rect.bottom) + 'px', display: 'block', visibility: 'visible' });
    }
    $dd.css({ 'min-width': w + 'px', display: 'block', visibility: 'visible' });
  }

  function setupInputForSelect($input, $el, index){
    $input.removeAttr('readonly');
    var $selBox = $el.next('.select2, .select2-container');
    if(!$selBox.length && select2Available){
      try { $selBox = $el.select2('container'); } catch(e){}
    }
    if($selBox && $selBox.length){
      $selBox.insertAfter($input);
      $selBox.removeClass('select2-display-none');
      $selBox.show();
      $selBox.css({ display: 'inline-block', visibility: 'visible', 'pointer-events': 'auto', width: $input.outerWidth() + 'px' });
      $selBox.find('.select2-selection__choice__remove, .select2-search-choice-close').css({ display:'inline-block', visibility:'visible', opacity:1, cursor:'pointer' });
    }
    var updateSelBoxWidth = function(){ if($selBox && $selBox.length){ $selBox.css('width', $input.outerWidth() + 'px'); } };

    $input.off('.lwPronouns');
    $input.on('focus.lwPronouns', function(){ log('input focus on index', index); updateSelBoxWidth(); });
    $input.on('click.lwPronouns', function(){
      log('input trigger click on index', index);
      try {
        if($selBox && $selBox.length){
          $selBox.css({ visibility: 'visible', 'pointer-events': 'auto' });
          updateSelBoxWidth();
        }
        if(select2Available && $el.data('select2')){
          log('opening select2');
          $el.select2('open');
        } else if(select2Available) {
          log('select2 missing, reinit');
          $el.select2({
            placeholder: $el.attr('data-placeholder') || 'Your Pronouns',
            width: '100%',
            closeOnSelect: true,
            maximumSelectionLength: 2,
            language: { maximumSelected: function(args){ return 'You can only select ' + (args.maximum || 2) + ' items'; }, noResults: function(){ return ''; } },
            maximumSelectionSize: 2,
            formatSelectionTooBig: function(limit){ return 'You can only select ' + (limit || 2) + ' items'; },
            formatNoMatches: function(term){ return ''; },
            dropdownParent: jQuery(document.body)
          });
          (function(){
            var vals = $el.val() || [];
            if(vals.length > 2){
              vals = vals.slice(0,2);
              $el.val(vals).trigger('change');
              showLimitMessage($el);
            } else if(vals.length >= 2){
              showLimitMessage($el);
            } else {
              hideLimitMessage($el);
            }
          })();
          $el.off('select2:open.lwPronouns select2-open.lwPronouns').on('select2:open.lwPronouns select2-open.lwPronouns', function(){
            ensureDropdownVisibleForSelect($el);
            var reposition = function(){ ensureDropdownVisibleForSelect($el); };
            jQuery(window).off('.lwPronounsPos').on('scroll.lwPronounsPos resize.lwPronounsPos', reposition);
            setTimeout(function(){
              var $sf = jQuery('.select2-container--open .select2-search__field, .select2-drop-active .select2-search input');
              if($sf.length){ try{ $sf[0].focus(); }catch(e){} }
              ensureDropdownVisibleForSelect($el);
            }, 0);
            var v = $el.val() || [];
            if(v.length >= 2){ showLimitMessage($el); } else { hideLimitMessage($el); }
          });
          $el.select2('open');
        } else {
          // Fallback: focus native select
          $el.trigger('focus');
        }
        ensureDropdownVisible($input);
      } catch(e) {
        log('open failed, fallback focus', e);
        $el.trigger('focus');
      }
    }).on('keydown.lwPronouns', function(e){
      log('input keydown', e.key || e.keyCode);
      if(e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32){
        e.preventDefault();
        try {
          log('opening via key');
          if(select2Available){ $el.select2('open'); }
          ensureDropdownVisible($input);
        } catch(err){ log('open via key failed, focusing'); $el.trigger('focus'); }
      }
    });

    $el.off('select2:open.lwPronouns select2-open.lwPronouns').on('select2:open.lwPronouns select2-open.lwPronouns', function(){
      ensureDropdownVisible($input);
      var reposition = function(){ ensureDropdownVisible($input); };
      jQuery(window).off('.lwPronounsPos').on('scroll.lwPronounsPos resize.lwPronounsPos', reposition);
      setTimeout(function(){
        var $sf = jQuery('.select2-container--open .select2-search__field, .select2-drop-active .select2-search input');
        if($sf.length){ try{ $sf[0].focus(); }catch(e){} }
        ensureDropdownVisible($input);
      }, 0);
    });
    $el.off('select2:close.lwPronouns select2-close.lwPronouns').on('select2:close.lwPronouns select2-close.lwPronouns', function(){
      jQuery(window).off('.lwPronounsPos');
    });

    var syncInput = function(){
      var vals = $el.val() || [];
      // Get display mapping from LW General Settings
      var pronounsOptions = window.lwPronounsOptions || [];
      var displayMap = {};
      pronounsOptions.forEach(function(pronoun){
        displayMap[pronoun.toLowerCase()] = pronoun;
      });
      var text = vals.map(function(v){ return displayMap[v] || v; }).join('/');
      log('syncInput values', vals, 'text', text);
      $input.val(text);
    };
    $el.off('change.lwPronouns').on('change.lwPronouns', syncInput);
    syncInput();
  }

  function getSelect2Container($el){
    var $cont = $el.next('.select2, .select2-container');
    if(!$cont.length && select2Available){
      try { $cont = $el.select2('container'); } catch(e){}
    }
    return $cont;
  }
  function showLimitMessage($el){
    var $cont = getSelect2Container($el);
    if(!$cont || !$cont.length){ return; }
    var $msg = $cont.next('.lw-pronouns-limit-msg');
  }
  function hideLimitMessage($el){
    var $cont = getSelect2Container($el);
    if(!$cont || !$cont.length){ return; }
    var $msg = $cont.next('.lw-pronouns-limit-msg');
    if($msg.length){ $msg.hide(); }
  }
  function focusSearchField(){
    var $sf = jQuery('.select2-container--open .select2-search__field');
    if(!$sf.length){ $sf = jQuery('.select2-drop-active .select2-search input'); }
    if($sf.length){ try { $sf[0].focus(); } catch(e){} }
  }
  function enforceMaxTwo($el, lastId){
    var vals = ($el.val() || []).slice(0);
    if(vals.length > 2){
      if(lastId != null){
        try { $el.find('option[value="'+lastId+'"]').prop('selected', false); } catch(e){}
      }
      vals = vals.slice(0,2);
      $el.val(vals).trigger('change');
      showLimitMessage($el);
      return true;
    } else {
      hideLimitMessage($el);
      return false;
    }
  }

  function initPronouns(ctx){
    var $ctx = jQuery(ctx);
    var $sels = $ctx.find('select.lw-pronouns-select');
    $sels.each(function(){
      var $el = jQuery(this);
      var wasInit = !!$el.data('select2');
      if(select2Available && !wasInit){
        $el.select2({
          placeholder: $el.attr('data-placeholder') || 'Your Pronouns',
          width: '100%',
          closeOnSelect: true,
          maximumSelectionLength: 2,
          language: { maximumSelected: function(args){ return 'You can only select ' + (args.maximum || 2) + ' items'; }, noResults: function(){ return ''; } },
          maximumSelectionSize: 2,
          formatSelectionTooBig: function(limit){ return 'You can only select ' + (limit || 2) + ' items'; },
          formatNoMatches: function(term){ return ''; },
          dropdownParent: jQuery(document.body)
        });
      }

      (function(){
        var trimmed = enforceMaxTwo($el, null);
        if(!trimmed){
          var $cont = getSelect2Container($el);
          var chipCount = $cont && $cont.length ? $cont.find('.select2-selection__choice, .select2-search-choice').length : 0;
          if(chipCount > 2){ enforceMaxTwo($el, null); }
        }
      })();

      $el.off('select2:open.lwPronouns select2-open.lwPronouns').on('select2:open.lwPronouns select2-open.lwPronouns', function(){
        ensureDropdownVisibleForSelect($el);
        var reposition = function(){ ensureDropdownVisibleForSelect($el); };
        jQuery(window).off('.lwPronounsPos').on('scroll.lwPronounsPos resize.lwPronounsPos', reposition);
        setTimeout(function(){ focusSearchField(); ensureDropdownVisibleForSelect($el); }, 0);
      });
      $el.off('select2:close.lwPronouns select2-close.lwPronouns').on('select2:close.lwPronouns select2-close.lwPronouns', function(){
        jQuery(window).off('.lwPronounsPos');
      });

      
      $el.off('select2:select.lwPronouns').on('select2:select.lwPronouns', function(e){
        var lastId = e && e.params && e.params.data ? e.params.data.id : null;
        enforceMaxTwo($el, lastId);
        // Do not reopen the dropdown; allow it to close automatically
      });
      $el.off('select2-selecting.lwPronouns').on('select2-selecting.lwPronouns', function(e){
        var vals = $el.val() || [];
        if(vals.length >= 2){
          try { e.preventDefault(); } catch(err){}
          showLimitMessage($el);
          setTimeout(function(){ focusSearchField(); }, 0);
        }
      });
      
      $el.off('change.lwPronounsLimit').on('change.lwPronounsLimit', function(){ enforceMaxTwo($el, null); });
    });
  }

  window.lwPronounsInitRan = (window.lwPronounsInitRan||0)+1;
  initPronouns(document);
  try {
    var mo = new MutationObserver(function(muts){
      log('MutationObserver mutations', muts.length);
      muts.forEach(function(m){
        if(m.addedNodes){
          m.addedNodes.forEach(function(n){ if(n.nodeType === 1){ log('init due to added node', n); initPronouns(n); } });
        }
      });
    });
    mo.observe(document.body, { childList: true, subtree: true });
  } catch(e) { log('MutationObserver init failed', e); }

  // Inject Pronouns select for Users > Extended Profile screen to replace the text input with a Select2 multi-select like user_info.php
  (function initAdminExtendedProfilePronouns(){
    var isUsersExtendedProfile = jQuery('body').hasClass('users_page_bp-profile-edit') || (location.href.indexOf('users.php?page=bp-profile-edit') !== -1);
    var inputExists =
      jQuery('#profile-edit-form input[id^="field_21"]').length > 0 ||
      jQuery('#profile-edit-form input[name^="field_21"]').length > 0 ||
      jQuery('#profile-edit-form .field_pronouns input').length > 0 ||
      jQuery('#your-profile input[id^="field_21"]').length > 0 ||
      jQuery('#your-profile input[name^="field_21"]').length > 0 ||
      jQuery('#your-profile .field_pronouns input').length > 0;
    if(!(isUsersExtendedProfile || inputExists)){ return; }

    // Find the Pronouns input field in admin (#your-profile) or front-end (#profile-edit-form)
    var $input = jQuery('#profile-edit-form input[id^="field_21"], #your-profile input[id^="field_21"]').first();
    if(!$input.length){
      // Fallback by name selector if id changes
      $input = jQuery('#profile-edit-form input[name^="field_21"], #your-profile input[name^="field_21"]').first();
    }
    if(!$input.length){
      // Fallback by class selector
      $input = jQuery('#profile-edit-form .field_pronouns input, #your-profile .field_pronouns input').first();
    }
    if(!$input.length){
      // Try by label text: find legend/label containing "Pronouns" and get the next input
      var $legend = jQuery('#your-profile .bp-profile-field legend:contains("Pronouns"), #profile-edit-form .editfield legend:contains("Pronouns")').first();
      if($legend.length){
        var $candidate = $legend.closest('fieldset, .editfield').find('input[type="text"], input').first();
        if($candidate.length){ $input = $candidate; }
      }
    }
    if(!$input.length){ return; }

    // Avoid duplicate injection
    if($input.data('lwPronounsInjected')){ return; }
    $input.data('lwPronounsInjected', true);

    // Build the select element with allowed pronouns from LW General Settings
    var $sel = jQuery('<select multiple class="form-control lw-pronouns-select" data-placeholder="Your Pronouns" />');
    // Get pronouns options from global variable set by PHP
    var pronounsOptions = window.lwPronounsOptions || [];
    pronounsOptions.forEach(function(pronoun){
      var val = pronoun.toLowerCase();
      $sel.append('<option value="'+val+'">'+pronoun+'</option>');
    });

    // Insert select after the input, initialize behaviors, and sync values
    $sel.insertAfter($input);

    // Initialize select2 via existing helper (if available)
    initPronouns(document);

    // Preselect based on current input value (supports formats like "She/Her", "She, Her", etc.)
    var raw = ($input.val() || '').toLowerCase();
    var tokens = raw.split(/[\s,;\/]+/).filter(function(t){ return !!t; });
    // Get allowed pronouns from LW General Settings
    var allowed = (window.lwPronounsOptions || []).map(function(p){ return p.toLowerCase(); });
    var vals = [];
    tokens.forEach(function(t){ if(allowed.indexOf(t) !== -1 && vals.indexOf(t) === -1){ vals.push(t); } });
    if(vals.length > 2){ vals = vals.slice(0,2); }
    if(vals.length){ $sel.val(vals).trigger('change'); }

    // Wire up the input to open/select and keep synced
      try {
        setupInputForSelect($input, $sel, 0);
      } catch(e){ /* if helper failed, ensure at least syncing happens */
        $sel.on('change.lwPronounsFallback', function(){
          // Get display mapping from LW General Settings
          var pronounsOptions = window.lwPronounsOptions || [];
          var displayMap = {};
          pronounsOptions.forEach(function(pronoun){
            displayMap[pronoun.toLowerCase()] = pronoun;
          });
          var v = ($sel.val()||[]).map(function(x){ return displayMap[x] || x; }).join('/');
          $input.val(v);
        });
        $sel.trigger('change');
      }

    // Retry once after a short delay to catch late-rendered admin form
    setTimeout(function(){
      if($input && $input.length && !$input.data('lwPronounsRechecked')){
        $input.data('lwPronounsRechecked', true);
        initPronouns(document);
      }
    }, 500);
  })();
});
