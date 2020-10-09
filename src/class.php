<?php

/**
  класс модуля
 */

namespace Nyos\mod;

//if (!defined('IN_NYOS_PROJECT'))
//    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JOBDESC_DAYOCENKA {

    /**
     * показать чё каво 
     * @var bool
     */
    public static $show = false;

    
    /**
     * Перенёс из jobdesc 2007
     * @param type $db
     * @param array $jobmans
     * @param string $date
     */
    public static function calcHoursDaysForOcenka($db, string $date, string $sp, array $jobmans, array $actions) {
        // ($db, $return['date'], $return['sp'], array_keys($jobmans['data']['jobmans']), $actions);

        $return = [
            'hours' => 0,
            'hours_all' => 0,
            'summa_if5' => 0,
            'calc_checks' => [],
            'date' => $date,
            'sp' => $sp,
            'jms' => $jobmans,
            'act' => $actions
        ];


        
        
        
        $info7 = [
            'id' => 'id смены',
            'hour_on_job' => 'часов на работе',
            'position_sp' => 'номер тп у смены норм назначение',
            'position_d' => 'должность у смены норм назначение',
            'sale_point' => 'номер тп если смену добавили руками',
            'spec_sp' => 'номер тп если есть спец назначение',
            'spec_d' => 'спец. назначение должность',
            'spec_position_auto' => 'считаем в ФОТ если спец. назначение',
        ];





        foreach ($actions as $k => $v) {

            if (!empty($v['type']) && $v['type'] == 'check') {

                // \f\pa($k);
                // \f\pa($v, 2);
//                $inf2 = [];
//                echo '<table class="table table-condense" ><tbody>';
//                foreach ($info7 as $k2 => $v2) {
//                    $inf2[$k2] = ['name' => $v2, 'val' => $v[$k2]];
//                    echo '<tr><td>' . $k2 . '</td><td>' . $v2 . '</td><td>' . $v[$k2] . '</td></tr>';
//                }

                $now_calc = $now_sp = $now_d = null;

                // пропускаем если у смены указана точка не текущая (добавили руками)
//                if ($v['sale_point'] != $sp && empty($v['spec_sp'])) {
//                    
//                    continue;
//                }
                // если указана точка продаж у смены (добавили руками)
                // else
                if (!empty($v['sale_point'])) {
                    if ($v['sale_point'] == $sp) {
                        $now_sp = $sp;

                        if ($v['spec_sp'] == $now_sp) {
                            // $now_sp = $sp;

                            if (!empty($v['spec_d']))
                                $now_d = $v['spec_d'];

                            $now_calc = $v['spec_position_auto'];
                        } elseif ($v['position_sp'] == $now_sp) {
                            // $now_sp = $sp;

                            if (!empty($v['position_d']))
                                $now_d = $v['position_d'];

                            $now_calc = $v['position_calc_auto'];
                        }
                    }
                } elseif (!empty($v['spec_sp'])) {

                    if ($v['spec_sp'] == $sp) {

                        $now_sp = $sp;

                        if (!empty($v['spec_d']))
                            $now_d = $v['spec_d'];

                        $now_calc = $v['spec_position_auto'];
                    }
                } elseif (!empty($v['position_sp'])) {

                    if ($v['position_sp'] == $sp) {
                        $now_sp = $sp;

                        if (!empty($v['position_d']))
                            $now_d = $v['position_d'];

                        $now_calc = $v['position_calc_auto'];
                    }
                } else {
                    //  echo '<br/>пропускаем';
                    continue;
                }


                $v2 = $k2 = 'id смены';
                // echo '<tr><td>' . $k2 . '</td><td>' . $v2 . '</td><td>' . $v['id'] . '</td></tr>';
                $v2 = $k2 = 'точка сейчас';
                // echo '<tr><td>' . $k2 . '</td><td>' . $v2 . '</td><td>' . $now_sp . '</td></tr>';
                $v2 = $k2 = 'должность сейчас';
                // echo '<tr><td>' . $k2 . '</td><td>' . $v2 . '</td><td>' . $now_d . '</td></tr>';
                $v2 = $k2 = 'считаем часы в авто';
                // echo '<tr><td>' . $k2 . '</td><td>' . $v2 . '</td><td>' . $now_calc . '</td></tr>';
                // echo '</tbody></table>';
                //\f\pa($inf2,2,'','инфа о смене');


                if ($now_sp == $sp) {

                    if( self::$show === true )
                    echo '<div style="border: 2px solid green; padding: 10px;" >';
                    if( self::$show === true )
                    echo '<br/>считаем смену';
                    if( self::$show === true )
                    \f\pa([$now_sp, $now_d, $now_calc, $v], 2);

                    $return['hours_all'] += $v['hour_on_job'];
                    
                    // hour_on_job
                    if( !empty($now_calc) ){
                    $return['hours'] += $v['hour_on_job'];
                    
                    echo '<br/>'.$v['id'];
                    
                    if ($v['spec_sp'] == $sp && !empty($v['s_pay5'])) {
                        $r = $v['s_pay5'] * $v['hour_on_job'];
                    }elseif ($v['spec_sp'] == $sp && !empty($v['s_pay_base'])) {
                        $r = $v['s_pay_base'] * $v['hour_on_job'];
                    } else {
                        $r = $v['pay5'] * $v['hour_on_job'];
                    }
                    
                    }
                    
                    if( self::$show === true )
                    echo '<br/>rr1 ' . $v['id'] . ' ' . $r;
                    
                    $return['summa_if5'] += $r;

                    $return['calc_checks'][] = $v['id'];

                    if( self::$show === true )
                    \f\pa($return['summa_if5'],2,'','summa_if5');

                    if( self::$show === true )
                    echo '</div>';

                } else {
                    if( self::$show === true )
                    echo '<br/>пропускаем смену';
                }






////                continue;
//                // пропускаем если у смены указана точка не текущая (добавили руками)
//                if ($v['sale_point'] != $sp && empty($v['spec_sp'])) {
//                    
//                }
//                // если указана точка продаж у смены (добавили руками)
//                elseif ($v['sale_point'] == $sp) {
//
////                    // $return['type2'] = 1;
////                    // echo '<br/>считаем 1 назнач в смене';
////                    $return['hours'] += $v['hour_on_job'];
////
////                    if ($v['spec_sp'] == $sp && !empty($v['s_pay5'])) {
////                        $r = $v['s_pay5'] * $v['hour_on_job'];
////                    } else {
////                        $r = $v['pay5'] * $v['hour_on_job'];
////                    }
////                    // echo '<br/>rr1 ' . $v['id'] . ' ' . $r;
////                    $return['summa_if5'] += $r;
////                    $return['calc_checks'][] = $v['id'];
////
//////                    \f\pa($return, 2, '', 'r2 1 ' . $v['id']);
//                }
//                //
//                elseif ($v['spec_sp'] == $sp) {
//
////                    // $return['type2'] = 2;
////                    // echo '<br/>считаем 2 спец';
////                    if (!empty($return['spec_position_auto']))
////                        $return['hours'] += $v['hour_on_job'];
////
////                    $return['hours_all'] += $v['hour_on_job'];
////
////
////                    $r = $v['s_pay5'] * $v['hour_on_job'];
////                    $return['summa_if5'] += $v['s_pay5'] * $v['hour_on_job'];
////                    // echo '<br/>rr2 ' . $v['id'] . ' ' . $r;
////                    $return['calc_checks'][] = $v['id'];
////
//////                    \f\pa($return, 2, '', 'r2 2 ' . $v['id']);
//                }
//                //
//                elseif ($v['position_sp'] == $sp) {
//
////                    //$return['type2'] = 3;
////                    // echo '<br/>считаем 3 должность';
////
////                    if (!empty($return['position_calc_auto'])) {
////                        $return['hours'] += $v['hour_on_job'];
////                        $return['fot'] = 'da';
////                    } else {
////                        // $return['fot'] = false;
////                        $return['hours_all'] += $v['hour_on_job'];
////                    }
////
////                    $r = $v['pay5'] * $v['hour_on_job'];
////                    $return['summa_if5'] += $v['pay5'] * $v['hour_on_job'];
////                    // echo '<br/>rr3 ' . $v['id'] . ' ' . $r;
////                    $return['calc_checks'][] = $v['id'];
////
//////                    \f\pa($return, 2, '', 'r2 3 ' . $v['id']);
//                }
////                else{
////                    echo '<br/>не считаем';
////                }
//                // \f\pa($v,2,'','check');
                
            }
        }

        return $return;
    }

    /**
     * на какие даты нет оценок у точек продаж
     * на выходе массив = дата - точки
     * @param type $db
     */
    public static function whereBlankOcenka($db, $scan_day = 60) {

        \Nyos\mod\items::$sql_select_vars[] = 'id';
        $sps = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_sale_point, 'show', 'id_id');
        // $sps = array_keys($sps0);
        // \f\pa($sps);

        $return_sp_date = [];

        try {

            $s2 = $db->prepare('SELECT `sale_point`, `date` FROM `mod_' . \f\translit(\Nyos\mod\JobDesc::$mod_ocenki_days, 'uri2') . '` WHERE `status` = \'show\' AND `date` >= :date ;');
            $date = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 3600 * 24 * $scan_day);
            // \f\pa($date);
            $s2->execute([':date' => $date]);

//            $r2 = $s2->fetchAll();
//            \f\pa($r2);

            while ($r = $s2->fetch()) {
                $return_sp_date[$r['sale_point']][$r['date']] = 1;
            }


            $return_date_sp = [];

            $ndate = '';

            for ($t = 1; $t <= ( $scan_day + 2 ); $t++) {

                $ndate = date('Y-m-d', $_SERVER['REQUEST_TIME'] - $t * 3600 * 24);

                // echo '<br/>'.$ndate;

                foreach ($sps as $sp => $v) {

                    // echo '<br/>++ '.$sp;

                    if (!isset($return_sp_date[$sp][$ndate])) {
                        // echo ' 0 ';
                        $return_date_sp[$ndate][$sp] = 1;
                    }
                }
            }

            // \f\pa($return_sp_date);

            return $return_date_sp;
        } catch (\PDOException $ex) {
            // \f\pa($ex);
            \nyos\Msg::sendTelegramm(__FILE__ . ' #' . __LINE__ . ' ошибка ' . $ex->getMessage(), null, 2);
        } catch (\Exception $ex) {
            // \f\pa($ex);
            \nyos\Msg::sendTelegramm(__FILE__ . ' #' . __LINE__ . ' ошибка ' . $ex->getMessage(), null, 2);
        }

        return false;
    }

}
