<?php
namespace model;

class order extends \app\model
{
    public static $table = "order";


    // static $STATUS = [
    //   'NEW_ORDER' => 0,
    //   'PAIED' => 1,
    //   'CANCELED' => 2,
    // ];
    // //支付信息
    // static $PAYMENT = [
    //   'Alipay' => 1,
    //   'WeChat' => 2,
    //   'Bank' => 3,
    // ]; 
    // /**
    //  $products = [];
    //  $extra = [];
    // **/
    // // public static $test = 'test in order';

    // static function test(){
    //   echo 'static::'.static::$test.'<br />';
    //   echo 'self::'.self::$test.'<br />';
    //   // return static::$test;
    // }

    // function __parse() {
    //   $this->courses = \de($this->data['info']);
    //   $this->extra = \de($this->data['extra']);
    // }

    // function havePaied(){
    //   return $this->data['status'] == self::$STATUS['PAIED'];
    // }

    // function cancel() {
    //   if($this->havePaied()) \except(\CODE::ORDER_ALREADY_PAIED);

    //   $this->data['status'] = self::$STATUS['CANCELED'];
    //   $this->save();
    //   return $this;
    // }


    // function pay() {
    //   if($this->havePaied()){
    //     \except(\CODE::ORDER_ALREADY_PAIED);
    //   }

    //   $oMember = \model\member::loadById();

    //   $courses = \de($this->data['info']);
    //   foreach ($courses as $course) {
    //     $oOrderCourse = new \model\order_course;
    //     $oOrderCourse->data = [
    //       'member_id' => $oMember->data['id'],
    //       'course_id' => $course['course_id'],
    //       'create_at' => \datetime(),
    //       'order_id' => $this->data['id'],
    //     ];

    //     if( $oMember->isAdmin() ){
    //       $oOrderCourse->data['company_id'] = $oMember->data['company_id'];
    //     }

    //     $oOrderCourse->save();
    //   }

    //   $this->data['status'] = self::$STATUS['PAIED'];
    //   $this->save();
    //   return $this;
    // }

    // static function getByUser($userId=NULL){
    //   if(!$userId) $userId = $_SESSION['user']['id'];
    //   return self::finds("where member_id='".$userId."' ORDER BY id desc");
    // }

    // function getCourses(){
    //   $courses = \de($this->data['info']);
    //   $courseIds = [];
    //   foreach ($courses as $course) {
    //     $courseIds[] = $course['course_id'];
    //   }
    //   $courses = \model\course::getByIds($courseIds);
    //   \vd($courses,'$courses');
    //   return $courses;
    // }

}