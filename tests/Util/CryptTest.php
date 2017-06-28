<?php

namespace Tests\Util;

use Stone\SDK\Util\Crypt;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    public function testPartnerEncrypt()
    {
        $key = config('pub_key');
        $params = [
            'appId' => '123123123',
            'outTradeNo' => '456456456456234',
        ];
        $encrypted = Crypt::partnerEncrypt($params, $key);
        //var_dump($encrypted);

        $private_key = config('private_key');
        $data = Crypt::platformDecrypt($encrypted, $private_key);
        //var_dump($data);

        $this->assertEquals($params, $data);
    }

    public function testPartnerDecrypt()
    {
        $private_key = config('private_key');
        $response = [
            'code' => 0,
            'message' => 'OK',
            'data' => [
                'id' => '1',
                'name' => 'xxxx',
            ],
        ];
        $encrypted = Crypt::platformEncrypt($response, $private_key);

        $key = config('pub_key');
        $data = Crypt::partnerDecrypt($encrypted, $key);

        $this->assertEquals($response, $data);
    }
}
