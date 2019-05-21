
//color picker start

$(function(){

    $('#cp1').colorpicker();

    $('#cp2, #cp3a, #cp3b').colorpicker();
    $('#cp4').colorpicker({"color": "#eac459"});

    $('#cp5').colorpicker({
        format: null
    });

    $('#cp5b').colorpicker({
        format: "rgba"
    });

    $('#cp6').colorpicker({
        horizontal: true
    });

});





