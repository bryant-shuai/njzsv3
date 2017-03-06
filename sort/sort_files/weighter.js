
if (window) {
  window.$$ = $$
}
var lastMarkTime = 0;
$$.SCANNER_INPUT = '';
var patt = new RegExp('/[^\+\-0-9.]/', 'g');


var __weighter__ = '';


var getWeighterInput = function (key) {
  if ((key < 48 || key > 57) && (key != 190) && (key != 13) && (key != 187) && (key != 71) && (key != 75)) {
    return;
  }
  if (key == 187) {
    __weighter__ = ''
  }
  var num = String.fromCharCode((96 <= key && key <= 105) ? key - 48 : key);
  if (key == 190) num = '.';

  console.log('key:' + key + 'num:' + num + ' __weighter__:' + __weighter__);

  if (num == '+') {
    __weighter__ = '';
  } else if (num == '-') {
    __weighter__ = '-';
  } else {
    if (num != ' ' && ( parseInt(num) >= 0 || num == '.' )) {
      num = '' + num
      __weighter__ += num
    }
    if (num == 'g' || num == 'G') {
      //判断是否符合条件
      var __weighter__str = '' + __weighter__
      __weighter__ = '';

      var findpos = __weighter__str.indexOf('.')
      if (findpos > 0 && ( __weighter__str.length - findpos == 4 )) {
        $$.SCANNER_INPUT = __weighter__str
        // $$.event.pub('SCANNER_INPUT', $$.SCANNER_INPUT);
        // console.log('result:'+__weighter__str);
        return $$.SCANNER_INPUT
      }


    }
  }
  // console.log('....................'+__weighter__);

};

var __weighter__history_count = 0;
var __weighter__latest = '';
var getRealWeighterInput = function (e) {
  var __weighter__input = '' + getWeighterInput(e)
  if (__weighter__input === 'undefined') return

  if (__weighter__latest === __weighter__input) {
    __weighter__history_count += 1
  } else {
    __weighter__latest = __weighter__input
    __weighter__history_count = 1
  }
  console.log('__weighter__input:' + __weighter__input)
  console.log('__weighter__history_count:' + __weighter__history_count)

  if (__weighter__history_count >= 2) {
    // $$.event.pub('RECEIVE_WEIGHT', __weighter__input);
    // console.log('[event] RECEIVE_WEIGHT:', __weighter__input)
    $$.SCANNER_INPUT = __weighter__input
  }
}

$(document).keydown(function (e) {
  // alert(e.which)
  // console.dir(e)
  getRealWeighterInput(e.which);
});
var getCurrentTime = function () {
  return new Date().getTime()
};
window.$$ = $$;


try {
    var electronRequire = global.require;
    var electron = require('electron');
    var ipcRenderer = electron.ipcRenderer
    $$.ipc = ipcRenderer;
    $$.ipc.on('ENTER', function () {

        if ($$.SCANNER_INPUT.trim() !== '') {
            if (isNaN($$.SCANNER_INPUT)) {
                $$.SCANNER_INPUT = '';
                return;
            }
            $$.SCANNER_INPUT = Number($$.SCANNER_INPUT).toFixed(3);
            $$.event.pub('RECEIVE_WEIGHT', $$.SCANNER_INPUT);
            $$.SCANNER_INPUT = '';
        }
    });

    $$.ipc.on('SPACE', function () {
        $$.SCANNER_INPUT += ' ';
    })
} catch (e) {

}

