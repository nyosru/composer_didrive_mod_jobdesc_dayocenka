<?php

/**
 * считаем оценку за 1 день
 */
if (isset($skip_start) && $skip_start === true) {
    
} else {
    require_once '0start.php';
    $skip_start = false;
}


// \f\pa($_REQUEST);

try {

    if (
            !empty($_REQUEST['sp']) && !empty($_REQUEST['date']) && !empty($_REQUEST['s']) && \Nyos\Nyos::checkSecret($_REQUEST['s'], $_REQUEST['sp'] . $_REQUEST['date']) !== false
    ) {
        
    } else {
        \f\end2('хьюстон что то пошло не так (обновите страницу и повторите) #' . __LINE__, false);
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
    // $actions = [];
    //\f\pa($actions, 2, '', '$actions');
//    foreach ($actions['data']['actions'] as $k => $v) {
//        if ($v['type'] == 'check')
//            \f\pa($v, 2, '', 'check');
//    }
// считаем сколько часов отработано
    $hours = \Nyos\mod\JOBDESC_DAYOCENKA::calcHoursDaysForOcenka($db, $return['date'], $return['sp'], array_keys($jobmans['data']['jobmans']), $actions['data']['actions']);
    // $hours = [];
    //\f\pa($hours, 2, '', 'колво hours');
// proc_zp_ot_oborota_if5
// часы что в фот
    $return['hour_day'] = $return['hours'] = $hours['hours'];
// общее число часов
    $return['hours_all'] = $hours['hours_all'];

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
                $return['cold'] = $v['cold'] ?? '';
                $return['delivery'] = $v['delivery'] ?? '';
            }
        }
    }

// считаем норму на 1 руки
    $return['smen_in_day'] = round($return['hours'] / $return['norms']['kolvo_hour_in1smena'], 1);

// считаем выручку в 1 руки
    $return['vuruchka_on_1_hand'] = $return['summa_na_ruki'] = ceil($return['oborot'] / $return['smen_in_day']);

//$return['vuruchka'] = $return['oborot'];

    $return['summa_zp_if5'] = $hours['summa_if5'] ?? '';

    if ($return['summa_na_ruki'] >= $return['norms']['vuruchka_on_1_hand']) {
        $return['ocenka_naruki_ot_oborota'] = $return['ocenka_naruki'] = 5;
    } else {
        $return['ocenka_naruki_ot_oborota'] = $return['ocenka'] = $return['ocenka_naruki'] = 3;
    }

    $return['txt'] .= '<Br/><b>сумма на зп, если оценка 5</b>'
            . '<Br/>посчитали сколько отработано смен <nobr>' . $return['hours'] . '/' . $return['norms']['kolvo_hour_in1smena'] . ' = ' . $return['smen_in_day'] . '</nobr>'
            . '<Br/>часть оборота на 1 руки ' . $return['summa_na_ruki']
            . ' (норматив ' . $return['norms']['vuruchka_on_1_hand'] . ') '
            . '<div style="background-color: rgba(' . ( $return['ocenka_naruki'] == 5 ? '0,255,0' : '255,255,0' ) . ',0.3);" >оценка: ' . $return['ocenka_naruki'] . '</div>';

    if ($return['timeo']['cold'] <= $return['norms']['time_wait_norm_cold'] && $return['timeo']['delivery'] <= $return['norms']['time_wait_norm_delivery']) {
        $return['ocenka_time'] = 5;
    } else {
        $return['ocenka'] = $return['ocenka_time'] = 3;
    }

    $return['txt'] .= '<Br/><b>сравниваем время ожидания</b>'
            . '<Br/>'
            . $return['timeo']['cold'] . '/' . $return['timeo']['delivery']
            . ' <nobr>(норматив ' . $return['norms']['time_wait_norm_cold'] . '/' . $return['norms']['time_wait_norm_delivery'] . ')</nobr> '
            . '<div style="background-color: rgba(' . ( $return['ocenka_time'] == 5 ? '0,255,0' : '255,255,0' ) . ',0.3);" >оценка: ' . $return['ocenka_time'] . '</div>';

    // $return['procent_oplata_truda_on_oborota'] = $return['proc_zp_ot_oborota_if5'] = $return['if5_proc_oborot'] = round($hours['summa_if5'] / ($return['oborot'] / 100), 1);
    $return['proc_zp_ot_oborota_if5'] = round($hours['summa_if5'] / ($return['oborot'] / 100), 1);

    if ($return['proc_zp_ot_oborota_if5'] < $return['norms']['procent_oplata_truda_on_oborota']) {
        $return['ocenka_proc_ot_oborot'] = 5;
    } else {
        $return['ocenka'] = $return['ocenka_proc_ot_oborot'] = 3;
    }

    $return['txt'] .= '<Br/><b>сравниваем % от оборота на ЗП</b>'
            . '<Br/>текущее значение ' . $return['proc_zp_ot_oborota_if5']
            . ' <nobr>(на зп ' . $hours['summa_if5'] . ' из ТО ' . $return['oborot'] . ')</nobr> '
            . '<Br/><nobr>норматив ' . $return['norms']['procent_oplata_truda_on_oborota'] . '</nobr> '
            . '<div style="background-color: rgba(' . ( $return['ocenka_proc_ot_oborot'] == 5 ? '0,255,0' : '255,255,0' ) . ',0.3);" >оценка: ' . $return['ocenka_proc_ot_oborot'] . '</div>'
    ;

    $txt_ocenka = '<div style="background-color: rgba(' . ( $return['ocenka'] == 5 ? '0,255,0' : '255,255,0' ) . ',0.8); padding:2px 5px; text-align:center;"><nobr><b>Новая итоговая оценка: ' . $return['ocenka'] . '</b></nobr></div>';
    $return['txt'] .= '<br/><br/><font style="color:gray;" >обновите страницу для обновиления оценки смен в графике</font>';

// \f\pa($return);

    $s2 = $db->prepare('DELETE FROM `mod_sp_ocenki_job_day` WHERE `sale_point` = :sp AND `date` = :date ;');
    $s2->execute([':sp' => $return['sale_point'], ':date' => $return['date']]);

    $return2 = $return;
    unset($return2['txt']);

    // \f\pa($return2, 12, '', '$return2');
    $e = \Nyos\mod\items::add($db, 'sp_ocenki_job_day', $return2);
// \f\pa($e);




    $sql = 'UPDATE `mod_050_chekin_checkout` SET `ocenka_auto` = :ocenka WHERE `id` = \'' . implode('\' OR `id` = \'', $return['checks']) . '\' ;';
//\f\pa($sql);
    $ff = $db->prepare($sql);
    $ff->execute([':ocenka' => $return['ocenka']]);
// echo implode( ', ' , $return['checks'] );




    $r = ob_get_contents();
    ob_end_clean();

    \f\end2($txt_ocenka . $r . $return['txt'], true, $return);
}
//
catch (\Exception $ex) {

    ob_start('ob_gzhandler');

    \f\pa($ex);

    $r = ob_get_contents();
    ob_end_clean();


    \f\end2('ok' . $r, false);
}
