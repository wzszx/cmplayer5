
(function(a){typeof a.CMP=="undefined"&&(a.CMP=function(){var b=/msie/.test(navigator.userAgent.toLowerCase()),c=function(a,b){if(b&&typeof b=="object")for(var c in b)a[c]=b[c];return a},d=function(a,d,e,f,g,h,i){i=c({width:d,height:e,id:a},i),h=c({allowfullscreen:"true",allowscriptaccess:"always"},h);var j,k,l,m=[];if(g){if(typeof g=="object"){for(l in g)m.push(l+"="+encodeURIComponent(g[l]));j=m.join("&")}else j=String(g);h.flashvars=j}k="<object ",k+=b?'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" ':'type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" data="'+f+'" ';for(l in i)k+=l+'="'+i[l]+'" ';k+=b?'><param name="movie" value="'+f+'" />':">";for(l in h)k+='<param name="'+l+'" value="'+h[l]+'" />';k+="</object>";return k},e=function(c){var d=document.getElementById(String(c));if(!d||d.nodeName.toLowerCase()!="object")d=b?a[c]:document[c];return d},f=function(a){if(a){for(var b in a)typeof a[b]=="function"&&(a[b]=null);a.parentNode.removeChild(a)}},g=function(a){if(a){var c=typeof a=="string"?e(a):a;if(c&&c.nodeName=="OBJECT"){b?(c.style.display="none",function(){c.readyState==4?f(c):setTimeout(arguments.callee,15)}()):c.parentNode.removeChild(c);return!0}}return!1};return{create:function(){return d.apply(this,arguments)},write:function(){var a=d.apply(this,arguments);document.write(a);return a},get:function(a){return e(a)},remove:function(a){return g(a)}}}())})(window);

(function(window) {
	if(typeof window.CMP == "undefined") {
		window.CMP = (function() {
			var msie = /msie/.test(navigator.userAgent.toLowerCase()),
			merge = function(_o, o) {
				if (o && typeof o == "object") {
					for (var k in o) {
						_o[k] = o[k];
					}
				}
				return _o;
			},
			make = function(id, width, height, url, flashvars, params, attrs) {
				attrs = merge({
					width : width,
					height : height,
					id : id
				}, attrs);
				params =  merge({
					allowfullscreen : "true",
					allowscriptaccess : "always"
				}, params);
				var vars,htm,k,arr = [];
				if (flashvars) {
					if (typeof flashvars == "object") {
						for (k in flashvars) {
							arr.push(k + "=" + encodeURIComponent(flashvars[k]));
						}
						vars = arr.join("&");
					} else {
						vars = String(flashvars);
					}
					params.flashvars = vars;
				}
				htm = '<object ';
				htm += msie ? 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" ' : 'type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" data="'+url+'" ';
				for (k in attrs) {
					htm += k + '="'+attrs[k]+'" ';
				}
				htm += msie ? '><param name="movie" value="'+url+'" />' : '>';
				for (k in params) {
					htm += '<param name="'+k+'" value="'+params[k]+'" />';
				}
				htm += '</object>';
				return htm;
			},
			getSWF = function(id) {
				var o = document.getElementById(String(id));
				if (!o || o.nodeName.toLowerCase() != "object") {
					o = msie ? window[id] : document[id];
				}
				return o;
			},
			removeInIE = function(obj) {
				if (obj) {
					for (var i in obj) {
						if (typeof obj[i] == "function") {
							obj[i] = null;
						}
					}
					obj.parentNode.removeChild(obj);
				}
			},
			removeSWF = function(id) {
				if (id) {
					var obj = (typeof id == "string") ? getSWF(id) : id;
					if (obj && obj.nodeName == "OBJECT") {
						if (msie) {
							obj.style.display = "none";
							(function() {
								if (obj.readyState == 4) {
									removeInIE(obj);
								} else {
									setTimeout(arguments.callee, 15);
								}
							})();
						} else {
							obj.parentNode.removeChild(obj);
						}
						return true;
					}
				}
				return false;
			};
			return {
				create : function() {
					return make.apply(this, arguments);
				},
				write : function() {
					var htm = make.apply(this, arguments);
					document.write(htm);
					return htm;
				},
				get : function(id) {
					return getSWF(id);
				},
				remove : function(id) {
					return removeSWF(id);
				}
			};
		})();
	}
})(window);
/*
*wmp配置
 * file: http://cenfunmusicplayer.googlecode.com/svn/trunk/developer/wmp/wmp.js
*/
(function(window) {
	var document = window.document;
	var msie = /msie/.test(navigator.userAgent.toLowerCase());
	// QVOD interface
	if (typeof window.QVOD === "undefined") {
		var QVOD = function(key, parent) {
			this.id = "QVOD_" + key;
			this.parent = parent;
		};
		QVOD.prototype = {
			ec : 0,
			ts : "stopped",
			t1 : 0,
			t2 : 0,
			bp : 0,
			dp : 0,
			qvod : null,
			QVO : null,
			ready : false,
			finish : false,
			init : function() {
				if (!this.qvod) {
					this.qvod = document.createElement("object");
					this.qvod.id = this.id;
					if (msie) {
						this.qvod.classid = "clsid:F3D0D36F-23F8-4682-A195-74C92B03D4AF";
					} else {
						this.qvod.type = "'application/qvod-plugin";
					}
					this.parent.appendChild(this.qvod);
				}
				this.ready = false;
				this.QVO = document.getElementById(this.id);
				if (this.QVO) {
					try {
						this.QVO.Showcontrol = 0;
						this.QVO.QvodAdUrl = "blank.htm";
						this.QVO.QvodTextAdUrl = "blank.htm";
						this.QVO.EnableTextAd = false;
						this.QVO.NextWebPage = "blank.htm";
						this.QVO.NumLoop = 0;
						this.ready = true;
					} catch (e) {
					}
				}
				// URL|Autoplay|Mute|Showcontrol|Full|Volume|Duration|Downrate|Canseek|Currentpos|NumLoop|
				// Version|PlayState|hWnd|MainInfo|ViewFrame|SoundTrack|DownPercent|BufferPercent|ParentWnd|
				// NextWebPage|QvodAdUrl|QvodTextAdUrl|EnableTextAd
				// var str = "";
				// for ( var k in this.QVO) {
				// str += k + "|";
				// }
				// var div = document.createElement("div");
				// div.innerHTML = str;
				// document.body.appendChild(div);
			},
			load : function(url) {
				if (!this.ready) {
					this.init();
				}
				if (this.ready) {
					this.finish = false;
					this.QVO.URL = url;
					this.play();
				} else {
					this.finish = true;
				}
			},
			play : function() {
				if (this.ready) {
					this.QVO.Play();
				}
			},
			pause : function() {
				if (this.ready) {
					this.QVO.Pause();
				}
			},
			stop : function() {
				if (this.ready) {
					this.QVO.Stop();
				}
			},
			seek : function(p) {
				if (this.ready) {
					this.QVO.Currentpos = p;
				}
			},
			volume : function(a) {
				if (this.ready) {
					this.QVO.Volume = a[0];
				}
			},
			status : function() {
				if (this.ready) {
					this.ec = 0;
					this.ts = this.getState(this.QVO.PlayState);
					this.t1 = this.QVO.Currentpos;
					this.t2 = this.QVO.Duration;
					this.bp = this.QVO.BufferPercent;
					this.dp = Math.round(this.QVO.get_CurTaskProcess() * 0.1);
				}
				if (this.t2 > 0 && this.dp == 100 && this.t1 > this.t2 - 1) {
					this.finish = true;
				}
				// if (this.bp > 0 && this.bp < 100) {
				// this.ts = "buffering";
				// }
				var arr = [this.ec, this.ts, this.t1, this.t2, this.bp, this.dp, this.finish];
				// document.title = "" + arr;
				return arr;
			},
			getState : function(n) {
				switch (n) {
				case 1:
					return "stopped";
					break;
				case 2:
					return "paused";
					break;
				case 3:
					return "playing";
					break;
				case 4:
					return "buffering";
					break;
				case 7:
					return "completed";
					break;
				default:
					return "connecting";
				}
			},
			error : function() {
				return null;
			},
			info : function() {
				if (this.ready) {
					var info = {
						filename : this.QVO.get_MainInfo()
					};
					return info;
				}
				return null;
			}
		};
		//
		var CMPEI = function() {
		};
		CMPEI.prototype = {
			key : null,
			cmpo : null,
			CMPO : null,
			wmpo : null,
			qvod : null,
			player : null,
			tx : 0,
			ty : 0,
			tw : 0,
			th : 0,
			display : null,
			playing : null,
			init : function(key, cmpo) {
				if (!this.cmpo) {
					// 添加QVOD支持类型
					this.CMPO = window[key];
					this.CMPO.qvod = new QVOD(key, this.CMPO.DIV);
					// CMP事件
					this.key = key;
					this.cmpo = cmpo;
					this.cmpo.addEventListener("model_start", "CMPEI.update");
					this.cmpo.addEventListener("model_state", "CMPEI.update");
					this.cmpo.addEventListener("resize", "CMPEI.update");
					this.cmpo.addEventListener("control_fullscreen", "CMPEI.fullscreen");
				}
			},
			update : function(data) {
				this.display = false;
				this.playing = false;
				var item = this.cmpo.item();
				if (item) {
					if (item.type == "wmp") {
						// 将WMP视频可见
						if (!this.wmpo) {
							this.wmpo = document.getElementById("WMP_" + this.key);
							if (this.wmpo) {
								this.wmpo.uiMode = "None";
								this.wmpo.fullScreen = false;
								this.wmpo.stretchToFit = true;
								this.wmpo.enableContextMenu = true;
								this.wmpo.style.top = "0px";
								this.wmpo.style.left = "0px";
								this.wmpo.style.position = "absolute";
								this.cmpo.parentNode.appendChild(this.wmpo);
							}
						}
						if (!this.qvod) {
							this.qvod = document.getElementById("QVOD_" + this.key);
							if (this.qvod) {
								this.qvod.style.top = "0px";
								this.qvod.style.left = "0px";
								this.qvod.style.position = "absolute";
								this.cmpo.parentNode.appendChild(this.qvod);
							}
						}
						// 根据地址前缀自动判断是否是QVOD
						var prefix = item.url.substr(0, 7);
						if (prefix.toLowerCase() == "qvod://") {
							this.CMPO.player = this.CMPO.qvod;
							this.player = this.qvod;
							if (this.wmpo) {
								this.wmpo.style.display = "none";
							}
						} else {
							this.CMPO.player = this.CMPO.wmp;
							this.player = this.wmpo;
							if (this.qvod) {
								this.qvod.style.display = "none";
							}
						}
						var state = this.cmpo.config("state");
						if (state == "playing") {
							this.playing = true;
							var is_show = this.cmpo.skin("media", "display");
							if (is_show) {
								this.display = true;
							}
						}
					}
				}
				if (this.display) {
					this.tx = 0;
					this.ty = 0;
					if (!this.cmpo.config("video_max")) {
						this.tx = parseInt(this.cmpo.skin("media", "x")) + parseInt(this.cmpo.skin("media.video", "x"));
						this.ty = parseInt(this.cmpo.skin("media", "y")) + parseInt(this.cmpo.skin("media.video", "y"));
					}
					this.tw = this.cmpo.config("video_width");
					this.th = this.cmpo.config("video_height");
					//
					this.player.width = this.tw;
					this.player.height = this.th;
					this.player.style.left = this.tx + "px";
					this.player.style.top = this.ty + "px";
					this.player.style.display = "block";
				} else {
					this.player.style.display = "none";
				}
			},
			fullscreen : function(data) {
				var item = this.cmpo.item();
				if (item.type == "wmp" && this.playing) {
					var fullscreen = this.cmpo.config("fullscreen");
					if (fullscreen) {
						this.cmpo.sendEvent("view_fullscreen");
						if (this.CMPO.player == this.CMPO.qvod) {
							this.player.Full = true;
						} else {
							this.player.fullScreen = true;
						}
					}
				}
			}
		};
		window.CMPEI = new CMPEI();
	}
})(window);


(function(window, undefined) {
	var deltaDispatcher = function(event) {
		event = event || window.event;
		var target = event.target || event.srcElement;
		if (target && typeof (target.cmp_version) == "function") {
			var maxPos = target.skin("list.tree", "maxVerticalScrollPosition");
			if (maxPos > 0) {
				target.focus();
				if (event.preventDefault) {
					event.preventDefault();
				}
				return false;
			}
		}
	};
	if (window.addEventListener) {
		window.addEventListener("DOMMouseScroll", deltaDispatcher, false);
	}
	window.onmousewheel = document.onmousewheel = deltaDispatcher;
})(window);
