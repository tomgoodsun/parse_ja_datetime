# parse_ja_datetime
日本語表記の日付と時刻をパースするプログラムです。

# 使い方

```php
echo (new DateTimeJp('平成2年5月7日午前1時15分30秒'))->format('Y-m-d H:i:s');
// 1990-05-07 12:15:30

var_export(DateTimeJp::parse('平成2年5月7日午後1時15分30秒'));
// array (
//   'year' => 1990,
//   'month' => '5',
//   'day' => '7',
//   'hour' => 13,
//   'minute' => '15',
//   'second' => '30',
// )
```
