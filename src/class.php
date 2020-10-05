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

    /**
     * на какие даты нет оценок у точек продаж
     * на выходе массив = дата - точки
     * @param type $db
     */
    public static function whereBlankOcenka($db, $scan_day = 60 ) {

        \Nyos\mod\items::$sql_select_vars[] = 'id';
        $sps = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_sale_point, 'show', 'id_id');
        // $sps = array_keys($sps0);
        // \f\pa($sps);

        $return_sp_date = [];

        try {

            $s2 = $db->prepare('SELECT `sale_point`, `date` FROM `mod_' . \f\translit(\Nyos\mod\JobDesc::$mod_ocenki_days, 'uri2') . '` WHERE `status` = \'show\' AND `date` >= :date ;');
            $date = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 3600 * 24 * $scan_day );
            // \f\pa($date);
            $s2->execute([':date' => $date]);

//            $r2 = $s2->fetchAll();
//            \f\pa($r2);

            while ($r = $s2->fetch()) {
                $return_sp_date[$r['sale_point']][$r['date']] = 1;
            }


            $return_date_sp = [];

            $ndate = '';

            for ($t = 1; $t <= ( $scan_day + 2 ) ; $t++) {

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
