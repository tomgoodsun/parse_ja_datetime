<?php
class DateTimeJp extends \DateTime
{
    /**
     * Constructor
     *
     * @param $time = "now"
     * @param \DateTimeZone $timezone = null
     */
    public function __construct($time = "now", \DateTimeZone $timezone = null)
    {
        if ('now' !== $time) {
            $tmpTime = self::toTimestamp($time);
            if (false !== $tmpTime) {
                $time = date('Y-m-d H:i:s', $tmpTime);
            }
        }
        parent::__construct($time, $timezone);
    }

    private static $eraList = [
        ['name_pattern' => ['令和', '令', 'R'], 'timestamp' => 1556668800],  // 2019-05-01,
        ['name_pattern' => ['平成', '平', 'H'], 'timestamp' => 600188400],  // 1989-01-08,
        ['name_pattern' => ['昭和', '昭', 'S'], 'timestamp' => -1357635600], // 1926-12-25'
        ['name_pattern' => ['大正', '大', 'T'], 'timestamp' => -1812186000], // 1912-07-30
        ['name_pattern' => ['明治', '明', 'M'], 'timestamp' => -3216790800], // 1868-01-25
    ];

    /**
     * 元号設定を返す
     *
     * @param string $str
     * @return array|false
     */
    public static function detectEraSetting($str)
    {
        $str = trim($str);
        foreach (self::$eraList as $setting) {
            foreach ($setting['name_pattern'] as $pattern) {
                if (0 === mb_strpos($str, $pattern)) {
                    $setting['detected'] = $pattern;
                    return $setting;
                }
            }
        }
        return false;
    }

    /**
     * 年のオフセット取得
     *
     * timestampは改元年の日付なので、これに加算して西暦を割り出すオフセット値を取得する。
     * また和暦の場合「元年」という表記があるので、これを1年目と判断する。
     *
     * @param string $str
     * @return int
     */
    public static function getYearOffset($str)
    {
        if ('元' == mb_substr($str, 0, 1)) {
            return 0;
        }

        $ret = intval($str) - 1;
        return $ret < 0 ? 0 : $ret;

    }

    /**
     * 時間を24時間制に変換する。
     * 「午後」であれば$hourに12を加算する。
     *
     * @param int $hour
     * @param string $time_convention = null
     * @return int
     */
    public static function to24hour($hour, $time_convention = null)
    {
        if ('午後' === $time_convention) {
            return $hour + 12;
        }
        return $hour;
    }

    /**
     * 日本語表記の日付と時刻をパースする。
     *
     * @param string $str
     * @return array
     */
    public static function parse($str)
    {
        $indexes = array(
            'year'   => array( 2, 'Y'),
            'month'  => array( 3, 'm'),
            'day'    => array( 5, 'd'),
            'hour'   => array( 8, 'H'),
            'minute' => array(10, 'i'),
            'second' => array(12, 's'),
        );

        $str = trim($str);

        $setting = self::detectEraSetting($str);
        if (false === $setting) {
            return false;
        }

        $regexp = '(' . $setting['detected'] . '(元|[0-9]+)[\.年]([0-9]+)[\.月](([0-9]+)[\.日]?)?)';
        $regexp .= '((午前|午後)?([0-9]+)時(([0-9]+)分(([0-9]+)秒)?)?)?';

        preg_match('/' . $regexp . '/u', $str, $matches);

        $parts = array();
        foreach ($indexes as $type => $index) {
            if (array_key_exists($index[0], $matches)) {
                $parts[$type] = $matches[$index[0]];
            } else {
                $parts[$type] = intval(date($index[1]));
            }
        }
        $parts['year'] = intval(date('Y', $setting['timestamp'])) + self::getYearOffset($parts['year']);
        $parts['hour'] = self::to24hour($parts['hour'], array_key_exists(7, $matches) ? $matches[7] : null);

        return $parts;
    }

    /**
     * タイムスタンプに変換する
     *
     * @param string $str
     * @return int|bool
     */
    public static function toTimestamp($str)
    {
        $str = trim($str);
        $parts = self::parse($str);
        if (false === $parts) {
            return false;
        }
        $dateTimeStr = vsprintf('%04d-%02d-%02d %02d:%02d:%02d', $parts);
        return strtotime($dateTimeStr);
    }
}
