<?php

/**
  класс модуля
 */

namespace Nyos\mod;

//if (!defined('IN_NYOS_PROJECT'))
//    throw new \Exception('Сработала защита от розовых хакеров, обратитесь к администрратору');

class JobDesc {

    public static $ar_autobonus_jm_sp_date = [];

    /**
     * выбранная ТП для выбора всех точек работ и всякое такое
     * @var type 
     */
    public static $sp = null;
    public static $dir_img_server = false;

    /**
     * запускать удаление бонусов каждый день или нет
     * @var type 
     */
    public static $no_delete_autobonus_1day = false;

    /**
     * возвращать или нет доп инфу
     * / работет в /
     * calculateHoursOnJob
     * @var type 
     */
    public static $return_dop_info = false;

    /**
     * используем одну дату или нет
     * @var булев
     */
    public static $one_date = false;
    public static $cash = [
        'salaris_all' => []
    ];

    /**
     * список модулей
     * назначения людей на точки продаж
     */
    public static $mod_man_job_on_sp = 'jobman_send_on_sp';

    /**
     * список модулей
     * спец. назначения людей на ТП
     */
    public static $mod_spec_jobday = '050.job_in_sp';

    /**
     * список модулей
     * метки на день
     */
    public static $mod_metki = '072.metki';

    /**
     * список модулей
     * должности
     */
    public static $mod_dolgn = '061.dolgnost';

    /**
     * список модулей
     * Связь данных по времени ожидания с точками продаж
     */
    public static $mod_sp_link_timeo = '074.time_expectations_links_to_sp';

    /**
     * модуль - проведённые оплаты
     */
    public static $mod_buh_oplats = '075.buh_oplats';

    /**
     * список модулей
     * / работники
     */
    public static $mod_jobman = '070.jobman';

    /**
     * список модулей
     * зарплаты
     */
    public static $mod_salary = '071.set_oplata';

    /**
     * список модулей
     * точки продаж
     */
    public static $mod_sale_point = 'sale_point';

    /**
     * доступ модераторов к точкам продаж
     * точки продаж
     */
    public static $mod_sale_point_access_moder = 'sale_point_access_moder';

    /**
     * список модулей
     * нормы на день
     */
    public static $mod_norms_day = 'sale_point_parametr';

    /**
     * список модулей 
     * оценки работы дня
     */
    public static $mod_ocenki_days = 'sp_ocenki_job_day';

    /**
     * список модулей //  
     * чеки
     * @var строка
     */
    public static $mod_checks = '050.chekin_checkout';

    /**
     * список модулей //  
     * оборот ыточек по дням
     * @var строка
     */
    public static $mod_oborots = 'sale_point_oborot';

    /**
     * список модулей //  
     * время ожидания по умолчанию
     * @var строка
     */
    public static $mod_timeo_default = '074.time_expectations_default';

    /**
     * список модулей //  
     * время ожидания
     * @var строка
     */
    public static $mod_timeo = '074.time_expectations_list';

    /**
     * модуль бонусов (день точка сотрудник сумма )
     * @var строка
     */
    public static $mod_bonus = '072.plus';

    /**
     * модуль минусов (день точка сотрудник сумма )
     * @var строка
     */
    public static $mod_minus = '072.vzuscaniya';

    /**
     * модуль комментариев
     * @var строка
     */
    public static $mod_comments = '073.comments';

    /**
     * модуль пратежи сотруднику от бух плюсы минусы
     * @var строка
     */
    public static $mod_buh_pm = '003_money_buh_pm';

    /**
     * модуль выбор какая точка главная в период оплаты для сотрудника
     * @var строка
     */
    public static $mod_buh_head_sp = '005_money_buh_head_sp';

    /**
     * массив люди чеки ( при расчёте автобонусов )
     * @var type 
     */
    public static $ar_jm_date_checks = [];

    /**
     * кто где работает ( при расчёте автобонусов )
     * @var type 
     */
    public static $WhereJobMans = [];

    /**
     * какие метки есть
     * @var type 
     */
    public static $ar_metki_jm_date_sp_type = [];

    /**
     * зарплаты все
     * @var type 
     */
    public static $ar_salaris_sp_dolgn_date = [];

    /**
     * выключить кеширование
     * @var type 
     */
    public static $no_cash = false;

    /**
     * переменная для размеров зп
     */
    public static $salary_ar__sp_dolgn_date = [];

    /**
     * массив для бонусов
     */
    public static $ar__sp_user_date = [];

    /**
     * массив для платежей по должностям
     * @var type 
     */
    public static $ar_pays__sp_position_d = [];
    public static $ar_oborots_sp_date = [];

    /**
     * получаем обороты точки за месяц
     * 2007
     * @param type $db
     * @param int $sp
     * @param string $date
     * @return type
     */
    public static function getOborotSpMonth($db, int $sp, string $date) {

        $in_sql = [
            ':date_start' => date('Y-m-01', strtotime($date)),
            ':sp' => $sp,
                // ':position' => $position ,
        ];
        $in_sql[':date_finish'] = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month -1 day'));

        $sql = 'SELECT
            date,
            sale_point,
            oborot_hand,
            oborot_server,
            CASE '
                . ' WHEN oborot_hand IS NOT NULL THEN oborot_hand '
                . ' WHEN oborot_server IS NOT NULL THEN oborot_server '
                . ' ELSE 0 
            END as oborot 
            FROM mod_sale_point_oborot
            WHERE
            date >= :date_start
            AND date <= :date_finish
            AND sale_point = :sp
            AND status = \'show\'
            GROUP BY date
            ORDER BY date DESC
            ';

        // \f\pa($sql);
        $ff = $db->prepare($sql);
        // \f\pa($in_sql);
        $ff->execute($in_sql);
//        $return = $ff->fetchAll();
//        return $return;
        // $re__sp_position_date_d = [];

        while ($r = $ff->fetch()) {
            // $re__sp_position_date_d[$r['sale_point']][$r['dolgnost']][$r['date']][] = $r;
            self::$ar_oborots_sp_date[$r['sale_point']][$r['date']] = $r;
        }

        return \f\end3('ok');

        return $return;
    }

    /**
     * получаем обороты по точке продаж за день
     * 2007
     * новая \Nyos\mod\IikoOborot::getDayOborot($db, $sp, $date);
     * @param type $db
     * @param int $sp
     * @param string $date
     * @return type
     */
    public static function getOborotSp($db, int $sp, string $date) {

        if (empty(self::$ar_oborots_sp_date[$sp][$date]))
            self::getOborotSpMonth($db, $sp, $date);

        if (!empty(self::$ar_oborots_sp_date[$sp][$date]))
            return self::$ar_oborots_sp_date[$sp][$date];

        return false;
    }

    /**
     * получаем размер оплат по должностям
     * @param type $db
     * @param int $sp
     * @param array $position
     * @param string $date
     * @return type
     */
    // public static function getSizePayPositionMonth( $db, int $sp, int $position, string $date) {
    public static function getSizePayPositionMonth($db, $sp, $date) {

        // \f\pa([$sp, $position, $date]);

        $in_sql = [
            // ':date_start' => date('Y-m-01', strtotime($date)),
            ':sp' => $sp,
                // ':position' => $position ,
        ];
        $in_sql[':date_finish'] = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month -1 day'));

        $cash_var = 'ar_position_pays_' . $sp . '_' . $in_sql[':date_finish'];

        $rr = \f\Cash::getVar($cash_var);

        if (!empty($rr)) {
            self::$ar_pays__sp_position_d = $rr;
            return \f\end3('ok cash');
        }

        $sql = ' SELECT '
                . ' p.*'
//                    . ', ob.oborot_hand ob1h '
//                    . ', ob.oborot_server ob1s '
//                    . ', ob2.oborot_hand ob2h '
//                    . ', ob2.oborot_server ob2s '
                . ' FROM '
                . ' mod_071_set_oplata p '

//                .' LEFT JOIN mod_sale_point_oborot ob ON ob.sale_point = :sp AND ob.date = p.date AND ob.oborot_hand > 0 AND ob.oborot_hand >= p.pay_from_day_oborot_bolee '
//                .' LEFT JOIN mod_sale_point_oborot ob2 ON ob2.sale_point = :sp AND ob2.date = p.date AND ob2.oborot_hand IS NULL AND ob2.oborot_server > 0 AND ob2.oborot_server >= p.pay_from_day_oborot_bolee '
                . ' WHERE '
                // . ' dolgnost = :position '
//                . ' AND '
                . ' p.sale_point = :sp '
                . ' AND '
                . ' p.status = \'show\' '
                // . ' AND `date` BETWEEN :date_start AND :date_finish '
//                . ' AND `date` >= :date_start '
                . ' AND p.`date` <= :date_finish '

                //                    WHEN oborot_d.oborot_hand > 0 THEN oborot_d.oborot_hand
                //                    WHEN oborot_d.oborot_server > 0 THEN oborot_d.oborot_server
//                        . ' AND ( 
//                            ( ' 
//                                .' oborot_d.oborot_hand > 0 '
//                                .' AND '
//                                .' oborot_d.oborot_hand >= pay_from_day_oborot_bolee '
//                            .' ) OR ( '
//                                .' oborot_d.oborot_hand IS NULL '
//                                .' AND '
//                               .' oborot_d.oborot_server > 0 '
//                                .' AND '
//                                .' pay.pay_from_day_oborot_bolee <= oborot_d.oborot_server '
//                            .' ) '
//                            .' OR 
//                            pay_from_day_oborot_bolee IS NULL '
//                        .' ) '
                // . ' GROUP BY sale_point, dolgnost, date '
                . ' ORDER BY p.date DESC, p.pay_from_day_oborot_bolee DESC '
                . ' ; '

        ;
        // \f\pa($sql);
        $ff = $db->prepare($sql);
        // \f\pa($in_sql);
        $ff->execute($in_sql);
//        $return = $ff->fetchAll();
//        return $return;
        // $re__sp_position_date_d = [];

        while ($r = $ff->fetch()) {
            // $re__sp_position_date_d[$r['sale_point']][$r['dolgnost']][$r['date']][] = $r;
            self::$ar_pays__sp_position_d[$r['sale_point']][$r['dolgnost']][] = $r;
        }

        \f\Cash::setVar($cash_var, self::$ar_pays__sp_position_d, 60 * 60 * 6);

        return \f\end3('ok');
    }

    /**
     * какой размер зарплаты сейчас
     * @param type $db
     * @param type $sp
     * @param type $position
     * @param type $date
     * @return type
     */
    public static function whoisSizeNowPay($db, $sp, $position, $date) {

        // \f\pa([$sp, $position, $date]);

        if (empty(self::$ar_pays__sp_position_d[$sp][$position])) {
            self::getSizePayPositionMonth($db, $sp, $date);

            if (empty(self::$ar_pays__sp_position_d[$sp][$position])) {
                return \f\end3('ne', false);
            }
        }


        foreach (self::$ar_pays__sp_position_d[$sp][$position] as $k => $v) {
            if ($date >= $k) {

                if (!empty($v['pay_from_day_oborot_bolee'])) {
                    $oborot = self::getOborotSp($db, $sp, $date);
                    // \f\pa($oborot);
                    if (!empty($oborot['oborot']) && $oborot['oborot'] >= $v['pay_from_day_oborot_bolee']) {
                        return $v;
                    } else {
                        continue;
                    }
                }

                return $v;
            }
        }

        return \f\end3('ne', false);
    }

    public static function calcSizePay($check = [], $salary = []) {

        if (empty($check))
            return \f\end3('нет чека', false);

        if (empty($salary))
            return \f\end3('нет размера зп', false);

        if (empty($check['fin']))
            return \f\end3('нет конца смены, ещё в работе', false);

        $v['hours'] = $check['hour_on_job_hand'] ?? $check['hour_on_job'] ?? 0;

        if ($v['hours'] == 0)
            return \f\end3('нечего считать 0 часов работы', false);

        $ocenka = $check['ocenka'] ?? $check['ocenka_auto'] ?? null;

        if (empty($ocenka))
            return \f\end3('оценка не выставлена', false);

        if (!empty($v['hours'])) {

            if (isset($salary['ocenka-hour-' . $ocenka])) {
                $v['pay_hour'] = $salary['ocenka-hour-' . $ocenka];
            }

            $v['summa'] = $v['hours'] * $v['pay_hour'];

            return \f\end3('ок', true, $v);
        }

//        \f\pa($check);
//        \f\pa($salary);
//        die();

        return \f\end3('error no pay size', false, ['check' => $check, 'salary' => $salary]);
    }

    /**
     * считаем есть нет бонус за эту смену у сотрудника
     * 2007
     * @param type $db
     * @param type $data
     * @return type
     */
    public static function getItogiDayOnMonth($db, $sp, $date) {

        if (empty($date))
            return \f\end3('пустые данные', false);


        $sql_vars = [
            ':sp' => $sp
            , ':date_start' => date('Y-m-01', strtotime($date))
        ];

        $sql_vars[':date_finish'] = date('Y-m-d', strtotime($sql_vars[':date_start'] . ' +1 month -1 day'));

        $sql = 'SELECT '
                // . ' od.* '
                . ' od.id item_id '
                . ' , od.date '
                . ' , od.sale_point '
                . ' , \'ocenka\' `item_type` '
                . ' , od.ocenka_time '
                . ' , od.ocenka_oborot '
                . ' , od.ocenka_naruki '
                . ' , od.ocenka '
                . ' , od.hour_day '
                . ' , od.ocenka_naruki_ot_oborota '
                . ' , od.money_naruki_ot_oborota '
                . ' , \'\' vuruchka '
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' hot '
                . ' , \'\' delivery '
                . ' , \'\' oborot_hand '
                . ' , \'\' oborot_server_hand '
                . ' , \'\' oborot_server '
                . ' , \'\' oborot '
                . ' FROM `mod_sp_ocenki_job_day` od '
                . ' WHERE '
                . ' od.status = \'show\' '
                . ' AND od.sale_point = :sp '
                . ' AND od.date BETWEEN :date_start AND :date_finish '
                . ' UNION ALL '
                . ' SELECT '
                // . ' od.* '
                . ' p.id item_id '
                . ' , p.date '
                . ' , p.sale_point '
                . ' , \'param\' `item_type` '
                . ' , \'\' ocenka_time '
                . ' , \'\' ocenka_oborot '
                . ' , \'\' ocenka_naruki '
                . ' , \'\' ocenka '
                . ' , \'\' hour_day '
                . ' , \'\' ocenka_naruki_ot_oborota '
                . ' , \'\' money_naruki_ot_oborota '
                . ' , p.vuruchka '
                . ' , p.time_wait_norm_cold '
                . ' , p.time_wait_norm_hot '
                . ' , p.time_wait_norm_delivery '
                . ' , p.procent_oplata_truda_on_oborota '
                . ' , p.kolvo_hour_in1smena '
                . ' , p.vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' hot '
                . ' , \'\' delivery '
                . ' , \'\' oborot_hand '
                . ' , \'\' oborot_server_hand '
                . ' , \'\' oborot_server '
                . ' , \'\' oborot '
                . ' FROM `mod_sale_point_parametr` p '
                . ' WHERE '
                . ' p.status = \'show\' '
                . ' AND p.sale_point = :sp '
                . ' AND p.date BETWEEN :date_start AND :date_finish '
                . ' UNION ALL '
                . ' SELECT '
                . ' t.id item_id '
                . ' , t.date '
                . ' , t.sale_point '
                . ' , \'timeo\' `item_type` '
                . ' , \'\' ocenka_time '
                . ' , \'\' ocenka_oborot '
                . ' , \'\' ocenka_naruki '
                . ' , \'\' ocenka '
                . ' , \'\' hour_day '
                . ' , \'\' ocenka_naruki_ot_oborota '
                . ' , \'\' money_naruki_ot_oborota '
                . ' , \'\' vuruchka '
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , t.cold '
                . ' , t.hot '
                . ' , t.delivery '
                . ' , \'\' oborot_hand '
                . ' , \'\' oborot_server_hand '
                . ' , \'\' oborot_server '
                . ' , \'\' oborot '
                . 'FROM mod_074_time_expectations_list t '
                . ' WHERE '
                . ' t.status = \'show\' '
                . ' AND t.sale_point = :sp '
                . ' AND t.date BETWEEN :date_start AND :date_finish '
                . ' UNION ALL '
                . ' SELECT '
                . ' oo.id item_id '
                . ' , oo.date '
                . ' , oo.sale_point '
                . ' , \'oborot\' `item_type` '
                . ' , \'\' ocenka_time '
                . ' , \'\' ocenka_oborot '
                . ' , \'\' ocenka_naruki '
                . ' , \'\' ocenka '
                . ' , \'\' hour_day '
                . ' , \'\' ocenka_naruki_ot_oborota '
                . ' , \'\' money_naruki_ot_oborota '
                . ' , \'\' vuruchka '
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' hot '
                . ' , \'\' delivery '
                . ' , oo.oborot_hand '
                . ' , oo.oborot_server_hand '
                . ' , oo.oborot_server '
                . ' , CASE
                        WHEN oo.oborot_hand > 0 THEN oo.oborot_hand
                        WHEN oo.oborot_server_hand > 0 THEN oo.oborot_server_hand
                        WHEN oo.oborot_server > 0 THEN oo.oborot_server
                    END oborot '
                . ' FROM mod_sale_point_oborot oo '
                . ' WHERE '
                . ' oo.status = \'show\' '
                . ' AND oo.sale_point = :sp '
                . ' AND oo.date BETWEEN :date_start AND :date_finish '

        ;

        $ff = $db->prepare($sql);
        $ff->execute($sql_vars);

        //return $ff->fetchAll();

        $return__date_type_n_ar = [];

        while ($r = $ff->fetch()) {
            $return__date_type_n_ar[$r['date']][$r['item_type']][] = $r;
        }

        return $return__date_type_n_ar;
    }

    /**
     * считаем есть нет бонус за эту смену у сотрудника
     * @param type $db
     * @param type $data
     * @return type
     */
    public static function calcAutoBonus($db, $sp, $data = []) {

        // \f\pa($data);

        if (empty($data))
            return \f\end3('пустые данные', false);

        // $e = [$data['date'], $data['jobman']];


        if (isset(self::$ar_autobonus_jm_sp_date[$data['jobman']][$sp][$data['date']]))
            return \f\end3('повтор', false, ['text' => 'отмена так как уже выдали бонус сегодня на ТП']);

        if (empty(self::$ar_metki_jm_date_sp_type)) {
            // echo '<br/>123123';
            self::$ar_metki_jm_date_sp_type = self::getMetki($db, $sp, date('Y-m-01', strtotime($data['date'])));
        }

        if (isset(self::$ar_metki_jm_date_sp_type[$data['jobman']][$data['date']][$sp]['no_autobonus']))
            return \f\end3('отменён', false, ['text' => 'отмена бонуса меткой no_autobonus']);

        $ocenka = $data['ocenka'] ?? $data['ocenka_auto'] ?? '';

        // \f\pa($data);
        // c
        if (!empty($data['spec_id']) && !empty($data['spec_sp']) && $data['spec_sp'] == $_GET['sp']) {

            // $data['pay_bonus_proc_from_oborot'] = $data['s_pay_bonus_proc_from_oborot']
            $kk = ['s_pay5', 's_pay3', 's_pay_if_kurit', 's_pay_base', 's_pay_premiya-5', 's_pay_premiya-3', 's_pay_oborot_sp_last_monht_bolee', 's_pay_bonus_proc_from_oborot', 's_pay_pay_from_day_oborot_bolee'];
            foreach ($kk as $v) {
                $data[substr($v, 2, 100)] = $data[$v];
            }
        }

        // % от оборота дня
        // бонус при спец размещении
        if (
                !isset(self::$ar_autobonus_jm_sp_date[$data['jobman']][$data['spec_sp']][$data['date']]) && !empty($data['spec_sp']) && $data['spec_sp'] > 0 && $data['s_pay_bonus_proc_from_oborot'] > 0 && ( empty($data['sale_point']) || $data['sale_point'] == $data['spec_sp'] )
        ) {

            if (!empty($data['spec_bonus'])) {

                self::$ar_autobonus_jm_sp_date[$data['jobman']][$data['spec_sp']][$data['date']] = 1;

                $bonus = $data['spec_bonus'];
                $bonus_text = $data['s_pay_bonus_proc_from_oborot'] . '% от ' . number_format($data['spec_oborot_day'], 0, '', '`');
            } else {
                $bonus_text = '#' . __LINE__;
            }
        }
        // % от оборота дня
        elseif (!empty($data['pay_bonus_proc_from_oborot'])) {

            if (empty($data['oborot_day'])) {
                $bonus_text = '#' . __LINE__ . ' оборот дня не определён ';
            }
            //
            elseif (!empty($data['oborot_day'])) {

                self::$ar_autobonus_jm_sp_date[$data['jobman']][$sp][$data['date']] = 1;

                $bonus = ceil($data['oborot_day'] / 100 * $data['pay_bonus_proc_from_oborot']);
                $bonus_text = $data['pay_bonus_proc_from_oborot'] . '% от ' . number_format($data['oborot_day'], 0, '', '`');
            }
        }
        // фиксированная оценка дня
        elseif (!empty($ocenka)) {
            if (!empty($data['pay_premiya-' . $ocenka])) {

                self::$ar_autobonus_jm_sp_date[$data['jobman']][$sp][$data['date']] = 1;

                $bonus = $data['pay_premiya-' . $ocenka];
                $bonus_text = 'оценка ' . $ocenka;
            } else {
                $bonus_text = '#' . __LINE__;
            }
        } else {
            $bonus_text = '#' . __LINE__;
        }

        // \f\pa( [ ( $ocenka ?? 'x' ), ( $bonus ?? 'x' ), ( $bonus_text ?? 'x' ), $data ] , 2);

        if (!empty($bonus)) {

            if (isset(self::$ar_metki_jm_date_sp_type[$data['jobman']][$data['date']][$sp]['autobonus50pr'])) {
                $bonus = ceil($bonus / 2);
                $bonus_text .= ' / 50% бонуса ';
            }

            return \f\end3('окей', true, ['in' => [$sp, $data], 'bonus' => $bonus, 'text' => ($bonus_text ?? '' )]);
        } else {
            return \f\end3('нет бонуса', false, ['in' => [$sp, $data], 'text' => ($bonus_text ?? '' )]);
        }
    }

    /**
     * получаем текущую должность и спец назначения если были
     * 2006270745
     * @param type $db
     * @param type $user
     * @param type $date_now
     * @return массив [ 'norm => [] , 'spec' => [] ]
     */
    public static function whatJobDate($db, $user, $date_now) {

        // \f\pa([$user, $date_now]);

        if (empty(self::$cash['whatJobDate'][$user])) {

            $date_start = date('Y-m-d', strtotime($date_now . ' -12 month'));
            $date_finish = date('Y-m-d', strtotime($date_now . ' +47 day'));
            \Nyos\mod\items::$between_date['date'] = [$date_start, $date_finish];

            \Nyos\mod\items::$search['jobman'] = $user;

            self::$cash['whatJobDate'][$user] = \Nyos\mod\items::get2($db, \Nyos\mod\JobDesc::$mod_man_job_on_sp);

            if (sizeof(self::$cash['whatJobDate'][$user]) > 1)
                \uasort(self::$cash['whatJobDate'][$user], '\\f\\sort_ar__start_time__desc');
        }

        $norm = [];
        foreach (self::$cash['whatJobDate'][$user] as $k => $v) {
            if ($v['date'] > $date_now)
                break;
            $norm = $v;
        }

        if (isset(self::$WhereJobMans['data']['spec'][$user][$date_now])) {
            $spec = self::$WhereJobMans['data']['spec'][$user][$date_now];
        }

        return ['norm' => ( $norm ?? [] ), 'spec' => ( $spec ?? [] )];
    }

    /**
     * 
     * @param type $db
     * @param string $date_start
     * @param type $date_finish
     * @param type $sp_id
     * @return string
     */
    public static function getJobsJobMans($db, string $date_start, $date_finish = '', $sp_id = null) {

        if (empty($date_finish)) {
            $date_start = date('Y-m-01', strtotime($date_start));
            $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day '));
        }


        if (self::$no_cash !== true)
            $cash_var = 'listJobsJobMans_jobs_' . ( $date_start ?? 'x' ) . '_' . ( $date_finish ?? 'x' ) . '_' . ( $sp_id ?? 'x' );

        $cash_time = 0;

        $ret = '';

        if (!empty($cash_var))
        //$ret = \f\Cash::getVar($cash_var);
            if (!empty($ret)) {
                //return $e;
                // \f\pa($ret);
            } else {

                $return = [];


                $ar_jm_sp = $ar_sp_jm = [];

//            if (!empty($sp_id))
//                \Nyos\mod\items::$search['sale_point'] = $sp_id;

                \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start . ' -1 year')), date('Y-m-d', strtotime($date_finish))];
                $norm = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_man_job_on_sp);

                $ar = [];

                foreach ($norm as $k => $v) {
                    if ($v['date'] <= $date_start) {

                        if (!empty($v['date_finish']) && $v['date_finish'] < $date_start)
                            continue;

                        $ar[$v['jobman']] = [];
                        $ar[$v['jobman']][] = $v;
                    } else {
                        $ar[$v['jobman']][] = $v;
                    }
                }

                foreach ($ar as $jm => $v0) {
                    foreach ($v0 as $k => $v) {
                        $return[$jm][] = $v;
                        $ar_jm_sp[$jm][$v['sale_point']] = $ar_sp_jm[$v['sale_point']][$jm] = 1;
                    }
                }

//            if (!empty($sp_id))
//                \Nyos\mod\items::$search['sale_point'] = $sp_id;

                \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start)), date('Y-m-d', strtotime($date_finish))];
                $spec0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_spec_jobday);

                $spec = [];

                foreach ($spec0 as $k => $v) {
                    $v['type'] = 'spec';
                    $spec[$v['jobman']][$v['date']][$v['sale_point']] = $v;
                    $ar_jm_sp[$v['jobman']][$v['sale_point']] = $ar_sp_jm[$v['sale_point']][$v['jobman']] = 1;
                }

                // \f\pa($spec, '', '', 'spec');        
                // \f\pa($return, '', '', 'return');

                $ret = \f\end3('получили список кто где работает', true, [
                    'checks' => $return,
                    'spec' => $spec,
                    'ar_jm_sp' => $ar_jm_sp,
                    'ar_sp_jm' => $ar_sp_jm,
                    'in' => [$date_start, $date_finish, ( $sp_id ?? 0 )]
                ]);

                if (!empty($cash_var))
                    \f\Cash::setVar($cash_var, $ret, ($cash_time ?? 0));
            }

        //\f\pa($ret);

        if (!empty($sp_id) && isset($ret['data']['ar_sp_jm'][$sp_id])) {

            $return = [
                'ar_jm' => $ret['data']['ar_sp_jm'][$sp_id],
                'checks' => [],
                'spec' => []
            ];


            foreach ($ret['data']['ar_sp_jm'][$sp_id] as $jm => $v) {

                if (!empty($ret['data']['checks'][$jm]))
                    $return['checks'][$jm] = $ret['data']['checks'][$jm];

                if (!empty($ret['data']['spec'][$jm]))
                    $return['spec'][$jm] = $ret['data']['spec'][$jm];
            }

            return \f\end3('получили список 2 по точке ' . $sp_id, true, $return);
        }

        return $ret;
    }

    /**
     * получаем список всех работников что работали на точках
     * 2004300126
     * @param type $db
     * @param string $date_start
     * @param string $date_finish
     * @param type $sp_id
     * id точки если указан то показываем только выбранные
     * @return type
     */
//    public static function whatJobDate($db, $user, $date_now) {
//
//        $norm = [];
//
//        if (isset(self::$WhereJobMans['data']['checks'][$user])) {
//
//            // usort($a, "\\f\\sort_ar_date_desc");
//
//            foreach (self::$WhereJobMans['data']['checks'][$user] as $k => $v) {
//                if ($v['date'] > $date_now)
//                    break;
//                $norm = $v;
//            }
//        }
//
//        if (isset(self::$WhereJobMans['data']['spec'][$user][$date_now])) {
//            $spec = self::$WhereJobMans['data']['spec'][$user][$date_now];
//        }
//        //$job = self::whatJobDate( $user, $date_now );
//
//        return ['norm' => ( $norm ?? [] ), 'spec' => ( $spec ?? [] )];
//    }

    /**
     * получаем список всех работников что работали на точках
     * 2004300126
     * @param type $db
     * @param string $date_start
     * @param string $date_finish
     * @param type $sp_id
     * id точки если указан то показываем только выбранные
     * @return type
     */
    public static function getPeriodWhereJobMans($db, string $date_start, string $date_finish, $sp_id = null) {

        if (1 == 2) {

            $return = [];

            if (!empty($sp_id))
                \Nyos\mod\items::$search['sale_point'] = $sp_id;

            \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start)), date('Y-m-d', strtotime($date_finish))];
            $spec = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_spec_jobday);

            foreach ($spec as $k => $v) {
                $v['type'] = 'spec';
                $return[] = $v;
            }


            // \f\pa($spec, '', '', 'spec');        


            if (!empty($sp_id))
                \Nyos\mod\items::$search['sale_point'] = $sp_id;

            \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start)), date('Y-m-d', strtotime($date_finish))];
            $norm = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_man_job_on_sp);

            foreach ($norm as $k => $v) {
                $return[] = $v;
            }

            \f\pa($return, '', '', 'return');

            return $return;
        }
























//        if ( strpos($_SERVER['HTTP_HOST'], 'dev') != false)
//            $timer_on = true;

        if (isset($timer_on) && $timer_on === true) {
            echo '<hr>';
            echo '<br/>#' . __LINE__ . ' ' . __FILE__;
            echo '<br/>#' . __LINE__ . ' ' . __FUNCTION__;
            echo '<br/>#' . __LINE__ . ' ' . $date_start;
            echo '<br/>#' . __LINE__ . ' ' . $date_finish;
            echo '<br/>#' . __LINE__ . ' ' . $sp_id;
            \f\timer_start(234);
        }

//        $cash_var = 'getListJobmans__list_jobmans';
//         $cash_time = 60 * 60 * 6;

        $return = false;

        if (!empty($cash_var))
            $return = \f\Cash::getVar($cash_var);

        if ($return !== false) {
            
        } else {



            if (1 == 2) {
                $sql = 'SELECT '
                        . ' i_jobon.id jobon_id '
//                    . ' , i_jobon.* '
//                    . ' , d_jobon.id d_id '
                        // . ' , d_jobon.* '
//                    . ' , CONCAT( d_jobon.value , d_jobon.value_date, d_jobon.value_datetime, d_jobon.value_int, d_jobon.value_text  ) as d_val  '
                        . ' , d_jobon_date.value_date date '
                        . ' , d_jobon_dolgn.value dolgn_id '
                        . ' , d_jobon_jm.value jobman '
                        . ' , d_jobon_sp.value sp_id '
                        . ' FROM `mitems-dops` d_jobon '
                        // . ' FROM `mitems-dops` d_jobon '
                        //
                        . ' INNER JOIN `mitems` i_jobon ON '
                        . ' i_jobon.`module` = :mod_job_on '
                        . ' AND i_jobon.id = d_jobon.id_item '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_date ON '
                        . ' d_jobon_date.id_item = d_jobon.id_item '
                        . ' AND d_jobon_date.name = \'date\' '
                        // . ' AND d_jobon_date.value_date BETWEEN :date_start and :date_fin '
                        . ' AND d_jobon_date.value_date <= :date_fin '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_dolgn ON '
                        . ' d_jobon_dolgn.id_item = d_jobon.id_item '
                        . ' AND d_jobon_dolgn.name = \'dolgnost\' '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_sp ON '
                        . ' d_jobon_sp.id_item = d_jobon.id_item '
                        . ' AND d_jobon_sp.name = \'sale_point\' '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_jm ON '
                        . ' d_jobon_jm.id_item = d_jobon.id_item '
                        . ' AND d_jobon_jm.name = \'jobman\' '
                        //
                        . ' WHERE '
                        . ' d_jobon.status IS NULL '
                        . ' GROUP BY i_jobon.id '
                ;

                $sql_vars = [];
                $sql_vars[':mod_job_on'] = \Nyos\mod\JobDesc::$mod_man_job_on_sp;
                //$sql_vars[':date_start'] = date('Y-m-d', strtotime($date_start));
                $sql_vars[':date_fin'] = date('Y-m-d', strtotime($date_finish));
                // $sql_vars[':status'] = 'show';
                // $sql_vars[':mod_user'] = \Nyos\mod\JobDesc::$mod_jobman;
                // $sql_vars[':mod_sp'] = \Nyos\mod\JobDesc::$mod_sale_point;
// \f\pa($ff1);

                $ff = $db->prepare($sql);
                $ff->execute($sql_vars);

// $return = [];
                // $return = $ff->fetchAll();
                // echo '<div style="padding:10px; border: 1px solid green;" >';

                $return = $return1 = [];

                while ($v = $ff->fetch()) {
                    //foreach ($return as $k => $v) {
                    // echo '<br/>' . $v['jobman'] . ' - ' . $v['jobon_date'] . ' + ' . $v['sp_id'];
                    $return1[$v['jobman']]['date'][] = $v;
                    // $return1[$v['jobman']]['job_on_sp'][$v['sp_id']] = 1;
                }
            } // закрыли копию для спец назначений

            $return = $return1 = [];

            // тащим спец назначения
            // \f\timer_start(231);
            if (1 == 1) {

                if (!empty($sp_id))
                    \Nyos\mod\items::$search['sale_point'] = $sp_id;

                \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($date_start)), date('Y-m-d', strtotime($date_finish))];
                $spec = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_spec_jobday);

                // \f\pa($spec, '', '', 'spec');

                foreach ($spec as $k => $v) {
                    $v['sp_id'] = $v['sale_point'];
                    $v['type'] = 'spec';
                    $return1[$v['jobman']]['date'][] = $v;
                }
                unset($spec);
            }
            // echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(231) . ' ' . __FILE__;
            // $return1[$v['jobman']]['date'][] = $v;
            // \f\pa($return1);
            // \f\timer_start(231);
            if (1 == 1) {
                $sql = 'SELECT '
                        . ' i_jobon.id jobon_id '
//                    . ' , i_jobon.* '
//                    . ' , d_jobon.id d_id '
                        // . ' , d_jobon.* '
//                    . ' , CONCAT( d_jobon.value , d_jobon.value_date, d_jobon.value_datetime, d_jobon.value_int, d_jobon.value_text  ) as d_val  '
                        . ' , d_jobon_date.value_date date '
                        . ' , d_jobon_dolgn.value dolgnost '
                        . ' , d_jobon_jm.value jobman '
                        . ' , d_jobon_sp.value sp_id '
                        . ' FROM `mitems-dops` d_jobon '
                        // . ' FROM `mitems-dops` d_jobon '
                        //
                        . ' INNER JOIN `mitems` i_jobon ON '
                        . ' i_jobon.`module` = :mod_job_on '
                        . ' AND i_jobon.id = d_jobon.id_item '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_date ON '
                        . ' d_jobon_date.id_item = d_jobon.id_item '
                        . ' AND d_jobon_date.name = \'date\' '
                        // . ' AND d_jobon_date.value_date BETWEEN :date_start and :date_fin '
                        . ' AND d_jobon_date.value_date <= :date_fin '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_dolgn ON '
                        . ' d_jobon_dolgn.id_item = d_jobon.id_item '
                        . ' AND d_jobon_dolgn.name = \'dolgnost\' '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_sp ON '
                        . ' d_jobon_sp.id_item = d_jobon.id_item '
                        . ' AND d_jobon_sp.name = \'sale_point\' '
                        //
                        . ' INNER JOIN `mitems-dops` d_jobon_jm ON '
                        . ' d_jobon_jm.id_item = d_jobon.id_item '
                        . ' AND d_jobon_jm.name = \'jobman\' '
                        //
                        . ' WHERE '
                        . ' d_jobon.status IS NULL '
                        . ' GROUP BY i_jobon.id '
                ;

                $sql_vars = [];
                $sql_vars[':mod_job_on'] = \Nyos\mod\JobDesc::$mod_man_job_on_sp;
                //$sql_vars[':date_start'] = date('Y-m-d', strtotime($date_start));
                $sql_vars[':date_fin'] = date('Y-m-d', strtotime($date_finish));
                // $sql_vars[':status'] = 'show';
                // $sql_vars[':mod_user'] = \Nyos\mod\JobDesc::$mod_jobman;
                // $sql_vars[':mod_sp'] = \Nyos\mod\JobDesc::$mod_sale_point;
// \f\pa($ff1);

                $ff = $db->prepare($sql);
                $ff->execute($sql_vars);

// $return = [];
                // $return = $ff->fetchAll();
                // echo '<div style="padding:10px; border: 1px solid green;" >';
                // $return = $return1 = [];

                while ($v = $ff->fetch()) {
                    //foreach ($return as $k => $v) {
                    // echo '<br/>' . $v['jobman'] . ' - ' . $v['jobon_date'] . ' + ' . $v['sp_id'];
                    $return1[$v['jobman']]['date'][] = $v;
                    // $return1[$v['jobman']]['job_on_sp'][$v['sp_id']] = 1;
                }
            } // тащим назначения норм за период
            // echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(231) . ' ' . __FILE__;
            // echo '</div>';
            // \f\pa($return1, 2, '', 'return1');

            $ds = date('Y-m-d', strtotime($date_start));
            $df = date('Y-m-d', strtotime($date_finish));

            foreach ($return1 as $jm => $vv) {


                // echo '<hr>';
                $new = [];

                // работает или нет на точке сотрудник в этот период
                $job_on_sp = false;

                if (!empty($vv['date'])) {

                    // \f\pa($vv['date'],'','','$vv');


                    $e = $vv['date'];

                    //usort($e, "\\f\\sort_ar_date_desc");
                    usort($e, "\\f\\sort_ar_date");
                    // $new = [];

                    foreach ($e as $k => $v2) {

                        if (!empty($v2['date'])) {
                            if ($v2['date'] <= $ds) {
                                $new = [0 => $v2];
                            }
                            //
                            elseif ($v2['date'] <= $df) {
                                $new[] = $v2;
                            }
                        }

                        // \f\pa($v2);
                    }

//                    if( !empty($new) > 1 )
//                    \f\pa($new);
                }

                $return[$jm] = $new;
            }

            // echo '<br/>#'.__LINE__.' '.__FILE__.' '.\f\timer_stop(234);

            if (!empty($cash_var))
                \f\Cash::setVar($cash_var, $return, ( $cash_time ?? 0));
        }


        if (!empty($sp_id)) {

            $re = [];
            foreach ($return as $jm => $v) {

                $add = false;

                foreach ($v as $k1 => $v1) {

                    if (!empty($v1['sp_id']) && $v1['sp_id'] == $sp_id) {
                        // $re[$jm] = $v;
                        $add = true;
                    }
                }

                if ($add === true)
                    $re[$jm] = $v;
            }

            // \f\pa($re,'','','re result');
        }


        if (isset($timer_on) && $timer_on === true)
            echo '<br/>#' . __LINE__ . ' ' . __FILE__
            . '<br/>' . 'f:' . __FUNCTION__ . ' ' . \f\timer_stop(234);

        // если искали 1 sp
        if (!empty($re))
            return $re;

        return $return;
    }

    /**
     * чистим переменные что дополнительные
     * возвращаем параметры со старта
     */
    public static function clearTempClassVars() {

        self::$return_dop_info = false;
    }

    public static function getJobmansJobingToSpMonth($db, int $sp_id, string $date) {

        // echo '<br/>sp:' . $sp_id . ' date:' . $date;

        try {

//            $in_sql = [
//                // ':sp' => $sp_id,
//                ':date_start' => date('Y-m-01', strtotime($date))
//            ];
//            $in_sql[':date_finish'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' +1 month -1 day'));

            $in_sql = [
                ':date_start' => date('Y-m-01', strtotime($date))
            ];
            // $in_sql[':date_start0'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' -6 month '));
            $in_sql[':date_start2'] = date('Y-m-02', strtotime($date));
            $in_sql[':date_finish'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' +1 month -1 day'));

            // $sql = 'SELECT * FROM `mod_jobman_send_on_sp` WHERE date <= DATE( :date_start ) AND sale_point = :sp ORDER BY date DESC';
            // $sql = 'SELECT * FROM `mod_jobman_send_on_sp` on_sp WHERE DATE( on_sp.date ) <= DATE( :date_start ) AND on_sp.sale_point = :sp ORDER BY on_sp.date DESC';
            // $sql = 'SELECT * FROM `mod_jobman_send_on_sp` on_sp WHERE on_sp.date <= :date_start AND on_sp.sale_point = :sp ORDER BY on_sp.date DESC';

            $sql = ' SELECT 
                spec.`id` ,
                spec.`date` , 
                \'\' as date_finish ,
                spec.`jobman` ,
                spec.`sale_point` ,
                spec.`dolgnost` ,
                `d`.`head` dolgnost_name,
                \'spec\' as `type` '
                    . ' , CONCAT( jm.lastName, \' \', jm.firstName, \' \', jm.middleName  ) as fio '
                    // /* , CONCAT( jm.firstName, \' \', jm.lastName ) as `if` */
//                .' , pay.id pay_id
//                , pay.date pay_date
//                , pay.`ocenka-hour-base`
//                , pay.`ocenka-hour-5`
//                , pay.`ocenka-hour-3` 	
//                , pay.`premiya-5`
//                , pay.`premiya-3`
//                , pay.`bonus_proc_from_oborot`
//                , pay.`if_kurit` 	
//                , pay.`oborot_sp_last_monht_bolee`
//                , pay.`pay_from_day_oborot_bolee` '
                    . ' FROM 
                `mod_050_job_in_sp` `spec` '
                    . ' LEFT JOIN mod_070_jobman jm ON jm.id = `spec`.`jobman` '
                    . ' LEFT JOIN mod_061_dolgnost d ON d.id = `spec`.`dolgnost` '
                    // .' LEFT JOIN mod_071_set_oplata pay ON pay.id = ( SELECT id FROM mod_071_set_oplata p WHERE p.dolgnost = `spec`.`dolgnost` AND p.date <= spec.date ORDER BY date DESC LIMIT 1 ) '
                    . ' WHERE '
                    . ' spec.date BETWEEN :date_start AND :date_finish '
//                    . ' spec.date >= :date_start '
//                    . ' AND '
//                    . ' spec.date <= :date_finish '
                    . ' AND '
                    . ' spec.status = \'show\' '
                    . '  UNION ALL '
                    . ' SELECT 
                        `on`.`id` ,
                        `on`.`date` , 
                        `on`.`date_finish` , 
                        `on`.`jobman` ,
                        `on`.`sale_point` ,
                        `on`.`dolgnost` ,
                        `d`.`head` dolgnost_name,
                        \'norm\' as `type`
                        , CONCAT( jm.lastName, \' \', jm.firstName, \' \', jm.middleName  ) as fio '
                    // /* , CONCAT( jm.firstName, \' \', jm.lastName ) as `if` */
//                .' ', pay.id pay_id
//                , pay.date pay_date
//                , pay.`ocenka-hour-base`
//                , pay.`ocenka-hour-5`
//                , pay.`ocenka-hour-3` 	
//                , pay.`premiya-5`
//                , pay.`premiya-3`
//                , pay.`bonus_proc_from_oborot`
//                , pay.`if_kurit` 	
//                , pay.`oborot_sp_last_monht_bolee`
//                , pay.`pay_from_day_oborot_bolee` '
                    . ' FROM
                `mod_jobman_send_on_sp` as `on` '
                    . ' LEFT JOIN mod_070_jobman jm ON jm.id = `on`.`jobman` '
                    . ' LEFT JOIN mod_061_dolgnost d ON d.id = `on`.`dolgnost` '
                    // .' LEFT JOIN mod_071_set_oplata pay ON pay.id = ( SELECT id FROM mod_071_set_oplata p WHERE p.dolgnost = `on`.`dolgnost` AND p.date <= on.date ORDER BY date DESC LIMIT 1 ) '
                    . ' WHERE '
                    . ' `on`.`date` >= :date_start2 '
                    . ' AND '
                    . ' `on`.`date` <= :date_finish '
                    . ' AND '
                    . ' `on`.status = \'show\' '
                    . '  UNION ALL '
                    . ' ( 
                        SELECT 
                        `on`.`id` ,
                        `on`.`date` , 
                        `on`.`date_finish` , 
                        `on`.`jobman` ,
                        `on`.`sale_point` ,
                        `on`.`dolgnost` ,
                        `d`.`head` dolgnost_name,
                        \'norm\' as `type`
                        , CONCAT( jm.lastName, \' \', jm.firstName, \' \', jm.middleName  ) as fio '
                    // /* , CONCAT( jm.firstName, \' \', jm.lastName ) as `if` */
//                .' ', pay.id pay_id
//                , pay.date pay_date
//                , pay.`ocenka-hour-base`
//                , pay.`ocenka-hour-5`
//                , pay.`ocenka-hour-3` 	
//                , pay.`premiya-5`
//                , pay.`premiya-3`
//                , pay.`bonus_proc_from_oborot`
//                , pay.`if_kurit` 	
//                , pay.`oborot_sp_last_monht_bolee`
//                , pay.`pay_from_day_oborot_bolee` '
                    . ' FROM
                `mod_jobman_send_on_sp` as `on` '
                    . ' LEFT JOIN mod_070_jobman jm ON jm.id = `on`.`jobman` '
                    . ' LEFT JOIN mod_061_dolgnost d ON d.id = `on`.`dolgnost` '
                    // .' LEFT JOIN mod_071_set_oplata pay ON pay.id = ( SELECT id FROM mod_071_set_oplata p WHERE p.dolgnost = `on`.`dolgnost` AND p.date <= on.date ORDER BY date DESC LIMIT 1 ) '
                    . ' WHERE '
                    // . ' `on`.`date` <= :date_start '
                    . ' `on`.`date` = ( SELECT MAX(date) FROM mod_jobman_send_on_sp WHERE date <= :date_start AND jobman = `on`.`jobman` LIMIT 1 ) '
                    . ' AND '
                    . ' `on`.status = \'show\' '
                    . ' GROUP BY `on`.`jobman` '
                    . ' ) '

            ;
            // \f\pa($sql);

            \f\timer_start(111);

            $ff = $db->prepare($sql);
            $ff->execute($in_sql);
            $return2 = $ff->fetchAll();

            usort($return2, "\\f\\sort_ar_date");

            // added from 200810
            if (1 == 1) {

                // return $return2;

                $ar__jobs_sp_jm = [];

                // search jobmans_on_sp
                foreach ($return2 as $v) {
                    $ar__jobs_sp_jm[$v['sale_point']][$v['jobman']] = 1;
                }

                $return = [
                    'jobmans' => [],
                    'send_on_job' => [],
                    // 'spec' => ( $return_spec_sp_jm_onjob[$sp_id] ?? [] ),
                    'spec' => [],
                    'ar__jm_sp' => [],
                ];

                foreach ($return2 as $v) {


                    if ($v['type'] == 'norm' && isset($ar__jobs_sp_jm[$sp_id][$v['jobman']])) {

                        if (isset($v['date_finish']) && $v['date_finish'] < $in_sql[':date_start'])
                            continue;

                        if (!isset($return['jobmans'][$v['jobman']]))
                            $return['jobmans'][$v['jobman']] = $v['fio'];

                        $return['send_on_job'][$v['jobman']][] = $v;

                        $return['ar__jm_sp'][$v['jobman']][$v['sale_point']] = 1;
                    } elseif ($v['type'] == 'spec' && isset($ar__jobs_sp_jm[$sp_id][$v['jobman']])) {

                        if (!isset($return['jobmans'][$v['jobman']]))
                            $return['jobmans'][$v['jobman']] = $v['fio'];

                        $return['ar__jm_sp'][$v['jobman']][$v['sale_point']] = 1;

                        $return['spec'][$v['jobman']][] = $v;
                    }
                }

                unset($return2);

                return \f\end3('ok ' . \f\timer_stop(111), true, $return);
                // return $return;
            }
        } catch (\PDOException $exc) {

            \f\pa($exc);
        }

        die();
    }

    /**
     * 
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
     * 
     * @param type $db
     * @param array $jobmans
     * @param string $date
     * @return type
     */
    public static function getActionsJobmansOnMonth($db, array $jobmans, string $date, $get_one_day = false, $sp = null) {

        \f\timer_start(111);

        $return = [
            'jobmans' => $jobmans,
        ];

        $jms = ' ( jobman = \'' . implode('\' OR jobman = \'', $jobmans) . '\' ) ';
        $jms_c = ' ( c.jobman = \'' . implode('\' OR c.jobman = \'', $jobmans) . '\' ) ';

        // если тащим один день
        if ($get_one_day === true) {

            $in_sql = [
                // ':jobmans' => '\''.implode( '\', \'', $jobmans ).'\'' ,
                ':date_start' => date('Y-m-d', strtotime($date))
                , ':datet_start' => date('Y-m-d 05:00:00', strtotime($date))
            ];
            // $in_sql[':date_start0'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' -6 month '));
            $in_sql[':date_finish'] = $in_sql[':date_start'];
            $in_sql[':datet_finish'] = date('Y-m-d 05:00:00', strtotime($in_sql[':date_start'] . ' +1 day'));
        }
        // если тащим целый месяц
        else {

            $in_sql = [
                // ':jobmans' => '\''.implode( '\', \'', $jobmans ).'\'' ,
                ':date_start' => date('Y-m-01', strtotime($date))
                , ':datet_start' => date('Y-m-01 05:00:00', strtotime($date))
            ];
            // $in_sql[':date_start0'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' -6 month '));
            $in_sql[':date_finish'] = date('Y-m-d', strtotime($in_sql[':date_start'] . ' +1 month -1 day'));
            $in_sql[':datet_finish'] = date('Y-m-d 05:00:00', strtotime($in_sql[':date_start'] . ' +1 month'));
        }

        if (!empty($sp))
            $in_sql[':sp'] = $sp;

        $return['sql_in'] = $in_sql;


        $sql = PHP_EOL
                . 'SELECT 
                    `mm`.`id`,
                    `mm`.`status`,
                    `mm`.`jobman`,
                    `mm`.`sale_point`,
                    `mm`.`date`,
                    \'\' `summa`, '
                . ' `mm`.`type2` `var`, '
                . ' `mm`.`head` `text`,
                    \'\' as `auto_bonus_zp`,
                    \'metki\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , \'\' oborot_day_id '
                . ' , \'\' oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM 
                    `mod_072_metki` `mm` '
                . ' WHERE '
                . $jms
                . ' AND `mm`.`date` BETWEEN :date_start AND :date_finish '
                . ' AND `mm`.`status` = \'show\' '
                . PHP_EOL . ' UNION ALL '
                . PHP_EOL
                . 'SELECT 
                    c.`id` ,
                    c.`status` ,
                    c.`jobman`
                    , c.`sale_point` '
                . ' , DATE( ( c.start - INTERVAL 300 MINUTE  ) ) `date` '
                . ' , '
                . ' \'\' `summa`, '
                . ' \'\' `var`, '
                . ' \'\' `text`,
                    \'\' as `auto_bonus_zp`,
                    \'check\' as `type` '
                . ' , c.start '
                . ' , c.fin '
                . ' , c.who_add_item '
                . ' , c.ocenka_auto '
                . ' , c.ocenka '

//                . ' , c.hour_on_job '
//                . ' , c.hour_on_job_hand '
                . ' , ( CASE 
                    WHEN c.hour_on_job_hand > 0 THEN c.hour_on_job_hand
                    WHEN c.hour_on_job > 0 THEN c.hour_on_job
                    ELSE NULL END ) as `hour_on_job` '
                . ' , on_sp.id position_id '
                . ' , on_sp.date position_date '
                . ' , on_sp.date_finish position_date_finish '
                . ' , on_sp.dolgnost position_d '
                . ' , on_sp.sale_point position_sp '
                . ' , dol.calc_auto position_calc_auto '
                . ' , pay.id pay_id '
                . ' , pay.date pay_date '
                . ' , pay.`ocenka-hour-5` pay5 '
                . ' , pay.`ocenka-hour-3` pay3 '
                . ' , pay.`if_kurit` pay_if_kurit '
                . ' , pay.`ocenka-hour-base` pay_base '
                . ' , pay.`premiya-5` `pay_premiya-5` '
                . ' , pay.`premiya-3` `pay_premiya-3` '
                . ' , pay.`oborot_sp_last_monht_bolee` pay_oborot_sp_last_monht_bolee '
                . ' , pay.`bonus_proc_from_oborot` pay_bonus_proc_from_oborot '
                . ' , pay.`pay_from_day_oborot_bolee` pay_pay_from_day_oborot_bolee '

                // .' , \'\' oborot_day '
                . ' , oborot_d.id oborot_day_id '
                . ' , oborot_d.date oborot_day_date '
                . ' , oborot_d.oborot_hand oborot_day_hand '
                // . ' , oborot_d.oborot_server oborot_day_server '
                . ' , ( CASE 
                    WHEN oborot_d.oborot_server_hand > 0 THEN oborot_d.oborot_server_hand
                    WHEN oborot_d.oborot_server > 0 THEN oborot_d.oborot_server
                    ELSE NULL END ) as oborot_day_server '
                . ' , ( CASE 
                    WHEN oborot_d.oborot_hand > 0 THEN oborot_d.oborot_hand
                    WHEN oborot_d.oborot_server_hand > 0 THEN oborot_d.oborot_server_hand
                    WHEN oborot_d.oborot_server > 0 THEN oborot_d.oborot_server
                    ELSE NULL END ) as oborot_day '
                . ' , s.id spec_id '
                . ' , s.date spec_date '
                . ' , s.dolgnost spec_d '
                . ' , s.sale_point spec_sp '
                . ' , s_pay.id s_pay_id '
                . ' , s_pay.dolgnost s_pay_d '
                . ' , s_pay.date s_pay_date '
                . ' , s_pay.`ocenka-hour-5` s_pay5 '
                . ' , s_pay.`ocenka-hour-3` s_pay3 '
                . ' , s_pay.`if_kurit` s_pay_if_kurit '
                . ' , s_pay.`ocenka-hour-base` s_pay_base '
                . ' , s_pay.`premiya-5` `s_pay_premiya-5` '
                . ' , s_pay.`premiya-3` `s_pay_premiya-3` '
                . ' , s_pay.`oborot_sp_last_monht_bolee` s_pay_oborot_sp_last_monht_bolee '
                . ' , s_pay.`bonus_proc_from_oborot` s_pay_bonus_proc_from_oborot '
                . ' , s_pay.`pay_from_day_oborot_bolee` s_pay_pay_from_day_oborot_bolee '
                . ' , ( CASE 
                        WHEN oborot_spec.oborot_hand > 0 THEN oborot_spec.oborot_hand
                        WHEN oborot_spec.oborot_server > 0 THEN oborot_spec.oborot_server
                        ELSE NULL END ) as spec_oborot_day '
                . ' , ( CASE 
                    
                        WHEN 
                                oborot_spec.oborot_hand > 0 
                                AND s_pay.`bonus_proc_from_oborot` > 0
                            THEN 
                                FLOOR( oborot_spec.oborot_hand / 100 * s_pay.`bonus_proc_from_oborot` )
                                
                        WHEN 
                                oborot_spec.oborot_server > 0 
                                AND s_pay.`bonus_proc_from_oborot` > 0
                            THEN 
                                FLOOR( oborot_spec.oborot_server / 100 * s_pay.`bonus_proc_from_oborot` )

                        ELSE NULL END ) as  spec_bonus  '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM `mod_050_chekin_checkout` `c` '
                . ' LEFT JOIN mod_jobman_send_on_sp on_sp ON on_sp.id = ( SELECT id FROM mod_jobman_send_on_sp WHERE '
                . ' jobman = c.jobman '
                // . ' AND sale_point = c.sale_point '
                . ' AND date <= DATE( ( c.start - INTERVAL 300 MINUTE  ) ) '
                . ' AND status = \'show\' '
                . ' ORDER BY date DESC '
                . ' LIMIT 1 ) '
                . ' LEFT JOIN mod_sale_point_oborot oborot_d ON '
                . ' oborot_d.date = DATE( ( c.start - INTERVAL 300 MINUTE  ) ) '
                . ' AND oborot_d.sale_point = on_sp.sale_point '
                . ' AND oborot_d.status = \'show\' '
                . ' LEFT JOIN mod_061_dolgnost dol ON dol.id = on_sp.dolgnost '
                . ' LEFT JOIN mod_071_set_oplata pay ON '
                . ' pay.id = ( SELECT id FROM mod_071_set_oplata WHERE '
                // .' jobman = c.jobman '
                . ' dolgnost = on_sp.dolgnost '
                . ' AND sale_point = on_sp.sale_point '
                . ' AND date <= DATE( ( c.start - INTERVAL 300 MINUTE  ) ) '
                . ' AND status = \'show\' '

                //                    WHEN oborot_d.oborot_hand > 0 THEN oborot_d.oborot_hand
                //                    WHEN oborot_d.oborot_server > 0 THEN oborot_d.oborot_server
                . ' AND ( 
                                ( '
                . ' oborot_d.oborot_hand > 0 '
                . ' AND '
                . ' oborot_d.oborot_hand >= pay_from_day_oborot_bolee '
                . ' ) OR ( '
                . ' oborot_d.oborot_hand IS NULL '
                . ' AND '
                . ' oborot_d.oborot_server > 0 '
                . ' AND '
                . ' pay.pay_from_day_oborot_bolee <= oborot_d.oborot_server '
                . ' ) '
                . ' OR 
                                pay_from_day_oborot_bolee IS NULL '
                . ' ) '
                . ' ORDER BY date DESC, pay_from_day_oborot_bolee DESC  '
                . ' LIMIT 1 ) '
                . ' LEFT JOIN mod_050_job_in_sp s ON 
                        s.date = DATE( ( c.start - INTERVAL 300 MINUTE  ) )
                        AND s.jobman = c.jobman
                        AND s.status = \'show\'
                        '
                . ' LEFT JOIN mod_071_set_oplata s_pay ON s_pay.id = ( SELECT id FROM mod_071_set_oplata WHERE '
                // .' jobman = c.jobman '
                . ' dolgnost = s.dolgnost '
                . ' AND sale_point = on_sp.sale_point '
                . ' AND date <= s.date '
                . ' AND status = \'show\' '
//                    WHEN oborot_d.oborot_hand > 0 THEN oborot_d.oborot_hand
//                    WHEN oborot_d.oborot_server > 0 THEN oborot_d.oborot_server
                . ' AND ( '
                . ' ( oborot_d.oborot_hand > 0 AND ( pay_from_day_oborot_bolee <= oborot_d.oborot_hand ) ) '
                . ' OR ( oborot_d.oborot_hand IS NULL AND oborot_d.oborot_server > 0 AND ( pay_from_day_oborot_bolee <= oborot_d.oborot_server ) ) '
                . ' OR pay_from_day_oborot_bolee IS NULL '
                . ' ) '
                . ' ORDER BY date DESC, pay_from_day_oborot_bolee DESC  '
                . ' LIMIT 1 ) '
                . ' LEFT JOIN mod_sale_point_oborot oborot_spec ON '
                . ' s.sale_point > 0 '
                . ' AND oborot_spec.date = DATE( ( c.start - INTERVAL 300 MINUTE  ) ) '
                . ' AND oborot_spec.sale_point = s.sale_point '
                . ' AND oborot_spec.status = \'show\' '
                . ' WHERE '
                . $jms_c
                . ' AND c.`start` BETWEEN :datet_start AND :datet_finish '
                . ' AND '
                . ' c.`status` = \'show\' '

                //.' GROUP BY c.id '
                . PHP_EOL . ' UNION ALL '
                . PHP_EOL
                . ' SELECT 
                    `mi`.`id`,
                    `mi`.`status`,
                    `mi`.`jobman`,
                    `mi`.`sale_point`,
                    `mi`.`date_now` `date`,
                    `mi`.`summa`,
                    \'\' `var`,
                    `mi`.`text`,
                    \'\' as `auto_bonus_zp`,
                    \'minus\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , \'\' oborot_day_id '
                . ' , \'\' oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM 

                    `mod_072_vzuscaniya` `mi`
                WHERE '
                . $jms
                . ' AND '
                . ' `mi`.`date_now` BETWEEN :date_start AND :date_finish '
                . ' AND '
                . ' mi.`status` = \'show\' '
                . PHP_EOL . ' UNION '
                . ' SELECT 
                    `m`.`id`,
                    `m`.`status`,
                    `m`.`jobman`,
                    `m`.`sale_point`,
                    `m`.`date_now` `date`,
                    `m`.`summa`,
                    \'\' `var`,
                    `m`.`text`,
                    `m`.`auto_bonus_zp`,
                    \'plus\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , \'\' oborot_day_id '
                . ' , \'\' oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM `mod_072_plus` `m`
                WHERE '
                . $jms
                . ' AND '
                . ' `m`.`date_now` BETWEEN :date_start AND :date_finish '
                . ' AND '
                . ' m.`status` = \'show\' '
                . PHP_EOL . ' UNION ALL '
//
////
                . PHP_EOL . 'SELECT 
                    `id`,
                    `status`,
                    `jobman`,
                    `sale_point`,
                    `date_to` `date`,
                    \'\' `summa`,
                    \'\' `var`,
                    `comment` `text`,
                    \'\' as `auto_bonus_zp`,
                    \'comment\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , \'\' oborot_day_id '
                . ' , \'\' oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM `mod_073_comments` '
                . ' WHERE '
                . $jms
                . ' AND '
                . ' `date_to` BETWEEN :date_start AND :date_finish '
                . ' AND '
                . ' `status` = \'show\' '
//                
                . PHP_EOL . ' UNION ALL '
//
                . PHP_EOL . 'SELECT 
                    `id`,
                    `status`,
                    \'\' `jobman`,
                    `sale_point`,
                    date `date`,
                    \'\' `summa`,
                    \'\' `var`,
                    \'\' `text`,
                    \'\' as `auto_bonus_zp`,
                    \'oborot\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , id oborot_day_id '
                . ' , date oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , ( CASE 
                        WHEN oborot_hand > 0 THEN oborot_hand
                        WHEN oborot_server_hand > 0 THEN oborot_server_hand
                        WHEN oborot_server > 0 THEN oborot_server
                        ELSE NULL END ) as oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM `mod_sale_point_oborot` '
                . ' WHERE '
                . ' `date` BETWEEN :date_start AND :date_finish '
                . (!empty($sp) ? ' AND `sale_point` = :sp ' : '' )
                . ' AND '
                . ' `status` = \'show\' '

//                
                . PHP_EOL . ' UNION ALL '
//
                . PHP_EOL . 'SELECT 
                    `id`,
                    `status`,
                    \'\' `jobman`,
                    `sale_point`,
                    `date`,
                    \'\' `summa`,
                    \'\' `var`,
                    \'\' `text`,
                    \'\' as `auto_bonus_zp`,
                    \'norms\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , id oborot_day_id '
                . ' , date oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , vuruchka 	'
                . ' , time_wait_norm_cold '
                . ' , time_wait_norm_hot '
                . ' , time_wait_norm_delivery '
                . ' , procent_oplata_truda_on_oborota '
                . ' , kolvo_hour_in1smena '
                . ' , vuruchka_on_1_hand '
                . ' , \'\' cold '
                . ' , \'\' delivery '
                . ' FROM `mod_sale_point_parametr` '
                . ' WHERE '
                . ' `date` BETWEEN :date_start AND :date_finish '
                . (!empty($sp) ? ' AND `sale_point` = :sp ' : '' )
                . ' AND '
                . ' `status` = \'show\' '

//                
                . PHP_EOL . ' UNION ALL '
//
                . PHP_EOL . 'SELECT 
                    `id`,
                    `status`,
                    \'\' `jobman`,
                    `sale_point`,
                    `date`,
                    \'\' `summa`,
                    \'\' `var`,
                    \'\' `text`,
                    \'\' as `auto_bonus_zp`,
                    \'timeo\' as `type` '
                . ' , \'\' as `start` '
                . ' , \'\' as `fin` '
                . ' , \'\' as `who_add_item` '
                . ' , \'\' as `ocenka_auto` '
                . ' , \'\' as `ocenka` '
                . ' , \'\' as `hour_on_job` '
//                . ' , \'\' as `hour_on_job_hand` '
                . ' , \'\' as position_id '
                . ' , \'\' as position_date '
                . ' , \'\' as position_date_finish '
                . ' , \'\' as position_d '
                . ' , \'\' as position_sp '
                . ' , \'\' as position_calc_auto '
                . ' , \'\' pay_id '
                . ' , \'\' pay_date '
                . ' , \'\' pay5 '
                . ' , \'\' pay3 '
                . ' , \'\' pay_if_kurit '
                . ' , \'\' pay_base '
                . ' , \'\' `pay_premiya-5` '
                . ' , \'\' `pay_premiya-3` '
                . ' , \'\' pay_oborot_sp_last_monht_bolee '
                . ' , \'\' pay_bonus_proc_from_oborot '
                . ' , \'\' pay_pay_from_day_oborot_bolee '
                . ' , \'\' oborot_day_id '
                . ' , \'\' oborot_day_date '
                . ' , \'\' oborot_day_hand '
                . ' , \'\' oborot_day_server '
                . ' , \'\' oborot_day '
                . ' , \'\' spec_id '
                . ' , \'\' spec_date '
                . ' , \'\' spec_d '
                . ' , \'\' spec_sp '
                . ' , \'\' s_pay_id '
                . ' , \'\' s_pay_d '
                . ' , \'\' s_pay_date '
                . ' , \'\' s_pay5 '
                . ' , \'\' s_pay3 '
                . ' , \'\' s_pay_if_kurit '
                . ' , \'\' s_pay_base '
                . ' , \'\' `s_pay_premiya-5` '
                . ' , \'\' `s_pay_premiya-3` '
                . ' , \'\' s_pay_oborot_sp_last_monht_bolee '
                . ' , \'\' s_pay_bonus_proc_from_oborot '
                . ' , \'\' s_pay_pay_from_day_oborot_bolee '
                . ' , \'\' spec_oborot_day '
                . ' , \'\' spec_bonus '
                . ' , \'\' vuruchka 	'
                . ' , \'\' time_wait_norm_cold '
                . ' , \'\' time_wait_norm_hot '
                . ' , \'\' time_wait_norm_delivery '
                . ' , \'\' procent_oplata_truda_on_oborota '
                . ' , \'\' kolvo_hour_in1smena '
                . ' , \'\' vuruchka_on_1_hand '
                . ' , cold '
                . ' , delivery '
                . ' FROM `mod_074_time_expectations_list` '
                . ' WHERE '
                . ' `date` BETWEEN :date_start AND :date_finish '
                . (!empty($sp) ? ' AND `sale_point` = :sp ' : '' )
                . ' AND '
                . ' `status` = \'show\' '

        ;
        //\f\pa($sql);
        $ff = $db->prepare($sql);
        $ff->execute($in_sql);
        $return['actions'] = $ff->fetchAll();


        if (isset($_REQUEST['show_info'])) {
            foreach ($return as $k => $v) {
                \f\pa($v, 2, '', 'return data[' . $k . ']');
            }
            \f\pa(\f\timer_stop(111));
        }

        return \f\end3('ok ' . \f\timer_stop(111), true, $return);
    }

    /**
     * получаем смены, назначения и зарплаты сотрудников на точке
     * @param type $db
     * @param type $d_start
     * @param type $d_finish
     * @param type $sp
     */
    public static function getSmensJobmansOnSp($db, $date_start, $date_finish, $sp_id) {

        try {

            if (empty($_REQUEST['date_start']))
                throw new \Exception('не определена дата выборки', __LINE__);

            if (empty($_REQUEST['sp']))
                throw new \Exception('не определена точка продаж', __LINE__);

            $d_start = $_REQUEST['date_start'];
            $d_finish = $_REQUEST['date_finish'] ?? date('Y-m-d', date(strtotime($_REQUEST['date_start'] . ' +1 month ')));

            $result = [
                    // 'in' => $_REQUEST
            ];

            $result['who_is_job'] = self::getPeriodWhereJobMans($db, $date_start, $date_finish, $sp_id);
            $result['jobmans'] = array_keys($result['who_is_job']);

            \Nyos\mod\items::$between_datetime['start'] = [date('Y-m-d 05:00:00', strtotime($d_start)), date('Y-m-d 05:00:00', strtotime($d_finish . ' + 1day '))];
            \Nyos\mod\items::$search['jobman'] = $result['jobmans'];
            $result['checks'] = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);

            return \f\end3($r ?? 'x', true, $result);
        } catch (\Exception $ex) {

            return \f\end3('error', false, $ex);
        }









        // \f\end2('ok', true, [ 'res' => $_REQUEST ] );
        //    $d_start = '';
        //    $d_finish = '';
        //
        //    if (!empty($_REQUEST['d']))
        //        foreach ($_REQUEST['d'] as $k => $v) {
        //
        //            if (empty($d_start))
        //                $d_start = $v['date_start'];
        //
        //            if (empty($d_finish))
        //                $d_finish = $v['date_stop'];
        //
        //            if (!empty($d_start) && !empty($v['date_start']) && $d_start > $v['date_start'])
        //                $d_start = $v['date_start'];
        //
        //            if (!empty($d_finish) && !empty($v['date_stop']) && $d_finish < $v['date_stop'])
        //                $d_finish = $v['date_stop'];
        //        }

        if (!empty($d_start) && !empty($d_finish))
            \Nyos\mod\items::$between_datetime['start'] = [date('Y-m-d 05:00:00', strtotime($d_start)), date('Y-m-d 05:00:00', strtotime($d_finish . ' + 1day '))];

        $sp = '';

        $sps_list = [];





        if (strpos($_REQUEST['jobman'], '|') !== false) {

            // \f\pa($_REQUEST['jobman'],'','','jobmans');
            $jmans = explode('|', $_REQUEST['jobman']);
            // \f\pa($e, '', '', 'jobmans');

            foreach ($jmans as $k) {
                \Nyos\mod\items::$search['jobman'][$k] = 1;
            }
        } else {

            $jobmans = [];

            if (!empty($_REQUEST['d']))
                foreach ($_REQUEST['d'] as $k => $v) {

                    if (!isset($sps_list[$v['sp']]))
                        $sps_list[$v['sp']] = 1;

                    if (empty($sp) && isset($v['sp']))
                        $sp = $v['sp'];

                    // \f\pa($v);
                    if (isset($v['jobman'])) {

                        \Nyos\mod\items::$search['jobman'][] = $v['jobman'];
                        $jobmans[$v['jobman']] = 1;
                    }
                }

            \Nyos\mod\items::$search['jobman'] = $_REQUEST['jobman'];
        }

        // \f\pa(\Nyos\mod\items::$search);
        // \Nyos\mod\items::$show_sql = true;
        $checks0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);

        // \f\pa($checks0);

        $checks = [];

        foreach ($checks0 as $k => $v) {

            if (!isset($v['start']))
                continue;

            $v['sp'] = $sp;
            $v['s'] = \Nyos\Nyos::creatSecret('hour_' . $v['id']);
            $checks[date('Y-m-d', strtotime($v['start']))][] = $v;
        }

        $job_on = [];
        foreach ($sps_list as $sp => $v) {
            $job_on[$sp] = \Nyos\mod\JobDesc::getPeriodWhereJobMans($db, $d_start, $d_finish, $sp);
        }


        //    \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($d_start . ' -6 month')), $d_finish];
        $dolgn = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_dolgn);

        if (!empty($d_start) && !empty($d_finish)) {
            \Nyos\mod\items::$between_date['date'] = [date('Y-m-d', strtotime($d_start . ' -6 month')), $d_finish];
            $salary0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_salary);

            foreach ($salary0 as $k => $v) {
                $salary[$v['dolgnost']][$v['date']] = $v;
            }
        }

        //    \f\end2('ок', true, [
        //        'in' => $_REQUEST,
        //        'checks' => $checks,
        //        'job_on' => $job_on,
        //        'dolgn' => $dolgn,
        //        'dolgn_money' => ( $salary ?? null )
        //    ]);
        //
        //    \f\pa($sps);

        ob_start('ob_gzhandler');

        // \f\pa($_REQUEST);

        $sps = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_sale_point);
        // \f\pa($sps,2);

        $d = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_dolgn);
        // \f\pa($d,2);

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'jobman\' '
                . ' AND mid.value = :user ';
        \Nyos\mod\items::$var_ar_for_1sql[':user'] = $_REQUEST['user'];
        $naznach = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_man_job_on_sp, '');
        // \f\pa($e,2,'','nazn');

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'jobman\' '
                . ' AND mid.value = :user ';
        \Nyos\mod\items::$var_ar_for_1sql[':user'] = $_REQUEST['user'];
        $spec = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_spec_jobday, '');
        // \f\pa($spec, 2, '', 'spec');

        foreach ($spec as $k => $v) {
            $v['type'] = 'spec';
            $naznach[] = $v;
        }

        usort($naznach, "\\f\\sort_ar_date");

        //\f\pa($naznach, 2, '', '$naznach');

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'jobman\' '
                . ' AND mid.value = :user ';
        \Nyos\mod\items::$var_ar_for_1sql[':user'] = $_REQUEST['user'];

        // echo $_REQUEST['date_start'];
        if (isset($_REQUEST['date_start'])) {

            \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` mid2 '
                    . ' ON mid2.id_item = mi.id '
                    . ' AND mid2.name = \'start\' '
                    . ' AND mid2.value_datetime >= :ds '
                    . ' AND mid2.value_datetime <= :df ';
            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-01 05:00:00', strtotime($_REQUEST['date_start']));
            \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d 03:00:00', strtotime(\Nyos\mod\items::$var_ar_for_1sql[':ds'] . ' +1 month'));
        }

        $checks = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks, '');
        usort($checks, "\\f\\sort_ar_date");

        echo
        // '<link rel="stylesheet" href="/didrive/design/css/vendor/bootstrap.min.css" />'

        '<style> '
        . ' .d345 th, '
        . ' .d345 tbody td{ text-align: center; } '
        . ' .d345 tbody td.r{ text-align: right; } '
        . '</style>'
        . '<table class="table table-bordered d345" >'
        . '<thead>'
        . '<tr>'

        //    . '<th>статус записи</th>'
        //    . '<th>тип</th>'
        //    . '<th>точка продаж</th>'
        //    . '<th>должность</th>'
        //    . '<th>принят</th>'
        //    . '<th>уволен</th>'
        . '<th>старт</th>'
        . '<th>конец</th>'
        . '<th>длительность (авто/вручную)</th>'
        . '<th>оценка (авто/вручную)</th>'
        . '<th>тех. статус</th>'
        . '</tr>'
        . '</thead>'
        . '<tbody>';

        $date_start = date('Y-m-01', strtotime($_REQUEST['date_start']));
        $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));


        $last_dolgn = [];

        foreach ($naznach as $k => $v) {

            if (isset($v['type']) && $v['type'] == 'spec')
                continue;

            if ($v['date'] < $date_start) {
                $last_dolgn = $v;
                //\f\pa($v);
            }
        }


        if (!empty($last_dolgn)) {
            $v = $last_dolgn;
            echo '<tr>'

            //                . '<td>' . ( isset($v['status']) && $v['status'] == 'show' ? 'вкл' : ( isset($v['status']) && $v['status'] == 'hide' ? 'выкл' : ( isset($v['status']) && $v['status'] == 'delete' ? 'удалено' : 'x' ) ) ) . '</td>'
            . '<td>' . $v['date'] . ' ' . ((isset($v['type']) && $v['type'] == 'spec') ? 'спец. назначение' : 'приём на должность') . '</td>'
            //                . '<td>' . ( $sps[$v['sale_point']]['head'] ?? 'не определена' ) . '</td>'
            //                . '<td class="r" >' . $v['date'] . '</td>'
            . '<td class="r" >' . ($v['date_finish'] ?? '-') . '</td>'
            . '<td colspan=2 >' . ($d[$v['dolgnost']]['head'] ?? '- - -') . '</td>'
            //                . '<td>&nbsp;</td>'
            . '<td>'
            . ($v['status'] == 'show' ? 'норм' : ($v['status'] == 'hide' ? 'отменено' : ($v['status'] ?? 'x')))
            . '</td>'
            . '</tr>';
        }

        for ($nn = 0; $nn <= 31; $nn++) {

            $date_now = date('Y-m-d', strtotime($date_start . ' +' . $nn . ' day'));
            /**
             * старт дня ( дата время )
             */
            $datetime_start = $date_now . ' 08:00:00';
            /**
             * конец дня ( дата время )
             */
            $datetime_finish = date('Y-m-d 03:00:00', strtotime($datetime_start . ' +1 day'));

            echo '<tr>'
            . '<td colspan=5 >' . $date_now . '</td>'
            . '</tr>';

            foreach ($naznach as $k => $v) {
                if ($v['date'] == $date_now) {
                    echo '<tr>'

                    //                . '<td>' . ( isset($v['status']) && $v['status'] == 'show' ? 'вкл' : ( isset($v['status']) && $v['status'] == 'hide' ? 'выкл' : ( isset($v['status']) && $v['status'] == 'delete' ? 'удалено' : 'x' ) ) ) . '</td>'
                    . '<td>' . $v['date'] . ' ' . ((isset($v['type']) && $v['type'] == 'spec') ? 'спец. назначение' : 'приём на должность') . '</td>'
                    //                . '<td>' . ( $sps[$v['sale_point']]['head'] ?? 'не определена' ) . '</td>'
                    //                . '<td class="r" >' . $v['date'] . '</td>'
                    . '<td class="r" >' . ($v['date_finish'] ?? '-') . '</td>'
                    . '<td colspan=2 >' . ($d[$v['dolgnost']]['head'] ?? '- - -') . '</td>'
                    //                . '<td>&nbsp;</td>'
                    //                . '<td>&nbsp;</td>'
                    . '<td>';
                    // $v['status']

                    if ($v['status'] == 'show') {
                        echo 'норм';
                    } else if ($v['status'] == 'hide') {
                        echo 'отменено';
                    }

                    // echo ( $v['status'] == 'show' ? 'норм' : ( $v['status'] == 'hide' ? 'отменено' : ( $v['status'] ?? 'x' ) ) );


                    echo '
                <span class="action">
                    <div onclick=\'$("#but_{{ v1.id }}").show();\' >

                        <b>Статус:</b>
                        <span id="' . $v['id'] . '" >'
                    . ($v['status'] == 'show' ? 'видно' : ($v['status'] == 'hide' ? 'отменено' : ($v['status'] == 'delete' ? 'удалено' : ($v['status'] ?? 'x'))))
                    . '</span>
                    </div>

                        <input class="edit_item" type="button" alt="status" rev="show" '
                    . ' value="показать" '
                    . ' rel="' . $v['id'] . '" '
                    . ' s=\'' . \Nyos\Nyos::creatSecret($v['id']) . '\' '
                    . ' for_res="shows' . $v['id'] . '" '
                    . ' />11
                    <input class="edit_item" type="button" rel="' . $v['id'] . '" alt="status" rev="hide" value="скрыть"  s=\'{{ creatSecret(v1.id) }}\' for_res="shows{{ v1.id }}"  />
                    <input class="edit_item" type="button" rel="' . $v['id'] . '" alt="status" rev="delete" s=\'{{ creatSecret(v1.id) }}\' for_res="shows{{ v1.id }}" value="Удалить" />
                </span>';

                    echo '</td>'
                    . '</tr>';
                }
            }

            foreach ($checks as $k => $v) {
                if ($v['start'] >= $datetime_start && $v['start'] <= $datetime_finish) {

                    // \f\pa($v);
                    echo '<tr>'
                    //        . '<td>' . ( isset($v['status']) && $v['status'] == 'show' ? 'вкл' : ( isset($v['status']) && $v['status'] == 'hide' ? 'выкл' : ( isset($v['status']) && $v['status'] == 'delete' ? 'удалено' : 'x' ) ) ) . '</td>'
                    //        . '<td>' . ( ( isset($v['type']) && $v['type'] == 'spec' ) ? 'спец. назначение' : 'приём' ) . '</td>'
                    //        . '<td>' . ( $sps[$v['sale_point']]['head'] ?? 'не определена' ) . '</td>'
                    //        . '<td>' . ( $d[$v['dolgnost']]['head'] ?? '- - -' ) . '</td>'
                    //        . '<td class="r" >' . $v['date'] . '</td>'
                    //                . '<td xclass="r" >';
                    //                \f\pa($v);
                    //                echo '</td>'
                    . '<td class="c" >'
                    . ($v['start'] ?? '-')
                    . '</td>'
                    // . '<td class="r" >'
                    . '<td class="c" >'
                    . ($v['fin'] ?? 'x')
                    . '</td>'
                    . '<td class="r" >' . (!empty($v['hour_on_job_hand']) ? '<strike style="color:gray;" >' . ($v['hour_on_job'] ?? '-') . '</strike> <b>' . $v['hour_on_job_hand'] . '</b>' : ($v['hour_on_job'] ?? 'x')) . '</td>'
                    . '<td class="r" >' .
                    (!empty($v['ocenka']) ? '<strike style="color:gray;" >' . ($v['ocenka_auto'] ?? '-') . '</strike> <b>' . $v['ocenka'] . '</b>' : ($v['ocenka_auto'] ?? '-')) . '</td>'
                    //                . '<td>&nbsp;</td>'
                    . '<td>'
                    // . ( $v['status'] == 'show' ? 'норм' : $v['status'] )
                    . '
                                        <span class="action">
                                            <div onclick=\'$("#but_{{ v1.id }}").show();\' >

                                                <b>Статус:</b>
                                                <span id="shows_22_' . $v['id'] . '" >'
                    . ($v['status'] == 'show' ? 'норм' : ($v['status'] == 'hide' ? 'отменено' : ($v['status'] == 'delete' ? 'удалено' : ($v['status'] ?? 'x'))))
                    . '</span>'
                    . ' <div id="shows_22r_' . $v['id'] . '" class="bg-warning" style="padding:5px 10px;display:none;" ><a href="">обновите страницу</a> для просмотра изменений в графике</div>'
                    . '</div>

                                            <input class="edit_item" type="button" alt="status" '
                    . ' rev="show" '
                    . ' value="показать" '
                    . ' rel="' . $v['id'] . '" '
                    . ' s=\'' . \Nyos\Nyos::creatSecret($v['id']) . '\' '
                    . ' for_res="shows_22_' . $v['id'] . '" '
                    . ' onclick="$(\'#shows_22r_' . $v['id'] . '\').show(\'slow\');" '
                    . ' />
                                            <input class="edit_item" type="button" '
                    . ' value="скрыть" '
                    . ' alt="status" rev="hide" '
                    . ' rel="' . $v['id'] . '" '
                    . ' s=\'' . \Nyos\Nyos::creatSecret($v['id']) . '\' '
                    . ' for_res="shows_22_' . $v['id'] . '" '
                    . ' onclick="$(\'#shows_22r_' . $v['id'] . '\').show(\'slow\');" '
                    . ' />
                    '
                    //                   .' <input class="edit_item" type="button" '
                    //                . ' alt="status" '
                    //                . ' rev="delete" '
                    //                . ' value="Удалить" '
                    //                . ' rel="' . $v['id'] . '" '
                    //                . ' s=\'' . \Nyos\Nyos::creatSecret($v['id']) . '\' '
                    //                . ' for_res="shows_22_' . $v['id'] . '" '
                    //                . ' onclick="$(\'#shows_22r_' . $v['id'] . '\').show(\'slow\');" '
                    //                . ' /> '
                    . ' </span>
'
                    . '</td>'
                    . '</tr>';
                }
            }
        }


        echo '</tbody></table>'
        . '<center>'
        . '<p>Если у записи нет увольнения, датой уволнениния считается дата ( -1 день назад ) от следующего приёма на работу</p>'
        . '</center>';


        $r = ob_get_contents();
        ob_end_clean();

        $timer = \f\timer_stop(78);








        return \f\end3('смены сотрудников на точке за период времени', true, $result);
    }

    /**
     * удаляем кеш если что то меняли
     * / кеш суммы часов за сутки
     * @param type $date
     * @param type $sp_id
     */
    public static function refreshCashHoursOnJob($date = null, $sp_id = null) {

        $ar = [];

        if (!empty($date))
            $ar[] = 'date' . $date;

        if (!empty($sp_id))
            $ar[] = 'sp' . $sp_id;

        \f\Cash::deleteKeyPoFilter($ar);
    }

    /**
     * кто где готовит
     * @param type $db
     * @param type $sp
     * @param type $date
     * @param type $list_jobman
     * @return boolean
     */
    public static function whoWhereCoocking($db, $sp, $date) {

        // \f\pa(__FUNCTION__);
        \f\timer_start(57);

        if (empty($date))
            $date = date('Y-m-d');

        $sql_in = [
            ':date_start' => date('Y-m-01', strtotime($date)),
            ':date_finish' => date('Y-m-d', strtotime($date . ' +1 month -1 day')),
//            ':dt_start' => date('Y-m-01 08:00:00', strtotime($date) ),
//            ':dt_finish' => date('Y-m-d 03:00:00', strtotime($date . ' +1 month')),
            ':sp' => $sp
        ];

        $sql = 'SELECT '
                . ' jm.id jobman '
//                . ' , '
//                . ' send.* '
                . ' , '
                . ' send.id send_id '
                . ' , '
                . ' send.sale_point send_sp '
                . ' , '
                . ' send.dolgnost send_d '
                . ' , '
                . ' send.date send_date '
                . ' , '
                . ' send.date_finish send_date_finish '
                . ' , '
                . ' send.smoke send_smoke '
                . ' , '
                . ' spec.id spec_id '
//                . ' , '
//                . ' spec.head spec_head '
//                . ' , '
//                . ' spec.sort spec_id '
//                . ' , '
//                . ' spec.status spec_id '
//                . ' , '
//                . ' spec.jobman spec_id '
                . ' , '
                . ' spec.sale_point spec_sp '
                . ' , '
                . ' spec.date spec_date '
                . ' , '
                . ' spec.dolgnost spec_d '
                . ' , '
                . ' spec.sale_point_from spec_from_sp '
                . ' , '
                . ' spec.dolgnost_from spec_from_d '
                . ' , '
                . ' spec.smoke spec_smoke '
                . ' FROM `mod_070_jobman` `jm` '
                //
                . ' LEFT JOIN `mod_050_job_in_sp` spec '
                . ' ON '
                . ' `spec`.`jobman` = `jm`.`id` '
                // . ' AND `spec`.`sale_point` = :sp '
                . ' AND `spec`.`status` = \'show\' '
                . ' AND `spec`.`date` BETWEEN :date_start AND :date_finish '
                //
                . ' LEFT JOIN `mod_jobman_send_on_sp` send '
                . ' ON '
                . ' `send`.`jobman` = `jm`.`id` '
                . ' AND `send`.`sale_point` = :sp '
                . ' AND `send`.`status` = \'show\' '
                . ' AND `send`.`date` <= :date_finish '
                . ' AND ( 
                        send.date_finish >= :date_start  
                        OR send.date_finish IS NULL 
                        ) '
                . ' WHERE '
                . ' jm.status = \'show\' '
        // . ' GROUP BY jm.id '
        ;

        $ff = $db->prepare($sql);
        $ff->execute($sql_in);

        $res = [];
        while ($v = $ff->fetch()) {

            if (empty($v['jobman']))
                continue;

            // if (!empty($v['spec_on']) || !empty($v['send_on'])) {
            // \f\pa($v);
            if (isset($v['spec_id'])) {
                $res['sp_jm'][( $v['send_sp'] ?? $v['spec_sp'] )][$v['jobman']]['spec'][] = $v['spec_id'];
                $res['jm_sp'][$v['jobman']][( $v['send_sp'] ?? $v['spec_sp'] )]['spec'][] = $v['spec_id'];
            }
            if (isset($v['send_id'])) {
                $res['sp_jm'][( $v['send_sp'] ?? $v['spec_sp'] )][$v['jobman']]['send'][] = $v['send_id'];
                $res['jm_sp'][$v['jobman']][( $v['send_sp'] ?? $v['spec_sp'] )]['send'][] = $v['send_id'];
            }
            // }

            $res['send'][$v['send_id']] = $v;
            $res['spec'][$v['spec_id']] = $v;
        }

        return \f\end3('достали ' . sizeof($res) . ' / ' . \f\timer_stop(57), true, $res);
    }

    /**
     * получаем сумму оборота за месяц по точке продаж
     * @param type $db
     * @param type $sp
     * @param type $str_date
     * @param type $module_sp
     * @param type $module_oborot
     * @return type
     */
    public static function calcDayBudget($db, $sp, $date, $list_jobman) {

        try {

//            \f\pa(__FUNCTION__);
//            \f\pa($list_jobman, 2);

            \f\timer_start(57);

            $sql_in = [
                ':date_start' => date('Y-m-01 08:00:00', strtotime($date)),
                ':date_finish' => date('Y-m-d 03:00:00', strtotime($date . ' +1 month')),
                ':sp' => $sp
            ];

            // $now_year = 2020;
            $now_year = date('Y', strtotime($date));
            // $now_month = '07';
            $now_month = date('m', strtotime($date));

            $sql = 'SELECT '

                    // . ' * '
                    // . ' , '
                    . ' DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) date '
                    . ' , '
                    . ' check.jobman '
                    //. ' , '
                    //. ' spec.date spec_date '
                    . ' , spec.sale_point spec_sp '
                    . ' , spec.dolgnost spec_d '
                    //. ' , '
                    //. ' send.date send_d_start '
                    . ' , send.sale_point send_sp '
                    . ' , send.jobman send_jm '
                    . ' , send.dolgnost send_d '
                    . ' , send.date send_date '
                    // . ' , send.* '
                    . ' , '
                    . ' ( 
                    CASE 
                    WHEN check.hour_on_job_hand IS NOT NULL THEN check.hour_on_job_hand
                    WHEN check.hour_on_job IS NOT NULL THEN check.hour_on_job
                    ELSE NULL 
                    END ) as check_hour_on_job '
                    . ' , '
                    . ' ( 
                    CASE 
                    WHEN check.ocenka IS NOT NULL THEN check.ocenka
                    WHEN check.ocenka_auto IS NOT NULL THEN check.ocenka_auto
                    ELSE NULL 
                    END ) as check_ocenka '



// сумма за день             
// amount_day
                    . ( 1 == 1 ? ' , ( CASE '

// оценка рук 5
// часы рук
                    . ' WHEN 
                        check.ocenka IS NOT NULL AND check.ocenka = 5 
                        AND salary.`ocenka-hour-5` IS NOT NULL
                        AND check.hour_on_job_hand IS NOT NULL
                        THEN check.hour_on_job_hand*salary.`ocenka-hour-5` '
// оценка рук 5
// часы авто
                    . ' WHEN 
                        check.ocenka IS NOT NULL AND check.ocenka = 5 
                        AND salary.`ocenka-hour-5` IS NOT NULL
                        AND check.hour_on_job IS NOT NULL
                        THEN check.hour_on_job*salary.`ocenka-hour-5` '

// оценка авто 5
// часы рук
                    . ' WHEN 
                        check.ocenka_auto IS NOT NULL AND check.ocenka_auto = 5 
                        AND salary.`ocenka-hour-5` IS NOT NULL
                        AND check.hour_on_job_hand IS NOT NULL
                        THEN check.hour_on_job_hand*salary.`ocenka-hour-5` '
// оценка auto 5
// часы авто
                    . ' WHEN 
                        check.ocenka_auto IS NOT NULL AND check.ocenka_auto = 5 
                        AND salary.`ocenka-hour-5` IS NOT NULL
                        AND check.hour_on_job IS NOT NULL
                        THEN check.hour_on_job*salary.`ocenka-hour-5` '


// оценка рук 3
// часы рук
                    . ' WHEN 
                        check.ocenka IS NOT NULL AND check.ocenka = 3 
                        AND salary.`ocenka-hour-3` IS NOT NULL
                        AND check.hour_on_job_hand IS NOT NULL
                        THEN check.hour_on_job_hand*salary.`ocenka-hour-3` '
// оценка рук 3
// часы авто
                    . ' WHEN 
                        check.ocenka IS NOT NULL AND check.ocenka = 3 
                        AND salary.`ocenka-hour-3` IS NOT NULL
                        AND check.hour_on_job IS NOT NULL
                        THEN check.hour_on_job*salary.`ocenka-hour-3` '

// оценка авто 3
// часы рук
                    . ' WHEN 
                        check.ocenka_auto IS NOT NULL AND check.ocenka_auto = 3 
                        AND salary.`ocenka-hour-3` IS NOT NULL
                        AND check.hour_on_job_hand IS NOT NULL
                        THEN check.hour_on_job_hand*salary.`ocenka-hour-3` '
// оценка auto 3
// часы авто
                    . ' WHEN 
                        check.ocenka_auto IS NOT NULL AND check.ocenka_auto = 3 
                        AND salary.`ocenka-hour-3` IS NOT NULL
                        AND check.hour_on_job IS NOT NULL
                        THEN check.hour_on_job*salary.`ocenka-hour-3` '

// оплата фикс час 
// часы рук
                    . ' WHEN 
                        salary.`ocenka-hour-base` IS NOT NULL
                        AND check.hour_on_job_hand IS NOT NULL
                        THEN check.hour_on_job_hand*salary.`ocenka-hour-base` '
// оплата фикс час 
// часы авто
                    . ' WHEN 
                        salary.`ocenka-hour-base` IS NOT NULL
                        AND check.hour_on_job IS NOT NULL
                        THEN check.hour_on_job*salary.`ocenka-hour-base` '
                    . '
                    ELSE NULL 
                    END ) as amount_day ' : '' )

//.'                
//                    WHEN check.hour_on_job_hand IS NOT NULL THEN check.hour_on_job_hand
//                    WHEN check.hour_on_job IS NOT NULL THEN check.hour_on_job
// '               
                    . ' , `check`.start '
                    . ' , `check`.fin '
                    // . ' , `check`.* '
                    // 
// сумма за смену
//                . ' , '
//                . ' ( 
//                    CASE '
//                . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 5 AND check.hour_on_job_hand IS NOT NULL THEN salary.`ocenka-hour-5`*check.hour_on_job_hand '
//                . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 3 AND check.hour_on_job_hand IS NOT NULL THEN salary.`ocenka-hour-3`*check.hour_on_job_hand '
//                . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 5 AND check.hour_on_job_hand IS NOT NULL THEN salary.`ocenka-hour-5`*check.hour_on_job_hand '
//                . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 3 AND check.hour_on_job_hand IS NOT NULL THEN salary.`ocenka-hour-3`*check.hour_on_job_hand '
//                . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 5 AND check.hour_on_job IS NOT NULL THEN salary.`ocenka-hour-5`*check.hour_on_job '
//                . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 3 AND check.hour_on_job IS NOT NULL THEN salary.`ocenka-hour-3`*check.hour_on_job '
//                . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 5 AND check.hour_on_job IS NOT NULL THEN salary.`ocenka-hour-5`*check.hour_on_job '
//                . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 3 and check.hour_on_job IS NOT NULL THEN salary.`ocenka-hour-3`*check.hour_on_job '
//                . ' ELSE NULL 
//                    END ) as check_amount '
// дневной оборот
                    . ' , ( CASE '
                    . ' WHEN oborot.oborot_hand IS NOT NULL THEN oborot.oborot_hand '
                    . ' WHEN oborot.oborot_server IS NOT NULL THEN oborot.oborot_server '
                    . ' ELSE NULL 
                    END ) as oborot_day '

// сумма бонуса
                    . ' , ( CASE '

// % от оборота
                    . ' WHEN 
                        oborot.oborot_hand IS NOT NULL 
                        AND salary.bonus_proc_from_oborot IS NOT NULL 
                        THEN CEILING( oborot.oborot_hand / 100 * salary.bonus_proc_from_oborot ) '
                    . ' WHEN 
                        oborot.oborot_server IS NOT NULL 
                        AND salary.bonus_proc_from_oborot IS NOT NULL 
                        THEN CEILING( oborot.oborot_server / 100 * salary.bonus_proc_from_oborot ) '


//                // фикс от оценки
                    . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 3 AND salary.`premiya-3` IS NOT NULL THEN salary.`premiya-3` '
                    . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 3 AND salary.`premiya-3` IS NOT NULL THEN salary.`premiya-3` '
                    . ' WHEN check.ocenka IS NOT NULL and check.ocenka = 5 AND salary.`premiya-5` IS NOT NULL THEN salary.`premiya-5` '
                    . ' WHEN check.ocenka_auto IS NOT NULL and check.ocenka_auto = 5 AND salary.`premiya-5` IS NOT NULL THEN salary.`premiya-5` '
//
                    . ' ELSE \'00\'
                    END ) as bonus_amount '

// оборот месячный
                    . ' , '
                    . ' ( 
                    CASE '
                    . ' WHEN oborot_m_spec.oborot IS NOT NULL THEN oborot_m_spec.oborot '
                    . ' WHEN oborot_m.oborot IS NOT NULL THEN oborot_m.oborot '
                    . ' ELSE \'error\'
                    END ) as oborot_month '
                    . ' , oborot_m.oborot ob_m '
                    . ' , oborot_m_spec.oborot ob_m_spec '


// должность и зп
//                . ' , salary.date salary_date '
//                . ' , salary.dolgnost salary_d '
//                . ' , salary.sale_point salary_sp '
//                . ' , salary.`ocenka-hour-5` '
//                . ' , salary.`ocenka-hour-3` '
//                // . ' , salary.`ocenka-hour-4` '
//                // . ' , salary.`ocenka-hour-2` '
//                . ' , salary.if_kurit '
//                . ' , salary.`ocenka-hour-base` '
//                . ' , salary.`premiya-5` '
//                . ' , salary.`premiya-3` '
//                . ' , salary.oborot_sp_last_monht_bolee '
////                 . ' , salary.oborot_sp_last_monht_menee '
//                . ' , salary.bonus_proc_from_oborot '
//                . ' , salary.pay_from_day_oborot_bolee '
////                 . ' , salary.`premiya-4` '
////                 . ' , salary.`premiya-2` '
                    // . ' , salary.id sal_id '
                    . ' , salary.`ocenka-hour-base`	 '
                    . ' , salary.`ocenka-hour-5` '
                    . ' , salary.`ocenka-hour-3` '
                    . ' , salary.`premiya-5` '
                    . ' , salary.`premiya-3` '
                    . ' , salary.`bonus_proc_from_oborot` '
                    . ' , salary.oborot_sp_last_monht_bolee	 '
                    . ' , salary.pay_from_day_oborot_bolee	 '
                    . ' , salary.dolgnost sal_d '
                    . ' , salary.sale_point sal_sp '

//                . ' , '
//                . ' salary.* '
                    . ' FROM `mod_050_chekin_checkout` `check` '



                    // спец назначения
                    . ' LEFT JOIN `mod_050_job_in_sp` spec '
                    . ' ON '
                    . ' spec.jobman = check.jobman '
                    // . ' AND spec.sale_point = check.sale_point '
                    . ' AND spec.status = \'show\' '
                    . ' AND spec.date = DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) '


                    // норм назначения
                    . ' LEFT JOIN `mod_jobman_send_on_sp` send '
                    . ' ON '
                    . ' spec.sale_point IS NULL '
                    . ' AND send.status = \'show\' '
                    . ' AND send.jobman = check.jobman '
                    . ' AND send.sale_point = :sp '
                    . ' AND send.date = ( 
                            SELECT onsp.date 
                            FROM mod_jobman_send_on_sp onsp
                            WHERE onsp.status = \'show\' '
                    // .' AND sale_point = :sp  '
                    . ' AND onsp.jobman = check.jobman 
                                AND onsp.date <= DATE( DATE_ADD( check.start , INTERVAL -5 HOUR ) ) 
                            ORDER BY onsp.date DESC LIMIT 1 
                        ) '



// тащим оборот для норм назначения или спец назначения
                    . ' LEFT JOIN `mod_sale_point_oborot` oborot '
                    . ' ON '
                    . ' oborot.date = DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) '
                    . ' AND oborot.status = \'show\' '
                    . ' AND ( '
                    . ' ( '
                    . ' spec.sale_point IS NOT NULL AND oborot.sale_point = spec.sale_point '
                    . ' ) OR ( '
                    . ' send.sale_point IS NOT NULL AND oborot.sale_point = send.sale_point '
                    . ' ) '
                    . ' ) '

                    // тащим оборот для норм назначения или спец назначения
                    . ' LEFT JOIN `temp_oborot` oborot_m '
                    . ' ON '
                    . ' send.sale_point IS NOT NULL '
                    . ' AND '
                    . ' oborot_m.sale_point = send.sale_point '
                    . ' AND '
                    . ' oborot_m.date_y = YEAR( DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) ) '
                    . ' AND '
                    . ' oborot_m.date_m = MONTH( DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) ) '

                    // тащим оборот для норм назначения или спец назначения
                    . ' LEFT JOIN `temp_oborot` oborot_m_spec '
                    . ' ON '
                    . ' spec.sale_point IS NOT NULL '
                    . ' AND '
                    . ' oborot_m_spec.sale_point = spec.sale_point '
                    . ' AND '
                    . ' oborot_m_spec.date_y = YEAR( DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) ) '
                    . ' AND '
                    . ' oborot_m_spec.date_m = MONTH( DATE( DATE_ADD( check.start , INTERVAL -5 HOUR) ) ) '

                    // тащим оборот для норм назначения или спец назначения
                    // попытка собрать сумму оборота (неудачная)
//                . ' LEFT JOIN `mod_sale_point_oborot` oborot_m '
//                . ' ON '
//                . ' oborot_m.date LIKE \''.$now_year.'-'.$now_month.'-%\' '
//                . ' AND oborot_m.status = \'show\' '
//                . ' AND ( '
//                    . ' ( '
//                    . ' spec.sale_point IS NOT NULL AND oborot_m.sale_point = spec.sale_point '
//                    . ' ) OR ( '
//                    . ' send.sale_point IS NOT NULL AND oborot_m.sale_point = send.sale_point '
//                    . ' ) '
//                . ' ) '
                    // размер зарплаты
                    . ' LEFT JOIN `mod_071_set_oplata` salary '
                    . ' ON '
                    . ' salary.status = \'show\' '
                    . ' AND ( '
                    . ' ( '
                    . ' spec.sale_point IS NOT NULL '
                    . ' AND salary.sale_point = spec.sale_point '
                    . ' AND salary.dolgnost = spec.dolgnost '
                    . ' AND salary.date = ( '
                    . ' SELECT date FROM mod_071_set_oplata '
                    . ' WHERE status = \'show\' '
                    . ' AND sale_point = spec.sale_point '
                    . ' AND dolgnost = spec.dolgnost '
                    . ' AND date <= DATE( DATE_ADD( check.start , INTERVAL -5 HOUR ) ) '
                    . ' ORDER BY date DESC LIMIT 1 '
                    . ' ) '
                    . ' AND '
                    . ' ( '
                    . ' salary.oborot_sp_last_monht_bolee IS NULL '
                    . ' OR ( '
                    . ' oborot_m_spec.oborot IS NOT NULL '
                    . ' AND salary.oborot_sp_last_monht_bolee < oborot_m_spec.oborot '
                    . ' ) '
                    . ' ) '
                    . ' ) OR ( '
                    . ' send.sale_point IS NOT NULL '
                    . ' AND salary.sale_point = send.sale_point '
                    . ' AND salary.dolgnost = send.dolgnost '
                    . ' AND salary.date = ( SELECT date FROM mod_071_set_oplata '
                    . ' WHERE status = \'show\' '
                    . ' AND sale_point = send.sale_point '
                    . ' AND dolgnost = send.dolgnost '
                    . ' AND date <= DATE( DATE_ADD( check.start , INTERVAL -5 HOUR ) ) '
                    . ' ORDER BY date DESC LIMIT 1 '
                    . ' ) '
                    . ' AND ( '
                    . ' salary.oborot_sp_last_monht_bolee IS NULL '
                    . ' OR ( '
                    . ' oborot_m.oborot IS NOT NULL '
                    . ' AND salary.oborot_sp_last_monht_bolee < oborot_m.oborot '
                    . ' ) '
                    . ' ) '
                    . ' ) '
                    . ' ) '
                    . ' WHERE '

                    // . ' check.jobman = \'33211\' AND '
                    // . ' check.jobman = 101362  '
                    // . ' check.jobman = 241  '
                    // . ' check.jobman IN ( 241,33216 )  '
                    . (!empty($list_jobman) ? ' check.jobman IN ( ' . implode(',', $list_jobman) . ' ) AND ' : '' )
                    . ' check.start BETWEEN :date_start AND :date_finish '
//                . ' AND '
//                . ' salary.date = MAX(salary.date) '
                    . ' GROUP BY check.id '
                    . ' ORDER BY check.start ASC '
            ;

// $sql_in = [];
//        $ff2 = $db->prepare('FLUSH(;)');
//        $ff2->execute();
            $ff = $db->prepare($sql);
            $ff->execute($sql_in);
            //$ff->execute();
//        $res = $ff->fetchAll();
//        \f\pa($res);







            \Nyos\mod\items::$between_date['date'] = [
                date('Y-m-01', strtotime($date)),
                date('Y-m-d', strtotime($date . ' +1 month -1 day'))
            ];
            $metki0 = \Nyos\mod\items::get($db, '072.metki');
            // \f\pa($metki0);
            $metki = [];
            foreach ($metki0 as $k => $v) {
                $metki[$v['jobman']][$v['sale_point']][$v['date']][( $v['type2'] ?? $v['type'] ?? '-' )] = 1;
            }
            // \f\pa($metki, 2, '', 'metki');



            $add_bonuses = [];
            $one_bonus__jobman_sp_date = [];

            if (isset($_REQUEST['show_html']))
                echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">'
                . '<table class="table table-bordered table-stripped" >';
            $ii = 0;
            while ($r = $ff->fetch()) {

                if (isset($_REQUEST['show_html']))
                    if ($ii == 0) {
                        echo '<thead style="background-color: rgba(155,255,255,0.9); position: sticky; top: 20px; " ><tr>';
                        foreach ($r as $k => $v) {
                            echo '<td style="" >' . $k . '</td>';
                        }
                        echo '</tr></thead><tbody>';
                        $ii++;
                    }


//                if( $r['jobman'] != 33211 )
//                    continue;
                // \f\pa($r,2);
                // echo '<br/>'.$r['id'].' start '.$r['start'].' date '.$r['date_smena'].' '.$r['norm_date_start'].' spec sp '.( $r['spec_sp'] ?? '-' );

                if (isset($_REQUEST['show_html'])) {
                    echo '<tr>';
                    foreach ($r as $k => $v) {
                        echo '<td>' . $v . '</td>';
                    }
                    echo '</tr>';
                }

                if ($r['bonus_amount'] == '00')
                    continue;

                $sp = (!empty($r['spec_sp']) ? $r['spec_sp'] : (!empty($r['send_sp']) ? $r['send_sp'] : false ) );

                if ($sp === false)
                    continue;

                if (isset($_REQUEST['show_html']))
                    echo '<tr><td colspan="5">' . $sp . ' /  ' . $r['jobman'] . ' / ' . $r['date'];

                if (isset($one_bonus__jobman_sp_date[$r['jobman']][$sp][$r['date']])) {
                    if (isset($_REQUEST['show_html']))
                        echo 'пропускаем автобонус так как один уже добавили</td></tr>';
                    continue;
                }
                $one_bonus__jobman_sp_date[$r['jobman']][$sp][$r['date']] = 1;


                if (isset($metki[$r['jobman']][$sp][$r['date']]['no_autobonus'])) {
                    if (isset($_REQUEST['show_html']))
                        echo 'пропускаем автобонус так как метка отмены</td></tr>';
                    continue;
                }



                $bonus_money = $r['bonus_amount'];

                $text = '';

                if (isset($r['bonus_proc_from_oborot']{1}) && !empty($r['oborot_day'])) {
                    $text = '(' . $r['bonus_proc_from_oborot'] . '% от ' . $r['oborot_day'] . ')';
                    $bonus_money = ceil($r['oborot_day'] / 100 * $r['bonus_proc_from_oborot']);
                }

                if (isset($metki[$r['jobman']][$sp][$r['date']]['autobonus50pr'])) {
                    $text .= ' половина';
                    $bonus_money = ceil($bonus_money / 2);
                }

                $we = [
                    'auto_bonus_zp' => 'da',
                    'jobman' => $r['jobman'],
                    'sale_point' => $sp,
                    'date_now' => $r['date'],
                    'summa' => $bonus_money,
                    'text' => 'бонус к зп ' . $text
                ];
                //\f\pa($we);

                if (isset($_REQUEST['show_html']))
                    echo '</td></tr>';

                $add_bonuses[] = $we;
            }

            if (isset($_REQUEST['show_html']))
                echo '</tbody></table>';

            if (isset($_REQUEST['show_html']))
                \f\pa(\f\timer_stop(57));


            // \f\pa($add_bonuses,2,'','$add_bonuses');
            \Nyos\mod\items::addNewSimples($db, \Nyos\mod\JobDesc::$mod_bonus, $add_bonuses);
        } catch (\PDOException $exc) {
            // echo $exc->getTraceAsString();
            if (strpos($exc->getMessage(), 'temp_oborot') !== false && strpos($exc->getMessage(), 'doesn\'t exist') !== false) {
                $skip_start = true;
                require_once './1/didrive/micro-service/creat-db-summ-table.php';

                \f\end3('создали таблицу с сумарными данными по оборотам за месяц', false);
            }
        }

        return \f\end3('ок', true, ['kolvo' => sizeof($add_bonuses)]);
//die();
        return true;

//        DATE()
//
//        
//            $f = 'SELECT * FROM `mitems` mi WHERE '
//                    . ' mi.`status` != \'delete2\' '
//                    // . ' AND mi.folder = :folder '
//                    . (isset($module{
//                            1}) ? ' AND mi.`module` = \'' . addslashes($module) . '\' ' : '')
//                    . (isset($stat{
//                            1}) ? ' AND mi.`status` = \'' . addslashes($stat) . '\' ' : '')
//                    . 'ORDER BY '
//                    . (self::$sort_head == 'desc' ? ' mi.head DESC, ' : '')
//                    . (self::$sort_head == 'asc' ? ' mi.head ASC, ' : '')
//                    . ' mi.`sort` DESC, mi.`add_d` DESC, mi.`add_t` DESC '
//                    . (isset($limit{
//                            1}) && is_numeric($limit) ? 'LIMIT ' . $limit . ' ' : '')
//                    . ';';
//
//            //            if( $_SERVER['HTTP_HOST'] == 'yapdomik.uralweb.info' )
//            //                echo '<br/>'.'<br/>'.$f;
//
//            $ff = $db->prepare($f);
//            $ff->execute(
//                    array(
//                    // ':folder' => $folder
//                    )
//            );
//
//            //$re1 = $ff->fetchAll();
//            $re = [];
//
//            while ($v = $ff->fetch()) {
//                // foreach ($re1 as $k => $v) {
//                $re[$v['id']] = $v;        
//            }

        \f\pa(\f\timer_stop(57));

        return false;

        return \f\end3('ок', true, [
            're' => ( $return ?? [] ),
            'fio' => $fio,
            'fio_names' => $names,
            'user' => \Nyos\mod\JobDesc::$WhereJobMans['data']['ar_jm']]
        );
    }

    /**
     * 
     * @param type $db
     * @param type $date
     * @param type $sp_id
     */
    public static function calculateHoursOnJob($db, $date = null, $sp_id = null) {

// если переменной нет то кеш не пишем и не читаем
// $show_timer = true;

        if (isset($show_timer) && $show_timer === true)
            \f\timer_start(7);

// если нет переменной то не пишем кеш
        $cash_var = 'jobdesc__hoursonjob_calculateHoursOnJob_' . $date . '_sp' . $sp_id;
        $cash_time_sec = 0;

        $return = [];

        if (isset($show_timer) && $show_timer === true)
            echo '<br/>#' . __LINE__ . ' var ' . $cash_var;

        if (!empty($cash_var))
            $return = \f\Cash::getVar($cash_var);

        if (!empty($return) && !empty($return['hours_all'])) {
            if (isset($show_timer) && $show_timer === true)
                echo '<br/>#' . __LINE__ . ' данные из кеша';
        } else {

            if (isset($show_timer) && $show_timer === true)
                echo '<br/>#' . __LINE__ . ' считаем данные и пишем в кеш';

// тут супер код делающий $return

            $return = [];
            $return['jobmans_now'] = self::whereJobmansNowDate($db, $date, $sp_id);

            $sql2 = '';
            foreach ($return['jobmans_now'] as $k => $v) {
                $sql2 .= (!empty($sql2) ? ' OR ' : '' ) . ' mid2.value = \'' . (int) $v['jobman'] . '\' ';
            }

            if (empty($sql2))
                return false;

            if (1 == 1) {
                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'start\' '
                        . ' AND mid.value_datetime BETWEEN :date1 AND :date2 '
                        . ' INNER JOIN `mitems-dops` mid2 '
                        . ' ON mid2.id_item = mi.id '
                        . ' AND mid2.name = \'jobman\' '
                        . ' AND (' . $sql2 . ') '
                ;

                \Nyos\mod\items::$var_ar_for_1sql[':date1'] = $date . ' 08:00:00';
                \Nyos\mod\items::$var_ar_for_1sql[':date2'] = date('Y-m-d 03:00:00', strtotime($date . ' +1day'));

                //\Nyos\mod\items::$show_sql = true;
                $return['checks'] = \Nyos\mod\items::get($db, self::$mod_checks);

                \Nyos\mod\items::$show_sql = false;
                \Nyos\mod\items::$var_ar_for_1sql = [];
            }

            $dolgn = \Nyos\mod\items::get($db, self::$mod_dolgn);
            $return['hours_calc_auto'] = $return['hours_all'] = 0;
            if (1 == 1) {

                foreach ($return['checks'] as $k => $v) {

                    if (!empty($dolgn[$return['jobmans_now'][$v['jobman']]['dolgnost']]['calc_auto']) && $dolgn[$return['jobmans_now'][$v['jobman']]['dolgnost']]['calc_auto'] == 'da') {
                        $return['hours_calc_auto'] += (!empty($v['hour_on_job_hand']) ? $v['hour_on_job_hand'] : ( $v['hour_on_job'] ?? 0 ) );
                    }

                    $return['hours_all'] += (!empty($v['hour_on_job_hand']) ? $v['hour_on_job_hand'] : ( $v['hour_on_job'] ?? 0 ) );
                }
            }

            self::clearTempClassVars();

            if (!empty($return)) {
// \f\Cash::setVar($cash_var, [ 'hours_calc_auto' => $return['hours_calc_auto'], 'hours_all' => $return['hours_all'] ], ( $cash_time_sec ?? 0));
                \f\Cash::setVar($cash_var, $return, ( $cash_time_sec ?? 0));
            }
        }

        if (isset($show_timer) && $show_timer === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(7);

// \f\pa($return);

        return \f\end3('окей', true, $return);
    }

    /**
     * получаем список всех работников что работали на точках
     * v2007
     * ( список для назначения, список для спец. назначения )
     * @param type $db
     * @param type $list
     * all - все
     * spec - работающие на точках (для спец назначения)
     * @param дата $date
     * пока отключил, нужно подключить для выбора списка сотрудников что в этот день работали для спец назначения
     * @return type
     */
    public static function getListJobmans($db, $list = 'all', $date = null) {

        $sql = 'SELECT 
                
                    jm.id,
                    CASE  
                        WHEN jm.head = 1 THEN CONCAT( jm.`lastName` , \' \', jm.`firstName`  , \' \', jm.`middleName`  ) '
                // .' WHEN jm.head != \'\' THEN jm.head '
                . ' ELSE CONCAT( jm.`lastName` , \' \', jm.`firstName` , \' \', jm.`middleName` ) 
                    END as `fio`  ,
                    jm.iiko_name,
                    jm.birthday bd,
                    sp.id sp_id,
                    sp.head sp

                FROM mod_070_jobman jm
                
                ' . ( $list == 'spec' ? ' INNER ' : ' LEFT ' ) . ' JOIN mod_jobman_send_on_sp on_sp ON 
                    on_sp.jobman = jm.id 
                    AND on_sp.status = :status '
                // .( !empty($date) ? ' AND on_sp.date <= :date AND ( on_sp.date_finish IS NULL OR on_sp.date_finish >= :date ) ' : '' )
                . ' LEFT JOIN mod_sale_point sp ON 
                    on_sp.sale_point = sp.id AND sp.status = :status

                WHERE jm.status = :status
                GROUP BY jm.id
                ORDER BY jm.lastName 
                ';

        $sql_vars = [':status' => 'show'];
        // $sql_vars[':mod_user'] = \Nyos\mod\JobDesc::$mod_jobman;
        // $sql_vars[':mod_job_on'] = \Nyos\mod\JobDesc::$mod_man_job_on_sp;
        // $sql_vars[':mod_sp'] = \Nyos\mod\JobDesc::$mod_sale_point;
        // $sql_vars[':date'] = date('Y-m-d', strtotime($date) );
// \f\pa($ff1);
        // \f\pa($sql);

        $ff = $db->prepare($sql);
        $ff->execute($sql_vars);

// $return = [];
        return $ff->fetchAll();
        // return \f\end3('окей', true, $return);;
        // $return = $ff->fetchAll();
//        if (isset($show_timer) && $show_timer === true)
//            echo '<br/>' . __FUNCTION__
//            . '<br/>' . __FILE__ . ' #' . __LINE__
//            . '<br/>' . \f\timer_stop(7);
// \f\pa($return);
//        return $return;
//        return \f\end3('окей', true, $return);
//        return \f\end3('окей', true, $return);
    }

    /**
     * + 191106
     * ищем где работают люди на указанную дату
     * @param класс $db
     * @param str $date
     * @param int $sp_id
     * или ноль или id той точки что интересует
     * @return array
     */
    public static function whereJobmansNowDate($db, $date = null, $sp_id = null) {

//        if (strpos($_SERVER['HTTP_HOST'], 'dev') != false)
//            $timer_on = true;
//        \f\timer_start(12);
//        // $job = \Nyos\mod\items::getItemsSimple($db, self::$mod_man_job_on_sp);
//        $jobmans = \Nyos\mod\items::getItemsSimple3($db, self::$mod_jobman);
//        echo '<br/>1 - '.\f\timer_stop(12);

        if (isset($timer_on) && $timer_on === true)
            \f\timer_start(12);

// $job = \Nyos\mod\items::getItemsSimple($db, self::$mod_man_job_on_sp);
        $jobmans = \Nyos\mod\items::get($db, self::$mod_jobman);

        if (isset($timer_on) && $timer_on === true)
            echo '<br/>2 - ' . \f\timer_stop(12);

//        \f\timer_start(12);
//        $job = \Nyos\mod\items::getItemsSimple3($db, self::$mod_man_job_on_sp);
//        // \f\pa($job,2,'','$job');
//        echo '<br/>21 - '.\f\timer_stop(12);

        if (isset($timer_on) && $timer_on === true)
            \f\timer_start(12);

        $job = \Nyos\mod\items::get($db, self::$mod_man_job_on_sp);
// \f\pa($job,2,'','$job');

        if (isset($timer_on) && $timer_on === true)
            echo '<br/>21 - ' . \f\timer_stop(12);

        $ar = [];

        foreach ($job as $k => $v) {

            if (!empty($jobmans[$v['jobman']]))
                $v['jm_fio'] = $jobmans[$v['jobman']]['head'];

            $ar[$v['jobman']][$v['date']] = $v;
        }

        foreach ($ar as $k => $v) {
            ksort($ar[$k]);
        }

//        \f\pa($ar, 2, '', 'массив пользователей и их перестановок');
//        $ar = array( 188 => $ar[188] );
//        \f\pa($ar, 2, '', 'массив пользователей и их перестановок');

        $now_job = [];

        foreach ($ar as $worker => $dates) {

//            echo '<br/>' . $worker;
//            \f\pa($dates);

            foreach ($dates as $date1 => $array) {

// echo '<Br/>'.__LINE__.' ++ '.$date1.' <= '.$date;

                if ($date1 <= $date) {

// если есть дата конца и она меньше даты поиска то не пишем значение
                    if (isset($array['date_finish']) && $array['date_finish'] <= $date) {

                        if (isset($now_job[$worker]))
                            unset($now_job[$worker]);
                    }
// если конец не раньше и не равен дате, то пишем значение
                    else {
                        $now_job[$worker] = $array;
                    }
                }
            }
        }

//$spec = \Nyos\mod\items::getItemsSimple($db, self::$mod_spec_jobday);
//        \f\timer_start(22);
//        $spec = \Nyos\mod\items::getItemsSimple3($db, self::$mod_spec_jobday);
//// \f\pa($spec,2,'','spec');
//        echo '<br/>22 '.__LINE__.' - '.\f\timer_stop(22);
//         \f\timer_start(22);
        $spec = \Nyos\mod\items::get($db, self::$mod_spec_jobday);
// \f\pa($spec,2,'','spec');
//        echo '<br/>22 '.__LINE__.' - '.\f\timer_stop(22);

        foreach ($spec as $k1 => $v1) {
            if (isset($v1['date']) && $v1['date'] == $date) {
                $v1['type'] = 'spec';
                $now_job[$v1['jobman']] = $v1;
            }
        }

// \f\pa($now_job, 2, '', 'массив работников на указанную дату');
// только точка что интересует
        if (!empty($sp_id)) {

            foreach ($now_job as $k => $v) {

                if (!empty($v['sale_point']) && $v['sale_point'] == $sp_id) {
                    
                } else {
                    unset($now_job[$k]);
                }
            }
        }

// все
        return $now_job;
    }

    /**
     * формируем список кто где работает по спец назначениям в промежутке
     * @param type $db
     * @param type $date_start
     * @param type $date_finish
     * @param type $sp_id
     * @param массив $jobmans
     * список для поиска в базе
     * @return string
     */
    public static function getListJobsPeriodSpec($db, $date_start, $date_finish, $sp_id = null, $jobmans = []) {

// $show_timer = true;

        if (isset($show_timer) && $show_timer === true)
            \f\timer_start(7);

// если нет переменной то не пишем кеш
        $cash_var = 'jobdesc__getListJobsPeriodSpec_mod' . self::$mod_spec_jobday . '_datestart' . $date_start . '_datefinish' . $date_finish . '_sp' . $sp_id;
// $cash_time_sec = 60 * 2;

        $return = [];

        if (!empty($cash_var))
            $return = \f\Cash::getVar($cash_var);

        if (empty($return)) {

// тут супер код делающий $return

            \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                    . ' ON mid.id_item = mi.id '
                    . ' AND mid.name = \'date\' '
                    . ' AND mid.value_date >= :ds '
                    . ' AND mid.value_date <= :df '
//                . ' INNER JOIN `mitems-dops` mid2 '
//                . ' ON mid2.id_item = mi.id '
//                . ' AND mid2.name = \'sale_point\' '
//                . ' AND mid2.value = :sp '

            ;
// \Nyos\mod\items::$var_ar_for_1sql[':sp'] = $sp;
            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
            \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));

            if (!empty($jobmans))
                \Nyos\mod\items::$search['jobman'] = $jobmans;

            $return = \Nyos\mod\items::get($db, self::$mod_spec_jobday);

            if (!empty($return))
                \f\Cash::setVar($cash_var, $return, ( $cash_time_sec ?? 0));
        }

        if (isset($show_timer) && $show_timer === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(7);

        return \f\end3('ок', true, $return);
    }

    /**
     * формируем список кто где работает во временной промежуток
     * @param type $db
     * @param type $date_start
     * @param type $date_finish
     * @param type $sp_id
     * @return string
     */
    public static function getListJobsPeriod($db, $date_start, $date_finish, $sp_id = null) {

// $show_timer = true;

        if (isset($show_timer) && $show_timer === true)
            \f\timer_start(7);

// если нет переменной то не пишем кеш
        $cash_var = 'jobdesc__getListJobsPeriod_mod' . self::$mod_man_job_on_sp . '_datestart' . $date_start . '_datefinish' . $date_finish;
        $cash_time_sec = 60;

        $return = [];

        if (!empty($cash_var)) {

            $return = \f\Cash::getVar($cash_var);
        }

        if (1 == 2 && !empty($return)) {

            if (isset($show_timer) && $show_timer === true)
                echo '<br/>#' . __LINE__ . ' достали ланные из кеша';
        } else {

            if (isset($show_timer) && $show_timer === true)
                echo '<br/>#' . __LINE__ . ' собираем данные в кеш';

// новая от 200412
            if (1 == 1) {

                $on_timer = false;

                if ($on_timer !== false)
                    \f\timer_start(121);

//// дополнение к запросу
                \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` midop01 ON '
                        . ' midop01.id_item = mi.id '
                        . ' AND midop01.name = :name71 '
                        . ' AND midop01.value_date <= :df '
                ;
//// переменные
                \Nyos\mod\items::$sql_vars[':name71'] = 'date';
                \Nyos\mod\items::$sql_vars[':df'] = date('Y-m-d', strtotime($date_finish));

//// выключатель кеша
                \Nyos\mod\items::$cancel_cash = true;

//// переменная для кеша
                \Nyos\mod\items::$cash_var_name = 'items_job_' . self::$mod_man_job_on_sp . '_do_' . \Nyos\mod\items::$sql_vars[':df'];
//
                //$send_on_job = \Nyos\mod\items::get2($db, self::$mod_man_job_on_sp, 'show', 'date_asc');
                $send_on_job = \Nyos\mod\items::get($db, self::$mod_man_job_on_sp, 'show', 'date_asc');

//          $send_on_job = \Nyos\mod\items::get2($db, self::$mod_man_job_on_sp);
// \f\pa($send_on_job, 2, '', '$send_on_job');

                if ($on_timer !== false)
                    echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(121);
            }















            /**
             * проходимся по масссиву и формируем массив кого показываем и какие есть назначения в период показа
             */
            foreach ($send_on_job as $v) {

                if ($v['date'] <= $date_start) {

                    // если уволен позже срока старта показа то начинаем ветку чтобы чела не было в массиве
                    if (!isset($v['date_finish']) || (isset($v['date_finish']) && $v['date_finish'] >= $date_start )) {
                        $return[$v['jobman']] = [$v['date'] => $v];
                    }
                    // если уволен раньше срока показа то удаляем ветку чтобы чела не было в массиве
                    else {
                        if (isset($return[$v['jobman']]))
                            unset($return[$v['jobman']]);
                    }
                }
                // если дата старта после даты старта и меньше финиша, то добавляем в массив для показа
                elseif ($v['date'] > $date_start && (!isset($v['date_finish']) || (isset($v['date_finish']) && $v['date_finish'] <= $date_finish ))) {

                    $return[$v['jobman']][$v['date']] = $v;
                }
            }

            if (!empty($return))
                \f\Cash::setVar($cash_var, $return, ( $cash_time_sec ?? 0));
        }

        if (isset($show_timer) && $show_timer === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(7);

        return \f\end3('ок', true, $return);
    }

    /**
     * получаем полный расклад за месяц
     * таблица на графике 200622 оптимизация под 1 точку
     * таблица на графике 200616
     * @param type $db
     * @param type $date_start
     * @param type $date_finish
     * @return type
     */
    public static function getListJobsPeriodAll($db, $date_start, $date_finish = null) {

        $timer1 = true;
        $timer1 = false;

        // echo '<br/>#'.__LINE__.' '.__FUNCTION__;
        // echo '<br/>['.$date_finish.']';

        /**
         * считаем без бонусов (1 ведомость)
         */
        $calc_no_bonus = ( date('d', strtotime($date_finish)) == 20 ) ? true : false;

// echo '<br/>['.$date_finish.']';
//        echo '<br/>#'.__LINE__.' '.__FUNCTION__;
//        return [];
// если есть то пишем кеш
// $cash_var = 'jobdesc__all_date' . $date_start;

        if (!empty($cash_var)) {

            $e = \f\Cash::getVar($cash_var);

            if (!empty($e))
                return \f\end3('окей (кеш)', true, $e);
        }

//            $e = \f\Cash::deleteKeyPoFilter( [ 'all' , 'jobdesc' , 'sp'.$_REQUEST['sale_point'], 'date'.$_REQUEST['delete_cash_start_date'] ] );
//            \f\pa($e);
// если финиша нет, то ставим финиш последним днём месяца
        if (empty($date_finish))
            $date_finish = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date_start)) . ' +1 month -1 day'));

        $return = [
//            'money' => [],
            'where_job__workman_date' => [],
//            'minusa' => [],
            'comments' => [], // норм + - 8
//            'plusa' => [],
            'jobmans' => [],
//             'job_on_sp' => [],
            'checks' => [],
        ];

        if (isset($timer1) && $timer1 === true)
            \f\timer_start(221);

        $return['spec'] = \Nyos\mod\JobDesc::getListJobsPeriodSpec($db, $date_start, $date_finish);

        if (isset($timer1) && $timer1 === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(221);

        if (isset($timer1) && $timer1 === true)
            \f\timer_start(221);

        $return['norm'] = \Nyos\mod\JobDesc::getListJobsPeriod($db, $date_start, $date_finish);

        if (isset($timer1) && $timer1 === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(221);


//if( isset($timer1) && $timer1 === true )
//    \f\timer_start (221);
//
//// переменная для кеша
//        \Nyos\mod\items::$cash_var_name = 'items_' . self::$mod_jobman;
//        $mans = \Nyos\mod\items::get2($db, self::$mod_jobman);
//        \f\pa($mans,2);
//        
//if( isset($timer1) && $timer1 === true )
//    echo '<br/>#'.__LINE__.' '.\f\timer_stop(221);

        if (isset($timer1) && $timer1 === true)
            \f\timer_start(221);

// переменная для кеша
        // \Nyos\mod\items::$cash_var_name = 'items_' . self::$mod_jobman;
        $mans = \Nyos\mod\items::get($db, self::$mod_jobman);
        // \f\pa($mans,2);


        if (isset($timer1) && $timer1 === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(221);

        if (isset($timer1) && $timer1 === true)
            \f\timer_start(221);

// переменная для кеша
//        \Nyos\mod\items::$cash_var_name = 'items_' . self::$mod_dolgn;
//        $dolgn = \Nyos\mod\items::get2($db, self::$mod_dolgn);
        $dolgn = \Nyos\mod\items::get($db, self::$mod_dolgn);

        if (isset($timer1) && $timer1 === true)
            echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(221);

//    [id] => 4
//    [head] => Альжанова Олеся Алексеевна
//    [sort] => 50
//    [status] => show
//    [iiko_id] => 6b553af8-3c09-4e87-bf6a-2ad20c662f0c
//    [code] => 333573377
//    [iiko_name] => CC home office Альжанова Олеся Алексеевна
//    [login] => AlzhanovaO
//    [mainRoleCode] => OCT
//    [roleCodes] => OCT
//    [cellPhone] => 89507924423
//    [firstName] => Олеся
//    [middleName] => Алексеевна
//    [lastName] => Альжанова
//    [email] => vk.comokleyn@mail.ru
//    [departmentCodes_0] => 01
//    [departmentCodes_1] => 02
//    [departmentCodes_2] => 03
//    [deleted] => false
//    [supplier] => false
//    [employee] => true
//    [client] => false
//    [birthday] => 1993-10-18
//    [bdate] => 1993-10-18
//    [iiko_checks_last_loaded] => 2020-07-08 14:0:15












        $jobman_in_sql = '';

// составляем выборку в БД $jobman_in_sql по сотрудникам
        if (1 == 1) {

            foreach ($return['norm']['data'] as $jm => $v1) {
                foreach ($v1 as $n => $v) {

                    if (!isset($return['job_on_sp'][$v['sale_point']][$v['jobman']]))
                        $return['job_on_sp'][$v['sale_point']][$v['jobman']] = [
                            'jobman' => $v['jobman'],
                            'fio' => ( $mans[$v['jobman']]['firstName'] ?? '-' ) . ' ' . ( $mans[$v['jobman']]['lastName'] ?? '-' ),
                            //'fio21' => ( $mans[$v['jobman']] ?? '-' ),
                            'fio2' =>
                            ( $mans[$v['jobman']]['lastName'] ?? '' ) . ' '
                            . ( $mans[$v['jobman']]['firstName'] ?? '' ) . ' '
                            . ( $mans[$v['jobman']]['middleName'] ?? '' )
                            ,
                            'dolgnost' => $v['dolgnost'],
                            'dolgnost_name' => ( $dolgn[$v['dolgnost']]['head'] ?? '-' )
                        ];


                    if (isset($ar__head_sp__jobman_sp[$v['jobman']]) && $ar__head_sp__jobman_sp[$v['jobman']] == $v['sale_point'])
                        $return['job_on_sp'][$v['sale_point']][$v['jobman']]['head_sp'] = 'da';


                    if (!isset($return['jobmans'][$v['jobman']])) {
                        $return['jobmans'][$v['jobman']] = 1;
                        $jobman_in_sql .= (!empty($jobman_in_sql) ? ',' : '' ) . $v['jobman'];
                    }
                }
            }

//echo '<br/>$jobman_in_sql ' . $jobman_in_sql;

            foreach ($return['spec']['data'] as $k => $v) {
// job_on_sp
//                \f\pa($date_start);
//                \f\pa($date_finish);
//                \f\pa($return['spec']['data']);
//                if($v['date'] < $date_start && $v['date'] > $date_finish )
//                    continue;

                if (!isset($return['job_on_sp'][$v['sale_point']][$v['jobman']]))
                    $return['job_on_sp'][$v['sale_point']][$v['jobman']] = [
                        'jobman' => $v['jobman'],
                        'fio' => ( $mans[$v['jobman']]['firstName'] ?? '-' ) . ' ' . ( $mans[$v['jobman']]['lastName'] ?? '-' ),
                        'dolgnost' => $v['dolgnost'],
                        'dolgnost_name' => ( $dolgn[$v['dolgnost']]['head'] ?? '-' ),
                        'type_job' => 'spec',
                    ];

                if (!isset($return['jobmans'][$v['jobman']])) {
                    $return['jobmans'][$v['jobman']] = 1;
                    $jobman_in_sql .= (!empty($jobman_in_sql) ? ',' : '' ) . $v['jobman'];
                }
            }
        }

// если считаем всё с бонусами
        if (1 == 1 || $calc_no_bonus !== true) {

// minus
            if (1 == 1) {

// \f\timer_start(7);
// если нет переменной то не пишем кеш
                $var_cash_minus = 'jobdesc__money_minus_mod' . self::$mod_minus . '_datestart' . $date_start . '_datefinish' . $date_finish;

                $return['money_minus'] = [];

                if (!empty($var_cash_minus))
                    $return['money_minus'] = \f\Cash::getVar($var_cash_minus);

                if (empty($return['money_minus'])) {

                    \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                            . ' ON mid.id_item = mi.id '
                            . ' AND mid.name = \'date_now\' '
                            . ' AND mid.value_date >= :ds '
                            . ' AND mid.value_date <= :df '
//                        . ' INNER JOIN `mitems-dops` mid2 '
//                        . ' ON mid2.id_item = mi.id '
//                        . ' AND mid2.name = \'jobman\' '
//                        . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '

                    ;
                    \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                    \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                    $return['money_minus'] = \Nyos\mod\items::get($db, self::$mod_minus);

                    if (!empty($var_cash_minus))
                        \f\Cash::setVar($var_cash_minus, $return['money_minus']);
                }

// echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(7);

                foreach ($return['money_minus'] as $k => $v) {

//            \f\pa($v);
//            \f\pa($v['summa']);

                    if (!isset($return['money_to_pay'][$v['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] = $v['summa'];
                    } else {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] += $v['summa'];
                    }
                }

//                        // $now_smena['salary'] = [];
//                        $now_smena['salary'] = 
//                                self::getSalaryJobman($db,
//                                        $now_smena['sale_point'],
//                                        $now_smena['dolgnost'],
//                                        $now_date);
            }


// bonus
            if (1 == 1) {

// \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'date_now\' '
                        . ' AND mid.value_date >= :ds '
                        . ' AND mid.value_date <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '
                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                $return['money_bonus'] = \Nyos\mod\items::get($db, self::$mod_bonus);

// echo '<br/>#'.__LINE__.' bonus '.\f\timer_stop(771);

                foreach ($return['money_bonus'] as $k => $v) {
//\f\pa($v);
                    if (!isset($return['money_to_pay'][$v['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] = $v['summa'];
                    } else {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] += $v['summa'];
                    }
                }
            }



// comment
            if (1 == 1) {

// \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'date_to\' '
                        . ' AND mid.value_date >= :ds '
                        . ' AND mid.value_date <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '

                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                $return['comments'] = \Nyos\mod\items::get($db, self::$mod_comments);

// echo '<br/>#'.__LINE__.' '.\f\timer_stop(771);
            }
        }

        // \f\pa($return['money_minus'],2,'','money_minus');
// собираем должности кто где работает и считаем сколько денег за смену заработал выводим цену часа
        if (1 == 1) {

            foreach ($return['jobmans'] as $wman => $v0) {

                for ($i = 0; $i <= 31; $i++) {

                    $now_date = date('Y-m-d', strtotime($date_start . ' +' . $i . ' day'));

                    if ($now_date > $date_finish)
                        break;

                    foreach ($return['spec']['data'] as $k => $v) {
                        if ($v['date'] == $now_date && $v['jobman'] == $wman) {
                            $spec_job = $v;
                            break;
                        }
                    }

// если было спец назначение в этот день
                    if (!empty($spec_job)) {
                        $spec_job['type'] = 'spec';
// $now_job = $spec_job;

                        if (!empty($now_job)) {
                            $spec_job['norm_sale_point'] = $now_job['sale_point'];
                            $spec_job['norm_dolgnost'] = $now_job['dolgnost'];
                        }
                    }
// если не было спец назначения в этот день
                    else {







// ищем последнюю дату до даты старта периода
                        if ($i == 0) {

                            if (isset($return['norm']['data'][$wman]))
                                foreach ($return['norm']['data'][$wman] as $k => $v) {
                                    if ($v['date'] > $now_date)
                                        continue;
                                    if ($v['date'] <= $now_date)
                                        $now_job = $v;
                                }
                        }

// проверяем текущую дату
                        else {
                            if (isset($return['norm']['data'][$wman]))
                                foreach ($return['norm']['data'][$wman] as $k => $v) {
                                    if ($v['date'] == $now_date)
                                        $now_job = $v;
                                }
                        }
                    }

                    $now_smena = $spec_job ?? $now_job ?? null;

                    if ($now_smena === null)
                        continue;

// если нет переменной то не пишем кеш
// $var_cash_salary = 'salary_dolgnost' . $now_smena['dolgnost'] . '_sp' . $now_smena['sale_point'] . '_date' . $now_date;

                    if (!empty($var_cash_salary))
                        $salary = \f\Cash::getVar($var_cash_salary);

                    if (!empty($salary)) {

                        $now_smena['salary'] = $salary;
                    } else {

// $now_smena['salary'] = [];

                        $now_smena['salary'] = self::getSalaryJobman($db,
                                        ( $now_smena['sale_point'] ?? null),
                                        $now_smena['dolgnost'],
                                        $now_date);

                        if (!empty($var_cash_salary))
                            \f\Cash::setVar($var_cash_salary, $now_smena);
                    }

                    $return['where_job__workman_date'][$wman][$now_date] = $now_smena;

                    if (isset($spec_job))
                        unset($spec_job);
                }
            }
        }

//        if (isset($return['salary']))
//            unset($return['salary']);
// \f\pa($return['salary']);
// тащим оплаты что были
        if (1 == 1) {

//\f\timer_start(771);

            \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                    . ' ON mid.id_item = mi.id '
                    . ' AND mid.name = \'date\' '
                    . ' AND mid.value_date >= :ds '
                    . ' AND mid.value_date <= :df '
            ;
            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
            \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
            $return['oplats'] = \Nyos\mod\items::get($db, self::$mod_buh_oplats);

// echo '<br/>#'.__LINE__.' '.\f\timer_stop(771);

            foreach ($return['oplats'] as $k => $v) {

// \f\pa($v);

                if (!isset($v['sale_point']) || !isset($v['jobman']))
                    continue;

                if (!isset($return['money_oplats'][$v['sale_point']][$v['jobman']])) {
                    $return['money_oplats'][$v['sale_point']][$v['jobman']] = $v['summa'];
                } else {
                    $return['money_oplats'][$v['sale_point']][$v['jobman']] += $v['summa'];
                }
            }
        }



// тащим смены и расставляем зарплату
        if (1 == 1) {

// получение чеков за месяц
// версия от 200412

            if (1 == 1) {
// \f\timer_start(771);
//                \Nyos\mod\items::$cash_var_name = 'checks_' . self::$mod_checks . '_d' . date('Y-m-d', strtotime($date_start)) . '_d' . date('Y-m-d', strtotime($date_finish));
                // \f\pa(\Nyos\mod\items::$cash_var_name);
                // \f\timer_start(221);
//                \Nyos\mod\items::$show_sql = true;
//
//                \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` midop01 ON '
//                        . ' midop01.id_item = mi.id '
//                        . ' AND midop01.name = :name71 '
//                        . ' AND midop01.value_datetime >= :ds '
//                        . ' AND midop01.value_datetime <= :df '
//                ;
//
//                \Nyos\mod\items::$sql_vars[':name71'] = 'start';
//                \Nyos\mod\items::$sql_vars[':ds'] = date('Y-m-d 08:00:00', strtotime($date_start));
//                \Nyos\mod\items::$sql_vars[':df'] = date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'));
//// \Nyos\mod\items::$cancel_cash = true;
//
//                $return['checks'] = \Nyos\mod\items::get2($db, self::$mod_checks);


                if (!empty($return['job_on_sp'][$_REQUEST['sp']])) {
                    // \Nyos\mod\items::$search['sale_point'] = array_keys($return['where_job__workman_date']);
                    \Nyos\mod\items::$search['jobman'] = array_keys($return['job_on_sp'][$_REQUEST['sp']]);
                }

                \Nyos\mod\items::$between_datetime['start'] = [date('Y-m-d 08:00:00', strtotime($date_start)), date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'))];
                $return['checks'] = \Nyos\mod\items::get($db, self::$mod_checks);

                // echo '<br/>' . sizeof($return['checks']);
                // \f\pa($return['checks'], 2, '', 'get checks');
                // echo '<br/>'.\f\timer_stop(221);
                // echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(771);
            }

// получение чеков за месяц
// старая версия / 200412

            if (1 == 2) {

                \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'start\' '
                        . ' AND mid.value_datetime >= :ds '
                        . ' AND mid.value_datetime <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '
                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d 08:00:00', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'));
                $return['checks'] = \Nyos\mod\items::get($db, self::$mod_checks);

                echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(771);
            }

            foreach ($return['checks'] as $k => $v) {

// пропускаем если нет конца или нет автооценки или нет количества отработанных часов
// автооценка не нужна сменам с фиксированной оплатой
// if (empty($v['fin']) || empty($v['ocenka_auto']) || empty($v['hour_on_job']))

                if (empty($v['fin']))
                    continue;

                if (empty($v['hour_on_job']))
                    $v['hour_on_job'] = 0;

                $dn = date('Y-m-d', strtotime($v['start']));

                if (!isset($return['where_job__workman_date'][$v['jobman']][$dn]))
                    continue;

                if (isset($v['ocenka']) && empty($v['ocenka']))
                    unset($v['ocenka']);

// временная переменная для простоты
                $ii = $return['where_job__workman_date'][$v['jobman']][$dn];

                if (isset($ii['salary']['ocenka-hour-base']) && $ii['salary']['ocenka-hour-base'] > 0) {
                    $return['checks'][$k]['salary_hour'] = $ii['salary']['ocenka-hour-base'];
                }
// 
// elseif ( !empty($v['ocenka_auto']) && isset($ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )])) {
                elseif ((!empty($v['ocenka_auto']) || !empty($v['ocenka']) ) && isset($ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )])) {
                    $return['checks'][$k]['salary_hour'] = $ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )];
                }

                if (empty($return['checks'][$k]['salary_hour']))
                    continue;

                if (isset($ii['smoke']) && $ii['smoke'] == 'da' && !empty($ii['salary']['if_kurit'])) {
                    $return['checks'][$k]['salary_hour'] += $ii['salary']['if_kurit'];
                }

                $return['checks'][$k]['summa'] = ceil($return['checks'][$k]['salary_hour'] * ( $v['hour_on_job_hand'] ?? $v['hour_on_job'] ));

                if (!empty($return['checks'][$k]['summa'])) {
                    if (!isset($return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']] = $return['checks'][$k]['summa'];
                    } else {
                        $return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']] += $return['checks'][$k]['summa'];
                    }
                }
            }
        }

        if (!empty($cash_var))
            \f\Cash::setVar($cash_var, $return, 60 * 60 * 12);

        return \f\end3('ок', true, $return);
    }

    public static function newGetSmensFullMonth($db, $user, $date) {

        try {

            $date_start = date('Y-m-01', strtotime($date));
            $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

            $dt_start = date('Y-m-01 07:00:00', strtotime($date_start));
            $dt_finish = date('Y-m-d 02:00:00', strtotime($date_finish . ' +1 day'));

            $sql = 'SELECT '
                    . ' c.* , '
                    // ' DISTINCT c.id i2 , '.
                    // . ' SUBSTRING( c.`start` , 1 , 10 ) as `date` '
//                    . ', ' 
//                    .' MAX(job_in.date) job_date2 '
                    // . ',  '
                    . ' job_in.date job_date '
                    . ' ,'
                    . ' job_in.sale_point job_sp '
                    . ' ,'
                    . ' job_in.dolgnost job_dolgn '
                    . ' ,'
                    . ' job_in.date_finish job_finish '

//            . ' ,'
//.' CASE
//   WHEN job_finish == null or job_finish < job_date
//      THEN \'yes\'
//      ELSE \'no\'
//   END as job_now '
                    . ' ,'
                    . ' spec1.sale_point spec1_sp '
                    . ' ,'
                    . ' spec1.dolgnost spec1_dolgn '
                    . PHP_EOL
                    . ' FROM '
                    . ' `mod_050_chekin_checkout` as `c`  '
                    // .' , `mod_jobman_send_on_sp` as `job_in` '
// ищем что за основная точка продаж
//                    . PHP_EOL
                    . ' LEFT JOIN `mod_jobman_send_on_sp` job_in ON job_in.jobman = c.jobman AND job_in.date <= DATE( c.`start` ) '

// тащим спец назначение
                    . PHP_EOL
                    . ' LEFT JOIN `mod_050_job_in_sp` spec1 ON spec1.jobman = c.jobman AND spec1.date = DATE( c.`start` ) '
                    . PHP_EOL
                    . ' WHERE '
                    . PHP_EOL . ' c.`start` BETWEEN :dt_start AND :dt_finish '
                    . ( $user == 'all' ? '' : PHP_EOL . ' AND `c`.`jobman` = :user ' )
                    . PHP_EOL . ' AND c.status = \'show\' '

//                    . ' AND job_in.jobman = c.`jobman` '
//                    . ' AND job_in.date <= DATE( c.`start` )  '
                    // .' AND `id` IN ( SELECT mid.id_item FROM `mitems-dops` mid WHERE mid.name = \'jobman\' AND mid.value = :id_user ) '
                    // . $dopsql
                    // . ' GROUP BY c.id  '
                    // . ' GROUP BY c.start  '
                    . ' ORDER BY c.start ASC, job_in.date DESC '
                    . ';';
            // \f\pa($sql);
            $ff = $db->prepare($sql);

            $vars = [];
            if ($user != 'all')
                $vars[':user'] = $user;
            $vars[':dt_start'] = $dt_start;
            $vars[':dt_finish'] = $dt_finish;
            $ff->execute($vars);

            // $res = $ff->fetchAll();
            $return = [];
            $skip = [];
            while ($res = $ff->fetch()) {

                if (isset($skip[$res['id']]))
                    continue;

                $res['date'] = date('Y-m-d', strtotime($res['start'] . ' -3 hour'));

                //\f\pa($res);

                if (!empty($res['spec1_sp']) && !empty($res['spec1_dolgn'])) {
                    $res['money'] = self::getSalarisNow($db, $res['spec1_sp'], $res['spec1_dolgn'], $res['date']);
                } else {
                    $res['money'] = self::getSalarisNow($db, $res['job_sp'], $res['job_dolgn'], $res['date']);
                }

                $res['today_hours'] = $res['hour_on_job_hand'] ?? $res['hour_on_job'] ?? '';

                if (!empty($res['money']['ocenka-hour-base']) && !empty($res['today_hours'])) {
                    $res['salary_hour'] = $res['money']['ocenka-hour-base'];
                    $res['summa'] = round($res['salary_hour'] * $res['today_hours'], 1);
                } else {

                    $res['today_ocenka'] = (!empty($res['ocenka']) ? $res['ocenka'] : (!empty($res['ocenka_auto']) ? $res['ocenka_auto'] : '' ) );

                    if (!empty($res['money']['ocenka-hour-' . $res['today_ocenka']]) && !empty($res['today_hours'])) {
                        $res['salary_hour'] = $res['money']['ocenka-hour-' . $res['today_ocenka']];
                        $res['summa'] = round($res['salary_hour'] * $res['today_hours'], 1);
                    }
                }
                $skip[$res['id']] = 1;
                $return[$res['id']] = $res;
            }
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            \f\pa($exc);
        }

        // \f\pa($res);

        return \f\end3('ок', true, $return);
    }

    public static function newGetPaysDopFullMonth($db, $user, $date) {

        try {

            $date_start = date('Y-m-01', strtotime($date));
            $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

            $dt_start = date('Y-m-01 07:00:00', strtotime($date_start));
            $dt_finish = date('Y-m-d 02:00:00', strtotime($date_finish . ' +1 day'));

            $sql = 'SELECT ' .
                    // ' * , '.
                    // ' SUBSTRING( `start` , 1 , 10 ) as `date`  '. 
                    ' \'bonus\' as `type` , ' .
                    ' `m`.`sale_point` , ' .
                    ' `sp`.`head` `sp`, ' .
                    ' `m`.`text` , ' .
                    ' `m`.`summa` , ' .
                    ' `m`.`auto_bonus_zp` as `auto` , ' .
                    ' `m`.`date_now` as `date`  ' .
                    ' FROM 
                `mod_072_plus` `m`
            LEFT JOIN `mod_sale_point` `sp` ON sp.id = m.sale_point
            WHERE '
                    . PHP_EOL . ' `m`.`date_now` BETWEEN :date_start AND :date_finish '
                    . PHP_EOL . ' AND `m`.`jobman` = :user '
                    . PHP_EOL . ' AND `m`.`status` = \'show\' '
                    // .' AND `id` IN ( SELECT mid.id_item FROM `mitems-dops` mid WHERE mid.name = \'jobman\' AND mid.value = :id_user ) '
                    // . $dopsql
                    . ';';
            // \f\pa($sql);
            $ff = $db->prepare($sql);

            $vars = [];
            $vars[':user'] = $user;
            $vars[':date_start'] = $date_start;
            $vars[':date_finish'] = $date_finish;
            $ff->execute($vars);

            $res = $ff->fetchAll();


            $sql = 'SELECT ' .
                    // ' * , '.
                    // ' SUBSTRING( `start` , 1 , 10 ) as `date`  '. 
                    ' \'minus\' as `type` , ' .
                    ' `m`.`sale_point` , ' .
                    ' `sp`.`head` `sp`, ' .
                    ' `m`.`text` , ' .
                    ' `m`.`summa` , ' .
                    ' `m`.`date_now` as `date`  ' .
                    ' FROM 
                `mod_072_vzuscaniya` `m`
            LEFT JOIN `mod_sale_point` `sp` ON sp.id = m.sale_point
            WHERE '
                    . PHP_EOL . ' `m`.`date_now` BETWEEN :date_start AND :date_finish '
                    . PHP_EOL . ' AND `m`.`jobman` = :user '
                    . PHP_EOL . ' AND `m`.`status` = \'show\' '
                    // .' AND `id` IN ( SELECT mid.id_item FROM `mitems-dops` mid WHERE mid.name = \'jobman\' AND mid.value = :id_user ) '
                    // . $dopsql
                    . ';';
            // \f\pa($sql);
            $ff = $db->prepare($sql);

            $vars = [];
            $vars[':user'] = $user;
            $vars[':date_start'] = $date_start;
            $vars[':date_finish'] = $date_finish;
            $ff->execute($vars);

            $res1 = $ff->fetchAll();

            foreach ($res1 as $k => $v) {
                $res[] = $v;
            }

            // $return = array_merge($res,$res1);
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            \f\pa($exc);
        }

        // \f\pa($res);

        return \f\end3('ок', true, $res);
    }

    public static function getListJobsPeriodAll_old2006220129($db, $date_start, $date_finish = null) {

        // echo '<br/>#'.__LINE__.' '.__FUNCTION__;
// echo '<br/>['.$date_finish.']';

        /**
         * считаем без бонусов (1 ведомость)
         */
        $calc_no_bonus = ( date('d', strtotime($date_finish)) == 20 ) ? true : false;

// echo '<br/>['.$date_finish.']';
//        echo '<br/>#'.__LINE__.' '.__FUNCTION__;
//        return [];
// если есть то пишем кеш
// $cash_var = 'jobdesc__all_date' . $date_start;

        if (!empty($cash_var)) {

            $e = \f\Cash::getVar($cash_var);

            if (!empty($e))
                return \f\end3('окей (кеш)', true, $e);
        }

//            $e = \f\Cash::deleteKeyPoFilter( [ 'all' , 'jobdesc' , 'sp'.$_REQUEST['sale_point'], 'date'.$_REQUEST['delete_cash_start_date'] ] );
//            \f\pa($e);
// если финиша нет, то ставим финиш последним днём месяца
        if (empty($date_finish))
            $date_finish = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date_start)) . ' +1 month -1 day'));

        $return = [
//            'money' => [],
            'where_job__workman_date' => [],
//            'minusa' => [],
            'comments' => [], // норм + - 8
//            'plusa' => [],
            'jobmans' => [],
//             'job_on_sp' => [],
            'spec' => \Nyos\mod\JobDesc::getListJobsPeriodSpec($db, $date_start, $date_finish),
            'norm' => \Nyos\mod\JobDesc::getListJobsPeriod($db, $date_start, $date_finish),
            'checks' => [],
        ];

        // \f\pa($return['norm'],2);
        // \f\pa($return['spec'],2);
//        \f\timer_start(771);
//        $mans = \Nyos\mod\items::get($db, self::$mod_jobman);
//        echo '<br/>#' . __LINE__ . ' mans ' . self::$mod_jobman .' '. \f\timer_stop(771);
//        \f\timer_start(771);
// переменная для кеша
        \Nyos\mod\items::$cash_var_name = 'items_' . self::$mod_jobman;
        $mans = \Nyos\mod\items::get2($db, self::$mod_jobman);
//        echo '<br/>#' . __LINE__ . ' mans2 ' . self::$mod_jobman .' '. \f\timer_stop(771);
//        \f\timer_start(771);
//        $dolgn = \Nyos\mod\items::get($db, self::$mod_dolgn);
//        echo '<br/>#' . __LINE__ . ' dolgn ' . \f\timer_stop(771);
//        \f\timer_start(771);
// переменная для кеша
        \Nyos\mod\items::$cash_var_name = 'items_' . self::$mod_dolgn;
        $dolgn = \Nyos\mod\items::get2($db, self::$mod_dolgn);
//        echo '<br/>#' . __LINE__ . ' dolgn2 ' . \f\timer_stop(771);
// \f\pa($return['norm']);
// return [];















        $jobman_in_sql = '';

// составляем выборку в БД $jobman_in_sql по сотрудникам
        if (1 == 1) {

            foreach ($return['norm']['data'] as $jm => $v1) {
                foreach ($v1 as $n => $v) {

                    if (!isset($return['job_on_sp'][$v['sale_point']][$v['jobman']]))
                        $return['job_on_sp'][$v['sale_point']][$v['jobman']] = [
                            'jobman' => $v['jobman'],
                            'fio' => ( $mans[$v['jobman']]['firstName'] ?? '-' ) . ' ' . ( $mans[$v['jobman']]['lastName'] ?? '-' ),
                            //'fio21' => ( $mans[$v['jobman']] ?? '-' ),
                            'fio2' =>
                            ( $mans[$v['jobman']]['lastName'] ?? '' ) . ' '
                            . ( $mans[$v['jobman']]['firstName'] ?? '' ) . ' '
                            . ( $mans[$v['jobman']]['middleName'] ?? '' )
                            ,
                            'dolgnost' => $v['dolgnost'],
                            'dolgnost_name' => ( $dolgn[$v['dolgnost']]['head'] ?? '-' )
                        ];


                    if (isset($ar__head_sp__jobman_sp[$v['jobman']]) && $ar__head_sp__jobman_sp[$v['jobman']] == $v['sale_point'])
                        $return['job_on_sp'][$v['sale_point']][$v['jobman']]['head_sp'] = 'da';


                    if (!isset($return['jobmans'][$v['jobman']])) {
                        $return['jobmans'][$v['jobman']] = 1;
                        $jobman_in_sql .= (!empty($jobman_in_sql) ? ',' : '' ) . $v['jobman'];
                    }
                }
            }

//echo '<br/>$jobman_in_sql ' . $jobman_in_sql;

            foreach ($return['spec']['data'] as $k => $v) {
// job_on_sp
//                \f\pa($date_start);
//                \f\pa($date_finish);
//                \f\pa($return['spec']['data']);
//                if($v['date'] < $date_start && $v['date'] > $date_finish )
//                    continue;

                if (!isset($return['job_on_sp'][$v['sale_point']][$v['jobman']]))
                    $return['job_on_sp'][$v['sale_point']][$v['jobman']] = [
                        'jobman' => $v['jobman'],
                        'fio' => ( $mans[$v['jobman']]['firstName'] ?? '-' ) . ' ' . ( $mans[$v['jobman']]['lastName'] ?? '-' ),
                        'dolgnost' => $v['dolgnost'],
                        'dolgnost_name' => ( $dolgn[$v['dolgnost']]['head'] ?? '-' ),
                        'type_job' => 'spec',
                    ];

                if (!isset($return['jobmans'][$v['jobman']])) {
                    $return['jobmans'][$v['jobman']] = 1;
                    $jobman_in_sql .= (!empty($jobman_in_sql) ? ',' : '' ) . $v['jobman'];
                }
            }
        }

// если считаем всё с бонусами
        if (1 == 1 || $calc_no_bonus !== true) {

// minus
            if (1 == 1) {

// \f\timer_start(7);
// если нет переменной то не пишем кеш
                $var_cash_minus = 'jobdesc__money_minus_mod' . self::$mod_minus . '_datestart' . $date_start . '_datefinish' . $date_finish;

                $return['money_minus'] = [];

                if (!empty($var_cash_minus))
                    $return['money_minus'] = \f\Cash::getVar($var_cash_minus);

                if (empty($return['money_minus'])) {

                    \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                            . ' ON mid.id_item = mi.id '
                            . ' AND mid.name = \'date_now\' '
                            . ' AND mid.value_date >= :ds '
                            . ' AND mid.value_date <= :df '
//                        . ' INNER JOIN `mitems-dops` mid2 '
//                        . ' ON mid2.id_item = mi.id '
//                        . ' AND mid2.name = \'jobman\' '
//                        . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '

                    ;
                    \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                    \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                    $return['money_minus'] = \Nyos\mod\items::get($db, self::$mod_minus);

                    if (!empty($var_cash_minus))
                        \f\Cash::setVar($var_cash_minus, $return['money_minus']);
                }

// echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(7);

                foreach ($return['money_minus'] as $k => $v) {

//            \f\pa($v);
//            \f\pa($v['summa']);

                    if (!isset($return['money_to_pay'][$v['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] = $v['summa'];
                    } else {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] += $v['summa'];
                    }
                }

//                        // $now_smena['salary'] = [];
//                        $now_smena['salary'] = 
//                                self::getSalaryJobman($db,
//                                        $now_smena['sale_point'],
//                                        $now_smena['dolgnost'],
//                                        $now_date);
            }


// bonus
            if (1 == 1) {

// \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'date_now\' '
                        . ' AND mid.value_date >= :ds '
                        . ' AND mid.value_date <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '
                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                $return['money_bonus'] = \Nyos\mod\items::get($db, self::$mod_bonus);

// echo '<br/>#'.__LINE__.' bonus '.\f\timer_stop(771);

                foreach ($return['money_bonus'] as $k => $v) {
//\f\pa($v);
                    if (!isset($return['money_to_pay'][$v['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] = $v['summa'];
                    } else {
                        $return['money_to_pay'][$v['sale_point']][$v['jobman']] += $v['summa'];
                    }
                }
            }



// comment
            if (1 == 1) {

// \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'date_to\' '
                        . ' AND mid.value_date >= :ds '
                        . ' AND mid.value_date <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '

                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
                $return['comments'] = \Nyos\mod\items::get($db, self::$mod_comments);

// echo '<br/>#'.__LINE__.' '.\f\timer_stop(771);
            }
        }

        // \f\pa($return['money_minus'],2,'','money_minus');
// собираем должности кто где работает и считаем сколько денег за смену заработал выводим цену часа
        if (1 == 1) {

            foreach ($return['jobmans'] as $wman => $v0) {

                for ($i = 0; $i <= 31; $i++) {

                    $now_date = date('Y-m-d', strtotime($date_start . ' +' . $i . ' day'));

                    if ($now_date > $date_finish)
                        break;

                    foreach ($return['spec']['data'] as $k => $v) {
                        if ($v['date'] == $now_date && $v['jobman'] == $wman) {
                            $spec_job = $v;
                            break;
                        }
                    }

// если было спец назначение в этот день
                    if (!empty($spec_job)) {
                        $spec_job['type'] = 'spec';
// $now_job = $spec_job;

                        if (!empty($now_job)) {
                            $spec_job['norm_sale_point'] = $now_job['sale_point'];
                            $spec_job['norm_dolgnost'] = $now_job['dolgnost'];
                        }
                    }
// если не было спец назначения в этот день
                    else {


// ищем последнюю дату до даты старта периода
                        if ($i == 0) {

                            if (isset($return['norm']['data'][$wman]))
                                foreach ($return['norm']['data'][$wman] as $k => $v) {
                                    if ($v['date'] > $now_date)
                                        continue;
                                    if ($v['date'] <= $now_date)
                                        $now_job = $v;
                                }
                        }

// проверяем текущую дату
                        else {
                            if (isset($return['norm']['data'][$wman]))
                                foreach ($return['norm']['data'][$wman] as $k => $v) {
                                    if ($v['date'] == $now_date)
                                        $now_job = $v;
                                }
                        }
                    }

                    $now_smena = $spec_job ?? $now_job ?? null;

                    if ($now_smena === null)
                        continue;

// если нет переменной то не пишем кеш
// $var_cash_salary = 'salary_dolgnost' . $now_smena['dolgnost'] . '_sp' . $now_smena['sale_point'] . '_date' . $now_date;

                    if (!empty($var_cash_salary))
                        $salary = \f\Cash::getVar($var_cash_salary);

                    if (!empty($salary)) {

                        $now_smena['salary'] = $salary;
                    } else {

// $now_smena['salary'] = [];

                        $now_smena['salary'] = self::getSalaryJobman($db,
                                        $now_smena['sale_point'],
                                        $now_smena['dolgnost'],
                                        $now_date);

                        if (!empty($var_cash_salary))
                            \f\Cash::setVar($var_cash_salary, $now_smena);
                    }

                    $return['where_job__workman_date'][$wman][$now_date] = $now_smena;

                    if (isset($spec_job))
                        unset($spec_job);
                }
            }
        }

//        if (isset($return['salary']))
//            unset($return['salary']);
// \f\pa($return['salary']);
// тащим оплаты что были
        if (1 == 1) {

//\f\timer_start(771);

            \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                    . ' ON mid.id_item = mi.id '
                    . ' AND mid.name = \'date\' '
                    . ' AND mid.value_date >= :ds '
                    . ' AND mid.value_date <= :df '
            ;
            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d', strtotime($date_start));
            \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d', strtotime($date_finish));
            $return['oplats'] = \Nyos\mod\items::get($db, self::$mod_buh_oplats);

// echo '<br/>#'.__LINE__.' '.\f\timer_stop(771);

            foreach ($return['oplats'] as $k => $v) {

// \f\pa($v);

                if (!isset($v['sale_point']) || !isset($v['jobman']))
                    continue;

                if (!isset($return['money_oplats'][$v['sale_point']][$v['jobman']])) {
                    $return['money_oplats'][$v['sale_point']][$v['jobman']] = $v['summa'];
                } else {
                    $return['money_oplats'][$v['sale_point']][$v['jobman']] += $v['summa'];
                }
            }
        }



// тащим смены и расставляем зарплату
        if (1 == 1) {

// получение чеков за месяц
// версия от 200412

            if (1 == 1) {
// \f\timer_start(771);

                \Nyos\mod\items::$cash_var_name = 'checks_' . self::$mod_checks . '_d' . date('Y-m-d', strtotime($date_start)) . '_d' . date('Y-m-d', strtotime($date_finish));
// \f\pa(\Nyos\mod\items::$cash_var_name);

                \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` midop01 ON '
                        . ' midop01.id_item = mi.id '
                        . ' AND midop01.name = :name71 '
                        . ' AND midop01.value_datetime >= :ds '
                        . ' AND midop01.value_datetime <= :df '
                ;

                \Nyos\mod\items::$sql_vars[':name71'] = 'start';
                \Nyos\mod\items::$sql_vars[':ds'] = date('Y-m-d 08:00:00', strtotime($date_start));
                \Nyos\mod\items::$sql_vars[':df'] = date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'));
// \Nyos\mod\items::$cancel_cash = true;

                $return['checks'] = \Nyos\mod\items::get2($db, self::$mod_checks);
// \f\pa($return['checks'], 2, '', 'get checks');
// echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(771);
            }

// получение чеков за месяц
// старая версия / 200412

            if (1 == 2) {

                \f\timer_start(771);

                \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                        . ' ON mid.id_item = mi.id '
                        . ' AND mid.name = \'start\' '
                        . ' AND mid.value_datetime >= :ds '
                        . ' AND mid.value_datetime <= :df '
//                    . ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'jobman\' '
//                    . ' AND mid2.value IN ( ' . $jobman_in_sql . ' ) '
                ;
                \Nyos\mod\items::$var_ar_for_1sql[':ds'] = date('Y-m-d 08:00:00', strtotime($date_start));
                \Nyos\mod\items::$var_ar_for_1sql[':df'] = date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'));
                $return['checks'] = \Nyos\mod\items::get($db, self::$mod_checks);

                echo '<br/>#' . __LINE__ . ' ' . \f\timer_stop(771);
            }

            foreach ($return['checks'] as $k => $v) {

// пропускаем если нет конца или нет автооценки или нет количества отработанных часов
// автооценка не нужна сменам с фиксированной оплатой
// if (empty($v['fin']) || empty($v['ocenka_auto']) || empty($v['hour_on_job']))

                if (empty($v['fin']) || empty($v['hour_on_job']))
                    continue;

                $dn = date('Y-m-d', strtotime($v['start']));

                if (!isset($return['where_job__workman_date'][$v['jobman']][$dn]))
                    continue;

                if (isset($v['ocenka']) && empty($v['ocenka']))
                    unset($v['ocenka']);

// временная переменная для простоты
                $ii = $return['where_job__workman_date'][$v['jobman']][$dn];

                if (isset($ii['salary']['ocenka-hour-base']) && $ii['salary']['ocenka-hour-base'] > 0) {
                    $return['checks'][$k]['salary_hour'] = $ii['salary']['ocenka-hour-base'];
                }
// 
// elseif ( !empty($v['ocenka_auto']) && isset($ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )])) {
                elseif ((!empty($v['ocenka_auto']) || !empty($v['ocenka']) ) && isset($ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )])) {
                    $return['checks'][$k]['salary_hour'] = $ii['salary']['ocenka-hour-' . ( $v['ocenka'] ?? $v['ocenka_auto'] )];
                }

                if (empty($return['checks'][$k]['salary_hour']))
                    continue;

                if (isset($ii['smoke']) && $ii['smoke'] == 'da' && !empty($ii['salary']['if_kurit'])) {
                    $return['checks'][$k]['salary_hour'] += $ii['salary']['if_kurit'];
                }

                $return['checks'][$k]['summa'] = ceil($return['checks'][$k]['salary_hour'] * ( $v['hour_on_job_hand'] ?? $v['hour_on_job'] ));

                if (!empty($return['checks'][$k]['summa'])) {
                    if (!isset($return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']])) {
                        $return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']] = $return['checks'][$k]['summa'];
                    } else {
                        $return['money_to_pay'][$return['where_job__workman_date'][$v['jobman']][date('Y-m-d', strtotime($v['start']))]['sale_point']][$v['jobman']] += $return['checks'][$k]['summa'];
                    }
                }
            }
        }

        if (!empty($cash_var))
            \f\Cash::setVar($cash_var, $return, 60 * 60 * 12);

        return \f\end3('ок', true, $return);
    }

    public static function getListJobsPeriod_old($db, $date_start, $date_finish = null, $sp_id = null) {

//        if (strpos($_SERVER['HTTP_HOST'], 'dev') != false)
//            $timer_on = true;
//        \f\timer_start(12);
//        // $job = \Nyos\mod\items::getItemsSimple($db, self::$mod_man_job_on_sp);
//        $jobmans = \Nyos\mod\items::getItemsSimple3($db, self::$mod_jobman);
//        echo '<br/>1 - '.\f\timer_stop(12);

        if (isset($timer_on) && $timer_on === true)
            \f\timer_start(12);

// $job = \Nyos\mod\items::getItemsSimple($db, self::$mod_man_job_on_sp);
        $jobmans = \Nyos\mod\items::get($db, self::$mod_jobman);

        if (isset($timer_on) && $timer_on === true)
            echo '<br/>2 - ' . \f\timer_stop(12);

//        \f\timer_start(12);
//        $job = \Nyos\mod\items::getItemsSimple3($db, self::$mod_man_job_on_sp);
//        // \f\pa($job,2,'','$job');
//        echo '<br/>21 - '.\f\timer_stop(12);

        if (isset($timer_on) && $timer_on === true)
            \f\timer_start(12);

        $job = \Nyos\mod\items::get($db, self::$mod_man_job_on_sp);
// \f\pa($job,2,'','$job');

        if (isset($timer_on) && $timer_on === true)
            echo '<br/>21 - ' . \f\timer_stop(12);

        $ar = [];

        foreach ($job as $k => $v) {

            if (!empty($jobmans[$v['jobman']]))
                $v['jm_fio'] = $jobmans[$v['jobman']]['head'];

            $ar[$v['jobman']][$v['date']] = $v;
        }

        foreach ($ar as $k => $v) {
            ksort($ar[$k]);
        }

//        \f\pa($ar, 2, '', 'массив пользователей и их перестановок');
//        $ar = array( 188 => $ar[188] );
//        \f\pa($ar, 2, '', 'массив пользователей и их перестановок');

        $now_job = [];

        foreach ($ar as $worker => $dates) {


//            echo '<br/>' . $worker;
//            \f\pa($dates);

            foreach ($dates as $date1 => $array) {



// echo '<Br/>'.__LINE__.' ++ '.$date1.' <= '.$date;

                if ($date1 <= $date) {

// если есть дата конца и она меньше даты поиска то не пишем значение
                    if (isset($array['date_finish']) && $array['date_finish'] <= $date) {

                        if (isset($now_job[$worker]))
                            unset($now_job[$worker]);
                    }
// если конец не раньше и не равен дате, то пишем значение
                    else {
                        $now_job[$worker] = $array;
                    }
                }
            }
        }

//$spec = \Nyos\mod\items::getItemsSimple($db, self::$mod_spec_jobday);
//        \f\timer_start(22);
//        $spec = \Nyos\mod\items::getItemsSimple3($db, self::$mod_spec_jobday);
//// \f\pa($spec,2,'','spec');
//        echo '<br/>22 '.__LINE__.' - '.\f\timer_stop(22);
//         \f\timer_start(22);
        $spec = \Nyos\mod\items::get($db, self::$mod_spec_jobday);
// \f\pa($spec,2,'','spec');
//        echo '<br/>22 '.__LINE__.' - '.\f\timer_stop(22);

        foreach ($spec as $k1 => $v1) {
            if (isset($v1['date']) && $v1['date'] == $date) {
                $v1['type'] = 'spec';
                $now_job[$v1['jobman']] = $v1;
            }
        }

// \f\pa($now_job, 2, '', 'массив работников на указанную дату');
// только точка что интересует
        if (!empty($sp_id)) {

            foreach ($now_job as $k => $v) {

                if (!empty($v['sale_point']) && $v['sale_point'] == $sp_id) {
                    
                } else {
                    unset($now_job[$k]);
                }
            }
        }

// все
        return $now_job;
    }

    /**
     * смотрим кто работает в указанную дату в указанной точке
     * / если точку не указываем то вывалится массив точек в которых ввсе те люди что там работают
     * / 1912
     * @param type $db
     * @param type $date
     * @param type $sp
     */
    public static function getJobmansDate($db, string $date, $sp = null) {

        if (empty($sp) || ( isset($sp) && is_numeric($sp) )) {
            
        } else {
            return \f\end3('SP не та', false);
        }

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'date\' '
                . ' AND mid.value_date <= \'' . $date . '\' '
                . ' INNER JOIN `mitems-dops` mid2 '
                . 'ON mid2.id_item = mi.id '
                . 'AND mid2.name = \'jobman\' ';

        if (!empty($sp))
            \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` mid3 '
                    . 'ON mid3.id_item = mi.id '
                    . 'AND mid3.name = \'sale_point\' '
                    . 'AND mid3.value = \'' . $sp . '\' '

//                . ' LEFT JOIN `mitems-dops` mid3 '
//                . 'ON mid3.id_item = mi.id '
//                . 'AND mid3.name = \'date_finish\' '
//                . ' INNER JOIN `mitems-dops` mid2 
//                ON mid2.id_item = mi.id 
//                AND mid2.name = \'dolgnost\' '
//                . ' LEFT JOIN `mitems-dops` mid3 
//                ON mid3.id_item = mid2.value
//                AND mid3.name = \'calc_auto\' '

            ;

        \Nyos\mod\items::$select_var1 = ' , mid.value_date date_start ';
        \Nyos\mod\items::$sql_order = //                ' GROUP BY mid2.value ' . 
                'ORDER BY date_start DESC '
//                'ORDER BY mid2.value DESC, date_start DESC '
        ;

        \Nyos\mod\items::$where2dop = ' AND ( '
                . ' `name` = \'sale_point\' '
                . ' OR '
                . ' `name` = \'jobman\' '
                . ' OR '
                . ' `name` = \'date\' '
                . ' OR '
                . ' `name` = \'date_finish\' '
                . ' OR '
                . ' `name` = \'dolgnost\' '
                . ' ) '
        ;

// $job_all0 = \Nyos\mod\items::getItemsSimple3($db, 'jobman_send_on_sp');
        $job_all0 = \Nyos\mod\items::get($db, self::$mod_man_job_on_sp);
// echo '11: '.sizeof($job_all0);
// \f\pa($job_all0, '','','$job_all0');

        $return = [];

// echo '<table>';

        foreach ($job_all0 as $k => $v) {

            if (!empty($v['date_finish']) && $v['date_finish'] < $date) {
// echo '<br/>-- '.$v['jobman'].' '.$v['sale_point'].' '.$v['date'].' '.( $v['date_finish'] ?? 'x' ).' '.$v['status'];
//echo '<tr><td>--'.$v['jobman'].'</td><td>'.$v['sale_point'].'</td><td>'.$v['date'].'</td><td>'.( $v['date_finish'] ?? 'x' ).'</td><td>'.$v['status'].'</td></tr>';
// unset($job_all0[$k]);
                continue;
            }

// echo '<br/>'.$v['jobman'].' '.$v['sale_point'].' '.$v['date'].' '.( $v['date_finish'] ?? 'x' ).' '.$v['status'];
// echo '<tr><td>'.$v['id'].'</td><td>'.$v['jobman'].'</td><td>'.$v['sale_point'].'</td><td>'.$v['date'].'</td><td>'.( $v['date_finish'] ?? 'x' ).'</td><td>'.$v['status'].'</td></tr>';

            if (!empty($sp)) {
                if (!isset($return[$v['jobman']]))
                    $return[$v['jobman']] = $v;
            } else {
                if (!isset($return[$v['sale_point']][$v['jobman']]))
                    $return[$v['sale_point']][$v['jobman']] = $v;
            }
        }

// echo '</table>';

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'date\' '
                . ' AND mid.value_date = \'' . $date . '\' '
        ;

        $all_spec = \Nyos\mod\items::get($db, self::$mod_spec_jobday);
// \f\pa($all_spec);

        foreach ($all_spec as $k => $v) {
            if (empty($sp)) {
                $return[$v['sale_point']][$v['jobman']] = $v;
            } else {
                $return[$v['jobman']] = $v;
            }
        }

        return $return;
    }

    /**
     * считаем сколько суммарно часов отработано за сегодня
     * @param type $db
     * @param type $date
     * @param int $sp
     * @return array
     */
    public static function calcJobHoursDay($db, $date, int $sp) {

        $ret = [];

        $ret['who_is_jobs'] = \Nyos\mod\JobDesc::whereJobmansOnSp($db, \Nyos\Nyos::$folder_now, $date);

        $jobs_mesta = self::getJobmansDate($db, $date, $sp);
//\f\pa($jobs_mesta,2,'','$jobs_mesta');

        $sql2 = '';
        foreach ($jobs_mesta as $k => $v) {
            $sql2 .= (!empty($sql2) ? ' OR ' : '' ) . ' mid2.value = \'' . $k . '\' ';
        }

        if (empty($sql2))
            return \f\end3('нет часов для суммирования', false);

//\Nyos\mod\items::$show_sql = true;
        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'start\' '
                . ' AND mid.value_datetime >= :date1 '
                . ' AND mid.value_datetime <= :date2 '
                . ' INNER JOIN `mitems-dops` mid2 '
                . ' ON mid2.id_item = mi.id '
                . ' AND mid2.name = \'jobman\' '
                . ' AND (' . $sql2 . ') '
        ;

        \Nyos\mod\items::$var_ar_for_1sql[':date1'] = $date . ' 08:00:00';
        \Nyos\mod\items::$var_ar_for_1sql[':date2'] = date('Y-m-d 03:00:00', strtotime($date . ' +1day'));

        if (1 == 1) {
            \Nyos\mod\items::$where2dop = ' AND ( '
//                . ' `name` = \'sale_point\' '
//                . ' OR '
//                . ' `name` = \'jobman\' '
//                . ' OR '
//                . ' `name` = \'date\' '
//                . ' OR '
                    . ' `name` = \'hour_on_job_hand\' '
                    . ' OR '
                    . ' `name` = \'hour_on_job\' '
                    . ' ) '
            ;
        }

        $checks = \Nyos\mod\items::get($db, self::$mod_checks);
// \f\pa($checks,2,'','checks');

        $ret['hours'] = 0;
        $ret['smen_in_day'] = 0;
        $ret['checks_for_new_ocenka'] = [];

        foreach ($checks as $k => $v) {

            $ret['hours'] += $v['hour_on_job_hand'] ?? $v['hour_on_job'] ?? 0;
            $ret['smen_in_day'] ++;
            $ret['checks_for_new_ocenka'][] = $v['id'];
            $ret['checks'][] = $v;
        }

        return \f\end3('ok', true, $ret);














        $return = [];

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid 
                ON mid.id_item = mi.id 
                AND mid.name = \'date\' 
                AND mid.value_date <= \'' . $date . '\'  '
                . ' INNER JOIN `mitems-dops` mid2 
                ON mid2.id_item = mi.id 
                AND mid2.name = \'dolgnost\' '
                . ' LEFT JOIN `mitems-dops` mid3 
                ON mid3.id_item = mid2.value
                AND mid3.name = \'calc_auto\' '
        ;

        \Nyos\mod\items::$select_var1 = ' , mid3.value calc_auto ';

// $job_all0 = \Nyos\mod\items::getItemsSimple3($db, 'jobman_send_on_sp');
        $job_all0 = \Nyos\mod\items::getItemsSimple3($db, self::$mod_man_job_on_sp);
//\f\pa($job_all0,2);

        usort($job_all0, "\\f\\sort_ar_date");

// \f\pa($job_all0,2);

        $job_now = [];

        foreach ($job_all0 as $k => $v) {
// echo '<br/>'.$v['jobman'].' - '.$v['sale_point'].' + '.$v['date'];
            $job_now[$v['jobman']] = $v;
        }

        /**
         * кто работает сегдня на нашей точке продаж
         */
        $job_now_on_sp = [];
        foreach ($job_now as $k => $v) {
            if ($v['sale_point'] == $sp) {

//\f\pa($v);

                $job_now_on_sp[$k] = $v;
            }
        }

// \f\pa($job_now_on_sp,2);

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid 
        ON mid.id_item = mi.id 
        AND mid.name = \'date\' 
        AND mid.value_date = \'' . $date . '\'  ';
        $spec = \Nyos\mod\items::getItemsSimple3($db, '050.job_in_sp');
// \f\pa($spec,2);

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'start\' '
                . ' AND mid.value_datetime >= \'' . $date . ' 08:00:00\' '
                . ' AND mid.value_datetime <= \'' . date('Y-m-d 03:00:00', strtotime($date . ' +1day')) . '\' ';
        $checks = \Nyos\mod\items::getItemsSimple3($db, '050.chekin_checkout');

//\f\pa($checks, 2,'','checks');

        $return['hours'] = 0;

        foreach ($checks as $k => $v) {

            if (isset($job_now_on_sp[$v['jobman']])) {

// \f\pa($job_now_on_sp[$v['jobman']]);

                /**
                 * если на должност ине стоит галочка считаем автоматически .. то пропускаем
                 */
                if (empty($job_now_on_sp[$v['jobman']]['calc_auto'])) {
                    \f\pa($v);
                    continue;
                }

//                if( empty($v['hour_on_job_hand']) && empty($v['hour_on_job']) ){
//                }
// \f\pa($job_now_on_sp[$v['jobman']]);
// \f\pa($v);

                $return['checks_for_new_ocenka'][] = $v['id'];

// $return['hours'] += ( $v['hour_on_job_hand'] ?? $v['hour_on_job'] );
                $return['hours'] += ( $v['hour_on_job_hand'] ?? ( $v['hour_on_job'] ?? 0 ) );
            }
        }

// echo '<br/>часов: '.$return['hours'];
        return $return;
    }

    /**
     * получаем какие цены по датам у должностей на точке продаж (старая)
     * @param type $db
     * @param type $folder
     * @param type $module_sp
     * @param type $module_slary
     * @return type
     */
    public static function configGetJobmansSmenas($db, $folder = null, $module_sp = 'sale_point', $module_slary = '071.set_oplata') {

        if ($folder === null)
            $folder = \Nyos\nyos::$folder_now;

// \f\pa( \Nyos\nyos::$folder_now );

        $re = [];

        /**
         * точки продаж
         */
        $sps = \Nyos\mod\items::getItems($db, $folder, $module_sp, 'show', null);
// \f\pa($sps, 2);

        /**
         * 
         */
// $salary = \Nyos\mod\items::getItems($db, $folder, $module_slary, 'show', null);
        $salary = \Nyos\mod\items::getItemsSimple($db, $module_slary, 'show');
// \f\pa($salary, 2);

        $re = [];

        foreach ($salary['data'] as $k => $v) {

            if (
                    $v['status'] == 'show' &&
                    isset($sps['data'][$v['dop']['sale_point']]) &&
                    $sps['data'][$v['dop']['sale_point']]['status'] == 'show') {
                
            } else {
                continue;
            }

            if (isset($v['dop']['sale_point']) && isset($sps['data'][$v['dop']['sale_point']])) {

                if (isset($sps['data'][$v['dop']['sale_point']]['head']) && $sps['data'][$v['dop']['sale_point']]['head'] == 'default') {

                    $re['default'][$v['dop']['dolgnost']][$v['dop']['date']] = $v['dop'];
                } else {

                    $re[$v['dop']['sale_point']][$v['dop']['dolgnost']][$v['dop']['date']] = $v['dop'];
                }
            }
        }

        $re2 = [];
        foreach ($re as $point => $v1) {
            foreach ($v1 as $dolg => $v2) {
                ksort($v2);
                $re2[$point][$dolg] = $v2;
            }
        }

        return $re2;
    }

    /**
     * получаем список сотрудников которые работают в указанный промежуток времени (новая версия)
     * @param type $db
     * @param type $dt_start
     * @param type $dt_fin
     * @param type $module_send_jobman_to_sp
     * @return int
     */
    public static function getJobmansOnTime1910($db, $dt_start, $dt_fin, $module_send_jobman_to_sp = 'jobman_send_on_sp') {

        /**
         * тащим список назначений на работу в точке продаж в период времени
         */
        $jobman_on = [];

        $send_jobm_to_sp = \Nyos\mod\items::getItemsSimple($db, $module_send_jobman_to_sp);
// \f\pa($send_jobm_to_sp, 2, '', '$send_jobm_to_sp');

        foreach ($send_jobm_to_sp['data'] as $k => $v) {

            if (isset($v['dop']['jobman']) && !isset($jobman_on[$v['dop']['jobman']])) {
                if (isset($v['dop']['date']) && $v['dop']['date'] <= $dt_fin) {

                    $jobman_on[$v['dop']['jobman']] = 1;

                    /*
                      if (isset($v['dop']['date']) && isset($v['dop']['date_finish'])) {
                      $jobman_on[$v['dop']['jobman']] = 1;
                      }
                     */
                }
            }
        }

// \f\pa($jobman_on, 2, '', '$return[jobman_on] допущенные сотрудники');


        return $jobman_on;
    }

    /**
     * получаем какие цены по датам у должностей на точке продаж (старая)
     * @param type $db
     * @param type $folder
     * @param type $module_sp
     * @param type $module_slary
     * @return type
     */
    public static function compileSalarysJobmans($db, $date, $module_sp = 'sale_point', $module_slary = '071.set_oplata') {

//if ($folder === null)
        $folder = \Nyos\nyos::$folder_now;

// \f\pa( \Nyos\nyos::$folder_now );

        $re = [];

        /**
         * точки продаж
         */
        $sps = \Nyos\mod\items::getItems($db, $folder, $module_sp, 'show', null);
// \f\pa($sps, 2);

        /**
         * 
         */
// $salary = \Nyos\mod\items::getItems($db, $folder, $module_slary, 'show', null);
        $salary = \Nyos\mod\items::getItemsSimple($db, $module_slary, 'show');
// \f\pa($salary, 2);

        $re = [];

        foreach ($salary['data'] as $k => $v) {

            if (
                    $v['status'] == 'show' &&
                    isset($sps['data'][$v['dop']['sale_point']]) &&
                    $sps['data'][$v['dop']['sale_point']]['status'] == 'show') {
                
            } else {
                continue;
            }

            if (isset($v['dop']['sale_point']) && isset($sps['data'][$v['dop']['sale_point']])) {

                if (isset($sps['data'][$v['dop']['sale_point']]['head']) && $sps['data'][$v['dop']['sale_point']]['head'] == 'default') {

                    $re['default'][$v['dop']['dolgnost']][$v['dop']['date']] = $v['dop'];
                } else {

                    $re[$v['dop']['sale_point']][$v['dop']['dolgnost']][$v['dop']['date']] = $v['dop'];
                }
            }
        }

        $re2 = [];
        foreach ($re as $point => $v1) {
            foreach ($v1 as $dolg => $v2) {
                ksort($v2);
                $re2[$point][$dolg] = $v2;
            }
        }

        return $re2;
    }

    /**
     * получаем id точки продаж по умолчанию
     * @param type $db
     * @param type $module_sp
     * @return type
     */
    public static function getDefaultSpId($db, $module_sp = 'sale_point') {

        if (!empty(self::$cash['sp_default']))
            return self::$cash['sp_default'];

        $sps = \Nyos\mod\items::getItemsSimple($db, $module_sp);
// \f\pa($sps,2,'','sps');

        $sp_default = null;

        foreach ($sps['data'] as $k => $v) {
            if ($v['head'] == 'default') {
                self::$cash['sp_default'] = $k;
                break;
            }
        }

        return self::$cash['sp_default'];
    }

    /**
     * считаем сумму заработанную за смену
     * @param массив $a
     * входящий массив с подборкой всех данных
     * @return цифра сумма или false
     */
    public static function calcSummaDay($a) {

// $summa = 0;
// \f\pa($a);

        if (isset($a['hour_on_job'])) {
            $hour = $a['hour_on_job'];
        }

        if (isset($a['ocenka'])) {
            $ocenka = $a['ocenka'];
        }

        $smoke = (isset($a['now_job']['smoke']) && $a['now_job']['smoke'] == 'da' ) ? true : false;

        if (
                !empty($hour) &&
                !empty($ocenka) &&
                !empty($a['salary-now']['ocenka-hour-' . $ocenka])
        ) {

            $summa = $hour * ( $a['salary-now']['ocenka-hour-' . $ocenka] + ( $smoke === true ? ( $a['salary-now']['if_kurit'] ?? 0 ) : 0 ) );
        }

        return $summa ?? false;
    }

    /**
     * получаем массив с размером оплат
     * $ar_sp_dolgn_date
     * @param type $db
     * @return array
     */
    public static function getAllSalary($db) {

        $cash_var = self::$mod_salary . '_all_ar_salary';
// \f\timer_start(123);

        if (!empty($cash_var))
            $ar_sp_dolgn_date = \f\Cash::getVar($cash_var);

        if (!empty($ar_sp_dolgn_date)) {
            
        }
        //
        else {


            $ff1 = ' SELECT '
                    . ' mi.id, '
                    . ' mi.head, '
                    . ' mi.sort, '
                    . ' mi.status, '
// .' midop.name, '
// .' midop.id_item id, '
// .' midop.id dops_id, '
                    . ' midop.* '
// . ', '
//                . ' midop.`name`, '
//                .' midop.`value`, '
//                .' midop.`value_date`, '
//                .' midop.`value_datetime`, '
//                .' midop.`value_text` '
// . self::$select_var1 
                    . ' FROM '
                    . ' `mitems-dops` midop '
// . ( self::$join_where ?? '' )
                    . ' INNER JOIN `mitems` mi ON '
                    . ' mi.`module` = :module '
                    . ' AND mi.status = \'show\' '
                    . ' AND mi.id = midop.id_item '
//
//                . ' INNER JOIN `mitems-dops` mid1 '
//                . ' ON mid1.id_item = mi.id '
//                . ' AND mid1.name = \'date\' '
//                // . ' AND mid.value_date >= :ds '
//                . ' AND mid1.value_date <= :df '
//
//                . ' INNER JOIN `mitems-dops` mid2 '
//                . ' ON mid2.id_item = mi.id '
//                . ' AND mid2.name = \'sale_point\' '
//                . ' AND '
//                . ' ( '
//                . ' mid2.value = :sp '
//                . ' OR mid2.value = :sp_def '
//                . ' )'
//                . ' WHERE '
//                . ' midop.status = \'show\' '
// . ( self::$where2 ?? '' )
// . self::$sql_order ?? ''
            ;

            $ff = $db->prepare($ff1);

            $ar_for_sql = [
                ':module' => self::$mod_salary,
//            ':sp' => $sp,
//            ':sp_def' => $sp_default,
//            ':df' => $date
            ];
//    \Nyos\mod\items::$var_ar_for_1sql[':ds'] = $date_start;

            $ff->execute($ar_for_sql);

            $ee = $ff->fetchAll();
// \f\pa($ee);

            $res_items = [];

            foreach ($ee as $k => $v) {

//                $dd = (!empty($v['value']) ? $v['value'] :
//                        (!empty($v['value_date']) ? $v['value_date'] :
//                        (!empty($v['value_datetime']) ? $v['value_datetime'] :
//                        (!empty($v['value_int']) ? $v['value_int'] :
//                        (!empty($v['value_text']) ? $v['value_text'] : null )
//                        )
//                        )
//                        )
//                        );
                $dd = $v['value'] ?? $v['value_date'] ?? $v['value_datetime'] ?? $v['value_int'] ?? $v['value_text'] ?? null;

                if (!empty($dd))
                    $res_items[$v['id_item']][$v['name']] = $dd;
            }

            usort($res_items, "\\f\\sort_ar_date_desc");
            usort($res_items, "\\f\\sort_ar__salary_oborot_m_desc");
            usort($res_items, "\\f\\sort_ar__salary_oborot_d_desc");

//            \f\pa($res_items,'','','$res_items');
//             die();
//            
            $ar_sp_dolgn_date = [];

            foreach ($res_items as $k => $v) {

                if (!empty($v['sale_point']) && !empty($v['dolgnost']) && !empty($v['date']))
                    $ar_sp_dolgn_date[$v['sale_point']][$v['dolgnost']][$v['date'] . '-' . $k] = $v;
            }

// unset($res_items);

            \f\Cash::setVar($cash_var, $ar_sp_dolgn_date);
        }

// \f\pa($ar_sp_dolgn_date);

        return $ar_sp_dolgn_date;
    }

    /**
     * получаем что за за на должности в точке тогда-то
     * @param type $db
     * @param type $sp
     * @param type $dolgn
     * @param type $date
     * @param type $module_sp
     * @param type $module_salary
     * @return type
     */
    public static function getSalaryJobman($db, $sp, $dolgn, $date) {

        if (empty($sp) || empty($dolgn) || empty($date))
            return false;

        $cash_var = \Nyos\mod\JobDesc::$mod_salary . '_sp' . $sp . '_dolgn' . $dolgn . '_date' . $date;
// $cash_time = 60 * 60 * 20;
// \f\timer_start(123);

        $salary = false;

        if (!empty($cash_var))
            $salary = \f\Cash::getVar($cash_var);

        if ($salary !== false) {
            
        } else {

            // $sp_default = self::getDefaultSpId($db);

            $ww = self::getAllSalary($db);
// \f\pa($ww, 2);

            $salary = [];

            if (isset($ww[$sp][$dolgn])) {
                foreach ($ww[$sp][$dolgn] as $k => $v) {

                    if (!empty($v['date']) && $date >= $v['date']) {

// если есть ограничения по бюджету
                        // день
                        if (!empty($v['pay_from_day_oborot_bolee'])) {

                            // $v['oborot_day'] =
                            $oborot_day = \Nyos\mod\JobBuh::getOborotSpDay($db, $sp, $date);
                            // \f\pa($oborot,'','','$oborot');
                            // echo '<br/>#'.__LINE__.' '.__FILE__;

                            if ($v['pay_from_day_oborot_bolee'] && $oborot_day >= $v['pay_from_day_oborot_bolee']) {
                                $v['oborot_day'] = $oborot_day;
                                $salary = $v;
                                break;
                            }
                        }
                        // месяц
                        elseif (!empty($v['oborot_sp_last_monht_bolee']) || !empty($v['oborot_sp_last_monht_menee'])) {

                            $oborot = \Nyos\mod\JobBuh::getOborotSpMonth($db, $sp, $date);
// \f\pa($oborot);

                            if (
                                    (!empty($v['oborot_sp_last_monht_bolee']) && $oborot >= $v['oborot_sp_last_monht_bolee'] ) ||
                                    (!empty($v['oborot_sp_last_monht_menee']) && $oborot <= $v['oborot_sp_last_monht_menee'] )
                            ) {
                                $v['oborot'] = $oborot;
                                $salary = $v;
                                break;
                            }
                        }
// если нет ограничений
                        else {
                            $salary = $v;
                            break;
                        }
                    }
                }
            }


            if (1 == 2 ?? !empty($sp_default) && empty($salary) && isset($ww[$sp_default][$dolgn])) {
                foreach ($ww[$sp_default][$dolgn] as $k => $v) {
                    if (!empty($v['date']) && $date >= $v['date']) {
// если есть ограничения по бюджету
                        if (!empty($v['oborot_sp_last_monht_bolee']) || !empty($v['oborot_sp_last_monht_menee'])) {

                            $oborot = \Nyos\mod\JobBuh::getOborotSpMonth($db, $sp, $date);

                            if (
                                    (!empty($v['oborot_sp_last_monht_bolee']) && $oborot >= $v['oborot_sp_last_monht_bolee'] ) ||
                                    (!empty($v['oborot_sp_last_monht_menee']) && $oborot <= $v['oborot_sp_last_monht_menee'] )
                            ) {
                                $v['oborot'] = $oborot;
                                $salary = $v;
                                break;
                            }
                        }
// если нет ограничений
                        else {
                            $salary = $v;
                            break;
                        }
                    }
                }
            }


            \f\Cash::setVar($cash_var, $salary, ( $cash_time ?? 0));
        }

// \f\pa($salary,'','','salary');
        return $salary;
    }

    public static function getSalaryJobman_old($db, $sp, $dolgn, $date, $module_sp = 'sale_point', $module_salary = '071.set_oplata') {





//        echo '<br/>sp: ' . $sp;
//        echo '<br/>d: ' . $dolgn;
//        if ($dolgn == 55497 && $date == '2020-01-03') {
//            echo '<br/>' . __LINE__ . ' ' . $date;
//            $show_info = true;
//        }

        if (isset(self::$cash['salary_now'][$sp][$dolgn][$date]))
            return self::$cash['salary_now'][$sp][$dolgn][$date];

        $sp_default = self::getDefaultSpId($db);

        if (isset($show_info) && $show_info === true) {
            \f\pa(self::$cash['salarys']);
        }

        if (empty(self::$cash['salarys'])) {

// self::$cash['salarys'] = \Nyos\mod\items::get($db, $module_salary);
            self::$cash['salarys'] = \Nyos\mod\items::get($db, self::$mod_salary);
            usort(self::$cash['salarys'], "\\f\\sort_ar_date");

            if (isset($show_info) && $show_info === true) {
                echo '<br/>' . __LINE__;
            }
        }

        if (isset($show_info) && $show_info === true) {
            \f\pa(self::$cash['salarys']);
        }

// если тру то используем точку по умолчанию
        $sp_def = true;

        foreach (self::$cash['salarys'] as $k => $v) {

            if ($v['date'] <= $date) {

//                echo '<div style="margin:5px; padding:5px; border:1px solid green; max-height:50px; overflow: auto;" >';
//                \f\pa($v);
//                echo '</div>';

                if (isset($v['dolgnost']) && $v['dolgnost'] == $dolgn) {

                    if (isset($v['sale_point']) && ( $v['sale_point'] == $sp || ( $sp_def === true && $v['sale_point'] == $sp_default ) )) {

                        if (isset($v['oborot_sp_last_monht_menee']) || isset($v['oborot_sp_last_monht_bolee'])) {

//                            echo '<br/>проверяем оборот:';
// достаём оборот этой точки продаж за текущий месяц
                            $oborot = \Nyos\mod\JobBuh::getOborotSpMonth($db, $sp, $date);
//                            \f\pa($oborot, '', '', 'oborot');

                            if (isset($v['oborot_sp_last_monht_menee']) || isset($v['oborot_sp_last_monht_bolee'])) {

                                if (isset($v['oborot_sp_last_monht_menee'])) {
                                    if (isset($v['oborot_sp_last_monht_menee']) && $v['oborot_sp_last_monht_menee'] <= $oborot) {
//                                        echo '<br/>++ ' . __LINE__ . ' ++';
                                        continue;
                                    }
//                                    else {
//                                        echo '<br/>-- menee ' . __LINE__ . ' ++';
//                                    }
                                }
                                if (isset($v['oborot_sp_last_monht_bolee'])) {
                                    if (isset($v['oborot_sp_last_monht_bolee']) && $v['oborot_sp_last_monht_bolee'] >= $oborot) {
// echo '<br/>++ ' . __LINE__ . ' ++';
                                        continue;
                                    }
//                                    else {
//                                        echo '<br/>-- bolee ' . __LINE__ . ' ++';
//                                    }
                                }
                            }
                        }

                        if ($v['sale_point'] != $sp_default)
                            $sp_def = false;

//                        \f\pa($v);
                        self::$cash['salary_now'][$sp][$dolgn][$date] = $v;
                    }
                }
            }
        }

        return self::$cash['salary_now'][$sp][$dolgn][$date] ?? [];
    }

    /**
     * ищем где работают люди
     * @param type $db
     * @param type $folder
     * @param type $date_start
     * @param type $date_fin
     * @param type $module_man_job_on_sp
     * @return type
     */
    public static function getTimeOgidanie($db, int $sp, string $date) {

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'date\' '
                . ' AND mid.value_date = :d '
                . ' INNER JOIN `mitems-dops` mid2 '
                . ' ON mid2.id_item = mi.id '
                . ' AND mid2.name = \'sale_point\' '
                . ' AND mid2.value = :sp '

        ;
        \Nyos\mod\items::$var_ar_for_1sql[':sp'] = $sp;
        \Nyos\mod\items::$var_ar_for_1sql[':d'] = $date;
        \Nyos\mod\items::$limit1 = true;

        $timeo = \Nyos\mod\items::get($db, '074.time_expectations_list');

        if (!empty($timeo)) {
            return \f\end3('ok', true, $timeo);
        } else {
            return \f\end3('что то не так #' . __LINE__ . ' ' . __FILE__, false, $timeo);
        }

// \f\pa($timeo0);
//
//        foreach ($timeo0 as $k => $v) {
//// echo '<br/>'.$v['dop']['date'];
//
//            if (isset($v['sale_point']) && $v['sale_point'] == $sp && isset($v['date']) && $v['date'] == $date) {
//                $timeo[] = $v;
//            }
//        }

        $return = [];

        if (isset($timeo)) {

            foreach ($timeo as $k => $v) {
                if (strpos($k, '_hand') !== false) {
                    if (!empty($v)) {
                        if (strpos($k, '_hand') !== false && !empty($v)) {
                            $timeo[str_replace('_hand', '', $k)] = $v;
                            unset($timeo[$k]);
                        }
                    }
                }
            }

            foreach ($timeo as $k => $v) {
                $return['timeo_' . $k] = $v;
            }
        }

        \f\pa($return);

        return $return;
    }

    /**
     * получаем количество часов отработанных в этот день
     * @param type $db
     * @param int $sp
     * @param string $date
     * @return type
     * @throws \Exception
     */
    public static function getTimesChecksDay($db, int $sp, string $date) {

//        echo '<hr>'
//        . __FILE__ . ' #' . __LINE__
//        . '<br/>'
//        . __FUNCTION__
//        . '<hr>';

        /**
         * тащим кто кем и где работал под дням в периоде
         */
        $jobinsp = \Nyos\mod\JobDesc::getSetupJobmanOnSp($db, $date);
        \f\pa($jobinsp, 2, '', '$jobinsp');


// echo '<br/>'.__FILE__.' #'.__LINE__;

        $worker_on_date = self::whereJobmans($db, $date);
        \f\pa($worker_on_date, 2, '', 'self::whereJobmans($db, $date);');

// echo '<br/>'.__FILE__.' #'.__LINE__;
// \f\pa($jobinsp2, 2, '', '$jobinsp2');



        $dd = date('Y-m-d', strtotime($date));

        $dds = strtotime(date('Y-m-d 09:00:00', strtotime($date)));
        $ddf = strtotime(date('Y-m-d 02:00:00', ( strtotime($date) . ' + 1 day ')));


        $checki = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout');
// \f\pa($checki,2,'','$checki'); // exit;

        $checki2 = [];

        echo '<Br/>' . $date . '<br/>';

        foreach ($checki['data'] as $k2 => $v2) {

            if (isset($v2['dop']['jobman']) && isset($jobinsp['jobs'][$v2['dop']['jobman']])) {

                $ddn = strtotime($v2['dop']['start']);

                if ($dds <= $ddn && $ddn <= $ddf) {

// echo '<br/>#' . __LINE__ . ' ' . $v2['dop']['jobman'];
// \f\pa($v2['dop']);
                    $checki2[$v2['dop']['jobman']][] = $v2['dop'];
                }
            }
        }

// \f\pa($checki2, 2, '', '$checki2'); // exit;
// $dt1 = date('Y-m-d 05:00:01', strtotime($date));
// $dt2 = date('Y-m-d 23:50:01', strtotime($date));

        $return = array('hours' => 0);

        foreach ($checki['data'] as $k => $v) {

            $now_d = substr($v['dop']['start'], 0, 10);

            if ($dd != $now_d)
                continue;

            $return[] = $v;

            if (isset($jobinsp['jobs'][$v['dop']['jobman']][$now_d]['sale_point'])) {

                $v['dop']['sale_point'] = $jobinsp['jobs'][$v['dop']['jobman']][$now_d]['sale_point'];
            } else {
                continue;
            }

            if (isset($v['dop']['sale_point']) && $v['dop']['sale_point'] == $sp && isset($v['dop']['start']) && ( $v['dop']['start'] >= $dt1 && $v['dop']['start'] <= $dt2 )) {
//echo '+2';
            } else {
                continue;
            }

            $return['id_check_for_new_ocenka'][$v['id']] = 1;

            if (!empty($v['dop']['hour_on_job_hand'])) {
                $return['hours'] += $v['dop']['hour_on_job_hand'];
            } elseif (!empty($v['dop']['hour_on_job'])) {
                $return['hours'] += $v['dop']['hour_on_job'];
            }
        }

        \f\pa($return, 2, '', '$return');

        if ($return['hours'] == 0)
            throw new \Exception('Количество отработанных часов = 0', 11);

//\f\pa($return);

        return $return;
    }

    /**
     * ищем где работают люди (олд)
     * новая whereJobmansPeriod
     * @param type $db
     * @param type $folder
     * @param type $date_start
     * @param type $date_fin
     * @param type $module_man_job_on_sp
     * @return type
     */
    public static function whereJobmansOnSp($db, $folder = null, $date_start = null, $date_fin = null
            , $module_man_job_on_sp = 'jobman_send_on_sp'
            , $module_spec_naznach_on_sp = '050.job_in_sp'
    ) {

//whereJobmansOnSp( $db, $folder, $date_start, $date_finish );

        if ($folder === null)
            $folder = \Nyos\nyos::$folder_now;

// \f\pa( \Nyos\nyos::$folder_now );
// $re = [];







        /**
         * назначения сорудников на сп
         */
// $jobs = \Nyos\mod\items::getItems($db, $folder, $module_man_job_on_sp, 'show', null);
        $jobs = \Nyos\mod\items::getItemsSimple($db, $module_man_job_on_sp);
//\f\pa($jobs, 2);

        $d = array('jobs' => []);

        foreach ($jobs['data'] as $k => $v) {

// \f\pa($v,2,'','v');
// exit;

            if (
                    ( isset($v['dop']['date'])
// && $date_start >= $v['dop']['date'] 
                    && $v['dop']['date'] <= $date_fin
                    ) &&
                    (!isset($v['dop']['date_finish']) || ( isset($v['dop']['date_finish']) && $date_start <= $v['dop']['date_finish'] && $date_fin >= $v['dop']['date_finish'] ) )
            ) {
                $v['dop']['id'] = $v['id'];
                $v['dop']['d'] = $v;
                $d['jobs'][$v['dop']['date'] . '--' . $v['id']] = $v['dop'];
            }
        }

//\f\pa($d['jobs'], 2,'','jobs');

        $spec = \Nyos\mod\items::getItemsSimple($db, $module_spec_naznach_on_sp);
//\f\pa($spec, 2,'','$spec');

        foreach ($spec['data'] as $k => $v) {

//\f\pa($v);

            if (isset($v['dop']['date']) && $v['dop']['date'] >= $date_start && $v['dop']['date'] <= $date_fin) {
                $v['dop']['id'] = $v['id'];
                $v['dop']['d'] = $v;
                $v['dop']['type2'] = 'spec';
                $d['jobs'][$v['dop']['date'] . '--' . $v['id']] = $v['dop'];
            }
        }

        krsort($d['jobs']);

//\f\pa($d['jobs'], 2,'','jobs');

        $re2 = [];
        $ret = [];
        $ret2 = [];

        foreach ($d['jobs'] as $k => $v) {

            if (isset($last_date[$v['jobman']]))
                $v['date_end'] = date('Y-m-d', strtotime($last_date[$v['jobman']]) - 3600 * 24);

//            \f\pa($date_start);
//            \f\pa($date_fin);
//            \f\pa($v);

            $u_date_start = strtotime($v['date']);

//                if (strtotime($date_start) <= $u_date_start) {
            $ret2['jobs_on_sp'][$v['sale_point']][$v['jobman']] = 1;
//            } else {
//                $ret2['jobs_on_sp'][$v['sale_point']][$v['jobman']] = 'hide';
//            }

            $re2['jobs'][$v['sale_point']][$v['jobman']][$v['date']] = $v;

            $last_date[$v['jobman']] = $v['date'];
        }






        if (!empty($re2['jobs']))
            foreach ($re2['jobs'] as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    ksort($v1);
//\f\pa($v1);
                    $ret2['jobs'][$k][$k1] = $v1;
                }
            }

/// \f\pa($ret2,2,'','$ret2');

        /**
         * выводим список точек по порядку сортировки
         */
        \Nyos\mod\items::$sql_order = ' ORDER BY mi.sort ASC ';
        $points = \Nyos\mod\items::getItemsSimple($db, self::$mod_sale_point);
        foreach ($points['data'] as $k => $v) {
            $ret2['sort'][] = $k;
        }

        return $ret2;
    }

    /**
     * ищем где работают люди за период
     * старая версия whereJobmansOnSp
     * (текущая старая версия) новая > whereJobmans
     * @param type $db
     * @param type $folder
     * @param type $date_start
     * @param type $date_fin
     * @param type $module_man_job_on_sp
     * @return type
     */
    public static function whereJobmansPeriod($db, $date_start = null, $date_fin = null
            , $module_man_job_on_sp = 'jobman_send_on_sp'
            , $module_spec_naznach_on_sp = '050.job_in_sp'
    ) {














//whereJobmansOnSp( $db, $folder, $date_start, $date_finish );
//        if ($folder === null)
//            $folder = \Nyos\nyos::$folder_now;
// \f\pa( \Nyos\nyos::$folder_now );
// $re = [];
//\f\pa($d['jobs'], 2,'','jobs');




        /*
          $re2 = [];
          $ret = [];
          $ret2 = [];

          foreach ($d['jobs'] as $k => $v) {

          if (isset($last_date[$v['jobman']]))
          $v['date_end'] = date('Y-m-d', strtotime($last_date[$v['jobman']]) - 3600 * 24);

          //            \f\pa($date_start);
          //            \f\pa($date_fin);
          //            \f\pa($v);

          $u_date_start = strtotime($v['date']);

          //                if (strtotime($date_start) <= $u_date_start) {
          $ret2['jobs_on_sp'][$v['sale_point']][$v['jobman']] = 1;
          //            } else {
          //                $ret2['jobs_on_sp'][$v['sale_point']][$v['jobman']] = 'hide';
          //            }

          $re2['jobs'][$v['sale_point']][$v['jobman']][$v['date']] = $v;

          $last_date[$v['jobman']] = $v['date'];
          }

          foreach ($re2['jobs'] as $k => $v) {
          foreach ($v as $k1 => $v1) {
          ksort($v1);
          //\f\pa($v1);
          $ret2['jobs'][$k][$k1] = $v1;
          }
          }
         */

        $return = [];

        for ($i = 0; $i <= 300; $i++) {

            $dt = date('Y-m-d', strtotime($date_start . ' + ' . $i . ' day'));

            if ($date_fin <= $dt)
                break;

// echo '<br/>' . $date_fin . ' + ' . $date_start . ' + ' . $dt;
// echo '<br/>' . $dt;

            $return[$dt] = self::whereJobmansNowDate($db, $dt);
// \f\pa($ee);
        }

        return $return;
    }

    /**
     * где работают сотрудники
     * @param type $db
     * @param type $date_start
     * @param type $date_fin
     * пустой (значит 1 день сегодня) или не пустой, тогда промежуток
     * @return type
     */
    public static function whereJobmans($db, $date_start = null, $date_fin = null, $sp = null) {

// echo '<br/>' . __FUNCTION__ . ' ( [' . $date_start . '] , [' . $date_fin . '] ) #' . __LINE__;

        if (empty($date_start))
            $date_start = '2019-09-01';

        if (empty($date_fin))
            $date_fin = $date_start;

        $return = [];

        $i = 0;
// $dt = date('Y-m-d', strtotime($date_start . ' + ' . $i . ' day'));
        $dt = date('Y-m-d', strtotime($date_start));

        $return[$dt] = self::whereJobmansNowDate($db, $dt);

// \f\pa($return[$dt],2,'','$return[$dt]');

        return $return;
    }

    /**
     * получаем массив дат и должностей кто сколько получает за период (старт - стоп)
     * дата - точка - должность - сумма за час
     * @param type $db
     * @param type $date_start
     * @param type $date_finish
     * @param type $module_man_job_on_sp
     * @param type $mod_spec_jobday
     * @return type
     */
    public static function getSalarisPeriod($db, string $dt_start, string $dt_finish, $mod_salary = '071.set_oplata', $mod_sale_point = 'sale_point') {

        if (!empty(self::$cash['salaris_all']))
            return self::$cash['salaris_all'];

        $sps = \Nyos\mod\items::getItemsSimple($db, self::$mod_sale_point);
// \f\pa($sps, 2, '', '$sps');

        $salary = \Nyos\mod\items::getItemsSimple($db, self::$mod_salary);
// \f\pa($salary, 2, '', '$salary');

        $ss = [];

        foreach ($salary['data'] as $k1 => $v1) {

            $v1['dop']['id'] = $v1['id'];
// $ss[$v1['dop']['date']][$v1['dop']['sale_point']][$v1['dop']['dolgnost']][$v1['id']] = $v1['dop'];
            $ss[$v1['dop']['date']][( ( isset($sps['data'][$v1['dop']['sale_point']]['head']) && $sps['data'][$v1['dop']['sale_point']]['head'] == 'default' ) ? 'default' : $v1['dop']['sale_point'] )][$v1['dop']['dolgnost']][$v1['id']] = $v1['dop'];
        }

        ksort($ss);

// \f\pa($ss, '', '', '$ss');

        $now_price = [];


        foreach ($ss as $dt => $ar1) {

            if ($dt <= $dt_start) {
                
            } else {
                break;
            }

//            echo '<br/>1--' . $dt ;
//            echo '<br/>' . __LINE__;

            foreach ($ar1 as $sp => $ar2) {

//                echo '<br/>2--' . $sp;
//                echo '<br/>' . __LINE__;

                foreach ($ar2 as $dolgn => $ar3) {

// echo '<br/>' . __LINE__;
// $now_price[$sp][$dolgn] = $ar3['id'];
                    $now_price[$sp][$dolgn] = $ar3;
                }
            }
        }

// \f\pa($now_price);

        self::$cash['salaris_all'] = [];

        for ($i = 0; $i <= 370; $i++) {

            $nd = date('Y-m-d', strtotime($dt_start . ' +' . $i . ' day'));

            self::$cash['salaris_all'][$nd] = $now_price;
// echo '<br/>' . $nd;

            if ($nd == $dt_finish)
                break;
        }

//\f\pa($price_time);

        return self::$cash['salaris_all'];

//return $ret2;
    }

    /**
     * получаем массив по датам когда кто сколько получает
     * на выходе только должности что катят под вход параметры
     * creatAutoBonusMonth 200615
     * @param type $db
     * @param int $sp
     * @param int $dolgn
     * @param string $date
     * @return type
     */
    public static function getSalarisNow($db, $sp = null, $dolgn = null, $date = null) {

        if (empty($sp) || empty($dolgn) || empty($date))
            return false;

        //salarys
        if (1 == 1 && empty(self::$ar_salaris_sp_dolgn_date)) {

            $salaris0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_salary, 'show', 'date_asc');
            $salaris0_kolvo = sizeof($salaris0);

            // \f\pa($salaris0, 2, '', '$salaris0');

            foreach ($salaris0 as $k => $v) {
                if (!empty($v['sale_point']) && !empty($v['dolgnost']) && !empty($v['date']))
                    self::$ar_salaris_sp_dolgn_date[$v['sale_point']][$v['dolgnost']][$v['date']][] = $v;
            }

            unset($salaris0);
        }

        // \f\pa(self::$ar_salaris_sp_dolgn_date);
//        if (isset(self::$ar_salaris_sp_dolgn_date[$sp][$dolgn])) {
//            \f\pa(self::$ar_salaris_sp_dolgn_date[$sp][$dolgn]);
//        }

        $salary = [];

        if (!isset(self::$ar_salaris_sp_dolgn_date[$sp][$dolgn]))
            return false;

        foreach (self::$ar_salaris_sp_dolgn_date[$sp][$dolgn] as $k => $v) {
            if ($k <= $date)
                $salary = $v;
        }

        if (sizeof($salary) > 0) {

            // \f\pa($salary);
            // echo '<br/>#' . __LINE__;

            $uslovie_oborot_month = false;
            $uslovie_oborot_day = false;

            foreach ($salary as $v) {
                if (!empty($v['oborot_sp_last_monht_bolee'])) {
                    $uslovie_oborot_month = true;
                }
                if (!empty($v['pay_from_day_oborot_bolee'])) {
                    $uslovie_oborot_day = true;
                }
            }

            // если есть условия по обороту месяца на зп
            // ищем 
            if ($uslovie_oborot_month === true) {

                $oborot = \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date);
                // \f\pa(\Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date]);
//                \f\pa($oborot,2,'','oborot');
//                \f\pa($salary,2,'','salary');

                foreach ($salary as $v) {
                    if (!empty($v['oborot_sp_last_monht_bolee']) && $v['oborot_sp_last_monht_bolee'] <= $oborot) {
                        return $v;
                    }
                }

                foreach ($salary as $v) {
                    if (empty($v['oborot_sp_last_monht_bolee'])) {
                        return $v;
                    }
                }
            } elseif ($uslovie_oborot_day === true) {

                if (empty(\Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date]))
                    \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date);

                // \f\pa( [ $sp, $date, \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date] ] );
//                \f\pa($oborot,2,'','oborot');
//                \f\pa($salary,2,'','salary');

                foreach ($salary as $v) {
                    if (!empty($v['pay_from_day_oborot_bolee']) && $v['pay_from_day_oborot_bolee'] <= \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date]) {
                        // \f\pa($v);
                        $v['oborot_day_now'] = \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date];
                        return $v;
                    }
                }

                foreach ($salary as $v) {
                    if (empty($v['pay_from_day_oborot_bolee'])) {
                        // \f\pa($v);
                        $v['oborot_day_now'] = \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date];
                        return $v;
                    }
                }
            }




            foreach ($salary as $v) {
                return $v;
            }

            if (1 == 1) {

                if (!empty($v['oborot_sp_last_monht_bolee'])) {

                    // self::getOborotSp($db, $sp, $date);
                    // $v['oborot_sp_last_monht_bolee'] > \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date);
                }
            }
        }

        return $salary;
    }

    /**
     * формируем массив данных для оценки
     * @param type $db
     * @param type $sp
     * @param type $date
     * @return type
     */
    public static function readVarsForOcenkaDays($db, $sp, $date) {

        $return = array(
            'txt' => '',
            // текст о времени исполнения
            'time' => '',
            // смен в дне
            'smen_in_day' => 0,
            // часов за день отработано
            'hours' => 0,
            // больше или меньше нормы сделано сегодня ( 1 - больше или равно // 0 - меньше // 2 не получилось достать )
            'oborot_bolee_norm' => 2,
            // сумма денег на руки от количества смен и процента на ФОТ
            'summa_na_ruki' => 0,
            // рекомендуемая оценка управляющего
// если 0 то нет оценки
            'ocenka' => 0,
            'ocenka_naruki' => 0
        );

        $return['date'] = date('Y-m-d', strtotime($date));
        $return['sp'] = $return['sale_point'] = $sp;

// id items для записи авто оценки

        /**
         * достаём чеки за день
         */
        if (1 == 1) {

            $id_items_for_new_ocenka = [];

            \f\timer::start();

// $return['hours'] = \Nyos\mod\JobDesc::getTimesChecksDay($db, $sp, $e) getOborotSp($db, $return['sp'], $return['date']);
            $times_day = \Nyos\mod\JobDesc::getTimesChecksDay($db, $return['sp'], $return['date']);

            \f\pa($times_day, 2, '', '$times_day');

            $return['hours'] = $times_day['hours'];
            $id_items_for_new_ocenka = $times_day['id_check_for_new_ocenka'];
// die($return['hours']);

            $return['time'] .= PHP_EOL . ' достали время работы по чекам за день : ' . \f\timer::stop()
                    . PHP_EOL . $return['hours'];
        }

//        if (!class_exists('Nyos\mod\JobDesc'))
//            require_once DR . DS . 'vendor/didrive_mod/jobdesc/class.php';
//        echo '<br/>' . __FILE__ . ' ' . __LINE__;
//        \f\pa($return);
//        die(__LINE__);

        /**
         * достаём нормы на день
         */
        if (5 == 5) {
            \f\timer::start();

            $now_norm = \Nyos\mod\JobDesc::whatNormToDay($db, $return['sp'], $return['date']);
//\f\pa($now_norm,2,'','$now_norm '.$return['sp'].' / '.$return['date'] );

            if ($now_norm === false)
                throw new \Exception('Нет плановых данных (дата)', 12);

            foreach ($now_norm as $k => $v) {
//$return['txt'] .= '<br/><nobr>[norm_' . $k . '] - ' . $v . '</nobr>';
                $return['norm_' . $k] = $v;
//echo '<br>'.PHP_EOL.'$return[\'norm_' . $k.'] = '. $v;
            }

            $return['time'] .= PHP_EOL . ' нормы за день время: ' . \f\timer::stop();
//\f\pa($return); die();

            if (empty($return['norm_date'])) {
// $error .= PHP_EOL . 'Нет плановых данных (дата)';
                throw new \Exception('Нет плановых данных (дата)', 12);
            } elseif (
// empty($return['norm_vuruchka']) 
                    empty($return['norm_vuruchka_on_1_hand']) || empty($return['norm_time_wait_norm_cold']) || empty($return['norm_procent_oplata_truda_on_oborota']) || empty($return['norm_kolvo_hour_in1smena'])
            ) {
                throw new \Exception('Не все плановые данные по ТП указаны', 204);
//$error .= PHP_EOL . 'Не все плановые данные по ТП указаны';
            }
        }

        /**
         * достаём оборот за сегодня
         */
        if (5 == 5) {

            \f\timer::start();

// $return['oborot'] = \Nyos\mod\JobDesc::getOborotSp($db, $_REQUEST['sp'], $_REQUEST['date']);
// die('<br/>'.__FILE__.' == '.__LINE__);

            $return['oborot'] = \Nyos\mod\IikoOborot::getDayOborot($db, $return['sp'], $return['date']);

// \f\pa($return);
// echo

            $return['time'] .= PHP_EOL . ' достали обороты за день: ' . \f\timer::stop()
                    . PHP_EOL . $return['oborot'];
        }

        /**
         * достаём время ожидания за сегодня
         */
        if (5 == 5) {

            \f\timer::start();

//            echo '<hr>'.__FILE__.' #'.__LINE__;
//            echo '<br/>'.$return['sp'].' , '.$return['date'];
//            echo '<hr>';

            $timeo = \Nyos\mod\JobDesc::getTimeOgidanie($db, $return['sp'], $return['date']);

// \f\pa($timeo);
//\f\pa($timeo);
            $return['time'] .= PHP_EOL . ' достали время ожидания за день: ' . \f\timer::stop();
            foreach ($timeo as $k => $v) {
                $return['time'] .= PHP_EOL . $k . ' > ' . $v;
                $return[$k] = $v;
            }
        }

//        echo '<fieldset style="border: 1px solid gray; padding: 5px; margin: 5px;" ><legend>'
//        . 'достаём суммарное время работы сотрудников за сегодня</legend>';
        if (1 == 3) {

//            echo '<hr>';
//            echo __FILE__.' #'.__LINE__;
//            echo '<hr>';
//            echo '<hr>';
// $sp

            $worker_on_date = self::whereJobmansNowDate($db, $return['date']);
// \f\pa($worker_on_date, 2, '', '$worker_on_date');

            $ds = strtotime($return['date'] . ' 09:00:00');
            $df = strtotime($return['date'] . ' 03:00:00 +1 day');

            $ds1 = date('Y-m-d H:i:s', $ds);
// echo '<Br/>'.date('Y-m-d H:i:s', $ds );
            $df1 = date('Y-m-d H:i:s', $df);
// echo '<Br/>'.date('Y-m-d H:i:s', $df );

            $checks = \Nyos\mod\items::getItemsSimple($db, self::$mod_checks);
            $return['checks_for_new_ocenka'] = [];

            foreach ($checks['data'] as $k3 => $v3) {

                if (
                        isset($v3['dop']['jobman']) &&
                        isset($v3['dop']['start']) &&
                        $v3['dop']['start'] >= $ds1 &&
                        $v3['dop']['start'] <= $df1 &&
                        isset($worker_on_date[$v3['dop']['jobman']]['sale_point']) &&
                        $worker_on_date[$v3['dop']['jobman']]['sale_point'] == $sp
                ) {

//\f\pa($v3['dop']);
//break;
// часы отредактированные в ручную
                    if (!empty($v3['dop']['hour_on_job_hand'])) {
                        $return['checks_for_new_ocenka'][] = $v3['id'];
                        $return['hours'] += $v3['dop']['hour_on_job_hand'];
                    }
// авторасчёт количества часов
                    elseif (!empty($v3['dop']['hour_on_job'])) {
                        $return['checks_for_new_ocenka'][] = $v3['id'];
                        $return['hours'] += $v3['dop']['hour_on_job'];
                    }
                }
            }
//die();
//            $return['smen_in_day'] = round($return['hours'] / $return['norm_kolvo_hour_in1smena'], 1);
//            
//            if( !empty($return['oborot']) && !empty($return['smen_in_day']) )
//            $return['summa_na_ruki'] = ceil( $return['oborot'] / $return['smen_in_day'] );
//
//            // если на руки больше нормы то оценка 5
//            if ( $return['summa_na_ruki'] >= $return['norm_vuruchka_on_1_hand']) {
//                $return['ocenka_naruki'] = 5;
//            }
//            // если на руки меньше нормы то оценка 3
//            else {
//                $return['ocenka_naruki'] = 3;
//            }
// $ee = self::getTimesChecksDay($db, $return['sp'], $return['date']);
// \f\pa($ee, 2, '', '$ee = self::getTimesChecksDay($db, $ar[\'sp\'], $ar[\'date\']);');
// $return['hours_job_days'] = $ee;
        }
// echo '</fieldset>';

        return \f\end3('ok', true, $return);

// return $return;
    }

    /**
     * запись автобонусов день + тп 
     * @param type $db
     * @param type $sp
     * @param type $date
     * @return type
     */
    public static function creatAutoBonus($db, $_sp, $dt0) {

        $show_comment = true;
//$show_comment = false;

        $dt = strtotime($dt0);
        $_d = date('Y-m-d', $dt);
        $ds = date('Y-m-d 05:00:00', $dt);
        $df = date('Y-m-d 04:00:00', $dt + 3600 * 24);


// удаление имеющихся бонусов в этот день
        if (self::$no_delete_autobonus_1day !== true) {

            if ($show_comment === true)
                \f\timer::start(123);

            $ee = self::deleteAutoBonus($db, $_sp, $_d);
//\f\pa($ee,'','','$ee удаление автобонусов');

            if ($show_comment === true)
                echo '<br/>tt(' . __LINE__ . '): ' . \f\timer::stop('str', 123);
        }


        /**
         * список должность и сколько бонуса накинуть
         * должность - оценка - бонус
         */
        $list_dolg_bonus = [];


        /**
         * массив работник > должность
         */
        $where_job = \Nyos\mod\JobDesc::whereJobmansNowDate($db, $_d);
// \f\pa($where_job);
// доп в запрос где выбираем чеки
        $sql_select_checks_dop = '';

        foreach ($where_job as $k => $v) {
            if (isset($v['sale_point']) && $v['sale_point'] == $_sp && isset($v['jobman']) && isset($v['dolgnost'])) {
                $job_in[$v['jobman']] = $v['dolgnost'];
                $list_dolg_bonus[$v['dolgnost']] = 0;

                $sql_select_checks_dop .= (!empty($sql_select_checks_dop) ? ',' : '' ) . $v['jobman'];
            }
        }

// \f\pa($job_in, 2, '', '$job_in работает в указанную дату на точке скана ' . $_sp . ' ( работник > должность )');

        if ($show_comment === true)
            echo '<br/>tt(' . __LINE__ . '): ' . \f\timer::stop('str', 123);


        \f\timer_start(47);

        /**
         * дневной оборот точки
         */
        $oborot_month = \Nyos\mod\IikoOborot::getOborotMonth($db, $_sp, $_d);

        if ($show_comment === true)
            echo '<br/>ob mont (' . __LINE__ . '): ' . \f\timer_stop(47);

// \f\pa($oborot_month, 2, '', '$oborot_month');
// \Nyos\mod\items::$get_data_simple = true;
// $checks0 = \Nyos\mod\items::getItemsSimple($db, \Nyos\mod\JobDesc::$mod_checks);

        if ($show_comment === true)
            \f\timer_start(47);

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` md ON '
                . ' `md`.`id_item` = mi.id '
                . 'AND `md`.`name` = \'start\' '
                . 'AND `md`.`value_datetime` >= \'' . $ds . '\'  '
                . 'AND `md`.`value_datetime` <= \'' . $df . '\'  '

//                .' INNER JOIN `mitems-dops` md2 '
//                . ' ON `md2`.`id_item` = mi.id '
//                . ' AND `md2`.`name` = \'jobman\' '
//                . ' AND `md2`.`value` IN ( '.$sql_select_checks_dop.' ) '

        ;

//$checks0 = \Nyos\mod\items::getItemsSimple3($db, \Nyos\mod\JobDesc::$mod_checks);
        $checks0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);
        $checks = [];

        foreach ($checks0 as $k => $v) {
// if (isset($v['jobman']) && isset($job_in[$v['jobman']]) && isset($v['start']) && $v['start'] >= $ds && $v['start'] <= $df) {
            if (isset($v['jobman']) && isset($job_in[$v['jobman']])) {
                $v['item_id'] = $v['id'];
                $checks[] = $v;
            }
        }

        if ($show_comment === true)
            echo '<br/>get checks (' . __LINE__ . '): ' . \f\timer_stop(47);


// usort($checks, "\\f\\sort_ar_start");
// \f\pa($checks, 2, '', '$checks');


        /*
          $cheki_da = [];
          $cheki_jobmans = [];

          // usort($dd, "\\f\\sort_ar_date");

          foreach ($checks as $k => $v) {
          if (
          !empty($v['start']) && $ds < $v['start'] && !empty($v['fin']) && $v['fin'] < $df && isset($v['jobman']) && isset($job_in[$v['jobman']])
          ) {
          $cheki_da[$v['id']] = $v;

          // если ещё не было записи
          if (!isset($cheki_jobmans[$v['jobman']])) {
          $ocenka = $v['ocenka'] ?? $v['ocenka_auto'];
          $cheki_jobmans[$v['jobman']] = $ocenka;
          }
          // если запись была, то смотрим где ниже оценка
          else {
          $ocenka = $v['ocenka'] ?? $v['ocenka_auto'];
          if ($ocenka < $cheki_jobmans['jobman']) {
          $cheki_jobmans[$v['jobman']] = $ocenka;
          }
          }
          }
          }

          // \f\pa($cheki_da, 2, '', '$cheki_da');
          \f\pa($cheki_jobmans, 2, '', '$cheki_jobmans список работников > оценка');
         */


//        \f\timer::start(11);
//        
//        $dolgns = \Nyos\mod\items::getItemsSimple($db, \Nyos\mod\JobDesc::$mod_salary);
//// \f\pa($dolgns, 5, '', '$dolgns');
//
//        $dd = [];
//
//        foreach ($dolgns['data'] as $k => $v) {
//            $dd[] = $v['dop'];
//        }
//
//        usort($dd, "\\f\\sort_ar_date");
//        // \f\pa($dd, 5, '', '$dolgns2');
//
//        echo '<br/>tt:'.\f\timer::stop('str',11);
//        
//        \f\timer::start(11);
//        


        if ($show_comment === true)
            \f\timer_start(12);

        $dd = \Nyos\mod\items::getItemsSimple3($db, \Nyos\mod\JobDesc::$mod_salary, 'show', 'date_asc');

        if ($show_comment === true)
            echo '<br/>get salary (' . __LINE__ . '): ' . \f\timer_stop(12);


// \f\pa($dolgns, 5, '', '$dolgns2');
//        echo '<br/>tt:'.\f\timer::stop('str',11);
//        die();

        $d = [];

        foreach ($dd as $k => $v) {

            if ($v['date'] > $_d)
                continue;

            if (isset($v['oborot_sp_last_monht_bolee'])) {

                if ($oborot_month >= $v['oborot_sp_last_monht_bolee']) {

//                echo '<br/>[' . $v['dolgnost'] . '] ' . __LINE__ . ' ' . $v['oborot_sp_last_monht_bolee'] . ' ++ ' . $oborot_month;
//                \f\pa($v);

                    $d[$v['dolgnost']] = $v;
                }
            }

//
            elseif (isset($v['oborot_sp_last_monht_menee'])) {
                if ($oborot_month <= $v['oborot_sp_last_monht_menee']) {

//                echo '<br/>[' . $v['dolgnost'] . '] ' . __LINE__ . ' ' . $v['oborot_sp_last_monht_menee'] . ' ++ ' . $oborot_month;
//                \f\pa($v);
                    $d[$v['dolgnost']] = $v;
                }
            }

//
            else {

//            echo '<br/>[' . $v['dolgnost'] . '] ' . __LINE__ . ' ';
//            \f\pa($v);
                $d[$v['dolgnost']] = $v;
            }
        }

        if ($show_comment === true)
            echo '<br/>tt(' . __LINE__ . '): ' . \f\timer::stop('str', 123);


        $adds = [];

        /**
         * массив куда ложим что за бонусы ужи дали, чтобы одному челу в один день делать только 1 бонус
         */
        $no_repeat_adds = [];

// \f\pa($d, 5, '', '$d должности на ' . $_d);
// перебираем чеки ищем кто работает и номер чека берём для комментария
        foreach ($checks as $k => $v) {

//        echo '<hr>';

            if (isset($job_in[$v['jobman']])) {

//            echo '<br/>jm:' . $v['jobman'];
//            echo '<br/>должность:' . $job_in[$v['jobman']];

                $ocenka = $v['ocenka'] ?? $v['ocenka_auto'] ?? null;
//            echo '<br/>оценка:' . $ocenka;

                if (empty($ocenka))
                    continue;

                $premiya = $d[$job_in[$v['jobman']]]['premiya-' . $ocenka] ?? null;

                if (!empty($premiya)) {
//                echo '<Br/>pr ' . $premiya;


                    if (!isset($no_repeat_adds[$_sp][$v['jobman']][$_d])) {
                        $no_repeat_adds[$_sp][$v['jobman']][$_d] = 1;
                        $adds[] = [
                            'auto_bonus_zp' => 'da',
                            'jobman' => $v['jobman'],
                            'sale_point' => $_sp,
                            'date_now' => $_d,
                            'summa' => $premiya,
                            'text' => 'бонус к зп',
                        ];
                    }

//                $add = [
//                    'auto_bonus_zp' => 'da',
//                    'jobman' => $v['jobman'],
//                    'sale_point' => $_sp,
//                    'date_now' => $_d,
//                    'summa' => $premiya,
//                    'text' => 'бонус к зп',
//                ];
//
//                \Nyos\mod\items::addNewSimple($db, \Nyos\mod\JobDesc::$mod_bonus, $add);
                }
            }
        }

        if ($show_comment === true)
            echo '<br/>tt(' . __LINE__ . '): ' . \f\timer::stop('str', 123);

        if (!empty($adds)) {
// \f\pa($adds);
            \Nyos\mod\items::addNewSimples($db, \Nyos\mod\JobDesc::$mod_bonus, $adds);
            return \f\end3('bonus exists', true, ['adds' => $adds]);
        } else {

            return \f\end3('no bonus', false);
        }

// return \f\end3('ok', true, $return);
// return $return;
    }

    /**
     * где работает работник
     * @param type $db
     * @param type $sp_id
     * @param type $user_id
     * @param type $jobs
     * список назначений сотрудника
     * @param type $date_start
     * дата старта
     * @param type $date_finish
     * дата финища
     */
    public static function whereJobJobman($db, $sp_id, $user_id, $jobs, $date_start, $date_finish) {

        if (empty(self::$WhereJobMans))
            throw new \Exception('капец нет инфы');

        $return_date_jobs = [];

        // \f\pa($jobs,2,'','jj');
        usort($jobs, "\\f\\sort_ar_date");
        \f\pa($jobs, 2, '', 'jj');

        $now_job = [];

        foreach ($jobs as $j) {
            if ($j['date'] < $date_start)
                $now_job = $j;
        }

        for ($n = 0; $n <= 32; $n++) {

            $now_spec = [];
            $date_now = date('Y-m-d', strtotime($date_start . ' +' . $n . ' day'));

            if ($date_now > $date_finish)
                break;

            foreach ($jobs as $v2) {

                if ($v2['date'] == $date_now) {

                    if (!empty($v2['type']) && $v2['type'] == 'spec') {
                        $now_spec[$v2['id']] = $v2;
                    } else {
                        $now_job = $v2;
                    }

                    if ($user_id == 187)
                        \f\pa($v2, 2, '', 'job v2');
                }
            }

            $return_date_jobs[$date_now] = [
                'job' => $now_job ?? [],
                'spec' => $now_spec ?? [],
                'metki' => self::$ar_metki_jm_date_sp_type[$user_id][$date_now][$sp_id] ?? []
            ];
        }

        return $return_date_jobs;
    }

    /**
     * считаем автобонусы на 1 точке всем сотрудникам
     * @param type $db
     * @param type $_sp
     * @param type $dt0
     * @return type
     */
    public static function getChecks($db, $jobMans = [], $date = '') {

        if (!empty($date)) {
            $ds = date('Y-m-01 03:00:00', strtotime($date));
            \Nyos\mod\items::$between_datetime['start'] = [$ds, date('Y-m-d 03:00:00', strtotime($ds . ' +1 month +1 day'))];
        }

        if (!empty($jobMans))
            \Nyos\mod\items::$search['jobman'] = array_keys($jobMans);

        $checks0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);
        // \f\pa($checks0,2,'','$checks0');
        \Nyos\mod\JobDesc::$ar_jm_date_checks = [];

        foreach ($checks0 as $k => $v) {

            if (isset($v['jobman']))
                $now_check_date = date('Y-m-d', strtotime($v['start'] . ' -4 hour'));

            \Nyos\mod\JobDesc::$ar_jm_date_checks[$v['jobman']][$now_check_date][] = $v;
        }

        return \Nyos\mod\JobDesc::$ar_jm_date_checks;
    }

    /**
     * считаем автобонусы на 1 точке всем сотрудникам
     * @param type $db
     * @param type $_sp
     * @param type $dt0
     * @return type
     */
    public static function creatAutoBonusMonth($db, $_sp, $dt0) {

        $show_html = true;
        $show_html = false;

        $show_comment = true;
        $show_comment = false;


        $dt = strtotime($dt0);
        $_d = date('Y-m-d', $dt);
        $ds = date('Y-m-d 05:00:00', $dt);
        $df = date('Y-m-d 04:00:00', $dt + 3600 * 24);


// старт и конец месяца
        $date_start = date('Y-m-01', $dt);
        $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

        /**
         * удаляем все смены что были ранее
         */
        $ww1 = \Nyos\mod\JobDesc::deleteAutoBonusMonth($db, $_sp, $_d);
        // \f\pa($ww1,'','','\Nyos\mod\JobDesc::deleteAutoBonusMonth');
//        exit;

        if ($show_comment === true)
            \f\timer_start(47);

        $bo = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_bonus);
        foreach ($bo as $v) {
            // \f\pa($v);
            self::$ar__sp_user_date[$v['sale_point']][$v['jobman']][$v['date_now']] = 1;
        }


        // data 
        if (1 == 1) {

            self::$WhereJobMans = self::getJobsJobMans($db, $date_start, '', $_sp);

            if ($show_comment === true)
                \f\pa(self::$WhereJobMans, 2, '', 'кто где работает');

            \Nyos\mod\items::$between_datetime['start'] = [date('Y-m-d 03:00:00', strtotime($date_start)), date('Y-m-d 03:00:00', strtotime($date_finish . ' +1 day'))];
            \Nyos\mod\items::$search['jobman'] = array_keys(self::$WhereJobMans['data']['ar_jm']);

            $checks0 = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_checks);

            // \f\pa($checks0,2,'','$checks0');
            self::$ar_jm_date_checks = [];

            foreach ($checks0 as $k => $v) {

                if (isset($v['jobman']))
                    $now_check_date = date('Y-m-d', strtotime($v['start'] . ' -4 hour'));

                self::$ar_jm_date_checks[$v['jobman']][$now_check_date][] = $v;
            }

            unset($checks0);
            if ($show_comment === true)
                \f\pa(self::$ar_jm_date_checks, 2, '', 'self::$ar_jm_date_checks');

            if ($show_comment === true)
                echo '<br/>get checks (' . __LINE__ . '): ' . \f\timer_stop(47);

            // metki // self::$ar_metki_jm_date_sp_type
            self::$ar_metki_jm_date_sp_type = self::getMetki($db, $_sp, $date_start, $date_finish);
            if ($show_comment === true)
                \f\pa(self::$ar_metki_jm_date_sp_type, 2, '', 'metki');

            self::$salary_ar__sp_dolgn_date = self::getAllSalary($db);
            if ($show_comment === true)
                \f\pa(self::$salary_ar__sp_dolgn_date, 2, '', 'self::$salary_ar__sp_dolgn_date');
        }


// перебираем пользователей, 
// даты их работы 
// и вычисляем кому сколько бонусов накинуть
//        foreach (self::$WhereJobMans['data']['ar_jm'] as $user_id => $v) {
//            echo '<Br/>'.$user_id;
//        }


        \f\timer_start(3);

        /**
         * сюда ложим все автобонусы
         */
        $add_bonuses = [];

        foreach (self::$WhereJobMans['data']['ar_jm'] as $user_id => $v) {

            $show_html = true;
            $show_html = false;

            if (isset($_REQUEST['show_user']) && $_REQUEST['show_user'] == $user_id) {
                //if ($user_id == 241)
                $show_html = true;
            }

            if ($show_html === true)
                echo '<ul>';
            if ($show_html === true)
                echo '<li> user #' . $user_id;

            if ($show_html === true)
                echo '<Br/>' . \f\timer_stop(3);


            $smens7 = \Nyos\mod\JobDesc::newGetSmensFullMonth($db, $user_id, $date_start);
            // \f\pa($smens7, 2, '', '$smens7');
            // die();

            if ($show_html === true)
                echo '<ul>';

            for ($n = 0; $n <= 32; $n++) {

                // если бонус дали = тру
                $bonus_day = false;

                $now_spec = [];
                $date_now = date('Y-m-d', strtotime($date_start . ' +' . $n . ' day'));




                if ($show_html === true)
                    echo '<li>';

                if (isset(self::$ar_metki_jm_date_sp_type[$user_id][$date_now][$_sp]['no_autobonus'])) {
                    if ($show_html === true)
                        echo '<br/>не считаем бонусы, так как есть метка "без бонусов"</li>';
                    continue;
                }

                if ($date_now > $date_finish) {
                    if ($show_html === true)
                        echo '<br/>конец по дате финиша ' . $date_now . '</li>';
                    break;
                }

                if ($show_html === true)
                    echo '<h3 style="background-color: rgba(255,150,150,0.2);" >date: ' . $date_now . '</h3>';

                if (isset(self::$ar_jm_date_checks[$user_id][$date_now])) {

                    $job = '';

                    // \f\pa(self::$ar_jm_date_checks[$user_id][$date_now], 2, '', 'checks');
                    foreach (self::$ar_jm_date_checks[$user_id][$date_now] as $check) {

                        //если уже выдали бонус .. не проверяем другие смены
                        if ($bonus_day !== false)
                            break;

                        if ($show_html === true)
                            \f\pa($check, 2, '', 'check');

                        $dolgnost_now = '';

                        // достаём где работает сегодня
                        if (empty($job)) {
                            $job = self::whatJobDate($db, $user_id, $date_now);
                            if ($show_html === true)
                                \f\pa($job);
                        }

//                        // если номр работа
//                        if (!empty($job['norm']) && empty($job['spec'])) {
//                            $dolgnost_now = $job['norm']['dolgnost'];
//                        }
//                        // если есть спец назначение на эту ТП
//                        elseif ( !empty($job['spec'][$_sp]['dolgnost']) ) {
//                            $dolgnost_now = $job['spec'][$_sp]['dolgnost'];
//                        }
//                        if ($user_id == 237 && $date_now == '2020-06-13') {
//                            \f\pa($check);
//                            \f\pa($job);
//                        }
                        // пропускаем                        
                        // если есть спец.назначение на др точку и нет на текущую
                        if (!empty($job['spec']) && empty($job['spec'][$_sp])) {
                            
                        }
                        // обрабатываем
                        else {

                            $dolgnost_now = $job['spec'][$_sp]['dolgnost'] ?? $job['norm']['dolgnost'] ?? null;
                        }

                        // if ($user_id == 237 && $date_now == '2020-06-13') {
                        if ($user_id == 101362 && $date_now == '2020-06-02') {
                            \f\pa($dolgnost_now);
                        }
// если нет должности
                        if (empty($dolgnost_now)) {
                            // \f\pa(\f\end3('Внимание, нет должности у сотрудника', true, ['sp' => $_sp, 'user' => $user_id, 'date' => $date_now]));
                        }
                        // если есть должность
                        else {

                            if ($show_html === true)
                                \f\pa($dolgnost_now, 2, '', '$dolgnost_now');

                            // $salarys = self::getSalarisNow($db, $_sp, $dolgnost_now, $date_now);
                            foreach ($smens7['data'] as $k => $v) {
                                if ($v['date'] == $date_now) {
                                    $salarys = $v['money'];
                                    $money_sp = !empty($v['spec1_sp']) ? $v['spec1_sp'] : !empty($v['job_sp']) ? $v['job_sp'] : '';
                                    break;
                                }
                            }


                            if ($show_html === true)
                                \f\pa($salarys, 2, '', 'salarys');


                            // $new_bonus = 
                            // \f\pa( [ $_sp, $user_id, $date_now, $salarys, $check , '$money_sp' => $money_sp ] );

                            if ($money_sp != $_sp)
                                continue;

                            $add = self::setupAutoBonus($db, $_sp, $user_id, $date_now, $salarys, $check);

                            if ($show_html === true)
                                \f\pa($add, 2, '', 'add bonus');

                            if (empty(self::$ar__sp_user_date[$add['data']['sale_point']][$add['data']['jobman']][$add['data']['date_now']])) {
                                if (!empty($add['data'])) {
                                    $bonus_day = true;
                                    $add_bonuses[] = $add['data'];
                                }
                            }
                        }
                    }
                }
                if ($show_html === true)
                    echo '</li>';
            }
            if ($show_html === true)
                echo '</ul>';

            if ($show_html === true)
                echo '</li>';
            if ($show_html === true)
                echo '</ul>';
        }

        if (!empty($add_bonuses)) {
            // \f\pa($add_bonuses, 2, '', '$add_bonuses');
            \Nyos\mod\items::addNewSimples($db, \Nyos\mod\JobDesc::$mod_bonus, $add_bonuses);
            return \f\end3('bonus exists', true, ['adds' => sizeof($add_bonuses)]);
        } else {
            return \f\end3('bonus no exists', false);
        }
    }

    /**
     * получаем все метки что есть в датах на точке
     * @param type $db
     * @param type $_sp
     * @param type $date_start
     * @param type $date_finish
     * @return int
     */
    public static function getMetki($db, $sp, $date_start, $date_finish = null) {

        if (empty($date_finish))
            $date_finish = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date_start)) . ' +1month -1 day'));

        \Nyos\mod\items::$between['date'] = [$date_start, $date_finish];
        \Nyos\mod\items::$search['sale_point'] = $sp;
        \Nyos\mod\items::$search['status'] = 'show';
        // $metki0 = \Nyos\mod\items::get($db, self::$mod_metki);
        // \Nyos\mod\items::$show_sql = true;
        // $metki0 = \Nyos\mod\items::get2007($db, self::$mod_metki);
        $metki0 = \Nyos\mod\items::get($db, self::$mod_metki);

        // self::$ar_metki_jm_date_sp_type = [];
        $r = [];

        foreach ($metki0 as $k => $v) {
            // self::$ar_metki_jm_date_sp_type[$v['jobman']][$v['date']][$v['sale_point']][$v['type']] = 1;
            $r[$v['jobman']][$v['date']][$v['sale_point']][( $v['type'] ?? $v['type2'] ?? 'x' )] = 1;
        }

        unset($metki0);
        return $r;
    }

    /**
     * удаляем автобонусы за весь месяц
     * @param type $db
     * @param type $sp
     * @param type $date0
     * @return type
     */
    public static function deleteAutoBonusMonth($db, $sp, $date0) {

        $date_start = date('Y-m-01', strtotime($date0));
        $date_finish = date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

        $who = \Nyos\mod\JobDesc::whereJobmansNowDate($db, $date_start, $_REQUEST['sp']);
        // \f\pa($who, 2, '', 'who on job today');

        \Nyos\mod\items::$search['auto_bonus_zp'] = 'da';
        \Nyos\mod\items::$search['jobman'] = array_keys($who);
        \Nyos\mod\items::$between_date['date_now'] = [$date_start, $date_finish];
        // \Nyos\mod\items::$return_items_header = true;
        $items = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_bonus);
        // \f\pa( sizeof($items) );
        // \f\pa($items,2,'','items');

        if (empty($items))
            return \f\end3('норм, нечего удалять', true);

        $list = array_keys($items);
        \Nyos\mod\items::deleteIds($db, $list);

        // \f\Cash::deleteKeyPoFilter([\Nyos\mod\JobDesc::$mod_bonus]);

        return \f\end3('что то удалили', true, ['ids' => $list]);
    }

    /**
     * считаем оценку 1 дня (при автоматическом выставлении оценок
     * @param type $db
     * @param type $sp
     * @param type $data
     */
    public static function calculateAutoOcenkaDays($db, $sp, $date) {

        $return = \Nyos\mod\JobDesc::readVarsForOcenkaDays($db, $sp, $date);
        \f\pa($return, 2, '', '$return readVarsForOcenkaDays');

        die('<br/>end ' . __FILE__ . ' #' . __LINE__);







// echo '<br/>' . __FILE__ . ' #' . __LINE__;
// return \f\end3( 'wef', true );
// ob_start('ob_gzhandler');

        try {

            if (1 == 1) {

//                die();
// массив чеков для новых оценок
// $return['checks_for_new_ocenka']
            }

            return \f\end3('ok ' . __LINE__, true);

            if (1 == 1) {
// \f\pa($return['data'], 2, '', '$return данные для оценки дня');
                $return['data']['ocenka-data'] = $ocenka = \Nyos\mod\JobDesc::calcOcenkaDay($db, $return['data']);
//\f\pa($ocenka, 2, '', '$ocenka');
//die();
// $return = array_merge($return, $ocenka);

                $ocenki_error = '';

                if (empty($ocenka['data']['ocenka_time']))
                    $ocenki_error .= 'нет оценки по времени ожидания';

                if (empty($ocenka['data']['ocenka_naruki']))
                    $ocenki_error .= (!empty($ocenki_error) ? ', ' : '' ) . 'нет оценки по сумме на руки';

                if (!empty($ocenki_error))
                    throw new \Exception($ocenki_error);

                $return['data']['ocenka_naruki'] = $ocenka['data']['ocenka_naruki'];
                $return['data']['ocenka_time'] = $ocenka['data']['ocenka_time'];
                $return['data']['ocenka'] = $ocenka['data']['ocenka'];
            }

            if (1 == 1) {

//        if ( class_exists('\Nyos\mod\items') )
//            echo '<br/>' . __FILE__ . ' ' . __LINE__;
// if (!empty($return['data']['checks_for_new_ocenka'])) {
// \f\pa( $return['checks_for_new_ocenka'], 2 , '' , 'checks_for_new_ocenka' );

                $return['data']['ocenka-save'] = \Nyos\mod\JobDesc::recordNewAutoOcenkiDay($db,
                                $return['data']['checks_for_new_ocenka'],
                                $ocenka['data']['ocenka']);

// }
                $return['data']['ocenka-save2'] = \Nyos\mod\items::addNewSimple($db, \Nyos\mod\jobdesc::$mod_ocenki_days, [
                            'sale_point' => $ocenka['data']['sp'],
                            'date' => $ocenka['data']['date'],
                            'ocenka_time' => $ocenka['data']['ocenka_time'],
                            'ocenka_naruki' => $ocenka['data']['ocenka_naruki'],
                            'ocenka' => $ocenka['data']['ocenka'],
                ]);
            }
// $r = ob_get_contents();
// ob_end_clean();

            return \f\end3('ok ' . ( $r ?? '--' ), true, $return['data']);

            if (1 == 2) {

// require_once DR . '/all/ajax.start.php';
// $ff = $db->prepare('UPDATE `mitems` SET `status` = \'hide\' WHERE `id` = :id ');
// $ff->execute(array(':id' => (int) $_POST['id2']));
//die('123');
//
//echo '<br/>'.__FILE__.' '.__LINE__;
//    $checki = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout', 'show');
//    \f\pa($checki,2,'','$checki');
//echo '<br/>'.__FILE__.' '.__LINE__;
//    $salary = \Nyos\mod\JobDesc::configGetJobmansSmenas($db);
//    \f\pa($salary,2,'','$salary');
//    $return['txt'] .= '<br/>salary';
//    foreach ($salary as $k => $v) {
//        $return['txt'] .= '<br/><nobr>[' . $k . '] - ' . $v . '</nobr>';
//        $return['salary_' . $k] = $v;
//    }
//echo '<br/>'.__FILE__.' '.__LINE__;
//echo '<br/>'.__FILE__.' '.__LINE__;
//echo '<br/>'.__FILE__.' '.__LINE__;
// \f\pa($return);
// exit;
//\f\pa($return);
// если есть ошибки
                if (!empty($error)) {

                    require_once DR . dir_site . 'config.php';

                    $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
// \f\pa($sp);

                    if (1 == 2 && !isset($_REQUEST['no_send_msg'])) {
                        $txt_to_tele = 'Обнаружены ошибки при расчёте оценки точки продаж (' . $sp['data'][$_REQUEST['sp']]['head'] . ') за день работы (' . $_REQUEST['date'] . ')' . PHP_EOL . PHP_EOL . $error;

                        if (class_exists('\nyos\Msg'))
                            \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);

                        if (isset($vv['admin_ajax_job'])) {
                            foreach ($vv['admin_ajax_job'] as $k => $v) {
                                \nyos\Msg::sendTelegramm($txt_to_tele, $v);
//\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
                            }
                        }
                    }
//echo '<br/>'.__FILE__.' '.__LINE__;

                    return \f\end2('Обнаружены ошибки при расчёте оценки точки продаж (' . $_REQUEST['sp'] . ') за день работы (' . $_REQUEST['date'] . ')' . $error, false);
                }
// если нет ошибок считаем
                else {

                    \f\timer::start();

                    /**
                     * сравниваем время ожидания холодный цех
                     */
                    if (isset($return['timeo_cold']) && isset($return['norm_time_wait_norm_cold'])) {

                        $return['txt'] .= '<br/><br/>-------------------';
                        $return['txt'] .= '<br/>время ожидания (хол.цех)';
                        $return['txt'] .= '<br/>по плану: ' . $return['norm_time_wait_norm_cold'] . ' и значение в ТП ' . $return['timeo_cold'];

                        if (isset($return['timeo_cold']) && isset($return['norm_time_wait_norm_cold']) &&
                                $return['timeo_cold'] > $return['norm_time_wait_norm_cold']) {

                            $return['txt'] .= '<br/>не норм, оценка 3';
                            $return['ocenka_time'] = 3;
                            $return['ocenka'] = 3;
                        } else {
                            $return['txt'] .= '<br/>норм, оценка 5';
                            $return['ocenka_time'] = 5;
                        }
                    } else {
                        throw new \Exception('Вычисляем оценку дня, прервано, не хватает данных по времени ожидания', 14);
                    }

                    /**
                     * сравниваем объём выручки
                     */
                    if (1 == 2) {
                        if (!empty($return['norm_vuruchka']) && !empty($return['oborot'])) {

                            $return['txt'] .= '<br/><br/>-------------------';
                            $return['txt'] .= '<br/>норма выручки';
                            $return['txt'] .= '<br/>по плану: ' . $return['norm_vuruchka'] . ' и значение в ТП ' . $return['oborot'];

                            if ($return['oborot'] >= $return['norm_vuruchka']) {
                                $return['oborot_bolee_norm'] = 1;
                                $return['ocenka_oborot'] = 5;
                                $return['txt'] .= '<br/>норм, оценка 5';
                            } else {
                                $return['oborot_bolee_norm'] = 0;
                                $return['ocenka_oborot'] = 3;
                                $return['ocenka'] = 3;
                                $return['txt'] .= '<br/>не норм, оценка 3';
                            }
                        }
//
                        else {
                            throw new \Exception('Вычисляем оценку дня, прервано, не хватает данных по обороту за сутки', 18);
                        }
                    }

                    /**
                     * считаем норму выручки на руки
                     */
// if (!empty($return['norm_kolvo_hour_in1smena'])) {
                    if (!empty($return['norm_kolvo_hour_in1smena']) && !empty($return['norm_vuruchka_on_1_hand'])) {

                        $return['txt'] .= '<br/><br/>-------------------';
                        $return['txt'] .= '<br/>норма выручки (на руки)';

                        $return['smen_in_day'] = round($return['hours'] / $return['norm_kolvo_hour_in1smena'], 1);
                        $return['txt'] .= '<br/>Кол-во поваров: ' . $return['smen_in_day'];

                        $return['on_hand_fakt'] = ceil($return['oborot'] / $return['smen_in_day']);
// $return['summa_na_ruki_norm'] = ceil($return['oborot'] / 100 * $return['norm_procent_oplata_truda_on_oborota']);
//$return['txt'] .= '<br/>по плану: ' . $return['summa_na_ruki_norm'] . ' и значение в ТП ' . $return['on_hand_fakt'];
                        $return['txt'] .= '<br/>по плану: ' . $return['norm_vuruchka_on_1_hand'] . ' и значение в ТП ' . $return['on_hand_fakt'];

                        if ($return['on_hand_fakt'] < $return['norm_vuruchka_on_1_hand']) {
                            $return['ocenka'] = 3;
                            $return['ocenka_naruki'] = 3;
                            $return['ocenka'] = 3;
                            $return['txt'] .= '<br/>не норм, оценка 3';
                        } else {
                            $return['ocenka_naruki'] = 5;
                            $return['txt'] .= '<br/>норм, оценка 5';
                        }
                    } else {
                        throw new \Exception('Вычисляем оценку дня, прервано, не хватает значения по плану (норма на руки)', 19);
                    }


                    $return['txt'] .= '<br/>';
                    $return['txt'] .= '<br/>';
                    $return['txt'] .= '-----------';
                    $return['txt'] .= '<br/>';
                    $return['txt'] .= 'оценка дня : ' . $return['ocenka'];
                    $return['txt'] .= '<br/>';
                    $return['txt'] .= '<br/>';
                    $return['txt'] .= '<br/>';

// $return['ocenka_upr'] = $return['ocenka'];
//            $return['time'] .= PHP_EOL . ' считаем ходится не сходится : ' . \f\timer::stop();
//            $return['txt'] .= '<br/><nobr>рекомендуемая оценка упр: ' . $return['ocenka_upr'] . '</nobr>';


                    /**
                     * запись результатов в бд
                     */
                    if (1 == 1) {
                        $sql_del = '';
                        $sql_ar_new = [];

                        foreach ($id_items_for_new_ocenka as $id_item => $v) {

                            $sql_del .= (!empty($sql_del) ? ' OR ' : '' ) . ' id_item = \'' . (int) $id_item . '\' ';
                            $sql_ar_new[] = array(
                                'id_item' => $id_item,
                                'name' => 'ocenka_auto',
                                'value' => $return['ocenka']
                            );
                        }

                        if (!empty($sql_del)) {
                            $ff = $db->prepare('DELETE FROM `mitems-dops` WHERE name = \'ocenka_auto\' AND ( ' . $sql_del . ' ) ');
                            $ff->execute();
                        }

                        \f\db\sql_insert_mnogo($db, 'mitems-dops', $sql_ar_new);
                        $return['txt'] .= '<br/>записали автоценки сотрудникам';
                    }

                    require_once DR . dir_site . 'config.php';

                    $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
// \f\pa($sp);

                    \Nyos\mod\items::addNewSimple($db, 'sp_ocenki_job_day', $return);

                    if (1 == 2 && (!isset($_REQUEST['no_send_msg']) && !isset($_REQUEST['telega_no_send']) )) {

                        $txt_to_tele = 'Расчитали автооценку ( ' . $sp['data'][$_REQUEST['sp']]['head'] . ' ) за день работы (' . $_REQUEST['date'] . ')'
                                . PHP_EOL
                                . PHP_EOL
                                . str_replace('<br/>', PHP_EOL, $return['txt'])
//                        . PHP_EOL
//                        . '-----------------'
//                        . PHP_EOL
//                        . 'время выполнения вычислений'
//                        . PHP_EOL
//                        . $return['time']
                        ;

                        if (class_exists('\nyos\Msg'))
                            \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);

                        if (isset($vv['admin_ajax_job'])) {
                            foreach ($vv['admin_ajax_job'] as $k => $v) {
                                \nyos\Msg::sendTelegramm($txt_to_tele, $v);
//\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
                            }
                        }
                    }

                    \f\end2(
                            $return['txt']
                            . '<br/>часов: ' . $return['hours']
                            . '<br/>смен в дне: ' . $return['smen_in_day']
                            , true, $return);
                }

//return \f\end2('Обнаружены ошибки: ' . $ex->getMessage() . ' <Br/>' . $text, false, array( 'error' => $ex->getMessage() ) );        
            }
        }
//
        catch (\Exception $ex) {

// if ( isset($_REQUEST['no_send_msg']) ) {}else{}

            echo '<br/>' . __FILE__ . ' #' . __LINE__;

            $text = $ex->getMessage()
                    . ' авторасчёт оценки дня'
                    . PHP_EOL
                    . PHP_EOL
                    . ' sp:' . ( $return['data']['sp'] ?? '--' )
                    . ' date:' . ( $return['data']['date'] ?? '--' )
                    . PHP_EOL
                    . PHP_EOL
                    . '--- ' . __FILE__ . ' ' . __LINE__ . '-------'
                    . PHP_EOL
                    . $ex->getMessage() . ' #' . $ex->getCode()
                    . PHP_EOL
                    . $ex->getFile() . ' #' . $ex->getLine()
                    . PHP_EOL
                    . $ex->getTraceAsString()
// . '</pre>'
            ;

            if (1 == 2 && class_exists('\Nyos\Msg')) {
                \Nyos\Msg::sendTelegramm($text, null, 1);
            }
            /*
              echo '<pre>'
              . PHP_EOL
              . PHP_EOL
              . '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
              . PHP_EOL
              . $ex->getMessage() . ' #' . $ex->getCode()
              . PHP_EOL
              . $ex->getFile() . ' #' . $ex->getLine()
              . PHP_EOL
              . $ex->getTraceAsString()
              . '</pre>';
             */

            /*

              require_once DR . dir_site . 'config.php';

              $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
              // \f\pa($sp);

              $txt_to_tele = 'Обнаружены ошибки при расчёте оценки точки продаж (' . $sp['data'][$_REQUEST['sp']]['head'] . ') за день работы (' . $_REQUEST['date'] . ')' . PHP_EOL . PHP_EOL . $error;

              if (class_exists('\nyos\Msg'))
              \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);

              if (isset($vv['admin_ajax_job'])) {
              foreach ($vv['admin_ajax_job'] as $k => $v) {
              \nyos\Msg::sendTelegramm($txt_to_tele, $v);
              //\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
              }
              }
             */

//            $r = ob_get_contents();
//            ob_end_clean();

            return \f\end3('Обнаружены ошибки: ' . $ex->getMessage(), false, [
                'error' => $ex->getMessage(),
                'code' => $ex->getCode(),
                'sp' => ( $return['data']['sp'] ?? null ),
                'date' => ( $return['data']['date'] ?? null ),
                // 'text' => $text . '<br/>' . str_replace('<br/>', PHP_EOL, $r),
// 'msg' => $ex->getMessage(),
                'file_line' => $ex->getFile() . ' #' . $ex->getLine(),
                'trace' => explode(PHP_EOL, $ex->getTraceAsString()),
                'datas' => ( $return ?? 'x' ),
            ]);
        }
    }

    public static function getDaysOcenkaNo($db, $start_day = '2019-09-01') {

// список точек продаж для скана
        $lisp_sp = [];
// сколько дней сканим от сегодня назад
        $scan_days = 70;
// echo $start_day;
        $last_day = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 3600 * 24);

        /**
         * собираем список точек продаж
         */
        $sps = \Nyos\mod\items::getItemsSimple($db, self::$mod_sale_point);
// \f\pa($sps, 2, '', '$sps');
// выбираем тех у кого id для получения оборота
        foreach ($sps['data'] as $k => $v) {
            if (!empty($v['dop']['id_tech_for_oborot']))
                $lisp_sp[$v['id']] = 1;
        }
//\f\pa($lisp_sp, 2, '', '$lisp_sp');


        $e = \Nyos\mod\items::getItemsSimple($db, self::$mod_ocenki_days);
// \f\pa($e,2,'','$e оценки self::$mod_ocenki_days');

        $r = [];


//        echo ' -+ ' . $start_day;
//        echo ' -- ' . $last_day;
//        echo '<br/>';

        foreach ($e['data'] as $k => $v) {

            if (isset($v['dop'])) {

                if (isset($v['dop']['date']) && ( $v['dop']['date'] > $last_day || $v['dop']['date'] < $start_day )) {

// echo '<br/>дата в центре, не сходится = ' . $start_day .' = '. $last_day .' | '.$v['dop']['date'];
                    continue;
                } else {
// echo '<br/>дата в центре, сходится = ' . $start_day .' = '. $last_day .' | '.$v['dop']['date'];
                }

//                if (!isset($lisp_sp[$v['dop']['sale_point']]))
//                    $lisp_sp[$v['dop']['sale_point']] = 1;
//                echo ' -- ' . $v['dop']['date'];
//                echo ' || ' . $v['dop']['sale_point'];
//                echo '<br/>';

                $r[$v['dop']['date']][$v['dop']['sale_point']] = $v['dop'];
            }
        }


        foreach ($r as $date => $v1) {

            foreach ($lisp_sp as $sp => $v2) {

                if (empty($v1[$sp])) {
                    $r[$date][$sp] = false;
                }
            }
        }


        krsort($r);
// \f\pa($r, 2, '', '$r');

        return \f\end3('ok', true, $r);
    }

    /**
     * записываем новые авто оценки для смен
     * @param type $db
     * @param type $array_checks
     * @param type $ocenka
     * @return type
     */
    public static function recordNewAutoOcenkiDay($db, $array_checks, $ocenka) {

// echo '<br/>'.__FUNCTION__;
// die();
// строка для удаления
        $check_string = '';

// массив для удаления
        $check_ar = // масив для вставки новых данных
                $rows_in = [];

        $nn = 1;
        foreach ($array_checks as $check) {

            $check_string .= (!empty($check_string) ? ' OR ' : '' ) . ' `id_item` = :check' . $nn . ' ';
            $check_ar[':check' . $nn] = $check;

            $rows_in[] = ['id_item' => $check];

            $nn++;
        }

        if (!empty($check_string)) {
            $ff = $db->prepare('DELETE FROM `mitems-dops` WHERE ( ' . $check_string . ' ) AND `name` = \'ocenka_auto\' ;');
            $ff->execute($check_ar);
        }

        \f\db\sql_insert_mnogo($db, 'mitems-dops', $rows_in, ['name' => 'ocenka_auto', 'value' => $ocenka]);

//        \f\db\db2_insert( $db, 'mitems-dops', array(
//            'id_item' => (int) $_REQUEST['work_id'],
//            'name' => 'date_finish',
//            'value_date' => date('Y-m-d', strtotime($_REQUEST['date_end']))
//                )
//        );

        return \f\end3('ок', true, ['check_string' => ( $check_string ?? 'x')]);
    }

    /**
     * 
     * @return считаем платим бонус или нет
     */
    public static function calcBonusOneDay() {


        return \f\end3('не', false, []);
    }

    /**
     * 
     * @return устанавливаем автобонус
     */
    public static function setupAutoBonus($db, $sp, $user_id, $date_now, $salary, $check) {

        $show_cod = false;

//        if ($user_id == 201)
//            $show_cod = true;

        if ($show_cod) {
            \f\pa($sp);
            \f\pa($user_id);
            \f\pa($date_now);
            \f\pa($salary, 2, '', '$salary1');
            \f\pa($check, 2, '', '$check1');
        }

        $dop_text = '';

        if (!empty($salary['bonus_proc_from_oborot'])) {

//            $bonus_put = true;

            if (empty(\Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date_now]))
                \Nyos\mod\IikoOborot::getOborotMonth($db, $sp, $date_now);

            //$oborot = \Nyos\mod\IikoOborot::getDayOborot($db, $_sp, $date_now);
            $oborot = \Nyos\mod\IikoOborot::$cash_oborot_day_ar_sp_date[$sp][$date_now] ?? 0;
            if ($show_cod)
                \f\pa($oborot, 2, '', '$oborot day');
            // $dop_text = ' ' . $salary['bonus_proc_from_oborot'] . '% от ' . $oborot;

            $dop_text = '(' . $salary['bonus_proc_from_oborot'] . '% от ' . ceil($oborot) . ')';
            $bonus_summa = ceil($oborot / 100 * $salary['bonus_proc_from_oborot']);
        }

        // обычный бонус по оценке в смене
        if (!empty($salary['premiya-3']) || !empty($salary['premiya-5'])) {

            $ocenka_now = $check['ocenka'] ?? $check['ocenka_auto'] ?? null;
//            $bonus_put = true;
            $bonus_summa = $salary['premiya-' . $ocenka_now] ?? null;
        }

        if (!empty($bonus_summa)) {

            if (isset(self::$ar_metki_jm_date_sp_type[$user_id][$date_now][$sp]['autobonus50pr'])) {
                $dop_text .= '(половина от ' . $bonus_summa . ')';
                $bonus_summa = ceil($bonus_summa / 2);
            }

            return \f\end3('добавляем бонус', true, [
                'auto_bonus_zp' => 'da',
                'jobman' => $user_id,
                'sale_point' => $sp,
                // 'date_now' => $_d,
                'date_now' => $date_now,
                'summa' => $bonus_summa,
                'text' => 'бонус к зп ' . $dop_text,
            ]);
        } else {
            return \f\end3('не', false);
        }
    }

    /**
     * расчитываем какая оценка дня опираясь на массив данных из функции self::compileVarsForOcenkaDay($db, $sp, $date)
     * 1911
     * @param type $db
     * @param type $ar
     * @return type
     */
    public static function calcOcenkaDay($db, $return) {

        $text = '';
        $text_s = '<br/>';

        $vv1 = [
            'date' => '',
            'sp' => '',
            'norm_vuruchka_on_1_hand' => '',
            'norm_time_wait_norm_cold' => '',
            'norm_time_wait_norm_hot' => '',
            'norm_time_wait_norm_delivery' => '',
            'oborot' => '',
            'hours' => '',
            'timeo_cold' => 'время хол цех',
            'timeo_hot' => 'время гор цех',
            'timeo_delivery' => 'время доставка',
        ];

        foreach ($vv1 as $vv => $vv_text) {

            if ($vv == 'norm_time_wait_norm_hot' || $vv == 'norm_time_wait_norm_delivery')
                continue;

            if (empty($return[$vv])) {

                if ($vv == 'timeo_hot') {

                    $norms_def = \Nyos\mod\JobDesc::whatNormToDayDefault($db);
                    $return[$vv] = $norms_def[2];
                } elseif ($vv == 'timeo_cold') {

                    $norms_def = \Nyos\mod\JobDesc::whatNormToDayDefault($db);
                    $return[$vv] = $norms_def[1];
                } elseif ($vv == 'timeo_delivery') {

                    $norms_def = \Nyos\mod\JobDesc::whatNormToDayDefault($db);
                    $return[$vv] = $norms_def[3];
                } else {

//
                    if (!empty($vv) && $vv == 'oborot') {
                        throw new \Exception('нет оборота по точке (' . $vv_text . ')', 201);
                    }
//
                    elseif (!empty($vv) && $vv == 'hours') {
                        // throw new \Exception('нет количества часов работы за день для расчёта (' . $vv_text . ')', 203);
                    }
//
                    else {
                        throw new \Exception('нет значения ' . $vv . ' (' . $vv_text . ')', __LINE__);
                    }
                }
            }
        }

        $re = [
            'sp' => $return['sp'],
            'date' => $return['date'],
            'ocenka_time' => 0,
            //$return['ocenka_oborot'] => 5;
            'ocenka_naruki' => 0,
            'ocenka' => 0,
                // 'txt' => ''
        ];

        $return['txt'] .= '<br/>оборот точки: ' . $return['oborot'];

        /**
         * вычисление оценки на руки
         */
        $return['smen_in_day'] = round($return['hours'] / $return['norm_kolvo_hour_in1smena'], 1);

        $re['hour_day'] = $return['hours'];
        $return['txt'] .= '<br/>суммарно отработано часов:' . $return['hours'];
        $return['txt'] .= '<br/>часов в 1 смене:' . $return['norm_kolvo_hour_in1smena'];
        $return['txt'] .= '<br/>отработано смен: ' . $return['hours'] . ' / ' . $return['norm_kolvo_hour_in1smena'] . ' = ' . $return['smen_in_day'];

        if (!empty($return['oborot']) && !empty($return['smen_in_day'])) {
            $re['money_norm_ot_oborota'] = $return['summa_na_ruki'] = ceil($return['oborot'] / $return['smen_in_day']);
            $return['txt'] .= '<br/>на 1 руки денег: ' . $return['oborot'] . ' / ' . $return['smen_in_day'] . ' = ' . $return['summa_na_ruki'];
        }

        $text .= $return['summa_na_ruki'] . ' (сейчас) >= (норма) ' . $return['norm_vuruchka_on_1_hand'] . $text_s;

// если на руки больше нормы то оценка 5
        if ($return['summa_na_ruki'] == 0 || $return['summa_na_ruki'] >= $return['norm_vuruchka_on_1_hand']) {

            $re['ocenka_naruki'] = 5;
            $text .= 'сумма на руки больше плана/норыы ' . $text_s;
            $return['txt'] .= '<br/>сумма на руки больше или равно норм ( ' . $return['summa_na_ruki'] . ' >= ' . $return['norm_vuruchka_on_1_hand'] . ' ) : оценка 5';
        }
// если на руки меньше нормы то оценка 3
        else {

            $re['ocenka_naruki'] = 3;
            $text .= 'сумма на руки меньше плана/нормы ' . $text_s;
            $return['txt'] .= '<br/>сумма Р на руки меньше НОРМы (' . $return['summa_na_ruki'] . ' < ' . $return['norm_vuruchka_on_1_hand'] . ') : оценка 3';
        }

        $money_proc = ceil($return['oborot'] / 100 * $return['norm_procent_oplata_truda_on_oborota']);

        $return['txt'] .= '<br/><br/><b>Проверяем % от оборота в 1 руки</b><br/>'
                . '( ' . $return['norm_procent_oplata_truda_on_oborota'] . '% от ' . $return['oborot'] . ' = ' . $money_proc . ')';
        ;

        if ($return['summa_na_ruki'] == 0 || $return['summa_na_ruki'] >= $money_proc) {
            // if ( $return['summa_na_ruki'] >= $return['oborot']/100*$return['norm_procent_oplata_truda_on_oborota'] ) {

            $re['ocenka_naruki_ot_oborota'] = 5;
            $text .= 'сумма на руки больше запланированного % от оборота '
                    . '( ' . $return['norm_procent_oplata_truda_on_oborota'] . '% от ' . $return['oborot'] . ' = ' . $money_proc . ')';
            $return['txt'] .= // 'сумма на руки больше запланированного % от оборота '
                    // . '( '.$return['norm_procent_oplata_truda_on_oborota'].'% от '.$return['oborot'].' = '.$money_proc.')';
                    '<br/>сумма на руки больше или равно нормы в % от оборота ( ' . $return['summa_na_ruki'] . ' >= ' . $money_proc . ' ) : оценка 5';
        }
// если на руки меньше нормы то оценка 3
        else {

            $re['ocenka_naruki_ot_oborota'] = 3;
            // $re['ocenka_naruki_ot_oborota'] = 5;
            $text .= 'сумма на руки меньше запланированного % от оборота '
                    . '( ' . $return['norm_procent_oplata_truda_on_oborota'] . '% от ' . $return['oborot'] . ' = ' . $money_proc . ')';
            $return['txt'] .= '<br/>сумма на руки меньше нормы в % от оборота ( ' . $return['summa_na_ruki'] . ' < ' . $money_proc . ' ) : оценка 3';
        }

        $ar = $return;

//         \f\pa($return);
//  die();      
// время ожидания
        if (1 == 1) {

            $return['txt'] .= '<br/><br/><b>смотрим на время ожидания</b>';

            $re['ocenka_time'] = 5;

            $tyty = 'cold';
            $text .= PHP_EOL . '<br/>время ожидания ' . $tyty;

            if (!empty($ar['timeo_' . $tyty]) && !empty($ar['norm_time_wait_norm_' . $tyty]) && $ar['timeo_' . $tyty] <= $ar['norm_time_wait_norm_' . $tyty]) {

                $text .= ' норм ( 5 )';
                $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' < ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) меньше максимума (нормы) : оценка 5';
            } else {
                $text .= ' не норм ( 3 )';
                $re['ocenka_time'] = 3;
                $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' > ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) больше максимума (нормы) : оценка 3';
            }

            $tyty = 'hot';

            $text .= PHP_EOL . '<br/>время ожидания ' . $tyty;
            if (1 == 1) {
                $text .= ' не участвует в оценке';
                $return['txt'] .= '<br/>цех ' . $tyty . ' не участвует в оценке';
//            if ( empty($ar['norm_time_wait_norm_' . $tyty])) {
//                $text .= ' параметра не указано, оценка максимум ( 5 )';
//                $return['txt'] .= '<br/>время (нормы) цеха ' . $tyty . ' не указано, не считаем';
            } else {
                if (!empty($ar['timeo_' . $tyty]) && !empty($ar['norm_time_wait_norm_' . $tyty]) && $ar['timeo_' . $tyty] <= $ar['norm_time_wait_norm_' . $tyty]) {
                    $text .= ' норм ( 5 )';
                    $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' < ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) меньше максимума (нормы) : оценка 5';
                } else {
                    $text .= ' не норм ( 3 )';
                    $re['ocenka_time'] = 3;
                    $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' > ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) больше максимума (нормы) : оценка 3';
                }
            }

            $tyty = 'delivery';
            $text .= PHP_EOL . '<br/>время ожидания ' . $tyty;

            if (empty($ar['norm_time_wait_norm_' . $tyty])) {

                $text .= ' параметра не указано, оценка максимум ( 5 )';
                $return['txt'] .= '<br/>время (нормы) цеха ' . $tyty . ' не указано, не считаем';
            } else {

                if (!empty($ar['timeo_' . $tyty]) && !empty($ar['norm_time_wait_norm_' . $tyty]) && $ar['timeo_' . $tyty] <= $ar['norm_time_wait_norm_' . $tyty]) {
                    $text .= ' норм ( 5 )';
                    $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' < ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) меньше максимума (нормы) : оценка 5';
                } else {
                    $text .= ' не норм ( 3 )';
                    $re['ocenka_time'] = 3;
                    $return['txt'] .= '<br/>время цеха ' . $tyty . ' ( ' . $ar['timeo_' . $tyty] . ' > ' . $ar['norm_time_wait_norm_' . $tyty] . ' ) больше максимума (нормы) : оценка 3';
                }
            }
        }

        $re['ocenka'] = 5;

        if ($re['ocenka_time'] == 3) {
            $re['ocenka'] = 3;
        } elseif ($re['ocenka_time'] == 3) {
            $re['ocenka'] = 3;
        } elseif ($re['ocenka_naruki'] == 3) {
            $re['ocenka'] = 3;
        }

        $return['txt'] .= '<h4>итоговая общая оценка: ' . $re['ocenka'] . '</h4>';

        $re['txt'] = $return['txt'] . '<hr><b>время обработки</b>' . $return['time'];

        return \f\end3($text, true, $re);
    }

    public static function getSetupJobmanOnSp($db, $date_start, $date_finish = null, $module_man_job_on_sp = 'jobman_send_on_sp', $mod_spec_jobday = '050.job_in_sp') {

//        echo '<hr>' . __FILE__ . ' #' . __LINE__
//        . '<br/>' . __FUNCTION__
//        . '<hr>';
//        \f\pa($date_start);
//        \f\pa($date_finish);
//        $ee = self::whereJobmans($db, $date_start, $date_start);
//        \f\pa($ee, 2, '', 'self::whereJobmans ' . __FILE__ . ' ' . __LINE__);

        $plus_minus_checks = \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_finish);
//\f\pa($plus_minus_checks, 2, '', '$plus_minus_checks');
//        $plus_minus_checks = \Nyos\mod\JobDesc:: Buh::getChecksMinusPlus($db, $date_start, $date_finish);
//        \f\pa($plus_minus_checks, 2, '', '$plus_minus_checks');
//        foreach ($plus_minus_checks['items'] as $jobman => $v1) {
//            foreach ($v1 as $k => $v) {
//                $checks[$jobman][$v['date']] = $v;
//            }
//        }
//        // $plus_minus_checks = '';
//\f\pa($checks, 2, '', '$checks');

        $ocenki = [];

        if (!empty($plus_minus_checks['items']))
            foreach ($plus_minus_checks['items'] as $jobman => $v1) {
                foreach ($v1 as $k => $item) {
                    if (isset($item['ocenka'])) {
                        $ocenki[$jobman][$item['date']] = $item['ocenka'];
                    } elseif (isset($item['ocenka_auto'])) {
                        $ocenki[$jobman][$item['date']] = $item['ocenka_auto'];
                    }
                }
            }

// \f\pa($ocenki);












        $return = [];

        if (empty($date_finish)) {
            $ds = $df = date('Y-m-d', strtotime($date_start));
        } else {
            $ds = date('Y-m-d', strtotime($date_start) - 3600 * 24);
            $df = date('Y-m-d', strtotime($date_finish));
        }

        /**
         * тащим спец назначения
         */
        if (1 == 1) {
// $spec_day = \Nyos\mod\items::getItemsSimple($db, $mod_spec_jobday);
            $spec_day = \Nyos\mod\items::get($db, $mod_spec_jobday);
//\f\pa($spec_day, 2, '', '$spec_day');
            $spec = [];
//            foreach ($spec_day['data'] as $k => $v) {
//                if ($v['dop']['date'] >= $ds && $v['dop']['date'] <= $df) {
//                    $spec[$v['dop']['jobman']][$v['dop']['date']] = $v['dop'];
//                }
//            }
            foreach ($spec_day as $k => $v) {
                if ($v['date'] >= $ds && $v['date'] <= $df) {
                    $spec[$v['jobman']][$v['date']] = $v;
                }
            }
//\f\pa($spec, 2, '', '$spec');
        }

// назначения сорудников на сп
// $jobs = \Nyos\mod\items::getItemsSimple($db, self::$mod_man_job_on_sp);
        $jobs = \Nyos\mod\items::getItemsSimple3($db, self::$mod_man_job_on_sp);
//        \f\pa($jobs, 2, '', '$jobs');

        $jobin = [];

        foreach ($jobs as $k => $v) {
// $v['fl'] = __FILE__ . ' #' . __LINE__;
            $jobin[$v['date']][] = $v;
        }

//\f\pa($jobin, 2, '', '$jobin');

        $j = [];

        foreach ($jobin as $k => $v) {

            if ($ds != $df)
                ksort($v);

            $j[$k] = $v;
        }

//      \f\pa($j, 2, '', '$j');

        $j2 = [];

        foreach ($j as $jobman => $v) {

// $start = false;
            $param_start = null;

            foreach ($v as $date => $v2) {

// if ($start === false && \strtotime($date) <= \strtotime($ds)) {
                if (\strtotime($date) <= \strtotime($ds)) {
                    $param_start = $v2;
                }

                if ($date >= $ds) {
// $start = true;
                    break;
                }
            }

            if (!empty($param_start)) {
                $j2[$jobman][$param_start['date']] = $param_start;
            }
        }

//\f\pa($j2, 2, '', '$j2');
// если ищем несколько дат
        if ($ds != $df) {

            $j3 = [];

            foreach ($j2 as $jobman => $date_ar) {
                foreach ($date_ar as $date => $ar) {
                    for ($i = 1; $i <= 35; $i++) {

                        $n = date('Y-m-d', strtotime($ds) + 3600 * 24 * $i);

                        if ($n > $df)
                            break;

//                    if (isset($j[$jobman][$n]))
//                        $ar = $j[$jobman][$n];
                        if (isset($j[$n]))
                            $ar = $j[$n];

                        $return['jobs_on_sp'][$ar['sale_point']][$ar['jobman']] = 1;

                        $a2 = $ar;

                        $a2['data_from_d'] = $a2['date'];
                        $a2['date'] = $n;

                        if (isset($spec[$jobman][$n])) {
                            $a2['sale_point'] = $spec[$jobman][$n]['sale_point'];
                            $a2['dolgnost'] = $spec[$jobman][$n]['dolgnost'];

                            $a2['type'] = 'spec';
                        }

                        $r[] = $a2;
                    }
                }
            }

            if ($ds != $df)
                usort($r, "\\f\\sort_ar_date");

            foreach ($r as $k => $v) {

                $salary = \Nyos\mod\JobDesc::getSalaryJobman($db, $v['sale_point'], $v['dolgnost'], $v['date']);
// $v['salary'] = $salary;
// $v['check'] = $checks[$v['jobman']][$v['date']] ?? 0 ;

                $v['hour'] = 0;
                if (isset($checks[$v['jobman']][$v['date']]['hour_on_job']) || isset($checks[$v['jobman']][$v['date']]['hour_on_job_hand'])) {
                    $v['hour'] = $checks[$v['jobman']][$v['date']]['hour_on_job_hand'] ?? $checks[$v['jobman']][$v['date']]['hour_on_job'];
                }

                $v['ocenka'] = $ocenki[$v['jobman']][$v['date']] ?? 0;

                $v['price_hour'] = 0;

// if (isset($salary['ocenka-hour-' . $v['ocenka']])) {
//\f\pa($v);
//\f\pa($salary,2,'','$salary');
// $v['prices'] = $salary;

                if (!empty($salary['ocenka-hour-base'])) {
                    $v['price_hour'] = $salary['ocenka-hour-base'] + ( $salary['if_kurit'] ?? 0 );
                    $v['ocenka_skip'] = 1;
                } elseif (isset($salary['ocenka-hour-' . $v['ocenka']])) {
                    $v['price_hour'] = $salary['ocenka-hour-' . $v['ocenka']] + ( $salary['if_kurit'] ?? 0 );
                }

                if ($v['price_hour'] != 0 && $v['hour'] != 0) {
                    $v['summa'] = ceil($v['price_hour'] * $v['hour']);
                }

                if (self::$one_date === true) {
                    $return[$v['jobman']] = $v;
                } else {
                    $return['jobs'][$v['jobman']][$v['date']] = $v;
                }
            }

//\f\pa($return);
        }
// если ищем одну дату
        else {
            foreach ($j2 as $jobman => $v2) {
                foreach ($v2 as $date => $v) {
                    if (self::$one_date === true) {
                        $return[$v['jobman']] = $v;
                    } else {
                        $return['jobs'][$v['jobman']][$ds] = $v;
                    }
                }
            }
        }

//\f\pa($return);

        return $return;
    }

    /*
      public static function getChecksToday( $db, $date ) {

      $checks = \Nyos\mod\items::getItemsSimple($db, $module);

      return $return;
      }
     */

    /**
     * получаем сотрудников кто работал в указанном промежутке
     * @param type $db
     * @param type $date_start
     * @param type $date_finish
     * @param type $module_man_job_on_sp
     * @param type $mod_spec_jobday
     * @return type
     */
    public static function getJobmansOnTime($db, $date_start, $date_finish = null, $module_man_job_on_sp = 'jobman_send_on_sp', $mod_spec_jobday = '050.job_in_sp') {

        echo '<br/>' . $date_start . ' , ' . $date_finish . '<br/>';

        $checks_all = \Nyos\mod\JobBuh::getChecksMinusPlus($db, $date_start, $date_finish);

// \f\pa($plus_minus_checks, 2, '', '$plus_minus_checks');
        foreach ($checks_all['items'] as $jobman => $v1) {
            foreach ($v1 as $k => $v) {
                $checks[$jobman][$v['date']]['checks'][] = $v;
            }
        }

//        // $plus_minus_checks = '';
// echo '<br/>'.sizeof($checks); echo '<br/>';
//        \f\pa($checks, 2, '', '$checks');
// $ocenki = [];

        foreach ($checks_all['items'] as $jobman => $v1) {
            foreach ($v1 as $k => $item) {
                if (!empty($item['ocenka'])) {
                    $checks[$jobman][$item['date']]['ocenka'] = $item['ocenka'];
                } elseif (!empty($item['ocenka_auto'])) {
                    $checks[$jobman][$item['date']]['ocenka'] = $item['ocenka_auto'];
                }
            }
        }

// \f\pa($ocenki);
// \f\pa($checks, 2, '', '$checks');


        $return = [];

        if (empty($date_finish)) {
            $ds = $df = date('Y-m-d', strtotime($date_start));
        } else {
            $ds = date('Y-m-d', strtotime($date_start) - 3600 * 24);
            $df = date('Y-m-d', strtotime($date_finish));
        }

        /**
         * тащим спец назначения
         */
        if (1 == 1) {
            $spec_day = \Nyos\mod\items::getItemsSimple($db, $mod_spec_jobday);
//\f\pa($spec_day, 2, '', '$spec_day');
// $spec = [];
            foreach ($spec_day['data'] as $k => $v) {
                if ($v['dop']['date'] >= $ds && $v['dop']['date'] <= $df) {
// $spec[$v['dop']['jobman']][$v['dop']['date']] = $v['dop'];
                    $v['dop']['type'] = 'spec';
                    $checks[$v['dop']['jobman']][$v['dop']['date']]['checks'][] = $v['dop'];
                }
            }
//\f\pa($spec, 2, '', '$spec');
        }

//        \f\pa($checks, 2, '', '$checks');

        return $checks;










        /**
         * назначения сорудников на сп
         */
        $jobs = \Nyos\mod\items::getItemsSimple($db, $module_man_job_on_sp);
//        \f\pa($jobs, 2, '', '$jobs');


        $jobin = [];

        foreach ($jobs['data'] as $k => $v) {

// if ($v['dop']['date'] >= $ds && $v['dop']['date'] <= $df) {

            $jobin[$v['dop']['jobman']][$v['dop']['date']] = $v['dop'];

            /*
              (
              [jobman] => 187
              [sale_point] => 1
              [dolgnost] => 2
              [date] => 2019-05-01
              )
             */
//\f\pa($v['dop']);


            /*
              $nd = date('Y-m-d', strtotime($v['dop']['date']) + 3600 * 24 * 14);
              $v['dop']['date'] = $nd;
              $v['dop']['dolgnost'] = rand(10, 50);
              $jobin[$v['dop']['jobman']][$nd] = $v['dop'];

              $nd = date('Y-m-d', strtotime($v['dop']['date']) + 3600 * 24 * 7);
              $v['dop']['date'] = $nd;
              $v['dop']['dolgnost'] = rand(10, 50);
              $jobin[$v['dop']['jobman']][$nd] = $v['dop'];
             */
// }
        }

//\f\pa($jobin, 2, '', '$jobin');

        $j = [];

        foreach ($jobin as $k => $v) {

            if ($ds != $df)
                ksort($v);

            $j[$k] = $v;
        }

//      \f\pa($j, 2, '', '$j');

        $j2 = [];

        foreach ($j as $jobman => $v) {

// $start = false;
            $param_start = null;

            foreach ($v as $date => $v2) {

// if ($start === false && \strtotime($date) <= \strtotime($ds)) {
                if (\strtotime($date) <= \strtotime($ds)) {
                    $param_start = $v2;
                }

                if ($date >= $ds) {
// $start = true;
                    break;
                }
            }

            if (!empty($param_start)) {
                $j2[$jobman][$param_start['date']] = $param_start;
            }
        }

//\f\pa($j2, 2, '', '$j2');

        /**
         * если ищем несколько дат
         */
        if ($ds != $df) {

            $j3 = [];

            foreach ($j2 as $jobman => $date_ar) {
                foreach ($date_ar as $date => $ar) {
                    for ($i = 1; $i <= 35; $i++) {

                        $n = date('Y-m-d', strtotime($ds) + 3600 * 24 * $i);

                        if ($n > $df)
                            break;

//                    if (isset($j[$jobman][$n]))
//                        $ar = $j[$jobman][$n];
                        if (isset($j[$n]))
                            $ar = $j[$n];

                        $return['jobs_on_sp'][$ar['sale_point']][$ar['jobman']] = 1;

                        $a2 = $ar;

                        $a2['data_from_d'] = $a2['date'];
                        $a2['date'] = $n;

                        if (isset($spec[$jobman][$n])) {
                            $a2['sale_point'] = $spec[$jobman][$n]['sale_point'];
                            $a2['dolgnost'] = $spec[$jobman][$n]['dolgnost'];

                            $a2['type'] = 'spec';
                        }

                        $r[] = $a2;
                    }
                }
            }

            if ($ds != $df)
                usort($r, "\\f\\sort_ar_date");

            foreach ($r as $k => $v) {

                $salary = \Nyos\mod\JobDesc::getSalaryJobman($db, $v['sale_point'], $v['dolgnost'], $v['date']);
// $v['salary'] = $salary;
// $v['check'] = $checks[$v['jobman']][$v['date']] ?? 0 ;

                $v['hour'] = 0;
                if (isset($checks[$v['jobman']][$v['date']]['hour_on_job']) || isset($checks[$v['jobman']][$v['date']]['hour_on_job_hand'])) {
                    $v['hour'] = $checks[$v['jobman']][$v['date']]['hour_on_job_hand'] ?? $checks[$v['jobman']][$v['date']]['hour_on_job'];
                }

                $v['ocenka'] = $ocenki[$v['jobman']][$v['date']] ?? 0;

                $v['price_hour'] = 0;

// if (isset($salary['ocenka-hour-' . $v['ocenka']])) {
//\f\pa($v);
//\f\pa($salary,2,'','$salary');
// $v['prices'] = $salary;

                if (!empty($salary['ocenka-hour-base'])) {
                    $v['price_hour'] = $salary['ocenka-hour-base'] + ( $salary['if_kurit'] ?? 0 );
                    $v['ocenka_skip'] = 1;
                } elseif (isset($salary['ocenka-hour-' . $v['ocenka']])) {
                    $v['price_hour'] = $salary['ocenka-hour-' . $v['ocenka']] + ( $salary['if_kurit'] ?? 0 );
                }

                if ($v['price_hour'] != 0 && $v['hour'] != 0) {
                    $v['summa'] = ceil($v['price_hour'] * $v['hour']);
                }

                $return['jobs'][$v['jobman']][$v['date']] = $v;
            }

//\f\pa($return);
        }
        /**
         * если ищем одну дату
         */ else {
            foreach ($j2 as $jobman => $v2) {
                foreach ($v2 as $date => $v) {
                    $return['jobs'][$v['jobman']][$ds] = $v;
                }
            }
        }

//\f\pa($return);

        return $return;
    }

    /**
     * какие нормы на день по умолчанию
     * @param type $array
     * @param type $sp
     * @param type $man
     * @param string $date
     * @return type
     */
    public static function whatNormToDayDefault($db) {

        if (!empty(self::$cash['timeo_default']))
            return self::$cash['timeo_default'];

        $norms_def0 = \Nyos\mod\items::getItemsSimple($db, '074.time_expectations_default');

// \f\pa($norms_def);
//die();

        $norms_def = [];

        foreach ($norms_def0['data'] as $k => $v) {

            if (!empty($v['dop']['otdel']) && !empty($v['dop']['default'])) {

                $otd = null;

                if ($v['dop']['otdel'] == 1) {
                    $otd = 'cold';
                } elseif ($v['dop']['otdel'] == 2) {
                    $otd = 'hot';
                } elseif ($v['dop']['otdel'] == 3) {
                    $otd = 'delivery';
                }

                if ($otd !== null) {
                    $norms_def[$v['dop']['otdel']] = $v['dop']['default'];
                }
            }
        }

        return self::$cash['timeo_default'] = $norms_def;
// return $norms_def;
    }

    /**
     * какие нормы на день сегодня в сп
     * @param type $array
     * @param type $sp
     * @param type $man
     * @param string $date
     * @return type
     */
    public static function whatNormToDay($db, int $sp, string $date, $date_fin = null) {

        $date_start = date('Y-m-01', strtotime($date));
        $date_finish = (!empty($date_fin) ) ? date('Y-m-d', strtotime($date_finish)) : date('Y-m-d', strtotime($date_start . ' +1 month -1 day'));

        \Nyos\mod\items::$type_module = 3;
        \Nyos\mod\items::$search['sale_point'] = $sp;
        \Nyos\mod\items::$between['date'] = [$date_start, $date_finish];

        $res = [];
        $res = \Nyos\mod\items::get($db, \Nyos\mod\JobDesc::$mod_norms_day);

        $ar__date_d = [];

        foreach ($res as $k => $v) {

            $ar__date_d[$v['date']][] = $v;
        }

        return \f\end3('ok', true, $ar__date_d);

//        
//        
//// название переменной где храним кеш
//        $cash_var = 'jobdesc__whatNormToDay_sp' . $sp . '_date' . $date . '_date_fin' . $date_fin;
//// время в сек на которое храним кеш
//        $cash_time = 60;
//
//        if (!empty($cash_var)) {
//            $e = \f\Cash::getVar($cash_var);
//
//            if (!empty($e))
//                return $e;
//        }
//
//        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
//                . ' ON mid.id_item = mi.id '
//                . ' AND mid.name = \'sale_point\' '
//                . ' AND mid.value = :sp ';
//        \Nyos\mod\items::$var_ar_for_1sql[':sp'] = $sp;
//
//        if (!empty($date_fin)) {
//            \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'date\' '
//                    . ' AND mid2.value_date >= :ds '
//                    . ' AND mid2.value_date <= :df '
//            ;
//            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = $date;
//            \Nyos\mod\items::$var_ar_for_1sql[':df'] = $date_fin;
//        } else {
//            \Nyos\mod\items::$join_where .= ' INNER JOIN `mitems-dops` mid2 '
//                    . ' ON mid2.id_item = mi.id '
//                    . ' AND mid2.name = \'date\' '
//                    . ' AND mid2.value_date = :ds '
//            ;
//            \Nyos\mod\items::$var_ar_for_1sql[':ds'] = $date;
//        }
//
//// $norms = \Nyos\mod\items::get($db, 'sale_point_parametr');
//        $norms = \Nyos\mod\items::get($db, self::$mod_norms_day);
//
//        $rr = [];
//
//        foreach ($norms as $k => $v) {
//
//// если ищем одну даты то отправляем этот 1 день
//            if ($date_fin === null) {
//                return $v;
//            }
//
//// если ищем несколько дат, то собираем по датам в массив и возвращаем
//            $rr[$v['date']] = $v;
//        }
//
//        if (!empty($cash_var) && !empty($cash_time))
//            \f\Cash::setVar($cash_var, $rr, $cash_time);
//
//        return $rr;
    }

    /**
     * получаем обороты точки с даты по дату
     * @param type $db
     * @param int $sp
     * @param string $date_start
     * @param string $date_stop
     * @return array
     */
    public static function get_oborots($db, int $sp, string $date_start, string $date_stop) {

        $return = [];

        \Nyos\mod\items::$join_where = ' INNER JOIN `mitems-dops` mid '
                . ' ON mid.id_item = mi.id '
                . ' AND mid.name = \'date\' '
                . ' AND mid.value_date >= :ds '
                . ' AND mid.value_date <= :df '
                . ' INNER JOIN `mitems-dops` mid2 '
                . ' ON mid2.id_item = mi.id '
                . ' AND mid2.name = \'sale_point\' '
                . ' AND mid2.value = :sp '

        ;
        \Nyos\mod\items::$var_ar_for_1sql[':sp'] = $sp;
        \Nyos\mod\items::$var_ar_for_1sql[':ds'] = $date_start;
        \Nyos\mod\items::$var_ar_for_1sql[':df'] = $date_stop;

        // \Nyos\mod\items::$where2dop = ' AND ( name ';

        $oborots = \Nyos\mod\items::get($db, 'sale_point_oborot');
        // \f\pa($oborots);

        $re = ['summa' => 0];

        foreach ($oborots as $k => $v) {
            $re[$v['date']] = $v;
            if (isset($v['oborot_hand']) && $v['oborot_hand'] > 0) {
                $re['summa'] += $v['oborot_hand'];
            } elseif (isset($v['oborot_server']) && $v['oborot_server'] > 0) {
                $re['summa'] += $v['oborot_server'];
            }
        }

        // \f\pa($r);

        for ($i = 0; $i <= 40; $i++) {

            // $date_start
            $date_now = date('Y-m-d', strtotime($date_start . ' +' . $i . ' day'));

            if ($date_now > $date_stop)
                break;

            if (!isset($re[$date_now]))
                $re[$date_now] = [];
        }


        return $re;
    }

    /**
     * сейчас работает человек на этой сп или нет
     * @param type $array
     * @param type $sp
     * @param type $man
     * @param string $date
     * @return type
     */
    public static function where_now_job_man($array, $sp, $man, string $date) {

// \f\pa($date);
//echo '<div style="width:150px;"></div>';
// \f\pa($array['jobs'][$sp][$man]);
//        \f\pa($sp);
//        \f\pa($man);
//        \f\pa($array['jobs'][$sp][$man]);

        $nowutime = strtotime($date . ' 00:00');

        if (!empty($array['jobs'][$sp][$man]))
            foreach ($array['jobs'][$sp][$man] as $k => $v) {
// \f\pa($v['date']);

                if (isset($v['date']) && $nowutime == strtotime($v['date'])) {

                    $v['d1'] = $date;
                    $v['d2'] = $v['date'];

                    return $v;
                }
            }

        $now = [];

        if (!empty($array['jobs'][$sp][$man]))
            foreach ($array['jobs'][$sp][$man] as $k => $v) {

// \f\pa($v['date']);

                if (isset($v['date']) && $nowutime > strtotime($v['date'] . ' 00:00')) {

                    $v['d1'] = $date;
                    $v['d2'] = $v['date'];

                    $now = $v;
                }
            }

        if (!empty($now)) {
            return $now;
        }

        return false;
    }

}
