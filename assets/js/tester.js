let getUploadedFiles = function(){

  let pages = [],
  filesInput = document.getElementById("file").files;

  for( let i = 0; i < filesInput.length; i++ ){

    let fileName = filesInput[i].name;
    if( fileName.slice((Math.max(0, fileName.lastIndexOf(".")) || Infinity) + 1) == 'html' ){
      pages.push(filesInput[i]);
    }

  }

  return pages;
}

let addedFilesToHtml = function(){

  let pages = getUploadedFiles(),
  $uploadSection = $("#section-test-uploaded-files");

  $( '.count-files b' ).html(pages.length);

  if( pages.length == 0 ){
    $("#section-test-form .sigma_btn").hide();
    return $uploadSection.html(' No Files uploaded yet. Once you upload the files, they will be added here. ');
  }

  $uploadSection.html('');

  pages.map( (page, index) => {
    let fileSize = (page.size / 1024).toFixed(2);
    $uploadSection.append(`<div class="sigma_file-test sigma_card" data-page="${page.name}" data-key="${index}"> <span> ${page.name} </span> <div class="sigma_file-test-content">
    <p><strong>Last Modified:</strong> ${page.lastModifiedDate} </p>
    <p><strong>Size:</strong> ${fileSize}kb </p>
  </div> </div>`);
  });

  $("#section-test-form .sigma_btn").show();

}

let ajaxRunTests = function( formData ){

  $.ajax({
    url: "ajaxRunTests.php",
    dataType: 'json',
    type: "POST",
    data: new FormData(formData),
    contentType: false,
    cache: false,
    processData:false,
  }).done(function(response) {

    if (response.status == 'error') {
      toastr.error(response.message, 'Error');
      $(".sigma_blockable").removeClass('blocked');
    } else if (response.status == 'success') {

      for( let i = 0; i < response.file.name.length; i++ ){

        let $item = $(`[data-page="${response.file.name[i]}"]`);

        if( response.errors[i] ){
          $item.addClass('error');
          response.errors[i].map(error => {
            $item.append(`<p>${error}</p>`);
          })
        } else{
          $item.addClass('success');
        }

      }

      toastr.success(response.message, 'Success');
      $(".sigma_blockable").removeClass('blocked');

    }

  }).fail(function(error) {
    toastr.error(`Error ${error.status} ${error.statusText}`, 'Error');
    $(".sigma_blockable").removeClass('blocked');
  });

}

$("#file").on('change', function(){
  addedFilesToHtml();
});

$("#section-test-form").on('submit', function( e ){
  e.preventDefault();

  $(".sigma_blockable").addClass('blocked');

  ajaxRunTests( this );

});
