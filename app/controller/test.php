<?php
namespace controller;

class test extends \app\controller
{

  function get_price() {
    $price = $this->di['PriceService']->get(1,61);
    \vd($price,'$price');

    $prices = $this->di['PriceService']->gets(1);
    \vd($prices,'$prices');
  }



  function test_excel() {
    $this->di['ExportService']->outputDiaoBoDan();
  }



  function get_list_1() {
    $data = '{\"289181\":\"9.706\",\"289182\":\"5.000\",\"289183\":\"5.000\",\"289184\":\"0.830\",\"289185\":\"3.156\",\"289186\":\"0.000\",\"289188\":\"2.152\",\"289192\":\"2.130\",\"289193\":\"1.938\",\"289202\":\"3.092\",\"289203\":\"1.454\",\"289205\":\"1.000\",\"289234\":\"18.074\",\"289238\":\"4.530\",\"289239\":\"4.000\",\"289242\":\"12.894\",\"289243\":\"12.274\",\"289246\":\"1.326\",\"289248\":\"1.334\",\"290356\":\"4.980\",\"290357\":\"51.688\",\"290358\":\"31.286\",\"290359\":\"19.982\",\"290360\":\"3.526\",\"290362\":\"3.308\",\"290370\":\"30.000\",\"290374\":\"1.672\",\"290385\":\"3.056\",\"290390\":\"30.000\",\"290395\":\"3.210\",\"290396\":\"2.564\",\"290397\":\"0.694\",\"290401\":\"1.000\",\"290406\":\"1.000\",\"290407\":\"1.000\",\"292308\":\"46.000\"}';
    $data = str_replace("\\",'',$data);
    $data = \de($data);
    print_r($data);

    $ids = array_keys($data);

    echo '<hr>';

    echo implode(',', $ids);

  }




  function get_list() {
    $data = '{\"289354\":\"9.188\",\"289355\":\"9.584\",\"289357\":\"2.260\",\"289367\":\"5.016\",\"289371\":\"0.924\",\"289383\":\"1.126\",\"289387\":\"0.518\",\"289390\":\"5.000\",\"289392\":\"3.178\",\"289397\":\"10.000\",\"289409\":\"20.000\",\"289415\":\"1.056\",\"289417\":\"0.544\",\"289421\":\"1.000\",\"289430\":\"0.726\",\"289431\":\"0.610\",\"289432\":\"1.828\",\"289433\":\"3.176\",\"289443\":\"0.652\",\"289464\":\"5.300\",\"289479\":\"2.108\",\"289490\":\"3.000\",\"289493\":\"0.560\",\"289500\":\"19.064\",\"289627\":\"20.918\",\"289628\":\"29.238\",\"289631\":\"0.938\",\"289633\":\"40.654\",\"289641\":\"1.164\",\"289642\":\"1.104\",\"289643\":\"1.862\",\"289644\":\"1.202\",\"289645\":\"1.098\",\"289650\":\"0.632\",\"289651\":\"0.860\",\"289653\":\"1.032\",\"289654\":\"30.000\",\"289657\":\"0.452\",\"289659\":\"0.654\",\"289661\":\"1.000\",\"289846\":\"40.144\",\"289983\":\"1.992\",\"289985\":\"1.708\",\"289986\":\"2.846\",\"289987\":\"1.020\",\"289988\":\"2.366\",\"289989\":\"3.438\",\"289990\":\"1.468\",\"289993\":\"1.770\",\"289994\":\"20.986\",\"289998\":\"19.324\",\"290000\":\"5.380\",\"290002\":\"4.992\",\"290003\":\"4.008\",\"290004\":\"1.138\",\"290012\":\"1.514\",\"290013\":\"1.046\",\"290015\":\"2.000\",\"290016\":\"2.000\",\"290017\":\"2.000\",\"290018\":\"3.062\",\"290019\":\"3.130\",\"290020\":\"1.126\",\"290021\":\"10.000\",\"290023\":\"5.000\",\"290025\":\"3.012\",\"290026\":\"3.434\",\"290028\":\"3.022\",\"290029\":\"1.928\",\"290032\":\"6.000\",\"290033\":\"2.102\",\"290034\":\"2.038\",\"290035\":\"3.000\",\"290036\":\"1.000\",\"290037\":\"1.886\",\"290048\":\"2.028\",\"290430\":\"4.414\",\"290432\":\"2.282\",\"290433\":\"3.450\",\"290434\":\"1.214\",\"290435\":\"2.266\",\"290436\":\"1.488\",\"290437\":\"1.146\",\"290438\":\"0.940\",\"290439\":\"1.000\",\"290440\":\"1.000\",\"290442\":\"4.000\",\"290443\":\"2.148\",\"290444\":\"20.000\",\"290445\":\"10.000\",\"290446\":\"3.136\",\"290447\":\"0.844\",\"290474\":\"2.972\"}';
    $data = str_replace("\\",'',$data);
    $data = \de($data);
    print_r($data);

    $ids = array_keys($data);

    echo '<hr>';

    echo implode(',', $ids);

  }
























}
