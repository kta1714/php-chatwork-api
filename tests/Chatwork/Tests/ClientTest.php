<?php
namespace Chatwork\Tests;

use Chatwork\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider apiProvider
     */
    public function APIオブジェクトを取得する($name, $className)
    {
        $client = new Client();
        $actual = $client->api($name);
        $this->assertInstanceOf($className, $actual);
    }

    /**
     * @test
     */
    public function 認証処理を行う()
    {
        $apiKey = "hogehoge";
        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
            ->method('authenticate')
            ->with($this->equalTo($apiKey));

        $client = new Client($httpClientMock);
        $client->authenticate($apiKey);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getHttpClientMock()
    {
        return $this->getMockBuilder('\Chatwork\HttpClient\HttpClient')
            ->setMethods([
                'authenticate',
                'get',
                'post',
                'put',
                'delete',
                'request',
                'setOption',
                'setHeaders'
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }



    /**
     * data provider
     * @return array
     */
    public function apiProvider()
    {
        return [
            ["me","Chatwork\Api\Me"],
            ["my","Chatwork\Api\My"],
            ["contacts","Chatwork\Api\Contacts"],
            ["rooms","Chatwork\Api\Rooms"],
        ];
    }
}