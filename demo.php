<?php
use Stone\SDK\Util\Crypt;

require_once(dirname(__FILE__) . '/bootstrap.php');

class OpenApiClient
{
    private $debug = false;

    public static function getInstance()
    {
        return new self();
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    public function disableDebug()
    {
        $this->debug = false;
    }

    public function request($url, $params)
    {
        $key = config('key');
        $requestData = Crypt::partnerEncrypt($params, $key);
        $responseData = $this->rawPost($url, $requestData);

        if ($this->debug) {
            printf("[%s] url: %s, request: %s, response: %s\n", date('Y-m-d H:i:s'), $url, $requestData, $responseData);
        }

        $result = Crypt::partnerDecrypt($responseData, $key);

        if (!isset($result['code'])) {
            throw new Exception('请求异常');
        }

        if ($result['code'] !== 0) {
            throw new Exception($result['message'], $result['code']);
        }

        return $result['data'];
    }

    public function rawPost($url, $data)
    {
        $app_id = config('app_id');
        $base_url = config('api_base_url');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-App-Id: ' . $app_id,
            'User-Agent: Open Api SDK Demo 1.0.0'
        ));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

$client = OpenApiClient::getInstance();

if (config('debug')) {
    $client->enableDebug();
}


$apiLists = [
    [
        'url' => '/api/v2/order/paymentInfo',
        'params' => ['appId' => 'CMIDF2E1GsTPVy90AthTLGdKpxxTRHhwIILF--', 'outTradeNo' => 'cmht000000001'],
    ],

    [
        'url' => '/api/v2/bill/paymentInfo',
        'params' => ['appId' => 'CMIDF2E1GsTPVy90AthTLGdKpxxTRHhwIILF--', 'outBillId' => 'b8783749384'],
    ],

    [
        'url' => '/api/v2/bill/settlementInfo',
        'params' => ['appId' => 'CMIDF2E1GsTPVy90AthTLGdKpxxTRHhwIILF--', 'outBillId' => 'b8783749384'],
    ],

    [
        'url' => '/api/v2/verifySign',
        'params' => ['appId' => 'CMIDF2E1GsTPVy90AthTLGdKpxxTRHhwIILF--', 'notifySignature' => 'xsdfsdf113EF'],
    ],
];

foreach ($apiLists as $item) {
    try {
        printf("request url: %s, request params: %s\n", $item['url'], json_encode($item['params']));
        $result = $client->request($item['url'], $item['params']);
        printf("response: %s\n\n\n", json_encode($result));
    } catch (Exception $e) {
        printf("request error: %s\n\n\n", $e->getMessage());
    }
}
