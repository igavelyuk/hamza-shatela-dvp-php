/*-------------------------------------------------------------------------------
Cookies & Local storage
-------------------------------------------------------------------------------*/
let setCookie = function(cname, cvalue, days){

  let expires = "";

  if (days) {
    let date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 *1000));
    expires = "; expires=" + date.toGMTString();
  }

  document.cookie = cname + "=" + cvalue + expires + "; path=/";
}

//Return a particular cookie
let getCookie = function(cname) {
  let name = cname + "=",
  decodedCookie = decodeURIComponent(document.cookie),
  ca = decodedCookie.split(';');

  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

//Checks if a cookie exists
let checkCookie = function(cookieToCheck){
  let cookie = getCookie(cookieToCheck);
  if (cookie != "") {
    return true;
  }
  return false;
}

//Delete an existing cookie
let deleteCookie = function( name, path, domain ) {
  setCookie(name, [], 1);
}



// Move a category to the bottom of the list
let bringToBottom = function(elem, notice = true) {

  $(".sigma_categories-wrap").append(elem);

  if( notice ){
    toastr.success(`${elem.find('h4').text()} - was moved to the bottom of the list`, 'Moved');
  }

}

/*-------------------------------------------------------------------------------
Filter Categories
-------------------------------------------------------------------------------*/
$('#search-categories, #category-list').on('keyup change', function() {

  let value = $(this).val(),
  expression = new RegExp('^' + value, 'i');

  filterCategories(expression);

});

let filterCategories = function(exp){
  $('.sigma_categories').each(function() {
    let isMatch = exp.test($(this).data('category')) || exp.test($(this).data('parent'));
    $(this).toggle(isMatch);
  });
}

/*-------------------------------------------------------------------------------
Select2
-------------------------------------------------------------------------------*/
$('select').select2();

/*-------------------------------------------------------------------------------
Tooltip
-------------------------------------------------------------------------------*/
$('[data-toggle="tooltip"]').tooltip();

/*-------------------------------------------------------------------------------
Collapse
-------------------------------------------------------------------------------*/
$("body").on('click', '.sigma_collapse-trigger', function(){
  $(this).toggleClass('closed');
  $(this).next('.sigma_collapse-content').slideToggle();
});

/*-------------------------------------------------------------------------------
Show Selected
-------------------------------------------------------------------------------*/
$(".toggle-selected").on('click', function(){

  $(this).toggleClass('active');
  $("#category-list").val('');
  $("#search-categories").val('');

  if( $(this).hasClass('active') ){
    $('.sigma_categories').each(function() {
      let isMatch = $(this).find('.sigma_category-section').hasClass('selected');
      $(this).toggle(isMatch);
    });
  }else{
    $('.sigma_categories').show();
  }

});

/*-------------------------------------------------------------------------------
Select all
-------------------------------------------------------------------------------*/
$(".toggle-all").on('click', function(){

  $('.sigma_category-section').toggleClass('selected');

});

/*-------------------------------------------------------------------------------
Magnific Popup
-------------------------------------------------------------------------------*/
$('body').magnificPopup({
  delegate: '.sigma_category-section a',
  type: 'iframe'
});

/*-------------------------------------------------------------------------------
Nestable list (Used for header mapping)
-------------------------------------------------------------------------------*/
let setNestableOutput = function(e){
  var list = e.length ? e : $(e.target),
  output = list.data('output');
}

let doNestable = function(){
  // Default nestable list
  $('#nestable_pages').nestable({
    maxDepth: 3,
    group: 1
  }).on('change', setNestableOutput);
}
doNestable();

let getNestableOutput = function(){
  return $('.dd').nestable('serialize');
}

// Print the result of the sections in the result tab
let renderResult = function() {

  let result = [],
    order,
    elem,
    $selected;

  $(".sigma_categories").each(function(index) {

    order = $(this).data('order');
    elem = $(".sigma_categories").eq(order);
    result[order] = [];
    $selected = elem.find('.selected');

    $selected.each(function() {
      result[order].push($(this).data('section'));
    });

  });

  return result;

}

// Convert the result to html markup
let resultToHtml = function(arr) {

  let $resultArea = $("#result-area");

  $resultArea.html('');

  arr.map(category => {

    if (category.length) {
      category.map(section => {
        $resultArea.append(`<p class="added-item">${section.name.replace('.html', '')}</p>`);
      });
    }

  });

  if ($(".added-item").length == 0) {
    $resultArea.html('<p>No Sections added</p>');
  }

}

// Recalculate the order of sections
let renderSectionsOrder = function() {

  $(".sigma_categories").each(function(index) {
    $(this).attr('data-order', index);
  });

  let result = renderResult();
  resultToHtml(result);

}

// Select 1 random section from each category
let selectRandom = function( array ){
  var m = array.length, t, i;

  // While there remain elements to shuffle…
  while (m) {

    // Pick a remaining element…
    i = Math.floor(Math.random() * m--);

    // And swap it with the current element.
    t = array[m];
    array[m] = array[i];
    array[i] = t;
  }

  return array;
}

// Get available pages
let getPages = function(){
  let pages = [];
  $.ajax({
    url: 'data/pages.json',
    async: false,
    dataType: 'json',
    success: function ( response ) {
      pages = response;
    }
  });
  return pages;
}

// Check if we are building a one page template
let isOnePage = function(){

  return localStorage.getItem('isOnePage');

}

// Get available One page templates
let getOnePageTemplates = function(){
  let pages = [];
  $.ajax({
    url: 'data/onePage.json',
    async: false,
    dataType: 'json',
    success: function ( response ) {
      pages = response;
    }
  });
  return pages;
}

// Get available topics
let getTopics = function(){

  let topics = [],
  pages = isOnePage() == 'true' ? getOnePageTemplates() : getPages();

  for( let prop in pages ){
    topics.push( prop.replace('_', ' ') );
  }

  return topics;

}

// Get the topic selected
let getTopic = function( selectedTopic ){

  let topics = getTopics();

  selectedTopic = topics.filter( topic => {
    return topic.toLowerCase() == selectedTopic.replace('_', ' ').toLowerCase();
  });

  return selectedTopic[0];

}

// Map the sections to each page
// This function will disable any categories that will not be used in a given page
let mapCategoriesToPage = function( page ){

  let data = renderResult(),
  pageSections = page.sections,
  pageLimit = page.limit;

  // Disable all the categories that are not available in this page
  $(".sigma_categories").each(function(){
    let dataCategory = $(this).data('category'),
    index = pageSections.indexOf(dataCategory);

    if( !pageSections.includes( dataCategory ) ){
      $(this).addClass('disabled');
    }

    if( index != -1 ){
      $(this).insertBefore($(this).parent().find(".sigma_categories").eq(index));
    }else{
      bringToBottom( $(this), false );
    }

  });

}

// Format the page data to something select2 can understand
let formatPageData = function(pagesData){

  let keys = [];

  for(data in pagesData) {
    keys.push({ 'id': data, 'text': data.replaceAll('_', ' ') })
  }

  return keys;

}

let isHomePage = function( pageName ){
  return pageName.includes('Home');
}

let $sortableCategories = $("#sortable-sections");

$sortableCategories.sortable({
  cursor: "grabbing",
  change: function(event, ui) {
    ui.placeholder.css({'border-radius': '10px', 'backdrop-filter': 'blur( 10.0px )', 'box-shadow': '0 8px 32px 0 rgb(132 133 146 / 17%)', 'visibility': 'visible', 'background': 'rgba( 255, 255, 255, 0.05 )'});
  },
  stop: renderSectionsOrder
}).disableSelection();
