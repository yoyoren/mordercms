<?php
$modules['01_dispatch']['01_dis_dispatch']					= 'shipping_dispatch.php';         
$modules['01_dispatch']['02_dis_address']					= 'area.php';


$modules['02_sorting']['03_sor_sorting']					= 'fenjian_stat2.php';
$modules['02_sorting']['04_sor_numberquery']				= 'order_check2.php';
$modules['02_sorting']['27_sor_orderquery']					= 'order_search.php';

$modules['03_stations']['05_sta_delivery']					='shipping_delivery.php';         
$modules['03_stations']['06_sta_settlement']				='station_check.php';
$modules['03_stations']['07_sta_schedule']					='delivery_plan.php';
$modules['03_stations']['08_sta_commission']				='shipping_commission.php';
$modules['03_stations']['09_sta_commit']					='shipping_commit.php'; 

$modules['04_management']['10_man_employees']				= 'employee.php';  
$modules['04_management']['11_man_site']					= 'station.php'; 
$modules['04_management']['12_man_route']					= 'route.php'; 
$modules['04_management']['13_man_commission']				= 'shipping_commission.php?act=retry'; 
$modules['04_management']['27_man_city']					= 'city.php'; 


$modules['05_statistics']['14_stat_todaycount']				= 'today.php';
$modules['05_statistics']['15_stat_historycount']			= 'history.php';
$modules['05_statistics']['16_stat_temcount']				= 'temp.php';
//$modules['05_statistics']['28_stat_gy']						= 'cake_stat_g.php';

$modules['06_financial']['17_fin_check']					= 'finan_check.php';
$modules['06_financial']['18_fin_xsetlement']				= 'finan_conlect.php';
$modules['06_financial']['19_fin_fsetlement']				= 'finan_conlect2.php';
$modules['06_financial']['20_fin_card']						= 'card_check.php';
$modules['06_financial']['21_fin_invoice']					= 'finan_inv.php';
$modules['06_financial']['31_fin_each_check']	            ='finan_each_check.php';

$modules['07_print']['22_pri_produce']						= 'pro_print.php';
$modules['07_print']['23_pri_delivery']						= 'order_print.php';
$modules['07_print']['45_pri_check1']                        = 'change_order.php?step=check&printed=1';
$modules['07_print']['46_pri_check2']                        = 'change_order.php?step=check&printed=2';

$modules['08_data']['29_data_export']						= 'data_export.php';
$modules['08_data']['30_data_sms']							= 'data_sms.php';

$modules['09_privilege']['24_priv_add']						= 'privilege.php?act=add';
$modules['09_privilege']['25_priv_list']						= 'privilege.php?act=list';
$modules['09_privilege']['26_priv_role']						= 'role.php';
?>
