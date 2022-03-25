let structureMenu = function( menu ){

  menu.map( (item, index) => {

    var $pageElem = $("#nestable_pages").find(`[data-id="${item.id}"]`);

    item.name = $pageElem.find('> .dd-content > p').text();
    item.path = item.name.includes('.html') ? item.name : "#";

    if( item.children != null && item.children != undefined && item.children != '' ){
      structureMenu( item.children, menu );
    }

  });

  return menu;

}

$("body").on('click', "#map-headers", function(){

  if( confirm( 'Do you want to map the current header setup?' ) ){
    $(".sigma_blockable").addClass('blocked');

    let menuOrder = getNestableOutput(),
    structuredMenu = structureMenu(menuOrder);

    $.ajax({
      url: "ajaxHeaderMapper.php",
      dataType: 'json',
      type: "POST",
      data: {
        menus: structuredMenu
      }
    }).done(function(response) {

      if (response.status == 'error') {
        toastr.error(response.message, 'Error');
      } else if (response.status == 'success') {

        if( response.missing_items.length == 0 ){
          toastr.success(response.message, 'Success');
        } else{
          toastr.warning(`${response.message}, but the following pages are missing a header so no changes were made to them: <br> ${response.missing_items.join('<br>')}`, 'Warning');
        }

      }

      $(".sigma_blockable").removeClass('blocked');
    }).fail(function(error) {
      toastr.error(`Error ${error.status} ${error.statusText}`, 'Error');
      $(".sigma_blockable").removeClass('blocked');

    });
  }

});
