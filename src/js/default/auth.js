var Auth = {
  init : function(){
    this.login();
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
  }
}

module.exports = Auth;
