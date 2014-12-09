/**
 * Created by Виталий on 09.12.2014.
 */

$( document ).ready(function() {
    $( ".coupon-item" ).mouseenter(function() {
        //console.log(this);
        $(this).find(".coupon-content").animate({height: "100%"}, 200);
        $(this).find(".coupon-description").animate({opacity: "1"}, 200);
    }).mouseleave(function() {
        //console.log(this);
        $(this).find(".coupon-content").animate({height: "60px"}, 200);
        $(this).find(".coupon-description").animate({opacity: "0"}, 200);
    });
});
