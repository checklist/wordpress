/* Add a checkbox to each item in the <LI/> */
jQuery(document).ready(function($){
      $('<input type="checkbox"/>').prependTo('.checklist-box li');
});