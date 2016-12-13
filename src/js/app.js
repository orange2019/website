"use strict";

var pjax = require('jquery-pjax');
var swal = require('sweetalert');

$(function() {
    addPjax();
    pageInit();
});


$(document).off('pjax:end');
$(document).on('pjax:end', function(e, xhr, options) {
    $('#pjax-container .container-content').addClass('we-animation-fade');
    setTimeout(function() {
        $('#pjax-container .container-content').removeClass('we-animation-fade');
    }, 1000);
    addPjax();
    pageInit();

});

// 添加pjax支持
function addPjax() {

    var a = $(document).find('a.pjax');

    a.unbind('click');
    a.bind('click', function(event) {
        var url = $(this).attr('href');
        var container = '#pjax-container';
        var fragment = '#pjax-content';

        $.pjax({
            url: url,
            container: container,
            fragment: fragment,
            timeout:0
        });

        return false;
    });

}

var pageInit = function() {

    formSubmit();
    confirmSubmit();

}

// 表单提交
var formSubmit = function() {

    $('.form-ajax').bind('submit', function() {

        var btnSubmit = $(this).find('[type="submit"]');
        btnSubmit.prop('disabled', true);

        var action = $(this).attr('action');
        var data = $(this).serialize();
        if (action) {
            $.ajax({
                    url: action,
                    type: 'POST',
                    dataType: 'json',
                    data: data
                })
                .done(function(response) {
                    console.log(response);
                    var code = response.code;
                    var msg = response.msg ? response.msg : '';
                    var data = response.data;
                    var url = data.url;
                    if (code == 1) {

                        swal({
                                title: 'SUCCESS',
                                text: msg,
                                type: 'success'
                            },
                            function() {
                                if (url) {
                                    location.href = url;
                                } else {
                                    location.reload();
                                }
                            });


                    } else {

                        // alert(msg);
                        swal({
                                title: 'ERROR',
                                text: msg,
                                type: 'error'
                            },
                            function() {
                                if (url) {
                                    location.href = url;
                                }
                            });

                    }
                    // console.log("success");
                })
                .fail(function() {
                    alert('网络错误，请稍后重试！');
                    // console.log("error");
                })
                .always(function() {
                    btnSubmit.prop('disabled', false);
                    // console.log("complete");
                });
        }

        return false;
    });
}

// 确认操作
var confirmSubmit = function() {
    $('.confirm-ajax').bind('click', function() {
        var title = $(this).attr('title') ? $(this).attr('title') : 'CONFIRM';
        var href = $(this).attr('href');
        swal({
            title: title,
            text: '确认进行此项操作？',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "是！",
            cancelButtonText: "否.",
            closeOnConfirm: false
        }, function() {
            // location.href = href;
            if (href) {
                $.ajax({
                        url: href,
                        type: 'GET',
                        dataType: 'json',
                        data: ''
                    })
                    .done(function(response) {
                        console.log(response);
                        var code = response.code;
                        var msg = response.msg ? response.msg : '';
                        var data = response.data;
                        var url = data.url;
                        if (code == 1) {

                            swal({
                                    title: 'SUCCESS',
                                    text: msg,
                                    type: 'success'
                                },
                                function() {
                                    if (url) {
                                        location.href = url;
                                    } else {
                                        location.reload();
                                    }
                                });


                        } else {

                            // alert(msg);
                            swal({
                                    title: 'ERROR',
                                    text: msg,
                                    type: 'error'
                                },
                                function() {
                                    if (url) {
                                        location.href = url;
                                    }
                                });

                        }
                        // console.log("success");
                    })
                    .fail(function() {
                        alert('网络错误，请稍后重试！');
                    })
                    .always(function() {
                        // console.log("complete");
                    });

            }
        });
        return false;
    });
}
