<?php

/**
 * считаем оценку за 1 день
 */
if (isset($skip_start) && $skip_start === true) {
    
} else {
    require_once '0start.php';
    $skip_start = false;
}

try {

    if (isset($_REQUEST['sp']) && isset($_REQUEST['date']) && isset($_REQUEST['s']) && \Nyos\Nyos::checkSecret($_REQUEST['s'], $_REQUEST['sp'] . $_REQUEST['date']) !== false) {
        
    } else {
        \f\end2('хьюстон что то пошло не так #' . __LINE__, false);
    }


    ob_start('ob_gzhandler');

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
        'ocenka' => 5,
        'ocenka_naruki' => 0,
        'ocenka_timeo' => 0,
        // оценка за процент от оборота
        'ocenka_proc_ot_oborot' => 0,
        'checks_for_new_ocenka' => [],
        'date' => date('Y-m-d', strtotime($_REQUEST['date'])),
        'sale_point' => $_REQUEST['sp'],
        'sp' => $_REQUEST['sp'],
        'oborot' => 0,
        'norms' => [],
        'timeo' => [],
            // 'oborot_in_one_hand' => 0
    );

    $jobmans = \Nyos\mod\JobDesc::getJobmansJobingToSpMonth($db, $return['sp'], $return['date']);
    // \f\pa($jobmans, 2);

    $actions = \Nyos\mod\JobDesc::getActionsJobmansOnMonth($db, array_keys($jobmans['data']['jobmans']), $return['date'], true, $return['sp']);
    // \f\pa($actions, 2, '', '$actions');
// считаем сколько часов отработано
    $hours = \Nyos\mod\JobDesc::calcHoursDaysForOcenka($db, $return['date'], $return['sp'], array_keys($jobmans['data']['jobmans']), $actions['data']['actions']);
    // \f\pa($hours, 2, '', 'колво hours');
    $return['hour_day'] = $return['hours'] = $hours['hours'];
    $return['checks'] = $hours['calc_checks'];
    
    foreach ($actions['data']['actions'] as $k => $v) {
        if (isset($v['date']) && $v['date'] == $return['date']) {
            if (isset($v['type']) && $v['type'] == 'oborot') {
                $return['oborot'] = $v['oborot_day'];
            } elseif (isset($v['type']) && $v['type'] == 'norms') {
                $return['norms'] = [
                    'time_wait_norm_cold' => $v['time_wait_norm_cold'] ?? '',
                    // 'time_wait_norm_hot' => $v['time_wait_norm_hot'] ?? '' ,
                    'time_wait_norm_delivery' => $v['time_wait_norm_delivery'] ?? '',
                    'procent_oplata_truda_on_oborota' => $v['procent_oplata_truda_on_oborota'] ?? '',
                    'kolvo_hour_in1smena' => $v['kolvo_hour_in1smena'] ?? '',
                    'vuruchka_on_1_hand' => $v['vuruchka_on_1_hand'] ?? ''
                ];
            } elseif (isset($v['type']) && $v['type'] == 'timeo') {
                $return['timeo'] = [
                    'cold' => $v['cold'] ?? '',
                    'delivery' => $v['delivery'] ?? '',
                ];
                $return['time_cold'] = $v['cold'] ?? '';
                $return['time_delivery'] = $v['delivery'] ?? '';
            }
        }
    }

    // считаем норму на 1 руки
    $return['smen_in_day'] = round($return['hours'] / $return['norms']['kolvo_hour_in1smena'], 1);

    // считаем выручку в 1 руки
    $return['summa_na_ruki'] = ceil($return['oborot'] / $return['smen_in_day']);

    $return['summa_zp_if5'] = $hours['summa_if5'] ?? '';

    if ( $return['summa_na_ruki'] >= $return['norms']['vuruchka_on_1_hand']) {
        $return['ocenka_naruki_ot_oborota'] = $return['ocenka_naruki'] = 5;
    } else {
        $return['ocenka_naruki_ot_oborota'] = $return['ocenka'] = $return['ocenka_naruki'] = 3;
    }

    $return['txt'] .= '<Br/><b>сумма на зп, если оценка 5</b>'
            . '<Br/>посчитали сколько отработано смен <nobr>' . $return['hours'] . '/' . $return['norms']['kolvo_hour_in1smena'] . ' = ' . $return['smen_in_day'].'</nobr>'
            . '<Br/>сумма на 1 руки ЗП ' . $return['summa_na_ruki']
            . ' (норматив ' . $return['norms']['vuruchka_on_1_hand'] . ') '
            . '<Br/>оценка: ' . $return['ocenka_naruki'] . '<Br/>';

    if ($return['timeo']['cold'] <= $return['norms']['time_wait_norm_cold'] && $return['timeo']['delivery'] <= $return['norms']['time_wait_norm_delivery']) {
        $return['ocenka_time'] = 5;
    } else {
        $return['ocenka'] = $return['ocenka_time'] = 3;
    }
    $return['txt'] .= '<Br/><b>сравниваем время ожидания</b>'
            . '<Br/>'
            . $return['timeo']['cold'] . '/' . $return['timeo']['delivery']
            . ' <nobr>(норматив ' . $return['norms']['time_wait_norm_cold'] . '/' . $return['norms']['time_wait_norm_delivery'] . ')</nobr> '
            . '<Br/>оценка: ' . $return['ocenka_time'] . '<Br/>';

    $return['proc_zp_ot_oborota_if5'] = $return['if5_proc_oborot'] = round($hours['summa_if5'] / ($return['oborot'] / 100), 1);

    if ($return['if5_proc_oborot'] < $return['norms']['procent_oplata_truda_on_oborota']) {
        $return['ocenka_proc_ot_oborot'] = 5;
    } else {
        $return['ocenka'] = $return['ocenka_proc_ot_oborot'] = 3;
    }

    $return['txt'] .= '<Br/><b>сравниваем % от оборота на ЗП</b>'
            . '<Br/>текущее значение ' . $return['if5_proc_oborot']
            . ' <nobr>(на зп ' . $hours['summa_if5'] . ' из ТО ' . $return['oborot'] . ')</nobr> '
            . '<Br/><nobr>норматив ' . $return['norms']['procent_oplata_truda_on_oborota'] . '</nobr> '
            . '<Br/>оценка: ' . $return['ocenka_proc_ot_oborot']
    ;

    $return['txt'] .= '<div style="background-color:yellow; padding:2px 5px; text-align:center;"><nobr><b>Новая итоговая оценка: ' . $return['ocenka'] . '</b></nobr></div>'
            . 'обновите страницу для обновиления оценки смен в графике';

    // \f\pa($return);

    $s2 = $db->prepare('DELETE FROM `mod_sp_ocenki_job_day` WHERE `sale_point` = :sp AND `date` = :date ;');
    $s2->execute([':sp' => $return['sale_point'], ':date' => $return['date']]);

    $return2 = $return;
    unset($return2['txt']);
    
    $e = \Nyos\mod\items::add($db, 'sp_ocenki_job_day', $return2);
    // \f\pa($e);

    
    
            
        $sql = 'UPDATE `mod_050_chekin_checkout` SET `ocenka_auto` = :ocenka WHERE `id` = \''. implode( '\' OR `id` = \'' , $return['checks'] ).'\' ;';
        //\f\pa($sql);
        $ff = $db->prepare($sql);
        $ff->execute( [ ':ocenka' => $return['ocenka'] ] );
        // echo implode( ', ' , $return['checks'] );


    
    
    $r = ob_get_contents();
    ob_end_clean();

    \f\end2($r . $return['txt'], true, $return);

} catch (\Exception $ex) {

    ob_start('ob_gzhandler');

    \f\pa($ex);

    $r = ob_get_contents();
    ob_end_clean();


    \f\end2('ok' . $r, false);
}































//
//// считаем автооценку дня и пишем
//
//try {
//
//    if (!empty($_REQUEST['show_info']))
//        \f\pa($_REQUEST, 2, '', 'request');
//
//
//
//
//// считаем сколько суммарно часов отработано за сегодня
//    if (1 == 2) {
//        \f\timer_start(2);
//// echo '<div style="border: 3px solid gray; padding: 20px; margin: 20px;" >hours<hr>';
//        $hours = \Nyos\mod\JobDesc::calcJobHoursDay($db, $date, $sp);
//// echo '</div>';
////\f\pa($hours,'','','hours');
//
//        if (isset($hours['status']) && $hours['status'] == 'error') {
//            throw new \Exception($hours['html'], 19);
//        }
//
//// \f\pa($hours,'','','calc_hours');
////            if (!empty($hours['data']['hours']))
////                $return['hours'] = $hours['data']['hours'];
//        $return = [];
//
//        foreach ($hours['data'] as $k => $v) {
//            $return[$k] = $v;
//        }
//
//        $return['time'] .= '<br/> посчитали сколько часов работы было в этот день'
//                . '<br/>' . \f\timer_stop(2);
//
//        \f\pa($return);
//    }
//
//// echo '11111111';
//// считаем сколько суммарно часов отработано за сегодня (версия 3 - 12010161058 )
//    if (1 == 1) {
//
//        \f\timer_start(2);
//
//// echo '<br/>$ert = \Nyos\mod\JobDesc::calcJobHoursDay($db, '.$date.', '.$sp.'); ';
//        $calc_hours = \Nyos\mod\JobDesc::calculateHoursOnJob($db, $date, $sp);
//
//        if (!empty($_REQUEST['show_info']))
//            \f\pa($calc_hours, 2, '', '\Nyos\mod\JobDesc::calculateHoursOnJob');
//
//        $return['hours'] = ($calc_hours['data']['hours_calc_auto'] ?? 0);
//
//        if (!empty($calc_hours['data']['checks']))
//            foreach ($calc_hours['data']['checks'] as $k => $v) {
//                if (!empty($v['id']))
//                    $return['checks_for_new_ocenka'][] = $v['id'];
//            }
//
//
//        $return['time'] .= '<br/> посчитали сколько часов работы было в этот день (в3) '
//                . '<br/>' . \f\timer_stop(2);
//    }
//
//// echo '2222222';
//
//    /**
//     * достаём нормы на день
//     */
//    if (5 == 5) {
//
//        \f\timer_start(2);
//        $now_norm = \Nyos\mod\JobDesc::whatNormToDay($db, $return['sp'], $return['date']);
//
//        if ($now_norm === false)
//            throw new \Exception('Нет плановых данных (дата) ', 12);
//
//        foreach ($now_norm as $k => $v) {
//            $return['norm_' . $k] = $v;
//        }
//
//        $return['time'] .= '<br/> грузим нормы за день'
//                . '<br/>' . \f\timer_stop(2);
//
//        if (empty($return['norm_date'])) {
//            throw new \Exception('Нет плановых данных (дата)', 12);
//        } elseif (empty($return['norm_vuruchka_on_1_hand']) || empty($return['norm_time_wait_norm_cold']) || empty($return['norm_procent_oplata_truda_on_oborota']) || empty($return['norm_kolvo_hour_in1smena'])) {
//            throw new \Exception('Не все плановые данные по ТП указаны', 204);
//        }
//    }
//
//    /**
//     * достаём оборот за сегодня
//     */
//    if (5 == 5) {
//
//        \f\timer_start(2);
//        try {
//
//            $return['oborot'] = \Nyos\mod\IikoOborot::getDayOborot($db, $return['sp'], $return['date']);
//
//            if (empty($return['oborot'])) {
//                $return['oborot'] = \Nyos\mod\IikoOborot::loadFromServerSaveItems($db, $return['sp'], $return['date']);
//            }
//        } catch (\Exception $exc) {
//
//            echo $exc->getTraceAsString();
//            $return['oborot'] = 0;
//        }
//
//        $return['time'] .= '<br/> достали обороты за день'
//                . '<br/>' . \f\timer_stop(2);
//    }
//
//    /**
//     * достаём время ожидания за сегодня
//     */
//    if (5 == 5) {
//
//
//        \f\timer_start(2);
//
//        $timeo = \Nyos\mod\JobDesc::getTimeOgidanie($db, $return['sp'], $return['date']);
//
//// \f\pa($timeo);
//
//        $return['time'] .= '<br/>достали время ожидания за день'
//                . '<br/>' . \f\timer_stop(2);
//
//
//        foreach ($timeo['data'] as $k => $v) {
//            if (strpos($k, '_hand') !== false && !empty($v)) {
//                $timeo['data'][str_replace('_hand', '', $k)] = $v;
//// unset($timeo[$k]);
//            }
//        }
//
//        foreach ($timeo['data'] as $k => $v) {
//// $return['time'] .= PHP_EOL . $k . ' > ' . $v;
//            $return['timeo_' . $k] = $v;
//// $return['timeo_'.$k] = $v;
//        }
//    }
//
//// \f\pa($return);
//
//    $ocenka = \Nyos\mod\JobDesc::calcOcenkaDay($db, $return);
//    if (!empty($_REQUEST['show_info']))
//        \f\pa($ocenka, 2, '', 'ocenka');
//
//    // \Nyos\mod\JobDesc::recordNewAutoOcenkiDay($db, $return['checks_for_new_ocenka'], $ocenka['data']['ocenka']);
//    // \Nyos\mod\items::deleteItems($db, $for_msg, $uri, $data_dops);
//
//    \Nyos\mod\items::deleteFromDops($db, \Nyos\mod\jobdesc::$mod_ocenki_days, [
//        'sale_point' => $ocenka['data']['sp'],
//        'date' => $ocenka['data']['date'],
//    ]);
//
//    // \f\pa($ocenka);
////    \Nyos\mod\items::addNewSimple($db, \Nyos\mod\jobdesc::$mod_ocenki_days, [
////        'sale_point' => $ocenka['data']['sp'],
////        'date' => $ocenka['data']['date'],
////        'hour_day' => $ocenka['data']['hour_day'],
////        'ocenka_time' => $ocenka['data']['ocenka_time'],
////        'ocenka_naruki' => $ocenka['data']['ocenka_naruki'],
////        'ocenka_naruki_ot_oborota' => $ocenka['data']['ocenka_naruki_ot_oborota'],
////        'money_naruki_ot_oborota' => $ocenka['data']['ocenka_naruki_ot_oborota'],
////        'money_norm_ot_oborota' => $ocenka['data']['money_norm_ot_oborota'],
////        'ocenka' => $ocenka['data']['ocenka'],
////            // 'txt' => ( $return['txt'] ?? '' ) . '<hr>' . ( $return['time']  ?? ''  ),
////    ]);
//
//    \Nyos\mod\items::add($db, \Nyos\mod\jobdesc::$mod_ocenki_days, [
//        'sale_point' => $ocenka['data']['sp'],
//        'date' => $ocenka['data']['date'],
//        'hour_day' => $ocenka['data']['hour_day'],
//        'ocenka_time' => $ocenka['data']['ocenka_time'],
//        'ocenka_naruki' => $ocenka['data']['ocenka_naruki'],
//        'ocenka_naruki_ot_oborota' => $ocenka['data']['ocenka_naruki_ot_oborota'],
//        'money_naruki_ot_oborota' => $ocenka['data']['ocenka_naruki_ot_oborota'],
//        'money_norm_ot_oborota' => $ocenka['data']['money_norm_ot_oborota'],
//        'ocenka' => $ocenka['data']['ocenka'],
//            // 'txt' => ( $return['txt'] ?? '' ) . '<hr>' . ( $return['time']  ?? ''  ),
//    ]);
//
//    //\Nyos\mod\items::addNewSimple($db, \Nyos\mod\jobdesc::$mod_ocenki_days, $ocenka['data'] );
//    //    \Nyos\mod\items::add($db, \Nyos\mod\jobdesc::$mod_ocenki_days, $ocenka['data'] );
//
//    if (!empty($_REQUEST['show_info']))
//        \f\pa(array_keys($calc_hours['data']['checks']), 2, '', 'checks');
//
//    if (!empty($_REQUEST['show_info']))
//        \Nyos\mod\items::$show_sql = true;
//    \Nyos\mod\items::edits($db, \Nyos\mod\jobdesc::$mod_checks, array_keys($calc_hours['data']['checks']), ['ocenka_auto' => $ocenka['data']['ocenka']]);
//    // $calc_hours['data']['checks']
//
//    if (!empty($_REQUEST['show_info']))
//        \f\pa(\f\end3('ok ' . ($ocenka['html'] ?? '--'), true, $ocenka), 2, '', 'end3');
//
//    \f\end2('ok ' . ($ocenka['html'] ?? '--'), true, $ocenka);
//}
////
//catch (Exception $ex) {
//
//    \f\end2(
//            $ex->getMessage(),
//            false,
//            [
//                'code' => $ex->getCode(),
//                'file' => $ex->getFile(),
//                'line' => $ex->getLine(),
//                'trace' => $ex->getTraceAsString()
//            ]
//    );
//}
//
//
//if (1 == 2) {
//    if (1 == 1) {
//
//        \f\pa($return, '', '', '$return');
//
////$job_now_on_sp
//// echo '<br/>'.__FILE__.' #'.__LINE__;
//        echo '<fieldset>';
//        $worker_on_date = \Nyos\mod\JobDesc::whereJobmans($db, $date);
//        echo '</fieldset>';
////\f\pa($worker_on_date, 2, '', 'self::whereJobmans($db, $date);');
//
//        die('<br/>end ' . __FILE__ . ' #' . __LINE__);
//
//// id items для записи авто оценки
//
//        /**
//         * достаём чеки за день
//         */
//        if (1 == 1) {
//
////        echo '<fieldset><legend>\Nyos\mod\JobDesc::getTimesChecksDay '.__FILE__.' #'.__LINE__.'</legend>';
////
////        $id_items_for_new_ocenka = [];
////        \f\timer::start();
////
////        // $return['hours'] = \Nyos\mod\JobDesc::getTimesChecksDay($db, $sp, $e) getOborotSp($db, $return['sp'], $return['date']);
//
//            $times_day = \Nyos\mod\JobDesc::getTimesChecksDay($db, $return['sp'], $return['date']);
//            \f\pa($times_day, 2, '', '\Nyos\mod\JobDesc::getTimesChecksDay');
////
////        $return['hours'] = $times_day['hours'];
////        $id_items_for_new_ocenka = $times_day['id_check_for_new_ocenka'];
////        // die($return['hours']);
////
////        $return['time'] .= PHP_EOL . ' достали время работы по чекам за день : ' . \f\timer::stop()
////            . PHP_EOL . $return['hours'];
////
////        echo '</fieldset>';
//        }
//
//        die('<br/>end ' . __FILE__ . ' #' . __LINE__);
//
////        if (!class_exists('Nyos\mod\JobDesc'))
////            require_once DR . DS . 'vendor/didrive_mod/jobdesc/class.php';
////        echo '<br/>' . __FILE__ . ' ' . __LINE__;
////        \f\pa($return);
////        die(__LINE__);
////        echo '<fieldset style="border: 1px solid gray; padding: 5px; margin: 5px;" ><legend>'
////        . 'достаём суммарное время работы сотрудников за сегодня</legend>';
//        if (1 == 3) {
//
////            echo '<hr>';
////            echo __FILE__.' #'.__LINE__;
////            echo '<hr>';
////            echo '<hr>';
//// $sp
//
//            $worker_on_date = self::whereJobmansNowDate($db, $return['date']);
//// \f\pa($worker_on_date, 2, '', '$worker_on_date');
//
//            $ds = strtotime($return['date'] . ' 09:00:00');
//            $df = strtotime($return['date'] . ' 03:00:00 +1 day');
//
//            $ds1 = date('Y-m-d H:i:s', $ds);
//// echo '<Br/>'.date('Y-m-d H:i:s', $ds );
//            $df1 = date('Y-m-d H:i:s', $df);
//// echo '<Br/>'.date('Y-m-d H:i:s', $df );
//
//            $checks = \Nyos\mod\items::getItemsSimple($db, self::$mod_checks);
//            $return['checks_for_new_ocenka'] = [];
//
//            foreach ($checks['data'] as $k3 => $v3) {
//
//                if (
//                        isset($v3['dop']['jobman']) &&
//                        isset($v3['dop']['start']) &&
//                        $v3['dop']['start'] >= $ds1 &&
//                        $v3['dop']['start'] <= $df1 &&
//                        isset($worker_on_date[$v3['dop']['jobman']]['sale_point']) &&
//                        $worker_on_date[$v3['dop']['jobman']]['sale_point'] == $sp
//                ) {
//
////\f\pa($v3['dop']);
////break;
//// часы отредактированные в ручную
//                    if (!empty($v3['dop']['hour_on_job_hand'])) {
//                        $return['checks_for_new_ocenka'][] = $v3['id'];
//                        $return['hours'] += $v3['dop']['hour_on_job_hand'];
//                    }
//// авторасчёт количества часов
//                    elseif (!empty($v3['dop']['hour_on_job'])) {
//                        $return['checks_for_new_ocenka'][] = $v3['id'];
//                        $return['hours'] += $v3['dop']['hour_on_job'];
//                    }
//                }
//            }
////die();
////            $return['smen_in_day'] = round($return['hours'] / $return['norm_kolvo_hour_in1smena'], 1);
////
////            if(  !empty ($return['oborot']) && !empty($return['smen_in_day']) )
////            $return['summa_na_ruki'] = ceil( $return['oborot'] / $return['smen_in_day'] );
////
////            // если на руки больше нормы то оценка 5
////            if ( $return['summa_na_ruki'] >= $return['norm_vuruchka_on_1_hand']) {
////                $return['ocenka_naruki'] = 5;
////            }
////            // если на руки меньше нормы то оценка 3
////            else {
////                $return['ocenka_naruki'] = 3;
////            }
//// $ee = self::getTimesChecksDay($db, $return['sp'], $return['date']);
//// \f\pa($ee, 2, '', '$ee = self::getTimesChecksDay($db, $ar[\'sp\'], $ar[\'date\']);');
//// $return['hours_job_days'] = $ee;
//        }
//// echo '</fieldset>';
////        return \f\end3('ok', true, $return);
//
//        die('<br/>end ' . __FILE__ . ' #' . __LINE__);
//
//
//
//
//
//        $e = \Nyos\mod\jobdesc::calculateAutoOcenkaDays($db, $_REQUEST['sp'], $_REQUEST['date']);
//
//// \f\pa($e, 2, '', '$ee1 результ оценки дня 1 (функция) action=calc_full_ocenka_day');
//
//        if (!empty($e['data']['error'])) {
//            \f\end2($e['data']['error'], false, $e);
//        } else {
//            \f\end2('ok', true, $e);
//        }
//
//        die('<br/>end ' . __FILE__ . ' #' . __LINE__);
//    }
//
//
//
//    if (1 == 2) {
//
//        echo __FUNCTION__ . ' ' . __FILE__ . ' ' . __LINE__ . '<hr>';
//
////
////    $r = \Nyos\mod\JobDesc::getTimesChecksDay($db, $_REQUEST['sp'], $_REQUEST['date']);
////    \f\pa($r,'','','\Nyos\mod\JobDesc::getTimesChecksDay');
////
//        /**
//         * перенёс в отдельную функцию
//         * \Nyos\mod\jobdesc\calculateAutoOcenkaDays($db, $sp, $data)
//         */
//        $ee1 = \Nyos\mod\jobdesc::calculateAutoOcenkaDays($db, $_REQUEST['sp'], $_REQUEST['date']);
//
//// \f\pa($ee1, 2, '', '$ee1 результ оценки дня 1 (функция)');
//        if (!empty($ee1['data']['error'])) {
//            \f\end2($ee1['data']['error'], false, $ee1);
//        } else {
//            \f\end2('ok', true, $ee1);
//        }
//    }
//
//// ob_start('ob_gzhandler');
//
//    try {
//
//        if (1 == 1) {
//            $return = \Nyos\mod\JobDesc::readVarsForOcenkaDays($db, $_REQUEST['sp'], $_REQUEST['date']);
//// \f\pa($return, 2, '', '$return данные для оценки дня');
//// массив чеков для новых оценок
//// $return['checks_for_new_ocenka']
//        }
//
//        if (1 == 1) {
//// \f\pa($return['data'], 2, '', '$return данные для оценки дня');
//            $ocenka = \Nyos\mod\JobDesc::calcOcenkaDay($db, $return['data']);
//// \f\pa($ocenka, 2, '', '$ocenka');
//        }
//
////        if ( class_exists('\Nyos\mod\items') )
////            echo '<br/>' . __FILE__ . ' ' . __LINE__;
//// if (!empty($return['data']['checks_for_new_ocenka'])) {
//// \f\pa( $return['checks_for_new_ocenka'], 2 , '' , 'checks_for_new_ocenka' );
//// }
//
//        \Nyos\mod\JobDesc::recordNewAutoOcenkiDay($db, $return['data']['checks_for_new_ocenka'], $ocenka['data']['ocenka']);
//
//        \Nyos\mod\items::addNewSimple($db, \Nyos\mod\jobdesc::$mod_ocenki_days, [
//            'sale_point' => $ocenka['data']['sp'],
//            'date' => $ocenka['data']['date'],
//            'ocenka_time' => $ocenka['data']['ocenka_time'],
//            'ocenka_naruki' => $ocenka['data']['ocenka_naruki'],
//            'ocenka' => $ocenka['data']['ocenka'],
//        ]);
//
////        $r = ob_get_contents();
////        ob_end_clean();
//
//        \f\end2('ok ' . ($r ?? '--'), true, $return['data']);
//
//        if (1 == 2) {
//
//// require_once DR . '/all/ajax.start.php';
//// $ff = $db->prepare('UPDATE `mitems` SET `status` = \'hide\' WHERE `id` = :id ');
//// $ff->execute(array(':id' => (int) $_POST['id2']));
////die('123');
////
////echo '<br/>'.__FILE__.' '.__LINE__;
////    $checki = \Nyos\mod\items::getItemsSimple($db, '050.chekin_checkout', 'show');
////    \f\pa($checki,2,'','$checki');
////echo '<br/>'.__FILE__.' '.__LINE__;
////    $salary = \Nyos\mod\JobDesc::configGetJobmansSmenas($db);
////    \f\pa($salary,2,'','$salary');
////    $return['txt'] .= '<br/>salary';
////    foreach ($salary as $k => $v) {
////        $return['txt'] .= '<br/><nobr>[' . $k . '] - ' . $v . '</nobr>';
////        $return['salary_' . $k] = $v;
////    }
////echo '<br/>'.__FILE__.' '.__LINE__;
////echo '<br/>'.__FILE__.' '.__LINE__;
////echo '<br/>'.__FILE__.' '.__LINE__;
//// \f\pa($return);
//// exit;
////\f\pa($return);
//// если есть ошибки
//            if (!empty($error)) {
//
//                require_once DR . dir_site . 'config.php';
//
//                $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
//// \f\pa($sp);
//
//                if (!isset($_REQUEST['no_send_msg'])) {
//                    $txt_to_tele = 'Обнаружены ошибки при расчёте оценки точки продаж (' . $sp['data'][$_REQUEST['sp']]['head'] . ') за день работы (' . $_REQUEST['date'] . ')' . PHP_EOL . PHP_EOL . $error;
//
//                    if (class_exists('\nyos\Msg'))
//                        \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);
//
//                    if (isset($vv['admin_ajax_job'])) {
//                        foreach ($vv['admin_ajax_job'] as $k => $v) {
//                            \nyos\Msg::sendTelegramm($txt_to_tele, $v);
////\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
//                        }
//                    }
//                }
////echo '<br/>'.__FILE__.' '.__LINE__;
//
//                return \f\end2('Обнаружены ошибки при расчёте оценки точки продаж (' . $_REQUEST['sp'] . ') за день работы (' . $_REQUEST['date'] . ')' . $error, false);
//            }
//// если нет ошибок считаем
//            else {
//
//                \f\timer::start();
//
//                /**
//                 * сравниваем время ожидания холодный цех
//                 */
//                if (isset($return['timeo_cold']) && isset($return['norm_time_wait_norm_cold'])) {
//
//                    $return['txt'] .= '<br/><br/>-------------------';
//                    $return['txt'] .= '<br/>время ожидания (хол.цех)';
//                    $return['txt'] .= '<br/>по плану: ' . $return['norm_time_wait_norm_cold'] . ' и значение в ТП ' . $return['timeo_cold'];
//
//                    if (
//                            isset($return['timeo_cold']) && isset($return['norm_time_wait_norm_cold']) &&
//                            $return['timeo_cold'] > $return['norm_time_wait_norm_cold']
//                    ) {
//
//                        $return['txt'] .= '<br/>не норм, оценка 3';
//                        $return['ocenka_time'] = 3;
//                        $return['ocenka'] = 3;
//                    } else {
//                        $return['txt'] .= '<br/>норм, оценка 5';
//                        $return['ocenka_time'] = 5;
//                    }
//                } else {
//                    throw new \Exception('Вычисляем оценку дня, прервано, не хватает данных по времени ожидания', 14);
//                }
//
//                /**
//                 * сравниваем объём выручки
//                 */
//                if (1 == 2) {
//                    if (!empty($return['norm_vuruchka']) && !empty($return['oborot'])) {
//
//                        $return['txt'] .= '<br/><br/>-------------------';
//                        $return['txt'] .= '<br/>норма выручки';
//                        $return['txt'] .= '<br/>по плану: ' . $return['norm_vuruchka'] . ' и значение в ТП ' . $return['oborot'];
//
//                        if ($return['oborot'] >= $return['norm_vuruchka']) {
//                            $return['oborot_bolee_norm'] = 1;
//                            $return['ocenka_oborot'] = 5;
//                            $return['txt'] .= '<br/>норм, оценка 5';
//                        } else {
//                            $return['oborot_bolee_norm'] = 0;
//                            $return['ocenka_oborot'] = 3;
//                            $return['ocenka'] = 3;
//                            $return['txt'] .= '<br/>не норм, оценка 3';
//                        }
//                    }
////
//                    else {
//                        throw new \Exception('Вычисляем оценку дня, прервано, не хватает данных по обороту за сутки', 18);
//                    }
//                }
//
//                /**
//                 * считаем норму выручки на руки
//                 */
//// if (!empty($return['norm_kolvo_hour_in1smena'])) {
//                if (!empty($return['norm_kolvo_hour_in1smena']) && !empty($return['norm_vuruchka_on_1_hand'])) {
//
//                    $return['txt'] .= '<br/><br/>-------------------';
//                    $return['txt'] .= '<br/>норма выручки (на руки)';
//
//                    $return['smen_in_day'] = round($return['hours'] / $return['norm_kolvo_hour_in1smena'], 1);
//                    $return['txt'] .= '<br/>Кол-во поваров: ' . $return['smen_in_day'];
//
//                    $return['on_hand_fakt'] = ceil($return['oborot'] / $return['smen_in_day']);
//// $return['summa_na_ruki_norm'] = ceil($return['oborot'] / 100 * $return['norm_procent_oplata_truda_on_oborota']);
////$return['txt'] .= '<br/>по плану: ' . $return['summa_na_ruki_norm'] . ' и значение в ТП ' . $return['on_hand_fakt'];
//                    $return['txt'] .= '<br/>по плану: ' . $return['norm_vuruchka_on_1_hand'] . ' и значение в ТП ' . $return['on_hand_fakt'];
//
//                    if ($return['on_hand_fakt'] < $return['norm_vuruchka_on_1_hand']) {
//                        $return['ocenka'] = 3;
//                        $return['ocenka_naruki'] = 3;
//                        $return['ocenka'] = 3;
//                        $return['txt'] .= '<br/>не норм, оценка 3';
//                    } else {
//                        $return['ocenka_naruki'] = 5;
//                        $return['txt'] .= '<br/>норм, оценка 5';
//                    }
//                } else {
//                    throw new \Exception('Вычисляем оценку дня, прервано, не хватает значения по плану (норма на руки)', 19);
//                }
//
//
//                $return['txt'] .= '<br/>';
//                $return['txt'] .= '<br/>';
//                $return['txt'] .= '-----------';
//                $return['txt'] .= '<br/>';
//                $return['txt'] .= 'оценка дня : ' . $return['ocenka'];
//                $return['txt'] .= '<br/>';
//                $return['txt'] .= '<br/>';
//                $return['txt'] .= '<br/>';
//
//// $return['ocenka_upr'] = $return['ocenka'];
////            $return['time'] .= PHP_EOL . ' считаем ходится не сходится : ' . \f\timer::stop();
////            $return['txt'] .= '<br/><nobr>рекомендуемая оценка упр: ' . $return['ocenka_upr'] . '</nobr>';
//
//
//                /**
//                 * запись результатов в бд
//                 */
//                if (1 == 1) {
//                    $sql_del = '';
//                    $sql_ar_new = [];
//
//                    foreach ($id_items_for_new_ocenka as $id_item => $v) {
//
//                        $sql_del .= (!empty($sql_del) ? ' OR ' : '') . ' id_item = \'' . (int) $id_item . '\' ';
//                        $sql_ar_new[] = array(
//                            'id_item' => $id_item,
//                            'name' => 'ocenka_auto',
//                            'value' => $return['ocenka']
//                        );
//                    }
//
//                    if (!empty($sql_del)) {
//                        $ff = $db->prepare('DELETE FROM `mitems-dops` WHERE name = \'ocenka_auto\' AND ( ' . $sql_del . ' ) ');
//                        $ff->execute();
//                    }
//
//                    \f\db\sql_insert_mnogo($db, 'mitems-dops', $sql_ar_new);
//                    $return['txt'] .= '<br/>записали автоценки сотрудникам';
//                }
//
//                require_once DR . dir_site . 'config.php';
//
//                $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
//// \f\pa($sp);
//
//                \Nyos\mod\items::addNewSimple($db, 'sp_ocenki_job_day', $return);
//
//                if (!isset($_REQUEST['no_send_msg']) && !isset($_REQUEST['telega_no_send'])) {
//
//                    $txt_to_tele = 'Расчитали автооценку ( ' . $sp['data'][$_REQUEST['sp']]['head'] . ' ) за день работы (' . $_REQUEST['date'] . ')'
//                            . PHP_EOL
//                            . PHP_EOL
//                            . str_replace('<br/>', PHP_EOL, $return['txt'])
////                        . PHP_EOL
////                        . '-----------------'
////                        . PHP_EOL
////                        . 'время выполнения вычислений'
////                        . PHP_EOL
////                        . $return['time']
//                    ;
//
//                    if (class_exists('\nyos\Msg'))
//                        \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);
//
//                    if (isset($vv['admin_ajax_job'])) {
//                        foreach ($vv['admin_ajax_job'] as $k => $v) {
//                            \nyos\Msg::sendTelegramm($txt_to_tele, $v);
////\Nyos\NyosMsg::se ndTelegramm( 'Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
//                        }
//                    }
//                }
//
//                \f\end2(
//                        $return['txt']
//                        . '<br/>часов: ' . $return['hours']
//                        . '<br/>смен в дне: ' . $return['smen_in_day'],
//                        true,
//                        $return
//                );
//            }
//
////return \f\end2('Обнаружены ошибки: ' . $ex->getMessage() . ' <Br/>' . $text, false, array( 'error' => $ex->getMessage() ) );
//        }
//    }
////
//    catch (\Exception $ex) {
//
//// if ( isset($_REQUEST['no_send_msg']) ) {}else{}
//
//        $text = $ex->getMessage()
//                . ' авторасчёт оценки дня'
//                . PHP_EOL
//                . PHP_EOL
//                . ' sp:' . ($return['data']['sp'] ?? '--')
//                . ' date:' . ($return['data']['date'] ?? '--')
//                . PHP_EOL
//                . PHP_EOL
//                . '--- ' . __FILE__ . ' ' . __LINE__ . '-------'
//                . PHP_EOL
//                . $ex->getMessage() . ' #' . $ex->getCode()
//                . PHP_EOL
//                . $ex->getFile() . ' #' . $ex->getLine()
//                . PHP_EOL
//                . $ex->getTraceAsString()
//// . '</pre>'
//        ;
//
//        if (1 == 2) {
//            if (class_exists('\Nyos\Msg'))
//                \Nyos\Msg::sendTelegramm($text, null, 1);
//        }
//
//        /*
//          echo '<pre>'
//          . PHP_EOL
//          . PHP_EOL
//          . '<pre>--- ' . __FILE__ . ' ' . __LINE__ . '-------'
//          . PHP_EOL
//          . $ex->getMessage() . ' #' . $ex->getCode()
//          . PHP_EOL
//          . $ex->getFile() . ' #' . $ex->getLine()
//          . PHP_EOL
//          . $ex->getTraceAsString()
//          . '</pre>';
//         */
//
//
//        /*
//
//          require_once DR . dir_site . 'config.php';
//
//          $sp = \Nyos\mod\items::getItemsSimple($db, 'sale_point', 'show');
//          // \f\pa($sp);
//
//          $txt_to_tele = 'Обнаружены ошибки при расчёте оценки точки продаж (' . $sp['data'][$_REQUEST['sp']]['head'] . ') за день работы (' . $_REQUEST['date'] . ')' . PHP_EOL . PHP_EOL . $error;
//
//          if (class_exists('\nyos\Msg'))
//          \nyos\Msg::sendTelegramm($txt_to_tele, null, 1);
//
//          if (isset($vv['admin_ajax_job'])) {
//          foreach ($vv['admin_ajax_job'] as $k => $v) {
//          \nyos\Msg::sendTelegramm($txt_to_tele, $v);
//          //\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
//          }
//          }
//         */
//
//        $r = ob_get_contents();
//        ob_end_clean();
//
//        \f\end2('Обнаружены ошибки: ' . $ex->getMessage(), false, [
//            'error' => $ex->getMessage(),
//            'code' => $ex->getCode(),
//            'sp' => ($return['data']['sp'] ?? null),
//            'date' => ($return['data']['date'] ?? null),
//            'text' => $text . '<br/>' . $r,
//        ]);
//    }
//}
