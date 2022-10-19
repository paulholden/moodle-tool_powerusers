<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_powerusers;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class marvelapi
 *
 * @package    tool_powerusers
 * @copyright  2022 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marvelapi {

    /**
     * Call Marvel API to get the requested user(s)
     *
     * @param string $name
     * @param string $type
     * @return array|null
     */
    public static function get_users(string $name, string $type): ?array {
        $ts = time();
        $privatekey = get_config('tool_powerusers', 'marvelprivatekey');
        $publickey = get_config('tool_powerusers', 'marvelpublickey');

        if (empty($privatekey) || empty($publickey)) {
            throw new moodle_exception('You need to add your public and private keys to plugin settings first!');
        }

        $name = str_replace( ' ', '%20', trim($name));
        $hash = md5($ts.$privatekey.$publickey);
        $type = ($type === 'exactmatch') ? 'name' : 'nameStartsWith';

        $url = "http://gateway.marvel.com/v1/public/characters?hash=$hash&apikey=$publickey&ts=$ts&$type=$name";
        $content = download_file_content($url);

        if (!$content) {
            return null;
        }

        $content = json_decode($content);
        return $content->data->results;
    }
}