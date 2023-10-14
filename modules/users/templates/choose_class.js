//**** choose direction
function choose_direction(subject) {
    $.get('body.php?module='+module+'&page=choose_class', {subject: subject, child_id: obj_value('var_child_id'), user_id: obj_value('var_user_id')})
        .done(function (data) {
            $("#choose_class").html(data);
            $("#choose_class_l").html(data);
            //$("#custom-content div").html(data);

            $("#choose_direction input[type=radio][value='"+subject+"']").prop('checked', true);
        })
}
$("#choose_class, #choose_class_l").on("change", "#choose_direction input[type=radio]",function () {
    choose_direction(this.value);
})
$("#choose_direction input[type=radio][value='"+obj_value('var_subject')+"']").prop('checked', true);


//**** calculate price
function calculate_price() {
    var subjects = obj_value('subject');
    //if (obj_value('subject') == 1) {
        if ($("#grade").val() == '') {
            $("#payment_price").html('აირჩიეთ კლასი');
            return;
        }
        else if ($("#dayes").val() == '') {
            $("#payment_price").html('აირჩიეთ სასურველი დღე');
            return;
        }
        else if ($("#hours").val() == '') {
            $("#payment_price").html('აირჩიეთ სასურველი დრო');
            return;
        }
        else if ($("#_payment_period").val() == '') {
            $("#payment_price").html('აირჩიეთ გადახდის პერიოდი');
            //return;
        }
    //}

    $.get('clear_post.php?module='+module+'&page=choose_class&action=calculate_price',
        {
            period: obj_value('_payment_period'),
            child_id: obj_value('var_child_id'),
            subject: obj_value('subject'),
            grade: obj_value('grade'),
            dayes_id: obj_value('dayes'),
            hours_id: obj_value('hours'),
        })
        .done(function (data) {
            info = jQuery.parseJSON(data);

            $(".class_full").css('display', 'none');
            if (info.message == 'class_full') {
                for (i = 0; i < info.subjects.length; i++) {
                    $("#class_full_" + info.subjects[i]).css('display', 'block');
                }
            }
            else {
                $("#class_full").css('display', 'none');
                $("#payment_price").html(info.message)

                if(obj_value('_payment_period') == '9m'){
                    $("#special_price").show();
                }
                else{
                    $("#special_price").hide();
                }
            }

            if ($("#_payment_period").val() !== '') {
                select_monthes();
            }
        })

}

//**** select months
function select_monthes() {
    $.get('clear_post.php?module='+module+'&page=choose_class&action=monthes',
        {
            grade: obj_value('grade'),
            dayes_id: obj_value('dayes'),
            payment_period: obj_value('_payment_period')
        })
        .done(function (data) {
            info = jQuery.parseJSON(data);

            //** select monthes
            if ($("#payment_period").val() !== '') {
                $("#lessons_period").html(info.lesson_period);
            }
        })
}

//**** choose group
function choose_group(field_id, field_value, subject) {
    //$("."+field_id).children(".choose_button").removeClass('choose_button_active');
    if (field_value !== '') {
        $("." + subject + '_' + field_id + '_main').children("#" + field_value).addClass('choose_button_active');
        //**** show hours
        if (field_id == "dayes") {
            show_hours(subject);
        }
    }

//	calculate_price();
}

//**** show hours
function show_hours(subject) {
    loading_big_icon(subject + "_hours_list");
    $("#" + subject + "_hours_block").css("display", "block");
    $.get('clear_post.php?module='+module+'&page=choose_class&action=generate_hours',
        {
            grade: $("#grade").val(),
            dayes_id: $("#dayes").val(),
            subject: subject,
            edit_value: $("#hours").val()
        })
        .done(function (data) {
            info = jQuery.parseJSON(data);
            $("#" + info.subject + "_hours_list").html(info.source);

            // enable days
            $("." + subject + "_dayes_main>div").removeClass("choose_button_disabled");
            $("." + subject + "_dayes_main>div").each(function () {
                if ($.inArray(this.id, info.days) !== -1) {
                    $(this).removeClass("choose_button_disabled");
                    $(this).addClass("choose_button");
                }
                else {
                    $(this).addClass("choose_button_disabled");
                    $(this).removeClass("choose_button_active");
                    $(this).removeClass("choose_button");
                }
            })

        })

}

//**** choose button
$("#choose_class, #choose_class_l").on("click", ".choose_button", function () {
    $(this).parent(".registration_in_value").children(".choose_button").removeClass("choose_button_active");
    $(this).addClass("choose_button_active");

    //*** clear hour
    if($(this).data('type') == "dayes" && parseInt($("#dayes").val()) !== parseInt(this.id)){
        if($("#hours").data('type') !== "fixed"){
            $("#hours").val('');
        }
    }

    $("#" + $(this).data('type')).val(this.id);

    calculate_price();

    //**** show hours
    if ($(this).data('type') !== "hours" && $(this).data('type') !== "payment_period" && $("#" + $(this).data('subject') + "_grade").val() !== "") {
        show_hours($(this).data('subject'));
    }
})
//*****************
