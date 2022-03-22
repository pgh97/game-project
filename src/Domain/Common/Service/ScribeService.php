<?php

namespace App\Domain\Common\Service;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocketPool;
use \scribeClient;
final class ScribeService
{
    public const PROJECT_NAME = 'uruk-game';

    private scribeClient $client;

    public function __construct(scribeClient $client)
    {
        $this->client = $client;
    }

    public function Log(array $msg): void
    {
        $this->client->Log($msg);
    }

    public function getRealClientIp() {

        $ipaddress = '';

        if ($_SERVER['HTTP_CLIENT_IP']) {

            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];

        } else if($_SERVER['HTTP_X_FORWARDED_FOR']) {

            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else if($_SERVER['HTTP_X_FORWARDED']) {

            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];

        } else if($_SERVER['HTTP_FORWARDED_FOR']) {

            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];

        } else if($_SERVER['HTTP_FORWARDED']) {

            $ipaddress = $_SERVER['HTTP_FORWARDED'];

        } else if($_SERVER['REMOTE_ADDR']) {

            $ipaddress = $_SERVER['REMOTE_ADDR'];

        } else {

            $ipaddress = '알수없음';

        }

        return $ipaddress;
    }
}