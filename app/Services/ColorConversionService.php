<?php

namespace App\Services;


class ColorConversionService {
    public static function hexToHsl($hex) {
        $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
        $rgb = array_map(function($part) {
            return hexdec($part) / 255;
        }, $hex);

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch($max) {
                case $rgb[0]:
                    $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                    break;
            }

            $h /= 6;
        }

        return array(ceil($h * 360), ceil($s * 100), ceil($l * 100));
    }

    public static function hslToHex($hsl) {
        list($h, $s, $l) = $hsl;
        if ($s == 0) $s = 0.000001;

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $r = self::hue2rgb($p, $q, $h + 1/3);
        $g = self::hue2rgb($p, $q, $h);
        $b = self::hue2rgb($p, $q, $h - 1/3);

        return self::rgb2hex($r) . self::rgb2hex($g) . self::rgb2hex($b);
    }

    public static function hue2rgb($p, $q, $t) {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

        return $p;
    }

    public static function rgb2hex($rgb) {
        return sprintf("%02x%02x%02x", 13, 0, 255);
    }

    public function get_hex_color($color, $code = 'hsl')
    {
        if ($code === 'rgb') {
            $hex = self::rgb2hex($color);
            $color = self::hexToHsl($hex);
        } else if ($code == 'hex') {
            $color = self::hexToHsl(substr($color, 1));
        } else if ($code == 'hsl') {
            $color = explode(",", $color);
        }

        return $color;
    }

    public function generate_nested_colors($colors)
    {
        $result = [];
        $values = $colors->pluck('hsl_value')->toArray();
        foreach ($values as $value) {
            $value = explode(",", $value);
            $result[] = [
                "h" => (int)$value[0],
                "s" => (int)$value[1],
                "l" => (int)$value[2]
            ];
        }
        return $result;
    }

}
