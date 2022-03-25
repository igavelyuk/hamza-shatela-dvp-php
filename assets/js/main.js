// Reset localstorage value for one page on refresh
localStorage.setItem('isOnePage', false);

/*-------------------------------------------------------------------------------
  Select sections
  -------------------------------------------------------------------------------*/
$("body").on('click', ".sigma_category-section", function(e) {

  if (!e.target.classList.contains('sigma_category-section'))
    return;

  $(this).toggleClass('selected');

  let result = renderResult();
  console.log(result);
  resultToHtml(result);

});

// Disable a section
$("body").on('click', ".sigma_category-disable", function() {
  let $parent = $(this).closest('.sigma_categories');

  if (!$parent.hasClass('disabled')) {
    bringToBottom($parent);
  }

  $parent.toggleClass('disabled');
  $parent.find('.sigma_category-section').removeClass('selected');

  renderSectionsOrder();
});

// Convert the result of added pages to html markup
let addedPagesToHtml = function(pages) {

  pages = JSON.parse(pages);
  let $pagesArea = $("#pages-area");

  $pagesArea.html(pages);

}

let getPageItem = function( page, index ){
  return `<li title="${page.name}" class="dd-item sigma_section-added" data-id="${index}"><div class="dd-handle dd-burger-handle"></div><div class="dd-content"><p>${page.name}</p><div class="sigma_section-added-controls"><a target="_blank" href="${page.relative_path}" > <i class="far fa-external-link"></i></a><a target="_blank" class="delete-page" href="${page.relative_path}"> <i class="far fa-trash"></i > </a> </div></div> </li>`;
}

$("body").on('submit', "#add-custom-page", function(e){
  e.preventDefault();

  let $pagesArea = $("#pages-area .dd-list:first-child"),
  page = { name: $("#custom-page").val(), relative_path: '#' };

  $pagesArea.append( getPageItem( page, $(".sigma_section-added").length ) );

  $("#custom-page").val('');

});

// Main ajax function
let ajaxGetFileContents = function(data) {

  // Only process the arrays that are not empty
  data = data.filter( el => {
    return el.length != 0;
  });

  $(".sigma_blockable").addClass('blocked');

  $.ajax({
    url: "ajaxGetFileContents.php",
    dataType: 'json',
    type: "POST",
    data: {
      categories: data
    }
  }).done(function(result) {
    ajaxCreateProject(result);
  }).fail(function(error) {
    toastr.error(`Error ${error.status} ${error.statusText}`, 'Error');
    $(".sigma_blockable").removeClass('blocked');

  });

}

let ajaxCreateProject = function(content) {

  let $sections = $(content),
    pageName = $("#page-name").val(),
    styleSheet = $("#main-sheet").val(),
    btnStyle = $("#btn-style").val(),
    btnShape = $("#btn-shape").val(),
    fontStyle = $("#font-style").val(),
    sidebarStyle = $("#sidebar-style").val(),
    isOnePage = $("#one-page").is(":checked"),
    topic = $("#auto-generate-topic").val(),
    templateAuthor = $("#template-author").val();

  $("#page-name").removeClass('error');

  if (pageName == '' || pageName == null || pageName == undefined) {
    $("#page-name").addClass('error');
    $(".sigma_blockable").removeClass('blocked');
    return false;
  }

  $.ajax({
    url: "ajaxCreateProject.php",
    type: "POST",
    data: {
      pages: content,
      page_name: pageName,
      style_sheet: styleSheet,
      btn_style: btnStyle,
      btn_shape: btnShape,
      font_style: fontStyle,
      sidebar_style: sidebarStyle,
      template_author: templateAuthor,
      is_one_page: isOnePage,
      topic: topic
    }
  }).done(function(response) {

    response = JSON.parse(response);

    if (response.status == 'error') {
      toastr.error(response.message, 'Error');
      $(".sigma_blockable").removeClass('blocked');
    } else if (response.status == 'success') {

      addedPagesToHtml(response.pages);

      $("#page-name").val('');

      if (!$('#keep-sections').is(":checked")) {
        $(".sigma_category-section").removeClass('selected');
        let result = renderResult();
        resultToHtml(result);
      }

      $("#download-project").removeClass('d-none');
      $(".sigma_blockable").removeClass('blocked');

      doNestable();

    }

  }).fail(function(error) {
    toastr.error(`Error ${error.status}  ${error.statusText}`, 'Error');
    $(".sigma_blockable").removeClass('blocked');
  });

}

// Create zip and download project
let ajaxDownloadProject = function() {

  $(".sigma_blockable").addClass('blocked');

  if (confirm('Downloading the project will consider that your project is complete, and thus will be deleted. Are you sure you want to continue?')) {

    $.ajax({url: "ajaxDownloadProject.php", type: "POST"}).done(function(response) {

      response = JSON.parse(response);

      if (response.status == 'error') {
        toastr.error(response.message, 'Error');
      } else if (response.status == 'success' && (response.url != '' || response.url != undefined || response.url != null)) {
        toastr.success(response.message, 'Success');
        window.location = response.url;
        $("#download-project").addClass('d-none');
        addedPagesToHtml(response.pages);

        $(".sigma_blockable").removeClass('blocked');

      }

    }).fail(function(error) {
      toastr.error(`Error ${error.status}  ${error.statusText}`, 'Error');
      $(".sigma_blockable").removeClass('blocked');

    });

  }

  return false;

}

let deletePages = function() {
  if ($(".sigma_section-added").length == 0)
    return;

  let pages = [];

  $(".sigma_section-added").each(function() {
    pages.push($(this).find('.delete-page').attr('href'));
  });

  ajaxDeletePages(pages);
}

// Delete the entire project
let deleteProject = function(){

  $.ajax({
    url: "ajaxDeleteProject.php",
    type: "POST",
  }).done(function(response) {

    response = JSON.parse(response);

    if (response.status == 'error') {
      toastr.error(response.message, 'Error');
    } else if (response.status == 'success') {
      toastr.success(response.message, 'Success');
      addedPagesToHtml(response.pages);
    }

  }).fail(function(error) {
    toastr.error(`Error ${error.status}  ${error.statusText}`, 'Error');
  });

}

let ajaxDeletePages = function(pages) {

  $.ajax({
    url: "ajaxDeletePages.php",
    type: "POST",
    data: {
      pages: pages
    }
  }).done(function(response) {

    response = JSON.parse(response);

    if (response.status == 'error') {
      toastr.error(response.message, 'Error');
    } else if (response.status == 'success') {
      toastr.success(response.message, 'Success');
      addedPagesToHtml(response.pages);

      doNestable();
    }

  }).fail(function(error) {
    toastr.error(`Error ${error.status}  ${error.statusText}`, 'Error');
    $(".sigma_blockable").removeClass('blocked');

  });

}

// Download Project
$("#download-project").on('click', function() {
  ajaxDownloadProject();
});

// Generate HTML
$("#generate-html-form").on('submit', function(e) {

  e.preventDefault();

  let data = renderResult();
  ajaxGetFileContents(data);

});

// Main auto generate function
let autoGenerate = function( pages, predefinedSections = [] ){

  if( pages == '' || pages == undefined || pages == null ){

    $(".sigma_categories").removeClass('disabled');
    $(".sigma_category-section").removeClass('selected');

    $("#keep-sections").prop("checked", false);

    let result = renderResult();
    resultToHtml(result);

    // Preprocess
    $.ajax({
      url: "ajaxAutoGenerate.php",
      type: "POST",
    }).done(function(response) {

      response = JSON.parse(response);

      if (response.status == 'error') {
        toastr.error(response.message, 'Error');
      } else if (response.status == 'success') {
        toastr.success(response.message, 'Success');
      }

    }).fail(function(error) {
      toastr.error(`Error ${error.status}  ${error.statusText}`, 'Error');
      $(".sigma_blockable").removeClass('blocked');
    });

    return true;

  }

  // Reset categories to default before we map another page
  let $categories = $('.sigma_categories').removeClass('disabled');

  $('.sigma_category-section').removeClass('selected');

  // Disable all categories not related to the page
  mapCategoriesToPage( pages[0] );

  $categories = $('.sigma_categories:not(.disabled)');

  // Select random section from each enabled category
  $categories.each(function(){

    let $sections = $(this).find('.sigma_category-section'),
    dataCategory = $(this).data('category'),
    $randomSection = $(selectRandom($sections).slice(0, 1));

    // Select a fixed header/subheader/footer/to top/popup/preloader in the template
    if( dataCategory == 'subheader' ){
      predefinedSections['subheader'] = ( predefinedSections['subheader'] == undefined ) ? $randomSection.data('name') : predefinedSections['subheader'];
      $(this).find(`[data-name="${predefinedSections['subheader']}"]`).addClass('selected');
    }else if( dataCategory == 'header' && pages[0].name != 'Home 2' ){
      predefinedSections['header'] = ( predefinedSections['header'] == undefined ) ? $randomSection.data('name') : predefinedSections['header'];
      $(this).find(`[data-name="${predefinedSections['header']}"]`).addClass('selected');
    }else if( dataCategory == 'footer' && pages[0].name != 'Home 2' ){
      predefinedSections['footer'] = ( predefinedSections['footer'] == undefined ) ? $randomSection.data('name') : predefinedSections['footer'];
      $(this).find(`[data-name="${predefinedSections['footer']}"]`).addClass('selected');
    }else if( dataCategory == 'to-top' ){
      predefinedSections['to_top'] = ( predefinedSections['to_top'] == undefined ) ? $randomSection.data('name') : predefinedSections['to_top'];
      $(this).find(`[data-name="${predefinedSections['to_top']}"]`).addClass('selected');
    }else if( dataCategory == 'popup' ){
      predefinedSections['popup'] = ( predefinedSections['popup'] == undefined ) ? $randomSection.data('name') : predefinedSections['popup'];
      $(this).find(`[data-name="${predefinedSections['popup']}"]`).addClass('selected');
    }else if( dataCategory == 'preloader' ){
      predefinedSections['preloader'] = ( predefinedSections['preloader'] == undefined ) ? $randomSection.data('name') : predefinedSections['preloader'];
      $(this).find(`[data-name="${predefinedSections['preloader']}"]`).addClass('selected');
    }else{
      $randomSection.addClass("selected");
    }

  });

  // Display the results in the results area
  renderSectionsOrder();

  // Create the file with the assigned sections
  $("#page-name").val( pages[0].name );
  let data = renderResult();
  ajaxGetFileContents(data);

  // Remove the first page from the array of pages
  pages.shift();

  setTimeout(function(){
    autoGenerate( pages, predefinedSections );
  }, 1000);

}

$("#auto-generate").on('submit', function(e){
  e.preventDefault();

  $("#keep-sections").prop("checked", true);

  let topic = $("#auto-generate-topic").val(),
  selectedTopic = getTopic( topic ),
  formattedTopic = topic.replace('_', ' ');

  if( selectedTopic == null || selectedTopic == '' || selectedTopic == undefined ){
    return toastr.error('Undefined Topic', 'Error');
  }

  let allPages = isOnePage() == 'true' ? getOnePageTemplates() : getPages(),
  pages = allPages[topic];

  pages.map( page => {

    page.sections.unshift( 'preloader', 'popup' );

    // Add header/subheader/hero to the start of the page based on the type of the page
    if( isHomePage( page.name ) ){
      page.sections.unshift( 'header', 'hero' );
    }else if( !isHomePage( page.name ) ){
      page.sections.unshift( 'header', 'subheader' );
    }

    // Add a footer, popup and back to top button to the very end of the page
    page.sections.push( 'to-top', 'footer' );

  });

  if( confirm( `Are you sure you want to auto generate a project under the "${formattedTopic}" category? Keep note that nothing in the existing project will get deleted, but existing files will be overridden if the same name was used.` ) ){
    autoGenerate(pages);
  }

});

$("#one-page").on('change', function(){

  var $this = $(this),
  isOnePage = $this.is(':checked'),
  $topicData = $("#auto-generate-topic"),
  pagesData = formatPageData(getPages()),
  onePageData = formatPageData(getOnePageTemplates());

  // Update the application storage
  localStorage.setItem('isOnePage', isOnePage);

  // Update the select2 data
  $($topicData).html('').select2({
    data: isOnePage ? onePageData : pagesData,
    width: "215px"
  });

});

// Delete individual page
$("body").on('click', ".delete-page", function(e) {

  e.preventDefault();

  let $this = $(this),
  page = $this.attr('href');

  if (confirm('Are you sure you want to delete ')) {

    // Is this a custom page?
    if( page == '#' ){
      $this.closest('.sigma_section-added').remove();
    }else{
      ajaxDeletePages([page]);
    }
  }

});

// Clear added pages
$("body").on('click', "#clear-pages", function() {

  if (confirm('Are you sure you want to delete all the pages in this project')) {
    deletePages();
  }

});

$("body").on('click', "#delete-project", function() {

  if (confirm('Are you sure you want to delete the entire project')) {
    deleteProject();
  }

});
