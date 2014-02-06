<?php

namespace mediasilo\http;

use mediasilo\Config;
use mediasilo\Credentials;
use mediasilo\Meta;
use mediasilo\http\HttpResponseHandler;



    class WebClient {

        private $httpResponseHandler;

        public function __construct() {
            $this->httpResponseHandler = new HttpResponseHandler();
        }

        public function get($path) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->getRequestHeaders(),
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => (rtrim(CONFIG::MEDIASILO_API_BASE_URL, "/")."/".rtrim(ltrim($path, "/"))),
                CURLOPT_USERAGENT => Credentials::HOST.":".Credentials::USERNAME." PHP SDK Version ".META::MEDIASILO_SDK_VERSION
            ));

            $result = curl_exec($curl);

            $this->httpResponseHandler->handle($result, curl_getinfo($curl, CURLINFO_HTTP_CODE));

            curl_close($curl);

            return $result;
        }

        public function post($path, $payload) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->getRequestHeaders(),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => (rtrim(CONFIG::MEDIASILO_API_BASE_URL, "/")."/".rtrim(ltrim($path, "/"))),
                CURLOPT_USERAGENT => Credentials::HOST.":".Credentials::USERNAME." PHP SDK Version ".META::MEDIASILO_SDK_VERSION
            ));

            $result = curl_exec($curl);

            curl_close($curl);

            return $result;
        }

        public function put($path, $payload) {
            $fp = fopen('php://temp/maxmemory:256000', 'w');
            if (!$fp) {
                die('could not open temp memory data');
            }
            fwrite($fp, $payload);
            fseek($fp, 0);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->getRequestHeaders(),
                CURLOPT_PUT => 1,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_INFILE => $fp,
                CURLOPT_INFILESIZE, strlen($payload),
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => (rtrim(CONFIG::MEDIASILO_API_BASE_URL, "/")."/".rtrim(ltrim($path, "/"))),
                CURLOPT_USERAGENT => Credentials::HOST.":".Credentials::USERNAME." PHP SDK Version ".META::MEDIASILO_SDK_VERSION
            ));

            $result = curl_exec($curl);

            $this->httpResponseHandler->handle($result, curl_getinfo($curl, CURLINFO_HTTP_CODE));

            curl_close($curl);
        }

        public function delete($path) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => $this->getRequestHeaders(),
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => (rtrim(CONFIG::MEDIASILO_API_BASE_URL, "/")."/".rtrim(ltrim($path, "/"))),
                CURLOPT_USERAGENT => Credentials::HOST.":".Credentials::USERNAME." PHP SDK Version ".META::MEDIASILO_SDK_VERSION
            ));

            $result = curl_exec($curl);

            $this->httpResponseHandler->handle($result, curl_getinfo($curl, CURLINFO_HTTP_CODE));

            curl_close($curl);

            return $result;
        }

        private function getRequestHeaders() {
            $headers = array("Content-Type: application/json; charset=utf-8","Accept:application/json");
            $hostHeader = "MediaSiloHostContext:".Credentials::HOST;
            $authHeader = "Authorization: Basic ".base64_encode(Credentials::USERNAME.":".Credentials::PASSWORD);

            array_push($headers, $hostHeader);
            array_push($headers, $authHeader);

            return $headers;
        }

    }
