/*!
 * VERSION: 1.7.5
 * DATE: 2015-02-26
 * UPDATES AND DOCS AT: http://greensock.com
 *
 * @license Copyright (c) 2008-2015, GreenSock. All rights reserved.
 * This work is subject to the terms at http://greensock.com/standard-license or for
 * Club GreenSock members, the software agreement that was issued with your membership.
 *
 * @author: Jack Doyle, jack@greensock.com
 **/
var _gsScope="undefined"!=typeof module&&module.exports&&"undefined"!=typeof global?global:this||window;(_gsScope._gsQueue||(_gsScope._gsQueue=[])).push(function(){"use strict";var t=document.documentElement,e=window,i=function(i,r){var s="x"===r?"Width":"Height",n="scroll"+s,a="client"+s,o=document.body;return i===e||i===t||i===o?Math.max(t[n],o[n])-(e["inner"+s]||t[a]||o[a]):i[n]-i["offset"+s]},r=_gsScope._gsDefine.plugin({propName:"scrollTo",API:2,version:"1.7.5",init:function(t,r,s){return this._wdw=t===e,this._target=t,this._tween=s,"object"!=typeof r&&(r={y:r}),this.vars=r,this._autoKill=r.autoKill!==!1,this.x=this.xPrev=this.getX(),this.y=this.yPrev=this.getY(),null!=r.x?(this._addTween(this,"x",this.x,"max"===r.x?i(t,"x"):r.x,"scrollTo_x",!0),this._overwriteProps.push("scrollTo_x")):this.skipX=!0,null!=r.y?(this._addTween(this,"y",this.y,"max"===r.y?i(t,"y"):r.y,"scrollTo_y",!0),this._overwriteProps.push("scrollTo_y")):this.skipY=!0,!0},set:function(t){this._super.setRatio.call(this,t);var r=this._wdw||!this.skipX?this.getX():this.xPrev,s=this._wdw||!this.skipY?this.getY():this.yPrev,n=s-this.yPrev,a=r-this.xPrev;this._autoKill&&(!this.skipX&&(a>7||-7>a)&&i(this._target,"x")>r&&(this.skipX=!0),!this.skipY&&(n>7||-7>n)&&i(this._target,"y")>s&&(this.skipY=!0),this.skipX&&this.skipY&&(this._tween.kill(),this.vars.onAutoKill&&this.vars.onAutoKill.apply(this.vars.onAutoKillScope||this._tween,this.vars.onAutoKillParams||[]))),this._wdw?e.scrollTo(this.skipX?r:this.x,this.skipY?s:this.y):(this.skipY||(this._target.scrollTop=this.y),this.skipX||(this._target.scrollLeft=this.x)),this.xPrev=this.x,this.yPrev=this.y}}),s=r.prototype;r.max=i,s.getX=function(){return this._wdw?null!=e.pageXOffset?e.pageXOffset:null!=t.scrollLeft?t.scrollLeft:document.body.scrollLeft:this._target.scrollLeft},s.getY=function(){return this._wdw?null!=e.pageYOffset?e.pageYOffset:null!=t.scrollTop?t.scrollTop:document.body.scrollTop:this._target.scrollTop},s._kill=function(t){return t.scrollTo_x&&(this.skipX=!0),t.scrollTo_y&&(this.skipY=!0),this._super._kill.call(this,t)}}),_gsScope._gsDefine&&_gsScope._gsQueue.pop()();
 
var controller = new ScrollMagic.Controller();

function fadein(x,y){
    var anim_elem = x;
    var trig_elem = y;
    var fadein1 = TweenMax.staggerFromTo(anim_elem, 1, {opacity:0}, {opacity:1}, 0.2);
    new ScrollMagic.Scene({triggerElement: trig_elem,reverse: false,triggerHook: 'onEnter'}).setTween(fadein1).addTo(controller);
}

function fadeinup(x,y){
    var anim_elem = x;
    var trig_elem = y;
    var fadeinup1 = TweenMax.staggerFromTo(anim_elem, 0.4, {y:20}, {y:0,opacity:1,force3D:true}, 0.2);
    new ScrollMagic.Scene({triggerElement: trig_elem,reverse: false,triggerHook: 'onEnter', offset: 250}).setTween(fadeinup1).addTo(controller);
}

/*** parrlax bg effect ***/



/*  page animations */
var h_explore = $('.explore-bd');
var explore_item = $('.explore-bd .section-title>h2, .exp-item');
var h_sec3 = $('.section3');
var sec3_item = $('.section3 .section-title>h2, .office-spaces-that-impresses');
var h_sec4 = $('.section4');
var sec4_item = $('.section4 .section-title>h2, .bchoice-os');
var h_sec7 = $('.section7');
var sec7_item = $('.section7 .section-title>h2, .mngd-workspace, .mngd-workspace .col-xl-5, .mngd-workspace .col-xl-7');
var h_sec8 = $('.goodplaces');
var sec8_item = $('.good-place .good-places-ttl, .good-place .good-places-bx .gpbbx');
var h_urplc= $('.post-ur-place');
var plc_item = $('.pup-left .ttl-md, .pup-left .pup-text, .pup-left .pup-btn, .post-ur-place figure.img-center');
var h_sec10 = $('.section10');
var sec10_item = $('.section10 .section-title>h2, .section10 .home-blog');
var h_xbestarea = $('.explr-best-area');
var explorbest_item = $('.section11 .explr-best-area, .explr-best-area .col-lg-5, .explr-best-ctn .ttl, .explr-best-ctn .txt, .explr-best-ctn .contact-deatils');




if(h_explore.length !=0){
    // fadeinup(explore_item,'.explore-bd');
    TweenMax.staggerFromTo(explore_item, 0.5, {y:40}, {y:0,opacity:1,force3D:true}, 0.2);
    // var explor = TweenMax.staggerFromTo(explore_item, 0.6, {y:120}, {y:0,opacity:1,force3D:true}, 0.2);
    // new ScrollMagic.Scene({triggerElement: '.explore-bd',reverse: false,triggerHook: 'onEnter', offset: 500}).setTween(explor).addTo(controller);
}


if(h_sec3.length !=0){
    fadeinup(sec3_item,'.section3');
}
if(h_sec4.length !=0){
    fadeinup(sec4_item,'.section4');
}
if(h_sec7.length !=0){
    fadeinup(sec7_item,'.section7');
}
if(h_sec8.length !=0){
    fadeinup(sec8_item,'.goodplaces');
}
if(h_urplc.length !=0){
    fadeinup(plc_item,'.post-ur-place');
}
if(h_sec10.length !=0){
    fadeinup(sec10_item,'.section10');
}
if(h_xbestarea.length !=0){
    fadeinup(explorbest_item,'.explr-best-area');
}


/** work space animation **/

var ws_banner = $('.w-space-banner');
var ws_banner_details = $('.w-space-content h4, .w-space-content p');
var ws_icon_div = $('.icon-box-div .iconbox');
var ws_agreemnt_trigger = $('.agreement-sec .sec-row');
var ws_agreement_sec_item = $('.agreement-sec .sec-row .agreement-col h3,.agreement-sec .sec-row .agreement-col p, .agreement-sec .sec-row .agreement-col .find-out');
var ws_agreemnt_box = $('.agreement-sec .sec-row .agreement-icon-col .icon, .agreement-sec .sec-row .agreement-icon-col .line-box,.agreement-sec .sec-row .agreement-icon-col .white-box');
var ws_overview = $('.overview-row h4, .overview-row .overview-div .overview-item')

if(ws_banner.length !=0){
    TweenMax.staggerFromTo(ws_banner, 1, {opacity:0}, {opacity:1}, 0.2);
    TweenMax.staggerFromTo(ws_banner_details, 0.5, {y:40}, {y:0,opacity:1,force3D:true}, 0.2);
    fadeinup(ws_icon_div,'.icon-box-div');
    fadeinup(ws_agreement_sec_item,ws_agreemnt_trigger);
    
    var svg_icon_box = TweenMax.staggerFromTo(ws_agreemnt_box, 0.6, {scale:0.8}, {scale:1,opacity:1,force3D:true}, 0.1);
    new ScrollMagic.Scene({triggerElement: ws_agreemnt_trigger,reverse: false,triggerHook: 'onEnter', offset: 500}).setTween(svg_icon_box).addTo(controller);

    fadeinup(ws_overview,'.overview-row');
}





