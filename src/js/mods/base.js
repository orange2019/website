var Base = {
    init: function() {
			this.formSubmit();
      this.confirmSubmit();
    },
    // 表单提交
    formSubmit : function() {

        $('.form-ajax').bind('submit', function() {

            var btnSubmit = $(this).find('[type="submit"]');
            btnSubmit.prop('disabled', true);

            var action = $(this).attr('action');
            var data = $(this).serialize();
            // console.log(data);
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
    },
    // 确认操作
    confirmSubmit : function() {
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

};

module.exports = Base;
