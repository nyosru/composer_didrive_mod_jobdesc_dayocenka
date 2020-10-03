<?php

/**
  класс модуля
 */

namespace Nyos\mod;

//if (!defined('IN_NYOS_PROJECT'))
//    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JOBDESC_DAYOCENKA {

    /**
     * Перенёс из jobdesc 2007
     * @param type $db
     * @param array $jobmans
     * @param string $date
     */
    public static function calcHoursDaysForOcenka($db, string $date, string $sp, array $jobmans, array $actions) {
        // ($db, $return['date'], $return['sp'], array_keys($jobmans['data']['jobmans']), $actions);

        $return = ['hours' => 0, 'summa_if5' => 0, 'calc_checks' => [], 'date' => $date, 'sp' => $sp, 'jms' => $jobmans, 'act' => $actions];

        foreach ($actions as $k => $v) {
            if (!empty($v['type']) && $v['type'] == 'check') {

                if ($v['sale_point'] == $sp) {

                    // echo '<br/>считаем 1 назнач в смене';
                    $return['hours'] += $v['hour_on_job'];

                    if ($v['spec_sp'] == $sp && !empty($v['s_pay5'])) {
                        $r = $v['s_pay5'] * $v['hour_on_job'];
                    } else {
                        $r = $v['pay5'] * $v['hour_on_job'];
                    }
                    // echo '<br/>rr1 ' . $v['id'] . ' ' . $r;
                    $return['summa_if5'] += $r;
                    $return['calc_checks'][] = $v['id'];
                } elseif ($v['spec_sp'] == $sp) {
                    // echo '<br/>считаем 2 спец';
                    $return['hours'] += $v['hour_on_job'];
                    $r = $v['s_pay5'] * $v['hour_on_job'];
                    $return['summa_if5'] += $v['s_pay5'] * $v['hour_on_job'];
                    // echo '<br/>rr2 ' . $v['id'] . ' ' . $r;
                    $return['calc_checks'][] = $v['id'];
                } elseif ($v['position_sp'] == $sp) {
                    // echo '<br/>считаем 3 должность';
                    $return['hours'] += $v['hour_on_job'];
                    $r = $v['pay5'] * $v['hour_on_job'];
                    $return['summa_if5'] += $v['pay5'] * $v['hour_on_job'];
                    // echo '<br/>rr3 ' . $v['id'] . ' ' . $r;
                    $return['calc_checks'][] = $v['id'];
                }
//                else{
//                    echo '<br/>не считаем';
//                }
                // \f\pa($v,2,'','check');
            }
        }

        return $return;
    }

}
