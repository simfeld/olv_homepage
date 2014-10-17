
function loadDoubleButton(selector)
{
  // no elementselected
  jQuery( selector + " select.dselect" ).prop("selectedIndex", -1);
  jQuery( selector + " select.pselect" ).prop("selectedIndex", -1);
  // switch function
  jQuery( selector + " label.btn" ).click(function() {
    jQuery( selector + " label.btn" ).toggleClass("active");
    jQuery( selector + " select" ).toggleClass("hidden");
  });
}

function toggleForms()
{
  // hide accordion and login
  jQuery("div#accordion").toggleClass("hidden");
  jQuery("div#login").toggleClass("hidden");
  // show registration
  jQuery("div#register").toggleClass("hidden");
  jQuery("div#back").toggleClass("hidden");
  jQuery("div#registration").toggleClass("hidden");
}
