<?php

namespace Plancke\HypixelPHP\util;

use Plancke\HypixelPHP\color\ColorUtils;

/**
 * Class Utilities
 * @package Plancke\HypixelPHP\util
 */
abstract class Utilities {

    /** @deprecated */
    const COLOR_CHAR = ColorUtils::COLOR_CHAR;
    /** @deprecated */
    const MC_COLORS = [
        '0' => '#000000',
        '1' => '#0000AA',
        '2' => '#008000',
        '3' => '#00AAAA',
        '4' => '#AA0000',
        '5' => '#AA00AA',
        '6' => '#FFAA00',
        '7' => '#AAAAAA',
        '8' => '#555555',
        '9' => '#5555FF',
        'a' => '#3CE63C',
        'b' => '#3CE6E6',
        'c' => '#FF5555',
        'd' => '#FF55FF',
        'e' => '#FFFF55',
        'f' => '#FFFFFF'
    ];
    /** @deprecated */
    const MC_COLORNAME = [
        "BLACK" => '§0',
        "DARK_BLUE" => '§1',
        "DARK_GREEN" => '§2',
        "DARK_AQUA" => '§3',
        "DARK_RED" => '§4',
        "DARK_PURPLE" => '§5',
        "GOLD" => '§6',
        "GRAY" => '§7',
        "DARK_GRAY" => '§8',
        "BLUE" => '§9',
        "GREEN" => '§a',
        "AQUA" => '§b',
        "RED" => '§c',
        "LIGHT_PURPLE" => '§d',
        "YELLOW" => '§e',
        "WHITE" => '§f',
        "MAGIC" => '§k',
        "BOLD" => '§l',
        "STRIKETHROUGH" => '§m',
        "UNDERLINE" => '§n',
        "ITALIC" => '§o',
        "RESET" => '§r'
    ];

    /**
     * Parses MC encoded colors to HTML
     * @param $string
     * @param array $colorMap
     * @return string
     * @deprecated Replaced with {@see ColorParser}
     */
    public static function parseColors($string, $colorMap = Utilities::MC_COLORS) {
        if ($string == null) {
            return null;
        }

        if (strpos($string, Utilities::COLOR_CHAR) === false) {
            return $string;
        }
        $d = explode(Utilities::COLOR_CHAR, $string);
        $out = '';
        foreach ($d as $part) {
            if (strlen($part) == 0) continue;
            if (array_key_exists(substr($part, 0, 1), $colorMap)) {
                $color = $colorMap[substr($part, 0, 1)];
                $out = $out . "<span style='text-shadow: 1px 1px #eee; color: $color'>" . substr($part, 1) . "</span>";
            } else {
                $out .= substr($part, 1);
            }
        }
        return $out;
    }

    /**
     * Parses MC encoded colors to HTML
     * @param $string
     * @return string
     * @deprecated
     */
    public static function stripColors($string) {
        if ($string == null) {
            return null;
        }

        if (strpos($string, Utilities::COLOR_CHAR) === false) {
            return $string;
        }
        $d = explode(Utilities::COLOR_CHAR, $string);
        $out = '';
        foreach ($d as $part) {
            $out .= substr($part, 1);
        }
        return $out;
    }

    /**
     * Add a Minecraft color coded string to an image.
     * @param $img
     * @param $font
     * @param $fontSize
     * @param $startX
     * @param $startY
     * @param $string
     * @deprecated
     */
    public static function addMCColorString(&$img, $font, $fontSize, $startX, $startY, $string) {
        if (strpos($string, "§") === false) {
            $string = '§7' . $string;
        }

        $currentX = $startX;
        $currentY = $startY + 16;
        foreach (explode("§", $string) as $part) {
            $key = substr($part, 0, 1);
            if (array_key_exists($key, self::MC_COLORS)) {
                $rgb = Utilities::hex2rgb(self::MC_COLORS[$key]);
                $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);

                $part = substr($part, 1);
                $bbox = imagettftext($img, $fontSize, 0, $currentX, $currentY, $color, $font, $part);
                $currentX += ($bbox[4] - $bbox[0]);
            }
        }
    }

    /**
     * Converts Hexadecimal color code into RGB
     * @param $hex
     * @return array
     */
    public static function hex2rgb($hex) {
        // replace the pound symbol just in case
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return [$r, $g, $b];
    }

    /**
     * Get a value recursively in an array
     *
     * @param array $array
     * @param string $key
     * @param mixed $default default value to return if not found in array
     * @param string $delimiter where to split the key and go a level deeper in the array
     * @return mixed
     */
    public static function getRecursiveValue($array, $key, $default = null, $delimiter = '.') {
        $return = $array;
        foreach (explode($delimiter, $key) as $split) {
            $return = isset($return[$split]) ? $return[$split] : $default;
        }
        return $return ? $return : $default;
    }

    /**
     * @param $uuid
     * @return string
     */
    public static function ensureDashedUUID($uuid) {
        if (strpos($uuid, "-")) {
            if (strlen($uuid) == 32) {
                return $uuid;
            }
            $uuid = Utilities::ensureNoDashesUUID($uuid);
        }
        return substr($uuid, 0, 8) . "-" . substr($uuid, 8, 12) . substr($uuid, 12, 16) . "-" . substr($uuid, 16, 20) . "-" . substr($uuid, 20, 32);
    }

    /**
     * @param $uuid
     * @return string
     */
    public static function ensureNoDashesUUID($uuid) {
        return str_replace("-", "", $uuid);
    }

    /**
     * @param $filename
     *
     * @return null|string
     */
    public static function getFileContent($filename) {
        $content = null;
        if (file_exists($filename)) {
            $file = fopen($filename, 'r+');
            if (filesize($filename) > 0) {
                $content = fread($file, filesize($filename));
            }
            fclose($file);
        }
        return $content;
    }

    /**
     * @param $filename
     * @param $content
     */
    public static function setFileContent($filename, $content) {
        if (!file_exists(dirname($filename))) {
            @mkdir(dirname($filename), 0777, true);
        }
        $file = fopen($filename, 'w+');
        fwrite($file, $content);
        fclose($file);
    }
}