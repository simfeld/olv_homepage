// on site open
jQuery(document).ready(function($){
	loadAcc();
	loadLoginButton();
  loadpopRegistrationButton();
  loadRegistrationButton();
  loadBackButton();
  loadDoubleButton("#registration");
  loadDoubleButton("#login");
  document.formvalidator = new JFormValidator;
});

function loadpopRegistrationButton ()
{
  // add functionality to pop registration button
  jQuery( "#popRegistration" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    toggleForms();
    document.formvalidator = new JFormValidator;
  });
}

function loadBackButton ()
{
  // add functionality to pop registration button
  jQuery( "#goBack" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    toggleForms();
    document.formvalidator = new JFormValidator;
  });
}

function loadButtons()
{
  loadAcc();
  loadDoubleButton("#registration");
  loadLoginButton();
  loadRegistrationButton();
  loadpopRegistrationButton();
  loadBackButton();
}

function loadResetButton ()
{
  jQuery( "#resetForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', {reset:"true"});
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      // replace content
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadDoubleButton("#login");
      document.formvalidator = new JFormValidator;
    });
  });
}

function loadRegistrationButton()
{
  // functionality: enter name etc., then submit
  jQuery( "#registrationForm" ).submit(function( event ) {
    event.preventDefault();
    var $form = jQuery( this ),
    t = $form.find( "input[name='type']:checked" ).val(),
    f = $form.find( "input[name='first']" ).val(),
    l = $form.find( "input[name='last']" ).val(),
    e = $form.find( "input[name='email']" ).val();
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { type:t, first:f, last :l, email:e, register: "true"} );
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadDoubleButton("#login");
      document.formvalidator = new JFormValidator;
    });
  });
}

function loadLoginButton()
{
  // functionality: choose type (driver/passenger) and name, then submit
  jQuery( "#loginForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    //Get some values from elements on the page:
    var $form = jQuery( this ),
    t = $form.find( "input[name='type']:checked" ).val(),
    d = $form.find( "select.dselect" ).val(),
    p = $form.find( "select.pselect" ).val();
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { type : t, driver : d, passenger : p} );
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadResetButton();
      loadAddButton();
      document.formvalidator = new JFormValidator;
    });
  });
}

function loadAddButton()
{
  // functionality: enter data for adding driver to event, then submit
  jQuery( "#addDriverForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    //Get some values from elements on the page:
    var $form = jQuery( this ),
    i = $form.find( "input[name='id']" ).val(),
    eid = $form.find( "input[name='eventid']" ).val(),
    n = $form.find( "input[name='nr']" ).val(),
    p = $form.find( "input[name='place']" ).val(),
    t1 = $form.find( "input[name='timehr']" ).val(),
    t2 = $form.find( "input[name='timemin']" ).val();
    var t = t1 + ':' + t2 + ':00';
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { add:"true", type:"driver", ident:i, eventid:eid, nr:n, place:p, time:t} );
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      //jQuery( "div.moduletable.replace" ).text(data);
      loadButtons();
      loadResetButton();
      loadAddButton();
      document.formvalidator = new JFormValidator;
    });
  });
  // functionality: enter data for adding passenger to ride, then submit
  jQuery( "#addPassengerForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    //Get some values from elements on the page:
    var $form = jQuery( this ),
    i = $form.find( "input[name='id']" ).val(),
    rid = $form.find( "input[name='rid']" ).val();
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { add:"true", type:"passenger", ident:i, rideid:rid} );
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadResetButton();
      loadAddButton();
      document.formvalidator = new JFormValidator;
    });
  });
  // functionality: delete driver from event
  jQuery( "#deleteDriverForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    //Get some values from elements on the page:
    var $form = jQuery( this ),
    i = $form.find( "input[name='id']" ).val(),
    rid = $form.find( "input[name='rid']" ).val();
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { del:"true", type:"driver", ident:i, rideid:rid} );
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadResetButton();
      loadAddButton();
      document.formvalidator = new JFormValidator;
    });
  });
  // functionality: delete passenger from ride
  jQuery( "#deletePassengerForm" ).submit(function( event ) {
    // Stop form from submitting normally
    event.preventDefault();
    //Get some values from elements on the page:
    var $form = jQuery( this ),
    i = $form.find( "input[name='id']" ).val(),
    rid = $form.find( "input[name='rid']" ).val();
    // Send the data using post
    var posting = jQuery.post( document.baseurl + 'index.php?option=com_content&view=article&id=14&Itemid=139', { del:"true", type:"passenger", ident:i, rideid:rid} );
    // Put the results in a div
    posting.done(function( data ) {
      var content = jQuery(data).find("div.custom.replace");
      jQuery( "div.moduletable.replace" ).html( content );
      loadButtons();
      loadResetButton();
      loadAddButton();
      document.formvalidator = new JFormValidator;
    });
  });
}
