var $$ = {}

$$.then = Thenjs
$$.data = {TREEDATA:{},cache:{}}
$$.PageData = {}
$$.data.loading = true
$$.TREE = {
  app: null
}

$$.jsonToString = function (json) {
  return JSON.stringify(json)
};
$$.stringToJSON = function (str) {
  var r = null
  try{
    r = JSON.parse(str)
  } catch (e) {
    console.error("str parse to obj fail! str: " + str);
  }
  return r
};
$$.js2str = $$.jsonToString
$$.str2js = $$.stringToJSON
$$.copy = function(v) {
  return  $$.str2js($$.js2str(v))
}
$$.formatMonth = function(m){
  if(m<10) return '0'+m
  else return m
}

// $$.then = Thenjs


$$.ajax = function(args_){
  args_.success = args_.success || args_.succ || function(data,cont){
    cont = cont || function(){}
    cont(null)
  };
  args_.fail = args_.fail || function(message) {alert(message);};

  if(args_.type) args_.method = args_.type;

  args_.cont_ = args_.cont_ || function(){}

  var _fail = args_.fail;
  var _success = args_.success;
  args_.success = function(data_){
    // alert(data_)
    var res = $$.str2js(data_);
    var code = res.code;
    var message = res.message;
    if(res.msg) message = res.msg;
    var result = res.data;

    if (code == -2) {
      // $.removeCookie('client');
      window.location.href="/user";
      return;
    } else if (code != 0) {
      _fail(message,code, args_.cont_)
      return;
    } 

    _success(result, args_.cont_)
  };

  $.ajax(args_)

};

$$.wait = function(args_){
  return function(cont){
    args_.cont_ = cont
    $$.ajax(args_)
  }
}

$$.getTime = function(){
  return (new Date()).getTime()
}


////////////// $$.event

;(function ($) {
  $.event = $.event || function () {
      var _observer = {};
      var subscribe = function (eventName_, obj_, args_) {
        args_ = args_ || {};

        var fireOthers_ = args_.fireOthers || false;
        if (fireOthers_) {
          _observer[eventName_] = [];
        }
        _observer[eventName_] = _observer[eventName_] || [];

        if (_observer[eventName_].length > 0) {
          $.event.remove(eventName_, obj_);
        }

        _observer[eventName_].push(obj_);
        return true;
      };

      var publish = function (eventName_, data_, data2_, from_) { 
        // data_ = data_ || {};
        from_ = from_ || null

        console.log('eventName_:'+eventName_)
        console.log('data_:')
        console.dir(data_)

        var handlers = _observer[eventName_] || [];
        
        var l = handlers.length;
        
        var _stop = false;
        if (from_) {
          from_['hd_' + eventName_](data_, data2_);
          return
        }

        for (var _i = l - 1; _i >= 0; _i--) {
          if (!_stop) {
            var obj = handlers[_i];
            if (typeof obj === 'string') {
              if (!obj) {
                console.log('........fire')
                $.event.fire(obj)
              }
            }

            if (obj && obj['hd_' + eventName_]) {
              var res = obj['hd_' + eventName_](data_, data2_);
              if(res && res.stop){
                _stop = true 
              }
              // _stop = obj['hd_' + eventName_](data_, data2_);
            }
          }
        }
      };

      var remove = function (eventName_, obj_) {
        var handlers = _observer[eventName_];
        var l = handlers.length;
        for (var i = l - 1; i >= 0; i--) {
          if (handlers[i] === obj_) {
            handlers.splice(i, 1);
            break;
          }
        }
      };

      var removeEvent = function (eventName_) {
        _observer[eventName_] = [];
      };

      var fire = function (obj_) {
        for (var eventName in _observer) {
          var handlers;
          if (_observer.hasOwnProperty(eventName)) {
            handlers = _observer[eventName];
          }
          var length = handlers.length;
          for (var i = length; i > -1; i--) {
            if (handlers[i] === obj_) {
              handlers.splice(i, 1);
            }
          }
        }
      };

      var fireAll = function () {
        _observer = {};
      };

      return {
        nodes: _observer,
        sub: subscribe,
        pub: publish,
        remove: remove,
        fire: fire,
        fireAll: fireAll
      };
    }();
})($$);


$$.atPageEnd = function(func){
  var nScrollHeight = 0; //滚动距离总长(注意不是滚动条的长度)
  var nScrollTop = 0;   //滚动到的当前位置
  var nContainterHeight = $(document.body).height();
  nScrollHeight = $(document.body).scrollHeight();
  nScrollTop = $(document.body).scrollTop();

  // alert((nScrollTop + nContainterHeight + 110 - nScrollHeight)+'px')
  if(nScrollTop + nContainterHeight + 110 >= nScrollHeight){
    func()
  }
}

$$.autoload = function(func){
  var nScrollHeight = 0; //滚动距离总长(注意不是滚动条的长度)
  var nScrollTop = 0;   //滚动到的当前位置
  var nContainterHeight = $(document.body).height();
  $(document).scroll(function(){
    nScrollHeight = $(document.body).scrollHeight();
    nScrollTop = $(document.body).scrollTop();
    // alert('nScrollTop:'+nScrollTop)
    // alert('nContainterHeight:'+nContainterHeight)
    // alert('nScrollHeight:'+nScrollHeight)
    // alert()
    if(nScrollTop + nContainterHeight + 100 >= nScrollHeight){
      func()
    }
  });
}

$$.is_weixn = function(){
  var ua = navigator.userAgent.toLowerCase();
  if(ua.match(/MicroMessenger/i)=="micromessenger") return true;
  return false;
}


$$.vue = function(opts){

  if( !opts._init ) opts._init = function(){}

  var opt = {
    el: opts.el,
    data: opts.data,
    attached: function(){
      if(opts.EVENT){
        for(var i in opts.EVENT){
          var event = opts.EVENT[i]
          $$.event.sub(event,this)
        }
      }
      if(opts._init_before) opts._init_before.apply(this)
      opts._init.apply(this)
      if(opts._setup) opts._setup.apply(this)
      if(opts._init_after) opts._init_after.apply(this)
    },
    detached: function(){
      $$.event.fire(this)
    },
    watch: opts.watch,
    methods: opts.methods,
  }

  return new Vue(opt)
}

// var v_nav = new Vue({
//   el: '#v_nav',
//   data: {
//     selected: 'me',
//   },
//   attached: function(){
//     $$.event.sub('CHANGE_TAB',this)
//   },
//   dettached: function(){
//     $$.event.sub('CHANGE_TAB',this)
//   },
//   watch: {
//     'selected': function(val){
//       alert(val)
//       $$.event.pub('CHANGE_TAB',val)
//     }
//   },
//   methods: {
//     hd_CHANGE_TAB: function(tab){
//       alert(tab+'2')
//     },
//     edit: function(id){
//       alert('edit:'+id)
//     },
//   }
// }) // parent

$$.__debug__ = false
$$.__debug__ = true

$$.log = function( str ){
  if($$.__debug__) console.log(str)
}
$$.dir = function( obj ){
  if($$.__debug__) console.dir(obj)
}


$$.comp = function(name,opt){
  var _attached = function(){}
  var _detached = function(){}
  if( opt.attached ) _attached = opt.attached
  if( opt.detached ) _detached = opt.detached
  if( !opt._init ) opt._init = function(){}
  if( !opt.watch ) opt.watch = {}

  if( !opt.props ) opt.props = []
  if( !opt.props_watch ) opt.props_watch = opt.props
  if( !opt.props_ext ) opt.props_ext = []
  if( !opt.props_watch_ext ) opt.props_watch_ext = []

  if( opt.el ) opt.template = $(''+opt.el).html()
  delete opt.el

  opt.attached = function(){
    var self = this
    _attached.apply(this)
    if(opt._init_before) opt._init_before.apply(this)
    opt._init.apply(this)
    if(opt._init_after) opt._init_after.apply(this)
    if(opt._setup) opt._setup.apply(this)

    if(opt.EVENT){
      for(var i in opt.EVENT){
        var event = opt.EVENT[i]
        $$.event.sub(event,this)
      }
    }

    var find = false
    // console.dir(opt.props_watch)

    for (var i in opt.props_watch) {
      var k = opt.props_watch[i]
      // $$.dir('k:'+k)

      ;(function(watchkey){

          // $$.log('watchkey:'+watchkey)

          self.$watch(watchkey,function(){
            // $$.dir('watch----------------------'+'_change_'+watchkey)
            if(opt['_change_'+watchkey]){
              opt['_change_'+watchkey].apply(self)
            }else if(opt._change){
              opt._change.apply(self) 
            }
          })          
      })(k)

    }


  }

  opt.detached = function(){
    $$.event.fire(this)
    _detached.apply(this)
  }

  // Vue.component(name,Vue.extend(opt))
  Vue.component(name,opt)
}

$$.part = function(name,tpl){
  Vue.partial(name, tpl)
}


$$.loadToDiv = function(id,url,forceload){
  // alert($('#'+id).attr('loaded'))
  if(!$('#'+id).attr('loaded') || $('#'+id).attr('loaded')=='0' || forceload){
    $.get(url,function(res){
      $('#'+id).attr('loaded',1)
      // alert(res)
      $('#'+id).show().html(res)
    }) 
  }else{
    $('#'+id).show()
  }
}


$$.vCopy = function(from,args,name){
  if(!name) name = ''

  from.props = from.props || []
  from.props_watch = from.props_watch || []

  from.props_ext = from.props_ext || []
  from.props_watch_ext = from.props_watch_ext || []

  var r = {}

  for(var i in from){
    if(i=='methods' || i=='watch'){
      for(var j in from[i]){
        var fun = from[i][j]
        if(!r[i]) r[i] = {}
        r[i][j] = fun
      }
    }else{
      r[i] = from[i] 
    }
  }

  for(var i in args){

    if(i=='methods' || i=='watch'){
      for(var j in args[i]){
        var fun = args[i][j]
        if(!r[i]) r[i] = {}
        r[i][j] = fun
      }
    }else if(i=='props_ext'){
      r.props_ext = r.props_ext || []
      for(var j in args[i]){
        var it = args[i][j]
        if(name=='v_order_move_to') $$.log('props_ext:'+it);
        if(-1===r.props_ext.indexOf(it)) r.props_ext.push(it);
      }
    }else if(i=='props_watch_ext'){
      r.props_watch_ext = r.props_watch_ext || []
      for(var j in args[i]){
        var it = args[i][j]
        if(name=='v_order_move_to') $$.log('props_watch_ext:'+it);
        if(-1===r.props_watch_ext.indexOf(it)) r.props_watch_ext.push(it);
      }
    }else{
      if(i.indexOf('method_')===0){
        // alert(i)
        $$.log(i.substring('method_'.length))
        r.methods[i.substring('method_'.length)] = args[i]
      }else{
        r[i] = args[i]
      }
    }
     
  }


  for(var i in r.props_ext){
    var it = r.props_ext[i]
    $$.dir(r.props)
    if(-1===r.props.indexOf(it)) r.props.push(it);
  }

  for(var i in r.props_watch_ext){
    var it = r.props_watch_ext[i]
    if(-1===r.props_watch.indexOf(it)) r.props_watch.push(it);
  }


  $$.dir('copy result:'+name)
  $$.dir(r)
  return r
}


Vue.filter('process', function (product,howmany0) {
  if(!howmany0) howmany0 = 0
  console.dir(product)
  if(!product.max_id) return '0';
  var process = parseInt(product.current_id) / parseInt(product.max_id) * 100;
  if(process>10){
    return parseInt(process)
  }else if(process>1){
    return process.toFixed(1)
  }
  return process.toFixed(2)
})



Vue.filter('status', function (status) {
  if(status == 0) {
    return "启用";
  } else {
    return "禁用";
  }
})

//提问管理过滤器
Vue.filter('state',function (status) {
  if (status == 0) {
    return "已回答";
  } else {
    return "未回答";
  }
})



























Vue.prototype.setState = function(data){
  for(var i in data){
    this.$set(i,$$.copy(data[i]))
  }
}






;/* Copyright (c) 2010-2016 Marcus Westin */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.store = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
"use strict";module.exports=function(){function e(){try{return o in n&&n[o]}catch(e){return!1}}var t,r={},n="undefined"!=typeof window?window:global,i=n.document,o="localStorage",a="script";if(r.disabled=!1,r.version="1.3.20",r.set=function(e,t){},r.get=function(e,t){},r.has=function(e){return void 0!==r.get(e)},r.remove=function(e){},r.clear=function(){},r.transact=function(e,t,n){null==n&&(n=t,t=null),null==t&&(t={});var i=r.get(e,t);n(i),r.set(e,i)},r.getAll=function(){},r.forEach=function(){},r.serialize=function(e){return JSON.stringify(e)},r.deserialize=function(e){if("string"==typeof e)try{return JSON.parse(e)}catch(t){return e||void 0}},e())t=n[o],r.set=function(e,n){return void 0===n?r.remove(e):(t.setItem(e,r.serialize(n)),n)},r.get=function(e,n){var i=r.deserialize(t.getItem(e));return void 0===i?n:i},r.remove=function(e){t.removeItem(e)},r.clear=function(){t.clear()},r.getAll=function(){var e={};return r.forEach(function(t,r){e[t]=r}),e},r.forEach=function(e){for(var n=0;n<t.length;n++){var i=t.key(n);e(i,r.get(i))}};else if(i&&i.documentElement.addBehavior){var c,u;try{u=new ActiveXObject("htmlfile"),u.open(),u.write("<"+a+">document.w=window</"+a+'><iframe src="/favicon.ico"></iframe>'),u.close(),c=u.w.frames[0].document,t=c.createElement("div")}catch(l){t=i.createElement("div"),c=i.body}var f=function(e){return function(){var n=Array.prototype.slice.call(arguments,0);n.unshift(t),c.appendChild(t),t.addBehavior("#default#userData"),t.load(o);var i=e.apply(r,n);return c.removeChild(t),i}},d=new RegExp("[!\"#$%&'()*+,/\\\\:;<=>?@[\\]^`{|}~]","g"),s=function(e){return e.replace(/^d/,"___$&").replace(d,"___")};r.set=f(function(e,t,n){return t=s(t),void 0===n?r.remove(t):(e.setAttribute(t,r.serialize(n)),e.save(o),n)}),r.get=f(function(e,t,n){t=s(t);var i=r.deserialize(e.getAttribute(t));return void 0===i?n:i}),r.remove=f(function(e,t){t=s(t),e.removeAttribute(t),e.save(o)}),r.clear=f(function(e){var t=e.XMLDocument.documentElement.attributes;e.load(o);for(var r=t.length-1;r>=0;r--)e.removeAttribute(t[r].name);e.save(o)}),r.getAll=function(e){var t={};return r.forEach(function(e,r){t[e]=r}),t},r.forEach=f(function(e,t){for(var n,i=e.XMLDocument.documentElement.attributes,o=0;n=i[o];++o)t(n.name,r.deserialize(e.getAttribute(n.name)))})}try{var v="__storejs__";r.set(v,v),r.get(v)!=v&&(r.disabled=!0),r.remove(v)}catch(l){r.disabled=!0}return r.enabled=!r.disabled,r}();
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1])(1)
});



// // var __pageName__ = 'home'
// $(document).ready(function(){
//   // var nScrollTop = 0;   //滚动到的当前位置
//   $(document).on('touchend',function(){
//     // var nScrollTop = ;
//     if($$.PageData.PageName){
//       var nScrollTop = $(document.body).scrollTop()
//       console.log('set:'+$$.PageData.PageName+'_scrolltop:'+nScrollTop)
//       store.set($$.PageData.PageName+'_scrolltop', nScrollTop)
//       // $$.pagefunc.SavePos($$.PageData.PageName,$(document.body).scrollTop())
//     }
//   });
// })

$$.scrollTop = function(pageName){
  var _lasttop = store.get(pageName)
  if(_lasttop) $(document.body).scrollTop(_lasttop)
}

//获得元素在页面 的绝对位置
$$.getElementTop = function(elementid){
  var element = document.getElementById(elementid);
  var actualTop = element.offsetTop;
  var current = element.offsetParent;
  while (current !== null){
    actualTop += current.offsetTop;
    current = current.offsetParent;
  }
  return actualTop;
}


$$.getScrollTop = function() {
  var scrollPos;
  if (window.pageYOffset) {
  scrollPos = window.pageYOffset; }
  else if (document.compatMode && document.compatMode != 'BackCompat')
  { scrollPos = document.documentElement.scrollTop; }
  else if (document.body) { scrollPos = document.body.scrollTop; }
  return scrollPos;
}

$$.bottomPop = function(createContentFunc,height){
  createContentFunc()
  $('#holder_bottom_popup').html($('#tpl_bottom_popup').html())
  // alert($('#holder_bottom_popup').html())
  $$.vue_bottom_popup = $$.vue({
    el: '#v_bottom_popup',
    replace: false,
    data: function () {
      return {
        show: true,
        height: height || 200,
      }
    },
    // EVENT: ['BOTTOM_POPUP'],
    watch: {
      'show': function(show){
        if(!show){
          this.$remove(function(){
            // alert('remove')
          })
        }
      },
    },
    methods: {
      // hd_BOTTOM_POPUP: function(height){
      //   if(!height) height = 200;
      //   this.show = true
      //   this.height = height
      // },
    }

  })
  // $$.event.pub('BOTTOM_POPUP',height)
} 


