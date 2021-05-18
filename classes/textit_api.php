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


defined('MOODLE_INTERNAL') || die;

class TextitAPI {
    private $uri;
    private $apikey;

    public function __construct() {
        global $CFG;
        $this->uri = "https://api.textit.in/api/v2/broadcasts.json";
        $this->apikey = $CFG->block_sms_textit_apikey;
    }

    public function send_sms($to, $text) {
        $result = $this->trigger_api($to, $text);
        $status = false;
        try {
            $result = json_decode($result, true);
            // change to sent once channel is added
            $status = $result["status"] == "queued";
        } catch (Exception $e) {
            ";";
        }
        return $status;
    }

    private function trigger_api($to, $text) {
        $ch = curl_init($this->uri);
        $data = array (
            "text" => $text,
            "urns" => [$to]
        );
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token '.$this->apikey,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($data)
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
