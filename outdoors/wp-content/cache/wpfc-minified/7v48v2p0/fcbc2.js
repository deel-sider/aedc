// source --> https://www.assortedexplorations.com/outdoors/wp-content/cache/autoptimize/js/autoptimize_single_6857ed16327f63b33982ea69d8f73350.js 
document.addEventListener('DOMContentLoaded',function(event){var commentForm=document.getElementById("commentform");if(null===commentForm){return;}
var author=commentForm.querySelector("#author");if(null===author){return;}
author.addEventListener('blur',function(){this.value=this.value.replace(/\d+/g,'');},false);});