var Auth = {
  init : function(){
    this.login();
    this.wxBindSentSms();
    this.pcRegSentSms();
  },
  login(){
    // 更换验证码
    $('.o-login').find('#img-captcha').bind('click', function() {
      var src = $(this).attr('data-src');
      src += '?t=' + Math.random();
      $(this).attr('src',src);
    }).trigger('click');

    $('.o-login').find('a.change-captcha').click(function() {
      $('.o-login').find('#img-captcha').trigger('click');
    });
  },
  wxBindSentSms : function(){
    var phoneO = $('#wx-bind-phone');
    if (phoneO.length > 0) {
        this.sentSms(phoneO);
    }
  },
  pcRegSentSms : function(){
    var phoneO = $('#pc-reg-phone');
    if (phoneO.length > 0) {
        this.sentSms(phoneO);
    }
  },
  sentSms : function(phoneO){
    $('#sent-sms').click(function() {

      var that = $(this);
      var isSent = $(this).attr('data-sent');
      if (isSent == 0) {
        $(this).attr('data-sent' , 1);
        var html = $(this).html();
        var phone = phoneO.val();
        // console.log(phoneO);
        // console.log('手机号:' + phone);
        $.post('/auth/sentSms' , {phone : phone} , function(data){
          if (data.code == 1) {
            that.html('发送成功，<span class="num">60</span>s后重发');
            var num = 60;
            for (var i = 0; i < num ; i++) {

              setTimeout(function(){
                num--;
                // console.log(num);
                if (num == 0) {
                  that.html(html);
                  that.attr('data-sent' , 0);
                }else {
                  that.find('.num').html(num);
                }

              },1000 * i + 1000);
            }
          }else {
            alert(data.msg);
            that.attr('data-sent' , 0);
          }
        },'json');


      }
    });
  }
}

module.exports = Auth;
