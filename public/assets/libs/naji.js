
$$.data = $$.data || {}
$$.fun = $$.fun || {}

var __loadDataWithCacheKey_ = function(force){
  return function(f){

  }
}


$$.fun.LoadClients = function(force){
  return function(cont){
    if(force || !$$.data.__Clients__) {
      $$.data.__Clients__ = {}
      $$.ajax({
        url: '/client/ls',
        succ: function(data){
          $$.data.__Clients__ = data.ls
          cont(null)
        },
      })
    } 
  }
}

$$.fun.LoadProducts = function(force){
  return function(cont){

    if(force || !$$.data.__Products__) {
      $$.data.__Products__ = {}
      $$.ajax({
        url: '/product/ls',
        succ: function(data){
          $$.data.__Products__ = data.ls
          cont(null)
        },
      })
    }else{
      cont(null)
    } 
  }
}

$$.fun.LoadSpecailProducts = function(force){
  return function(cont){

    if(force || !$$.data.__Products__) {
      $$.data.__Products__ = {}
      $$.ajax({
        url: '/product/ls?data_type=special',
        succ: function(data){
          $$.data.__SpecailProducts__ = data.ls
          cont(null)
        },
      })
    }else{
      cont(null)
    } 
  }
}

$$.fun.LoadNormalProducts = function(force){
  return function(cont){

    if(force || !$$.data.__Products__) {
      $$.data.__Products__ = {}
      $$.ajax({
        url: '/product/ls?data_type=normal',
        succ: function(data){
          $$.data.__NormalProducts__ = data.ls
          cont(null)
        },
      })
    }else{
      cont(null)
    } 
  }
}


$$.fun.LoadBatches = function(force){
  return function(cont){
    if(force || !$$.data.__Batches__) {
      $$.data.__Batches__ = {}
      $$.ajax({
        url: '/aj_batch/ls',
        succ: function(data){
          $$.data.__Batches__ = data.ls
          cont(null)
        },
      })
    }else{
      cont(null)
    }  
  }
}


