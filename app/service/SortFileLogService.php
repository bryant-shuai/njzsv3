<?php

namespace service;

use model\order as OrderModel;
use app\service as Service;

class SortFileLogService extends Service {

  function getList(){
    $sort_files = \model\sortfile_log::finds(" where id > 0 ORDER BY create_at DESC limit 20");
    $sort_files = \indexBy($sort_files,'id');
    return $sort_files;
  }

}