window.CLOSURE_NO_DEPS = true;(function() {var l=!0;function p(a,b,e){this.key=a;this.value=b;this.i=e}p.prototype.getKey=function(){return this.key};function s(a,b){var e,i;for(e in a)"function"==window.jwplayer.utils.typeOf(a.hasOwnProperty)?a.hasOwnProperty(e)&&(i=a[e],b(e,i)):(i=a[e],b(e,i))};function u(a){if(window.jwplayer._tracker)return window.jwplayer._tracker;window.jwplayer._tracker=this;this.b={};this.l="/";this.k="assets/jwplayer/ping.gif?";this.j=window.jwplayer.version;if(this.f=window.top===window.self?0:1){this.c=document.referrer;try{this.c=this.c||window.top.location.href,this.d=window.top.document.title}catch(b){}}this.c=this.c||window.location.href;this.d=this.d||document.title;this.trackerVersion=4636;this.h="complete"==document.readyState;this.e=
[];(this.debug=a)&&(this.eventObjs=[])}(function(a){var b=window.onload;window.onload="function"!=typeof window.onload?a:function(){b&&b();a()}})(function(){var a=window.jwplayer._tracker;if(a){for(;0<a.e.length;){var b=a.e.shift();v(a,b)}a.h=l}});function B(a,b,e,i){a.b[b]||(a.b[b]={});a.b[b][e]||(a.b[b][e]={});var d=C(a,b,e,i,!1);a.b[b][e][d]&&(d+="&dup=1");a.debug&&(i=C(a,b,e,i,l),i.url=d,i.fired=!1,a.eventObjs.push(i));a.h?v(a,d):a.e.push(d);a.b[b][e][d]=l}
function C(a,b,e,i,d){b=[new p("tv",a.trackerVersion,0),new p("n",Math.random().toFixed(16).substr(2,16),2),new p("aid",b,4),new p("e",e,5),new p("i",a.f,6),new p("pv",a.j,7),new p("pu",a.c,101),new p("pt",a.d,103)].concat(i).sort(function(a,b){return a.i>b.i?1:-1});if(d){a={};for(d=0;d<b.length;d++)a[b[d].getKey()]=b[d].value;return a}e=[];for(d=0;d<b.length;d++)e.push(b[d].getKey()+"="+encodeURIComponent(b[d].value));return["/",
a.k,e.join("&")].join("")}function v(a,b){(new Image).src=b;a.debug&&s(a.eventObjs,function(a,i){i.url==b&&(i.fired=l)})};function F(a){this.a=a}function G(a,b){a.a.onReady(b)}function H(a,b){a.a.onSeek(b)}function I(a,b){a.a.onComplete(b)}function J(a,b){a.a.onTime(b)}function K(a){return a.a.getPlaylistItem()}function L(a){return"html5"===a.a.getRenderingMode().toLowerCase()};function M(a,b,e){function i(){k={};w=!1;m=0}function d(a){return function(j){var b=k[a];if("meta"===a&&(j=j.metadata||j,b&&(j.width=j.width||b.width,j.height=j.height||b.height,j.duration=j.duration||b.duration),L(h)&&(100===j.duration||0===j.duration)&&0===j.width&&0===j.height))return;k[a]=j;"play"===a&&(b||(n=0),q=h.a.getPosition());if(k.play&&k.meta&&k.levels&&!w){var c=K(h),j=x(c),b=c.title||"";a:{var f=k.levels;if(f&&f.g&&f.g.length&&(f=f.g[0])&&"auto"===(""+f.label).toLowerCase()){c=5;break a}if((c=
c.sources)&&c.length)if(c=c[0].type,"aac"==c||"mp3"==c||"vorbis"==c){c=6;break a}c=k.meta||{};f=c.width|0;c=0===f?0<(c.height|0)?0:6:320>=f?1:640>=f?2:1280>=f?3:4}var f=D(),d;d=f|0;B(y,r,"s",[g("ph",z,1),g("pi",A,8),g("a",h.a.config.autostart?1:0,11),g("ed",o,20),g("vs",c,21),g("l",0>=d?0:15>d?1:300>=d?2:1200>=d?3:4,22),g("q",t(f),23),g("mu",j,100),g("t",b,102)]);w=l}}}function D(){var a=h.a.getDuration();if(0>=a){var b=k.meta;b&&(a=b.duration)}return a|0}function t(a){a|=0;return 0>=a?0:30>a?1:60>
a?4:180>a?8:300>a?16:32}function x(a){var b;if(b=a.sources){for(var a=[],d=b.length;d--;)b[d].file&&a.push(b[d].file);a.sort();b=a[0]}else b=a.file;var c;var f=b;if(f.match(/^[a-zA-Z]+:\/\//))c=f;else{c=c||document.location.href;b=c.substring(0,c.indexOf("://")+3);a=c.substring(b.length,c.indexOf("/",b.length+1));d=f.split("/");0!==f.indexOf("/")&&(c=c.split("?")[0],c=c.substring(b.length+a.length+1,c.lastIndexOf("/")),d=c.split("/").concat(d));c=[];for(f=0;f<d.length;f++)d[f]&&"."!=d[f]&&(".."==
d[f]?c.pop():c.push(d[f]));c=b+a+"/"+c.join("/")}return c}function N(){var a=h.a.config,b=h.a.getWidth(),d=/\d+%/.test(a.width||b);if(d&&a.aspectratio)return 4;if(a.height){var c=0;a.listbar&&"bottom"===a.listbar.position&&(c=a.listbar.size);if(40>=a.height-c)return 5}d&&e&&e.parentNode&&(b=e.parentNode.offsetWidth);b|=0;return 0===b?0:320>=b?1:640>=b?2:3}function E(a,b,d){var c=K(h),f=x(c),c=c.title||"",b=b+0.5|0;0<b&&B(y,r,"t",[g("ph",z,1),g("pi",A,8),g("ed",o,20),g("ti",b,21),g("pw",a|0,22),g("q",
d,23),g("mu",f,100),g("t",c,102)])}if(!1!==b.enabled){var g=function(a,b,d){return new p(a,b,d)},O=false||b.debug===l,h=new F(a),A=(""+(b.id||"")).substring(0,34),z=(a=window.jwplayer.defaults)&&a.ph?a.ph:0,o=0,r;window.jwplayer.key&&(a=new window.jwplayer.utils.key(window.jwplayer.key),b=a.edition(),"invalid"!=b&&(r=a.token()),"invalid"==b?o=4:"ads"==b?o=3:"premium"==b?o=2:"pro"==b&&(o=1));r||(r="_");var y=new u(O),k,w,m,n=0,q=null;G(h,function(){var a=K(h),b=x(a),a=a.title||"",d=N();B(y,
r,"e",[g("ph",z,1),g("pi",A,8),g("a",h.a.config.autostart?1:0,11),g("ed",o,20),g("ps",d,21),g("mu",b,100),g("t",a,102)])});h.a.onPlay(d("play"));h.a.onMeta(d("meta"));h.a.onQualityLevels(d("levels"));J(h,function(a){var b=a.position,e=a.duration;if(b){if(1<b){if(!k.meta){a={duration:e};if(L(h)){var c=L(h)?h.a.getContainer().getElementsByTagName("video")[0]:null;c&&(a.width=c.videoWidth,a.height=c.videoHeight)}d("meta")(a)}k.levels||d("levels")({})}a=t(e);e=b/(e/a)+1|0;0===m&&(m=e);null===q&&(q=b);
c=b-q;q=b;c=Math.min(Math.max(0,c),4);n+=c;e===m+1&&(b=128*m/a,m=0,e>a||(E(b,n,a),n=0))}});I(h,function(){var a=D();0>=a||(E(128,n,t(a)),n=0)});H(h,function(){q=h.a.getPosition();m=0});h.a.onIdle(i);h.a.onPlaylistItem(i);i()}}window.jwplayer&&window.jwplayer()&&window.jwplayer().registerPlugin("jwpsrv","6.0",M);})();
