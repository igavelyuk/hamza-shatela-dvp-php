function saveTemplate(val){

  localStorage.setItem( `sigma-vault-${val}`, $("#sortable-sections").html() );

  $("#template-name").val('');

  toastr.success(`Template ${val} saved successfully`, 'Success');

}

function deleteTemplate(key){
  if( confirm('Are you sure you want to delete this template?') ){
    localStorage.removeItem(key);
    getTemplates();
    toastr.success('Template successfully deleted', 'Success');
  }
}

function applyTemplate(key){
  if( confirm( 'Importing a template will override your changes. Continue?' ) ){
    $("#sortable-sections").html( localStorage.getItem(key) );
    toastr.success('Template successfully loaded', 'Success');
  }
}

function getTemplates(){

  let $templatesArea = $("#templates-area"),
  vaultTemplates = [];

  $templatesArea.html('');

  for (let key in localStorage){
    if(key.includes('sigma-vault')){
      vaultTemplates.push(key);
    }
  }

  if( vaultTemplates.length > 0 ){
    vaultTemplates.map(template => {
      $templatesArea.append(`<div data-key="${template}" class="sigma_template-item"><p>${template.replaceAll('-', ' ').replace('sigma vault ', '')}</p><div class="sigma_template-item-controls"><span class="import-template"> <i class="far fa-download"></i> </span><span class="delete-template"> <i class="far fa-trash"></i> </span></div></div>`);
    });

    return true;
  }

  $templatesArea.html('<p>You don\'t have any saved templates</p>');

}
getTemplates();

$("body").on('click', '.sigma_template-item .import-template', function(){
  applyTemplate( $(this).closest('.sigma_template-item').data('key') );
});

$("body").on('click', '.sigma_template-item .delete-template', function(){
  deleteTemplate( $(this).closest('.sigma_template-item').data('key') );
});

$("#templates-form").on('submit', function(e){

  e.preventDefault();

  if( $(".sigma_template-item").length > 9 ){
    toastr.error('You can only have 10 templates at a time', 'Error');
    return false;
  }

  let val = $("#template-name").val();

  if(val == '' || val == null || val == undefined){
    return $("#template-name").addClass('success');
  }

  saveTemplate(val);
  getTemplates();
});
