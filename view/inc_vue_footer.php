
                </article>


              </div>
            </div>
          </div>

    
          <div style="position:absolute;bottom:-60px;left:4px;">
            <div style="padding-top:10px;height:60px;">&copy; 那记猪手 2017</div>
          </div>

        </div>
      </div>


      <div class="catalogue">
        <div class="ivu-card ivu-card-dis-hover ivu-card-shadow">
          
        </div>
      </div>


      <div class="ivu-modal-wrap ivu-modal-hidden vertical-center-modal">
        
      </div>



    </div>

    <!--drawer黑背景-->
    <div id="Id_Right_Drawer_Mask"
         style="position:fixed;width:110%;height:100%;background:#000;top:0;right:0;z-index:100;opacity:0.2;display:none;overflow-y:scroll;text-align:center;"
         onclick="$$.event.pub('CLOSE_DRAWER')">
    </div>
    <!--drawer黑背景 end-->

    <style type="text/css">
      .drawer_content_right {
        position:fixed;width:700px;height:100%;text-align:left;
         background:#FFF;top:0;right:-1000px;z-index:100;
         opacity:1; padding:40px 20px 40px 30px;
         display:none;overflow-y:scroll;
         box-shadow: -2px 2px 17px -4px rgba(0,0,0,0.67);
         border-left:2px solid #ADADAD;
      }
      .drawer_content_center {
        position: fixed;
        top:100px;
        left:150px;
        
        width:700px;
        text-align:left;
        /*background:#FFF;*/
        z-index:100;
        opacity:1; 
        /*padding:20px 20px 40px 30px;*/
        display:none;
        border-left:0px solid #ADADAD;
      }
    </style>

    <!--drawer内容-->
    <div id="Id_Right_Drawer_Content" class="drawer_content_center">
        
    </div>
    <!--drawer内容 end-->
    

    <script type="text/javascript">
      var __scrollbar_width__ = 0

      window.hd_OPEN_DRAWER = function (args) {
        args = args || {}
        var width = args.width || 500
        var top = args.top || 100
        args.data = args.data || {}
        args.data.data_from_parent = true
        // $('#Id_Right_Drawer_Content').html('<div id="Id_Right_Drawer_Content__Vue"></div>')

        $('#Id_Right_Drawer_Mask').css({'display':'block','opacity':0,'overflow-y':'hidden'}).animate({'opacity':0.6},200)


        if(args.url){
          $.get(args.url,function(res){
            $('#Id_Right_Drawer_Content').html(res)
          })
        }

        if(!args.center){
          $('#Id_Right_Drawer_Content').attr('class','drawer_content_right').css('width', width).css('right', 0).show()
        }else{
          var left = 0
          var windowWidth = $(window).width()
          left = (windowWidth-width) /2 
          $('#Id_Right_Drawer_Content').attr('class','drawer_content_center').css({'width':width,'top':top}).css('left', left+'px').show()
        }

        // $('#Id_Right_Drawer_Content').css('width', width).show().animate({"right": '0'}, {duration: 120})

        if ($(document.body).prop('clientHeight') < $(document.body).prop('scrollHeight')) {
            $(document.body).css('padding-right', __scrollbar_width__ + "px")
        }
        $(document.body).css('overflow-y', "hidden")
      }

      window.hd_CLOSE_DRAWER = function (id) {
        $('#Id_Right_Drawer_Content').css({"right": -1500}).hide()
        // $('#Id_Right_Drawer_Content').hide()
        $('#Id_Right_Drawer_Mask').css({'display':'none','opacity':0}).animate({'opacity':0.6},200)

        $(document.body).css('overflow-y', "auto")
        $(document.body).css('overflow-x', "hidden")
        $(document.body).css('padding-right', "0px")

        if($$.drawer){
          $$.drawer.$remove(function(){
            // alert('removed')
          }) 
          $$.drawer = null
        }

        // console.dir($('#Id_Right_Drawer_Content').children())
        if($('#Id_Right_Drawer_Content') && $('#Id_Right_Drawer_Content').children()){
          $('#Id_Right_Drawer_Content').children().remove()
          // alert($('#Id_Right_Drawer_Content').html())
        }
        $('#Id_Right_Drawer_Content').html('')

      }

      $$.event.sub('OPEN_DRAWER', window)
      $$.event.sub('CLOSE_DRAWER', window)







      // $$.event.pub('OPEN_DRAWER')

      $(document).ready(function(){
        var getScrollBarWidth = function () {
            var inner = document.createElement('p');
            inner.style.width = "100%";
            inner.style.height = "200px";

            var outer = document.createElement('div');
            outer.style.position = "absolute";
            outer.style.top = "0px";
            outer.style.left = "0px";
            outer.style.visibility = "hidden";
            outer.style.width = "200px";
            outer.style.height = "150px";
            outer.style.overflow = "hidden";
            outer.appendChild(inner);

            document.body.appendChild(outer);
            var w1 = inner.offsetWidth;
            outer.style.overflow = 'scroll';
            var w2 = inner.offsetWidth;
            if (w1 == w2) w2 = outer.clientWidth;

            document.body.removeChild(outer);

            return (w1 - w2);
        };
        __scrollbar_width__ = getScrollBarWidth()
      })

    </script>

  </body>

</html>