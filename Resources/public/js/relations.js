var _NUM_PAGE = 1;

function relationAdd(self) {
  var link = $(self).attr('href');
  $(self).attr('href', 'javascript:void(0)');
  
  $.ajax({
    url: link,
    success: function(json){
      $(self).attr('href', link);
      $(self).find('i').attr('class', 'icon-user');
      $(self).fadeOut(800, function(){
        $(this).remove();
      });
    },
    error: function(){
      $(self).attr('href', link);
      $(self).find('i').attr('class', 'icon-user');      
    }
  });
  return false;
}

function relationRemove(self) {
  var link = $(self).attr('href');
  $(self).attr('href', 'javascript:void(0)');
  
  $.ajax({
    url: link,
    success: function(json){
      $(self).attr('href', link);
      $(self).find('i').attr('class', 'icon-user');
      $(self).fadeOut(800, function(){
        $(this).remove();
      });
    },
    error: function(){
      $(self).attr('href', link);
      $(self).find('i').attr('class', 'icon-user');      
    }
  });
  return false;
}

/*function relationMore(self){
  _NUM_PAGE++;
  var collectionContainer = $('#' + $(self).data('container'));
  var _HREF = $(self).data('href');

  $('#agro-link-pager').hide();
  $('#agro-link-load').show();
  
  $.ajax({
    url: _HREF + _NUM_PAGE + '/',
    cache: true,
    success: function(json){
      collectionContainer.append($(json.response));
      if(json.haveToPaginate == '1'){
        $('#agro-link-pager').show();
      }else{
        $('#agro-link-pager').hide();
      }
      $('#agro-link-load').hide();
    }
  });
  return false;
}*/

;(function ( $ ) {
    'use strict';
    
    $(document).ready(function() {
      $('a[data-relation="add"]').on('click', function (e) {
        e.preventDefault();
        return relationAdd(this);
      });

      $('a[data-relation="remove"]').on('click', function (e) {
        e.preventDefault();
        return relationRemove(this);
      });

      /*$('a[data-pager-button="add"]').on('click', function (e) {
        e.preventDefault();
        return more(this);
      });*/
      
      $('a[data-load="true"]').bind('click', function() {
        $(this).find('i').attr('class', 'icon-spinner icon-spin');
      });
    });    
})( jQuery );

/*(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $('form').bind('submit', function() {
            $(this).find('button[type="submit"].btn-primary i').attr('class', 'icon-spinner icon-spin');
        });
   });
})( jQuery );*/