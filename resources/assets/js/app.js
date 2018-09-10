require('./bootstrap');

$(function() {
    $('.nav-tabs > li.active').prepend('<img src="/images/ramp-left.png" class="ramp ramp-left" />');
    $('.nav-tabs > li.active').append('<img src="/images/ramp-right.png" class="ramp ramp-right" />');
});
