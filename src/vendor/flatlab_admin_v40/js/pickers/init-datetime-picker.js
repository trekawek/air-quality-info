
//datetime picker start

$(function(){

    $(".form_datetime").datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left",
        templates: {
            leftArrow: '<i class="fa fa-angle-left"></i>',
            rightArrow: '<i class="fa fa-angle-right"></i>'
        }

    });


    $(".form_datetime-component").datetimepicker({
        format: "dd MM yyyy - hh:ii",
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left",
        templates: {
            leftArrow: '<i class="fa fa-angle-left"></i>',
            rightArrow: '<i class="fa fa-angle-right"></i>'
        }
    });

    $(".form_datetime-adv").datetimepicker({
        format: "dd MM yyyy - hh:ii",
        autoclose: true,
        todayBtn: true,
        startDate: "2013-02-14 10:00",
        minuteStep: 10,
        pickerPosition: "bottom-left",
        templates: {
            leftArrow: '<i class="fa fa-angle-left"></i>',
            rightArrow: '<i class="fa fa-angle-right"></i>'
        }

    });

    $(".form_datetime-meridian").datetimepicker({
        format: "dd MM yyyy - HH:ii P",
        // format: 'yyyy-mm-dd hh:ii',
        showMeridian: true,
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left",
        templates: {
            leftArrow: '<i class="fa fa-angle-left"></i>',
            rightArrow: '<i class="fa fa-angle-right"></i>'
        }
    });

//    time pickers

    $('.timepicker-default').timepicker();

    $('.timepicker-24').timepicker({
        autoclose: true,
        minuteStep: 1,
        showSeconds: true,
        showMeridian: false,
        pickerPosition: "top-left"
    });


});





