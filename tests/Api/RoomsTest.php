<?php
namespace Polidog\Chatwork\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use Phake;

/**
 * Class RoomsTest
 * @package Polidog\Chatwork\Api
 */
class RoomsTest extends \PHPUnit_Framework_TestCase
{
    
    private $httpClient;
    private $response;
    
    public function setUp()
    {
        $this->httpClient = Phake::mock(ClientInterface::class);
        $this->response = Phake::mock(ResponseInterface::class);

        Phake::when($this->response)->json()->thenReturn([]);
    }

    /**
     * @test
     */
    public function showChatRoom()
    {
        Phake::when($this->httpClient)->get('rooms')->thenReturn($this->response);
     
        $rooms = new Rooms($this->httpClient);
        $rooms->show();
        
        Phake::verify($this->httpClient, Phake::times(1))->get('rooms');
    }
    
    
}