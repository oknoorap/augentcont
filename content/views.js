function el(id) {if (document.getElementById) {return document.getElementById(id); } else if (document.all) {return window.document.all[id]; } else if (document.layers) {return window.document.layers[id]; } }

(function(e){"function"===typeof define&&define.amd?define(e):window.Blazy=e()})(function(){function e(b){if(!document.querySelectorAll){var f=document.createStyleSheet();document.querySelectorAll=function(b,a,e,c,g){g=document.all;a=[];b=b.replace(/\[for\b/gi,"[htmlFor").split(",");for(e=b.length;e--;){f.addRule(b[e],"k:v");for(c=g.length;c--;)g[c].currentStyle.k&&a.push(g[c]);f.removeRule(0)}return a}}k=!0;g=[];a=b||{};a.error=a.error||!1;a.offset=a.offset||100;a.success=a.success||!1;a.selector=a.selector||".b-lazy";a.separator=a.separator||"|";a.container=a.container?document.querySelectorAll(a.container):!1;a.errorClass=a.errorClass||"b-error";a.breakpoints=a.breakpoints||!1;a.successClass=a.successClass||"b-loaded";a.src=p=a.src||"data-src";r=1<window.devicePixelRatio;c=s(t,25);q=s(u,50);u();l(a.breakpoints,function(b){if(b.width>=window.screen.width)return p=b.src,!1});v()}function v(){y(a.selector);k&&(k=!1,a.container&&l(a.container,function(b){m(b,"scroll",c)}),m(window,"resize",q),m(window,"resize",c),m(window,"scroll",c));t()}function t(){for(var b=0;b<h;b++){var f=g[b],d=f.getBoundingClientRect(),c=w+a.offset;if(0<=d.left&&d.right<=x+a.offset&&(0<=d.top&&d.top<=c||d.bottom<=c&&d.bottom>=0-a.offset)||-1!==(" "+f.className+" ").indexOf(" "+a.successClass+" "))e.prototype.load(f),g.splice(b,1),h--,b--}0===h&&e.prototype.destroy()}function z(b){if(0<b.offsetWidth&&0<b.offsetHeight){var f=b.getAttribute(p)||b.getAttribute(a.src);if(f){var f=f.split(a.separator),d=f[r&&1<f.length?1:0],f=new Image;l(a.breakpoints,function(a){b.removeAttribute(a.src)});b.removeAttribute(a.src);f.onerror=function(){a.error&&a.error(b,"invalid");b.className=b.className+" "+a.errorClass};f.onload=function(){"img"===b.nodeName.toLowerCase()?b.src=d:b.style.backgroundImage='url("'+d+'")';b.className=b.className+" "+a.successClass;a.success&&a.success(b)};f.src=d}else a.error&&a.error(b,"missing"),b.className=b.className+" "+a.errorClass}}function y(b){b=document.querySelectorAll(b);for(var a=h=b.length;a--;g.unshift(b[a]));}function u(){w=window.innerHeight||document.documentElement.clientHeight;x=window.innerWidth||document.documentElement.clientWidth}function m(b,a,d){b.attachEvent?b.attachEvent&&b.attachEvent("on"+a,d):b.addEventListener(a,d,!1)}function n(b,a,d){b.detachEvent?b.detachEvent&&b.detachEvent("on"+a,d):b.removeEventListener(a,d,!1)}function l(b,a){if(b&&a)for(var d=b.length,c=0;c<d&&!1!==a(b[c],c);c++);}function s(a,c){var d=0;return function(){var e=+new Date;e-d<c||(d=e,a.apply(g,arguments))}}var p,a,x,w,g,h,r,k,c,q;e.prototype.revalidate=function(){v()};e.prototype.load=function(b){-1===(" "+b.className+" ").indexOf(" "+a.successClass+" ")&&z(b)};e.prototype.destroy=function(){a.container&&l(a.container,function(a){n(a,"scroll",c)});n(window,"scroll",c);n(window,"resize",c);n(window,"resize",q);h=0;g.length=0;k=!0};return e});

var blazy, imgEls = [], allImg = document.getElementsByTagName('img'), allImgSize = allImg.length;
for (var i=0;i<allImgSize;i++) {
	var img = allImg[i];
	if (img) {
		var src = img.getAttribute('data-src'), width = img.getAttribute('width') || img.width;
		if (src) {
			img.setAttribute('data-src', 'https://docs.google.com/viewer?url='+ encodeURIComponent(src) +'&a=bi&pagenumber=1&w='+ width);
		}
		imgEls.push(img);
	}
};

(function(funcName, baseObj) {
    // The public function name defaults to window.docReady
    // but you can pass in your own object and own function name and those will be used
    // if you want to put them in a different namespace
    funcName = funcName || "docReady";
    baseObj = baseObj || window;
    var readyList = [];
    var readyFired = false;
    var readyEventHandlersInstalled = false;
    
    // call this when the document is ready
    // this function protects itself against being called more than once
    function ready() {
        if (!readyFired) {
            // this must be set to true before we start calling callbacks
            readyFired = true;
            for (var i = 0; i < readyList.length; i++) {
                // if a callback here happens to add new ready handlers,
                // the docReady() function will see that it already fired
                // and will schedule the callback to run right after
                // this event loop finishes so all handlers will still execute
                // in order and no new ones will be added to the readyList
                // while we are processing the list
                readyList[i].fn.call(window, readyList[i].ctx);
            }
            // allow any closures held by these functions to free
            readyList = [];
        }
    }
    
    function readyStateChange() {
        if ( document.readyState === "complete" ) {
            ready();
        }
    }
    
    // This is the one public interface
    // docReady(fn, context);
    // the context argument is optional - if present, it will be passed
    // as an argument to the callback
    baseObj[funcName] = function(callback, context) {
        // if ready has already fired, then just schedule the callback
        // to fire asynchronously, but right away
        if (readyFired) {
            setTimeout(function() {callback(context);}, 1);
            return;
        } else {
            // add the function and context to the list
            readyList.push({fn: callback, ctx: context});
        }
        // if document already ready to go, schedule the ready function to run
        if (document.readyState === "complete") {
            setTimeout(ready, 1);
        } else if (!readyEventHandlersInstalled) {
            // otherwise if we don't have event handlers installed, install them
            if (document.addEventListener) {
                // first choice is DOMContentLoaded event
                document.addEventListener("DOMContentLoaded", ready, false);
                // backup is window load event
                window.addEventListener("load", ready, false);
            } else {
                // must be IE
                document.attachEvent("onreadystatechange", readyStateChange);
                window.attachEvent("onload", ready);
            }
            readyEventHandlersInstalled = true;
        }
    }
})("docReady", window);

docReady(function () {
	if (typeof document.querySelectorAll === 'undefined') {
		for(var j=0;j<imgEls.length;j++) {
			imgEls[j].onload = function () {
				imgEls[j].src = imgEls[j].getAttribute('data-src');
			};
		}
	} else {
		blazy = new Blazy({selector: 'img'});
	}
});

function insertNode(referenceNode, html) {
	var newNode = document.createElement('div');
	newNode.innerHTML = html;
	referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

var viewer = el('pdf-viewer');
if (viewer) {
	var documentSrc = viewer.getAttribute('data-src');

	var iframe = document.createElement('iframe');
	iframe.src = 'http://docs.google.com/viewer?url='+ encodeURIComponent(documentSrc) +'&embedded=true';
	iframe.setAttribute('width', '100%');
	iframe.setAttribute('height', '600');
	iframe.setAttribute('frameborder', '0');
	viewer.appendChild(iframe);

	insertNode(viewer, '<div id="pdf-download" style="display:none"><p>Please wait <span id="download-counter">15</span> seconds to download.</p> </div><div style="margin:5px 0;text-align:center"><a href="#" id="read-btn" class="read button" style="display:none" onclick="readPDF(this);return false;"><i class="fa fa-file-pdf-o"></i> Read Document</a></div>');

	var downloader = el('pdf-download'), downloadBtn = el('download-btn'), readerBtn = el('read-btn'), counter = el('download-counter'), dlCounter = 15, downloadProcess;
};

function downloadPDF (me) {
	downloader.style.display = 'block';
	readerBtn.style.display = 'inline-block';
	me.style.display = 'none';
	viewer.style.display = 'none';

	counter.innerHTML = dlCounter;
	downloadProcess = setInterval(function () {
		if(dlCounter === 0) {
			window.location = documentSrc;
			clearInterval(downloadProcess);
		}
		counter.innerHTML = dlCounter;
		dlCounter--;
	}, 1000);
};

function readPDF (me) {
	downloader.style.display = 'none';
	me.style.display = 'none';
	downloadBtn.style.display = 'inline-block';
	viewer.style.display = 'block';

	dlCounter = 15;
	clearInterval(downloadProcess);
}