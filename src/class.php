<?php

/**
  класс модуля
 */

namespace Nyos\mod;

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

                $now_calc = $now_sp = $now_d = null;

                if (!empty($v['sale_point'])) {
                    if ($v['sale_point'] == $sp) {
                        $now_sp = $sp;

                        if ($v['spec_sp'] == $now_sp) {

                            if (!empty($v['spec_d']))
                                $now_d = $v['spec_d'];

                            $now_calc = $v['spec_position_auto'];
                        } elseif ($v['position_sp'] == $now_sp) {

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
                    continue;
                }


                $v2 = $k2 = 'id смены';
                $v2 = $k2 = 'точка сейчас';
                $v2 = $k2 = 'должность сейчас';
                $v2 = $k2 = 'считаем часы в авто';

                if ($now_sp == $sp) {

                    if (self::$show === true)
                        echo '<div style="border: 2px solid green; padding: 10px;" >';
                    if (self::$show === true)
                        echo '<br/>считаем смену';
                    if (self::$show === true)
                        \f\pa([$now_sp, $now_d, $now_calc, $v], 2);

                    $return['hours_all'] += $v['hour_on_job'];

                    $return['hours'] += $v['hour_on_job'];

                    if (self::$show === true)
                        echo '<br/>' . $v['id'];

                    $r = self::calcDaySumma($v, $v['hour_on_job'], $sp);

                    if (2 == 1 or self::$show === true)
                        echo '<br/>rr1 ' . $v['id'] . ' ' . $r;

                    $return['summa_if5'] += $r;

                    $return['calc_checks'][] = $v['id'];

                    if (self::$show === true)
                        \f\pa($return['summa_if5'], 2, '', 'summa_if5');

                    if (self::$show === true)
                        echo '</div>';
                } else {
                    if (self::$show === true)
                        echo '<br/>пропускаем смену';
                }
            }
        }

        return $return;
    }

    /**
     * проверяем есть ли вся инфа чтобы выставить оценку ( нормы дня, время ожидания и обороты )
     * @param type $db
     * @param type $sp
     * @param type $date
     */
    public static function checkInfoForOcenka( $db, $sp, $date ) {
        
        $sql = 'SELECT mob.id 
            
            FROM 
                mod_sale_point_oborot mob 
                
            INNER JOIN mod_sale_point_parametr mp ON 
                mp.sale_point = mob.sale_point
                AND mp.date = mob.date
                AND mp.status = \'show\' 
                
            INNER JOIN mod_074_time_expectations_list mt ON 
                mt.sale_point = mob.sale_point
                AND mt.date = mob.date
                AND mt.status = \'show\' 
                
            WHERE 
                mob.sale_point = :sp 
                AND mob.date = :d 
                AND mob.status = \'show\' 
                
            LIMIT 1            
            ;';
        
// \f\pa($sql);
        $ff = $db->prepare($sql);
// \f\pa($var_in);
        
        $var_in = [
            ':sp' => (int) $sp,
            ':d' => date('Y-m-d',strtotime($date) )
        ];
        
        $ff->execute($var_in);

        return ( $ff->rowCount() == 1 ) ? true : false;
        
    }
    
    public static function calcDaySumma(array $v, $hours, int $sp) {

        $r = 0;

        if ($v['spec_sp'] == $sp && !empty($v['s_pay5'])) {
            $hpay = $v['s_pay5'];
        } elseif ($v['spec_sp'] == $sp && !empty($v['s_pay_base'])) {
            $hpay = $v['s_pay_base'];
        } elseif ($v['position_sp'] == $sp && !empty($v['pay_base'])) {
            $hpay = $v['pay_base'];
        } else {
            $hpay = $v['pay5'];
        }

        if (!empty($hpay))
            $r = $hpay * $hours;

        return $r;
    }

    /**
     * на какие даты нет оценок у точек продаж
     * на выходе массив = дата - точки
     * @param type $db
     */
    public static function whereBlankOcenka($db, $scan_day = 60) {

        \Nyos\mod\items::$sql_select_vars[] = 'id';
        $sps = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_sale_point, 'show', 'id_id');

        $return_sp_date = [];

        try {

            $s2 = $db->prepare('SELECT `sale_point`, `date` FROM `mod_' . \f\translit(\Nyos\mod\JobDesc::$mod_ocenki_days, 'uri2') . '` WHERE `status` = \'show\' AND `date` >= :date ;');
            $date = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 3600 * 24 * $scan_day);
            $s2->execute([':date' => $date]);

            while ($r = $s2->fetch()) {
                $return_sp_date[$r['sale_point']][$r['date']] = 1;
            }

            $return_date_sp = [];
            $ndate = '';

            for ($t = 1; $t <= ( $scan_day + 2 ); $t++) {

                $ndate = date('Y-m-d', $_SERVER['REQUEST_TIME'] - $t * 3600 * 24);

                foreach ($sps as $sp => $v) {

                    if (!isset($return_sp_date[$sp][$ndate])) {

                        $return_date_sp[$ndate][$sp] = 1;
                    }
                }
            }

            return $return_date_sp;
        } catch (\PDOException $ex) {
            \nyos\Msg::sendTelegramm(__FILE__ . ' #' . __LINE__ . ' ошибка ' . $ex->getMessage(), null, 2);
        } catch (\Exception $ex) {
            \nyos\Msg::sendTelegramm(__FILE__ . ' #' . __LINE__ . ' ошибка ' . $ex->getMessage(), null, 2);
        }

        return false;
    }

    /**
     * удаляем оценки дня по датам с указанием точки продаж
     * @param type $db
     * @param type $sp
     * @param type $datas
     */
    public static function deleteOcenka($db, $sp, $datas = '' ) {
        
        \Nyos\mod\items::deleteItemForDops($db, \Nyos\mod\JobDesc::$mod_ocenki_days, [ 'sale_point' => $sp, 'date' => $datas ] );
        
    }

}
