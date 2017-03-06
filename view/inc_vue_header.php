<!DOCTYPE html>
<html class="root" lang="zh">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>那记猪手</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="/iview/iview.css">
    <link rel="stylesheet" href="/iview/layout.css"></head>
    
    <script type="text/javascript" src="/assets/libs/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="/assets/libs/jquery.cookie.js"></script> -->
    <script type="text/javascript" src="/assets/libs/vue.min.js"></script>
    <script type="text/javascript" src="/assets/libs/then.js"></script>
    <script type="text/javascript" src="/assets/libs/aaa_init.js"></script>
    <!-- <script type="text/javascript" src="/assets/libs/naji.js"></script> -->
    <script type="text/javascript" src="/iview/iview.min.js"></script>

    <style type="text/css">
      html {
        
        background: #EEEEEE;
      }
      body {
        overflow-x: hidden;
      }
      .ivu-col-split-right {
        margin-top:-3px;
      }
    </style>
  </head>
  <body>
    <div id="app">
      <div class="wrapper">
        <div class="wrapper-header">
          <div class="wrapper-header-logo">
<!-- 
            <a href="/adm_manager/" class="v-link-active">
              <img src="/iview/76ecb6e76d2c438065f90cd7f8fa7371.png"></a>
               -->
          </div>
        </div>
        <div class="wrapper-container" style="position:relative;">
          <div style="position:absolute;top:-100px;left:0px;width:100%;background:#ffffff;border-bottom-right-radius:8px;border-bottom-left-radius:8px;">
            <a href="/adm_manager/" class="v-link-active" style="padding-left:8px;">
              <img src="/iview/logo.png" height="80px;"></a>
            <?php if (!empty($_SESSION['user'])) {?>
            <div style="position:absolute;left:320px;bottom:5px;">
              当前登录人:<?=$_SESSION['user']['name']?>
              <a href="/admin/logout">退出</a>
            </div>
            <?php } ?>
          </div>

          <div class="ivu-row">
            <div class="wrapper-navigate ivu-col ivu-col-span-4">
              <div class="navigate">
                <?php
                  include \view('inc_side_bar');
                ?>
              </div>
            </div>
            <!--v-component-->
            <div class="ivu-col ivu-col-span-20">
              <div class="wrapper-content ivu-article">
                <article>