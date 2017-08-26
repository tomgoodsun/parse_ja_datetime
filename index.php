<?php
require(__DIR__ . '/libs/include.php');

$list = array(
    '2017-08-27 03:16:25',
    '昭和64年5月6日',
    '平成元年5月7日',
    '平成1年5月7日',
    '平成2年5月7日',
    '平成02年05月07日',
    '平02年05月07日',
    '平成2年5月7日午前1時15分30秒',
    '平成2年5月7日午後1時15分30秒',
    '平成2年5月7日13時15分30秒',
    '平成2年5月7日13時15分',
    'S64.5.6',
    'H1.5.7',
    'H2.5.7',
    'H02.05.07',
);

foreach ($list as $item) {
    //var_export(DateTimeJp::parse($item));
    dump(sprintf(
        '%s is %s',
        $item,
        (new DateTimeJp($item))->format('Y-m-d H:i:s')
    ));
}

//dump($date->__toString());

