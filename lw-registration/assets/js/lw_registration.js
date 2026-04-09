jQuery(document).on("click", "#lwtogglePassword", function () {
  const password = document.querySelector("#lw_password");
  const type =
    password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);
  // toggle the eye slash icon
  if (type == "password") {
    jQuery(this).addClass("fa-eye").removeClass("fa-eye-slash");
  }
  if (type == "text") {
    jQuery(this).removeClass("fa-eye").addClass("fa-eye-slash");
  }
});
var LwcurrentTab = 0;
// function checkCustomPasswordStrength() {
//   var password = document.getElementById('lw_password').value;
//   var strengthContainer = document.getElementById('lw_passwordnotes');
//   var submitButton = document.getElementById("LwsubmitBtn");
//
//   // Use WordPress password strength meter function
//   var strength = wp.passwordStrength.meter(password, []);
//   submitButton.disabled = true;
//   strengthContainer.classList.remove(...strengthContainer.classList);
//   switch ( strength ) {
//     case 2:
//       strengthContainer.innerHTML = 'Password Strength: Bad';
//       strengthContainer.classList.add("error");
//       break;
//     case 3:
//       strengthContainer.innerHTML = 'Password Strength: Good';
//       submitButton.disabled = false;
//       break;
//     case 4:
//       strengthContainer.innerHTML = 'Password Strength: Strong';
//       submitButton.disabled = false;
//       break;
//     default:
//       strengthContainer.innerHTML = 'Password Strength: Short';
//       strengthContainer.classList.add("error");
//   }
// }
function lwGetAge() {
  var lw_birthday_day = jQuery("select[name='lw_birthday_day']").val();
  var lw_birthday_month = jQuery("select[name='lw_birthday_month']").val();
  var lw_birthday_year = jQuery("select[name='lw_birthday_year']").val();
  var selectedDate = new Date(
    lw_birthday_year,
    lw_birthday_month - 1,
    lw_birthday_day
  );
  var wrongDateFormat = false;
  if (
    selectedDate.getFullYear() == lw_birthday_year &&
    selectedDate.getMonth() + 1 == lw_birthday_month &&
    selectedDate.getDate() == lw_birthday_day
  ) {
    wrongDateFormat = false;
  } else {
    wrongDateFormat = true;
  }

  var today = new Date();

  //year first
  var age = today.getFullYear() - selectedDate.getFullYear();
  //check month and date
  if (
    today.getMonth() < selectedDate.getMonth() ||
    (today.getMonth() === selectedDate.getMonth() &&
      today.getDate() < selectedDate.getDate())
  ) {
    age--;
  }

  if (age < 18) {
    jQuery(".lw_mobilephone input").removeClass("required");
    jQuery(".lw_mobilephone .lw_required_label").css("visibility", "hidden");
  } else {
    jQuery(".lw_mobilephone input").addClass("required");
    jQuery(".lw_mobilephone .lw_required_label").css("visibility", "visible");
  }

  jQuery(".birthday-error-all").removeClass("show-birthday-error");
  jQuery(".birthday-error-all").html("");
  if (wrongDateFormat) {
    jQuery(".birthday-error-all").addClass("show-birthday-error");
    jQuery(".birthday-error-all").html(
      "Oops.. it seems the date you select is not correct"
    );
    return false;
  } else {
    jQuery("select[name='lw_birthday_day']").removeClass("error");
    jQuery("select[name='lw_birthday_year']").removeClass("error");
    jQuery("select[name='lw_birthday_month']").removeClass("error");
  }
  if (age >= 12 && age <= 20) {
    jQuery(".birthday-error-all").removeClass("show-birthday-error");
    jQuery(".birthday-error-all").html("");
    return true;
  } else {
    jQuery(".birthday-error-all").addClass("show-birthday-error");
    jQuery(".birthday-error-all").html(
      "Oops.. it seems you don't meet the criteria to join Livewire. Livewire is a community for young people aged 12-20 years. Please email us at livewire@starlight.org.au if you have any questions"
    );
    return false;
  }
  return true;
}

jQuery(document).on(
  "click",
  ".lw-welcome-close span,.lw-welcome-close-mobile span",
  function () {
    jQuery(".lw-welcome-inner").hide();

    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: lw_registration.ajaxUrl,

      data: {
        action: "lw_in_login_time",
      },
      error: function (jqXHR, exception) {
        //	window.location.reload();
      },
      success: function (data) {},
    });
  }
);

function LwshowTab(n) {
  var x = document.getElementsByClassName("lw_tabcontent");
  x[n].style.display = "block";
  jQuery("#LwsubmitBtn").hide();
  jQuery("#LwnextBtn").show();
  jQuery("#cancelBtn").show();

  if (n == 0) {
    document.getElementById("LwprevBtn").style.display = "none";
  } else {
    document.getElementById("LwprevBtn").style.display = "inline";
    jQuery("#cancelBtn").hide();
  }
  if (n == x.length - 1) {
    jQuery("#LwsubmitBtn").show();
    jQuery("#LwnextBtn").hide();
  } else {
    document.getElementById("LwnextBtn").innerHTML = "Next Step";
    jQuery("#LwnextBtn").attr("type", "button");
  }
}

function nextPrev(n) {
  if (n != "-1") {
    if (!jQuery("#LwRegistrationForm").valid()) {
      return false;
    }
  }

  var lw_form_type = jQuery("#lw_form_type").val();
  if (LwcurrentTab == 0 && n == 1 && lw_form_type == "form_c") {
    lw_check_email_address(1);
  } else if (LwcurrentTab == 0 && n == 1 && lw_form_type == "form_a") {
    lw_check_email_address(1);
  } else {
    var x = document.getElementsByClassName("lw_tabcontent");
    var y = document.getElementsByClassName("lw_tablinks");

    x[LwcurrentTab].style.display = "none";
    y[LwcurrentTab].classList.remove("active");

    LwcurrentTab = LwcurrentTab + n;
    if (LwcurrentTab < y.length) {
      y[LwcurrentTab].classList.add("active");
    }
    //alert(LwcurrentTab+"=>"+y.length);
    if (LwcurrentTab >= x.length) {
    } else {
      LwshowTab(LwcurrentTab);
    }
  }
}

function LW_show_loader() {
  jQuery("#lw_loader_info_data").hide(0);
  jQuery("#lw_loader_data").show(0);
  jQuery("#lw_loader").fadeIn(300);
}

function LW_hide_loader() {
  jQuery("#lw_loader").hide(0);
}
jQuery(document).on("click", ".lw-close-popup", function () {
  LW_hide_loader();
});

jQuery("#lw_loader_container_inner").click(function (e) {
  //if (!jQuery(e.target).closest('.LwFailedPopup').length){
  LW_hide_loader();
  //}
});
function LW_show_loader_output(text, status) {
  var fa =
    '<i class="fa fa-times lw-close-popup"></i> <i class="fa fa-times"></i>';
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
  window.location.href = url;
}

function checkSubstring(str1, str2) {
  for (let i = 0; i <= str1.length - 3; i++) {
    const threeChars = str1.slice(i, i + 3);

    if (str2.includes(threeChars)) {
      return true;
    }
  }

  return false;
}
jQuery(document).on("click", ".lw_action_link", function () {
  var type = jQuery(this).attr("data-type");

  if (type == "LwForgotPasswordForm") {
    jQuery("#" + type).show();
    jQuery("#LwLoginForm").hide();
  }
  if (type == "LwLoginForm") {
    jQuery("#" + type).show();
    jQuery("#LwForgotPasswordForm").hide();
  }
});
jQuery(document).ready(function () {
  jQuery(".lw-checkbox-custome-tc a").attr("target", "_blank");
  if (jQuery.fn && jQuery.fn.select2) {
    var $pronounSelects = jQuery(".lw-pronouns-select");
    $pronounSelects.each(function () {
      var $sel = jQuery(this);
      var placeholder = $sel.attr("data-placeholder") || "Your Pronouns";
      $sel.select2({
        placeholder: placeholder,
        allowClear: true,
        maximumSelectionLength: 2,
        maximumSelectionSize: 2,
        width: "100%",
      });
    });
  }
  jQuery.validator.addMethod(
    "username_valid",
    function (value, element) {
      var returndata = "";
      //returndata = /^[\w.]+$/i.test(value);

      const regex = /^(?=(?:\D*\d){0,5}\D*$)[a-zA-Z\d]*$/;

      returndata = regex.test(value);

      //if(value.length<5){
      //returndata = false;
      //}
      //if(value.length>15){
      //returndata = false;
      //}
      return returndata;
    },
    "Oops... your username can only include letters, up to 5 numbers and be a total of 15 characters."
  );

  jQuery.validator.addMethod(
    "user_3_consecutive",
    function (value, element) {
      const last_name = jQuery("#lw_last_name").val()|| "";
      if (last_name.length > 3) {
        return !checkSubstring(value.toLowerCase(), last_name.toLowerCase());
      }
      return true;
    },
    "Oops! Your last name cannot be used as part of your username."
  );

  jQuery.validator.addMethod(
    "lettersonly",
    function (value, element) {
      // return this.optional(element) || /^[a-z]+$/i.test(value);
      return this.optional(element) || /^[a-z' -]+$/i.test(value);
    },
    "Oops... this field can only contain letters."
  );

  jQuery.validator.addMethod(
    "lw_sibling_spent_time",
    function (value, element) {
      jQuery(".error-lw_sibling_spent_time").html("").hide();
      var returndata = true;
      if (value == "" || typeof value === "undefined") {
        jQuery(".error-lw_sibling_spent_time").show();
        jQuery(".error-lw_sibling_spent_time").html(
          "Sorry this field cannot be empty."
        );
        return false;
      }
      if (value == "no") {
        jQuery(".error-lw_sibling_spent_time").show();
        jQuery(".error-lw_sibling_spent_time").html(
          "Hmmm... it seems you don't meet the criteria to join Livewire. Livewire is a community for young people experiencing hospitalisation, living with a health condition or disability, and their siblings. Please email us at livewire@starlight.org.au if you have any questions."
        );
        return false;
      }

      return returndata;
    },
    ""
  );
  jQuery.validator.addMethod(
    "lw_birthday",
    function (value, element) {
      var returndata = true;
      jQuery(".birthday-error-all").html("").hide();
      if (
        jQuery(".lw_birthday_day").val() == "" ||
        jQuery(".lw_birthday_month").val() == "" ||
        jQuery(".lw_birthday_year").val() == ""
      ) {
        jQuery(".birthday-error-all").addClass("show-birthday-error");
        jQuery(".birthday-error-all").html(
          "Sorry please complete your birthday"
        );
        return false;
      }
      returndata = lwGetAge();

      return returndata;
    },
    " Sorry please complete your birthday"
  );

  jQuery.validator.addMethod(
    "chk_privacy_policy",
    function (value, element) {
      var returndata = true;
      if (typeof value === "undefined") {
        returndata = false;
      }
      return returndata;
    },
    " Please agree to the Terms & Conditions and Privacy Statement"
  );

  jQuery.validator.addMethod(
    "phone_number_validation",
    function (value, element) {
      var returndata = true;
      if (element.className.indexOf("required") != -1) {
        var phone_length = value.length;
        if (phone_length == 9 && value.slice(0, 1) == 0) {
          returndata = false;
        }
        if (phone_length == 9 && value.slice(0, 1) != 0) {
          returndata = true;
        }

        if (phone_length == 10 && value.slice(0, 1) != 0) {
          returndata = false;
        }
        if (phone_length > 10 || phone_length <= 8) {
          returndata = false;
        }
      }
      return returndata;
    },
    " Sorry this phone number is invalid, please check you have entered it correctly"
  );

  jQuery.validator.addMethod(
    "phone_number_validation_step_1",
    function (value, element) {
      var returndata = true;
      var phone_length = value.length;
      if (phone_length > 0) {
        if (phone_length == 9 && value.slice(0, 1) == 0) {
          returndata = false;
        }
        if (phone_length == 9 && value.slice(0, 1) != 0) {
          returndata = true;
        }

        if (phone_length == 10 && value.slice(0, 1) != 0) {
          returndata = false;
        }
        if (phone_length > 10 || phone_length <= 8) {
          returndata = false;
        }
      }
      return returndata;
    },
    " Sorry this phone number is invalid, please check you have entered it correctly"
  );

  //
  // jQuery.validator.addMethod(
  //   "username_not_same",
  //   function (value, element) {
  //     var returndata = true;
  //     var user_name = jQuery("#lw_username").val();
  //     user_name = user_name.toLowerCase();
  //     var first_name = jQuery("#lw_first_name").val();
  //     first_name = first_name.toLowerCase();
  //     if (user_name.indexOf(first_name) != -1) {
  //       returndata = false;
  //     }
  //     var last_name = jQuery("#lw_last_name").val();
  //     last_name = last_name.toLowerCase();
  //     if (user_name.indexOf(last_name) != -1 && returndata == true) {
  //       returndata = false;
  //     }
  //
  //     return returndata;
  //     return jQuery("#lw_username").val() != jQuery("#lw_last_name").val();
  //   },
  //   "Oops! Your last name and first name cannot be used as part of your username"
  // );

  jQuery.validator.addMethod(
    "validateEmailEdu",
    function (value, element) {
      //   var eduRegex = /^[^@]+@.+\.(edu|edu\..+)$/;;

      var eduRegex = /^[^@]+@((?!edu).)*$/;
      return this.optional(element) || eduRegex.test(value);
    },
    "Oops sorry, you seem to have entered a school email address, please enter your personal email address or your parent/guardian's email, to make sure you receive our messages"
  );

  jQuery.validator.addMethod(
    "validateEmail",
    function (value, element) {
      return (
        this.optional(element) ||
        /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(
          value
        )
      );
    },
    "Sorry this email is invalid, please check you have entered it correctly."
  );
  jQuery.validator.addMethod(
    "atleasteight",
    function (value) {
      // add symbol validation
      return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\S]{8,}$/.test(value);
    },
    "Passwords must contain at least 8 characters, 1 number and both uppercase and lowercase letters."
  );
  // jQuery.validator.addMethod(
  //   "checklower",
  //   function (value) {
  //     return /[a-z]/.test(value);
  //   },
  //   "Password must contain at least one uppercase."
  // );
  // jQuery.validator.addMethod(
  //   "checkupper",
  //   function (value) {
  //     return /[A-Z]/.test(value);
  //   },
  //   "Password must contain at least one uppercase."
  // );
  // jQuery.validator.addMethod(
  //   "checkdigit",
  //   function (value) {
  //     return /[0-9]/.test(value);
  //   },
  //   "Password must contain at least one digit."
  // );
  //
  // jQuery.validator.addMethod(
  //   "check12_to_20",
  //   function (value) {
  //     return /^(.{12,20}$)/.test(value);
  //   },
  //   "Password must be between 12 to 20 characters long."
  // );
  //
  // jQuery.validator.addMethod(
  //   "checkspecial",
  //   function (value) {
  //     return /^(?=.*[!@#$%&])/.test(value);
  //   },
  //   "Password must contain special characters from @#$%&."
  // );

  var lw_form_type = jQuery("input[name='lw_form_type']").val();
  if (lw_form_type == "form_a" || lw_form_type == "form_c" || lw_form_type == "form_a_direct") {
    LwshowTab(LwcurrentTab);
  }

  jQuery("body").append(
    '<div id="lw_loader" style="display:none"><div id="lw_loader_container"> <div id="lw_loader_container_inner"> <div id="lw_loader_data"><div id="DM_css_loader"></div><div id="lw_css_loader_text">We&apos;re working on it... </div></div><div id="lw_loader_info_data" style="display:none"></div></div></div></div>'
  );

  jQuery(document).on("submit", "#LwInvitationForm", function (event) {
    event.preventDefault();
    lw_ajax_request("LwInvitationForm", "");
  });
  jQuery(document).on("submit", "#LwRegistrationForm", function (event) {
    event.preventDefault();
    grecaptcha.ready(function () {
      grecaptcha
        .execute(lw_registration.site_key, { action: "submit" })
        .then(function (token) {
          // console.log(token)
          lw_ajax_request("LwRegistrationForm", token);
        });
    });
  });
  jQuery(document).on("submit", "#LwLoginForm", function (event) {
    event.preventDefault();
    lw_ajax_request("LwLoginForm", "");
  });
  jQuery(document).on("submit", "#LwForgotPasswordForm", function (event) {
    event.preventDefault();
    lw_ajax_request("LwForgotPasswordForm", "");
  });
});

function lw_check_email_address(tab) {
  LW_show_loader();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: lw_registration.ajaxUrl,

    data: {
      action: "lw_check_email_address",
      email_address: jQuery("#lw_email_address").val(),
    },
    error: function (jqXHR, exception) {
      //	window.location.reload();
    },
    success: function (data) {
      if (data.status == 0) {
        LW_show_loader_output(
          "<span class='error_msg'>" + data.message + "</span>",
          0
        );
        //setTimeout(function(){LW_hide_loader(); }, 1000);
      } else {
        jQuery(".lw_tablinks").removeClass("active");
        jQuery(".lw_tablinks:eq(" + tab + ")").addClass("active");
        jQuery(".lw_tabcontent").hide();
        LwshowTab(tab);
        LwcurrentTab = LwcurrentTab + 1;

        setTimeout(function () {
          LW_hide_loader();
        }, 1000);
      }
    },
  });
}
function lw_ajax_request(form_id, token) {
  if (jQuery("#" + form_id).valid()) {
    var pronounVals = jQuery("#" + form_id + " .lw-pronouns-select").val() || [];
    if (pronounVals.length > 2) {
      LW_show_loader_output("Please select up to two pronouns only.", 0);
      return;
    }
    //formData.append('')
    LW_show_loader();
    var formData = new FormData(jQuery("#" + form_id)[0]);
    if (token !== "") {
      formData.append("recaptcha_token", token);
    }
    jQuery.ajax({
      url: lw_registration.ajaxUrl,
      enctype: "multipart/form-data",
      method: "POST",
      dataType: "JSON",
      processData: false,
      contentType: false,
      cache: false,
      data: formData,
      success: function (resp) {
        if (resp.status == "1") {
          if (form_id == "LwInvitationForm") {
            if (resp.lw_generate_link != "") {
              window.location.href = resp.lw_generate_link;
            } else {
              LW_show_loader_output(resp.message, 1);
              setTimeout(function () {
                LW_hide_loader();
                window.location.reload();
              }, 3000);
            }
          } else if (form_id == "LwLoginForm") {
            LW_redirect_to(resp.redirect);
          } else if (form_id == "LwForgotPasswordForm") {
            LW_show_loader_output(resp.message, 1);
            setTimeout(function () {
              LW_hide_loader();
              window.location.reload();
            }, 3000);
          } else if (form_id == "LwRegistrationForm") {
            if (resp.redirect != "" && typeof resp.redirect !== "undefined") {
              window.location = resp.redirect;
            } else {
              setTimeout(function () {
                LW_hide_loader();
                window.location.reload();
              }, 3000);
            }
          } else {
            setTimeout(function () {
              LW_hide_loader();
              window.location.reload();
            }, 3000);
          }
        } else {
          LW_show_loader_output(resp.message, 0);
          //setTimeout(function(){ LW_hide_loader(); }, 3000);
        }
      },
      error: function (xhr, status) {
        LW_show_loader_output(
          "Sorry something wrong. Please try again later.",
          0
        );
        //setTimeout(function(){ LW_hide_loader(); }, 3000);
      },
    });
  }
}
//////////////////////////////////////hudson new codes
function un_focus_lw_check_email_address(tab) {
  LW_show_loader();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: lw_registration.ajaxUrl,

    data: {
      action: "lw_check_email_address",
      email_address: jQuery("#lw_email_address").val(),
    },
    error: function (jqXHR, exception) {
      //	window.location.reload();
    },
    success: function (data) {
      if (data.status == 0) {
        LW_show_loader_output(
          "<span class='error_msg'>" + data.message + "</span>",
          0
        );
        //setTimeout(function(){LW_hide_loader(); }, 1000);
        jQuery("#lw_registration_email_address").remove();
      } else {
        // jQuery(".lw_tablinks").removeClass("active");
        // jQuery(".lw_tablinks:eq(" + tab + ")").addClass("active");
        // jQuery(".lw_tabcontent").hide();
        // LwshowTab(tab);
        // LwcurrentTab = LwcurrentTab + 1;
        jQuery("#lw_registration_email_address").remove();
        setTimeout(function () {
          LW_hide_loader();
        }, 1000);
      }
    },
  });
}

jQuery("#lw_email_address").on("blur", function () {
  if (jQuery(this).val() !== "") {
    un_focus_lw_check_email_address(1);
  }
});
