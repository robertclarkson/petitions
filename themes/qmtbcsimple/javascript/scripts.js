import 'jquery';
import 'bootstrap';
import SignaturePad from "signature_pad/dist/signature_pad.min.js";

// import "./script.js";
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel';
require("../css/typography.css");
require("../css/form.css");
require("../css/tinyslider.css");
require("../css/layout.css");
import { tns } from "tiny-slider/src/tiny-slider"
import Shuffle from 'shufflejs';


$(document).ready(function(){ 
  if($('.my-slider').length) {
    var slider = tns({
      container: '.my-slider',
      items: 1,
      slideBy: 'page',
      autoplay: true,
      autoplayButton: false,
      autoplayButtonOutput: false,
      controlsPosition: 'bottom',
      navPosition: 'bottom',
    });
  }

  $('.events-container').each(function() {
    var shuffleInstance = new Shuffle($(this), {
      itemSelector: '.event-item',
      sizer: '.my-sizer-element',
      filterMode: Shuffle.FilterMode.ALL
    }); 

    $('.events-category-filter a').click(() => {
      const selectedCat = $(event.target).data('category');

      console.log(selectedCat);
      var cat = [];
      if($('#event_location option:selected').val()) {
        cat = [$('#event_location option:selected').val()];
      }
      if(selectedCat == 'All') {
        shuffleInstance.filter(cat);
      }
      else {
        cat.push(selectedCat);
        shuffleInstance.filter(cat);
      }

    });

    $('#event_location').change(function() {
      $(this).find('option:selected').each(function() {
        var optionValue = $(this).attr("value");
        if(optionValue){
          shuffleInstance.filter(optionValue);
        } else{
          shuffleInstance.filter(Shuffle.ALL_ITEMS);
        }
      })
    })

  });



  $('.event').hover((event) => {
    console.log('event hover on', event.target);
    $(event.target).parents('.event').find('.overlay').animate().css('height', '100%');
  }, (event) => {
    console.log('event hover off', event.target);
    $('.overlay').animate().css('height', 65);
  });

  $('.selectiongroup li input[type="radio"]').change();
  $(document).on('change', '.selectiongroup li input[type="radio"]', function() {
    console.log('selectchange', this, $(this).parents('li').addClass('selected'));
    $(this).parents('ul').find('li').removeClass('selected');
    $(this).parents('ul').find('li').find('.selectiongroup_item input, .selectiongroup_item select').prop('disabled', true);
    
    $(this).parents('li').addClass('selected');
    $(this).parents('li').find('.selectiongroup_item input, .selectiongroup_item select').prop('disabled', false);

  });

  $('#Form_form').submit((event) => {
    $('.cycle-loading-box').show('fast');
    $('#Form_form_action_submit').prop( "disabled", true );
  })

  $('#Form_EventForm').submit((event) => {
    $('#Form_EventForm_action_submit').prop( "disabled", true );
  });

    if($('.related-events-slider').length) {
        tns({
            container: '.related-events-slider',
            items: 1,
            gutter: 15,
            controls: false,
            navPosition: 'bottom',
            responsive: {
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    }

    $('#Form_EventForm_Age').focusout(function() {
        var age = $(this).val();
        if(age && $.isNumeric(age)) {
            if(age < 18) {
                $('#Form_EventForm_ParentName_Holder').show('fast');
            } else {
                $('#Form_EventForm_ParentName_Holder').hide('fast');
            }
        } else {
            $('#Form_EventForm_ParentName_Holder').hide('fast');
        }
    });
	
    $('#Form_EventForm_Age').trigger('focusout');

	var wrapper = document.getElementById("signature-pad");
  if(wrapper) {
    var undoButton = wrapper.querySelector("[data-action=undo]");
    var clearButton = wrapper.querySelector("[data-action=clear]");
    var changeColorButton = wrapper.querySelector("[data-action=change-color]");
    var savePNGButton = wrapper.querySelector("[data-action=save-png]");
    var saveJPGButton = wrapper.querySelector("[data-action=save-jpg]");
    var saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
    // var saveToServerButton = wrapper.querySelector("[data-action=save-server]");
    var saveToServerButton = document.getElementById("Form_form_action_submit");

    var canvas = wrapper.querySelector("canvas");
  
  	var signaturePad = new SignaturePad(canvas, {
  	  // It's Necessary to use an opaque color when saving image as JPEG;
  	  // this option can be omitted if only saving as PNG or SVG
  	  backgroundColor: 'rgb(255, 255, 255)'
  	});

    saveToServerButton.addEventListener("click", function (event) {
      if (signaturePad.isEmpty()) {
        event.preventDefault();
        alert("Please provide your signature.");

      } else {
        var dataURL = signaturePad.toDataURL('image/image/jpeg');

        jQuery('#Form_form_sig').val(dataURL);
        // download(dataURL, "signature.jpg");
     	//    jQuery.ajax({
    	//   type: "GET",
    	//   url: window.location.href+'saveSignature',
    	//   data: {sig: dataURL},
    	//   success: (event => {
    	//   	console.log('success', event)
    	//   }),
    	// });
      }
    });

    // Adjust canvas coordinate space taking into account pixel ratio,
    // to make it look crisp on mobile devices.
    // This also causes canvas to be cleared.
    function resizeCanvas() {
      // When zoomed out to less than 100%, for some very strange reason,
      // some browsers report devicePixelRatio as less than 1
      // and only part of the canvas is cleared then.
      var ratio =  Math.max(window.devicePixelRatio || 1, 1);

      // This part causes the canvas to be cleared
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext("2d").scale(ratio, ratio);

      // This library does not listen for canvas changes, so after the canvas is automatically
      // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
      // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
      // that the state of this library is consistent with visual state of the canvas, you
      // have to clear it manually.
      signaturePad.clear();
    }

    // On mobile devices it might make more sense to listen to orientation change,
    // rather than window resize events.
    window.onresize = resizeCanvas;
    resizeCanvas();

    function download(dataURL, filename) {
      var blob = dataURLToBlob(dataURL);
      var url = window.URL.createObjectURL(blob);

      var a = document.createElement("a");
      a.style = "display: none";
      a.href = url;
      a.download = filename;

      document.body.appendChild(a);
      a.click();

      window.URL.revokeObjectURL(url);
    }

    // One could simply use Canvas#toBlob method instead, but it's just to show
    // that it can be done using result of SignaturePad#toDataURL.
    function dataURLToBlob(dataURL) {
      // Code taken from https://github.com/ebidel/filer.js
      var parts = dataURL.split(';base64,');
      var contentType = parts[0].split(":")[1];
      var raw = window.atob(parts[1]);
      var rawLength = raw.length;
      var uInt8Array = new Uint8Array(rawLength);

      for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
      }

      return new Blob([uInt8Array], { type: contentType });
    }

    clearButton.addEventListener("click", function (event) {
      signaturePad.clear();
    });

    	
    undoButton.addEventListener("click", function (event) {
      var data = signaturePad.toData();

      if (data) {
        data.pop(); // remove the last dot or line
        signaturePad.fromData(data);
      }
    });
  }

});