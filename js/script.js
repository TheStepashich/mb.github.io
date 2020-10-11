var filterBlur = 'blur(3px)';
var filterNoBlur = 'blur(0px)';


$('.profile_log').click(function() {
   $('.popup_fone').css('display','flex');
   $('body').toggleClass('lock');
   $('main').css('filter',filterBlur);
});
$('.popup_close').click(function() {
   $('.popup_fone').css('display','none');
   $('body').toggleClass('lock');
   $('main').css('filter',filterNoBlur);
});
$('.popup_fone>.popup_fone1').click(function () {
   $('.popup_fone').css('display','none');
   $('body').toggleClass('lock');
   $('main').css('filter',filterNoBlur);
});