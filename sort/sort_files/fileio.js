var fs = require('fs');
$$.fs = fs;
var path = require('path');
var ipc = require('ipc');
$$.ipc = ipc;

var __fileio__ = function(file){
  var _file = file
  console.log('_file:'+_file)

  fs.openSync(_file,'a')

  var data = fs.readFileSync(_file);
  data = data.toString()
  console.log('文件内容是：', data);
  var _data = {}
  try{
    eval('_data = '+data+'')
  }catch(e){
    _data = {}
    //如果没有文件，创建一个？
  }


  var _save = function(cont){
    cont = cont || function(){}
    var content = JSON.stringify(_data)
    console.log('content')
    console.log(content)
    $$.fs.writeFile(_file, content, function (err) {
      if (err) throw err;
      console.log('..It\'s saved!');
      cont(null)
      console.log('..call back called!');
    });
  }


  return {
    get: function(key){
      if(!key) return _data
      return _data[key]
    },

    set: function(key,val){
      _data[key] = val
      return this
    },

    reset: function(data){
      _data = data
      return this
    },

    write: _save,
    save: _save,
  }

};

if(window){
  window.fileio = __fileio__
  console.log('window.fileio')
}

if($$){
  $$.fileio = __fileio__
  console.log('$$.fileio')
}



//遍历目录下文件
function query_upan_dir(dir,callback){
  var file_exists = $$.fs.existsSync(dir);
  if(file_exists == true){
    $$.fs.readdirSync(dir).forEach(function(file){
      if(file.substring(0,1)!='_'){
        var filename = null
        if(path.extname(file) == '.txt'){
          filename = path.join(dir, file);
        }

        if(filename){
          if (fs.statSync(filename).isDirectory()) {
            travel(filename,callback);
          } else {
            callback(filename);
          }
        }
      } 
    })
  }else{
    console.log(dir+'没有这个路径!');
  }

}



//遍历目录下文件
function query_writable_dir(dir, callback){
  var file_exists = $$.fs.existsSync(dir);
  if(file_exists == true){
    callback(dir)
  }else{

  }

}









// var configfile = $$.fileio('/usr/local/tiangou/app.json')
// console.log('---------file content:-----------')
// console.log(configfile.get())



// var dailydatajson = $$.fileio('/usr/local/tiangou/daily.json')
// console.log('---------dailydatajson content:-----------')
// console.log(dailydatajson.get('order_20160116'))

// // console.log(configfile.get('printer'))

// configfile
//   .set('shop_name','天狗ok'+(new Date()).getTime())
//   .set('app_key','36:001')
//   .set('secret','aaa')
//   .set('global_member','1')
//   .set('printer_host','192.168.1.101:9110')
//   .save()

// file.save()
