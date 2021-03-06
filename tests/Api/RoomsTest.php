<?php
namespace Polidog\Chatwork\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use Phake;
use Polidog\Chatwork\Api\Rooms\Files;
use Polidog\Chatwork\Api\Rooms\Members;
use Polidog\Chatwork\Api\Rooms\Messages;
use Polidog\Chatwork\Api\Rooms\Tasks;
use Polidog\Chatwork\Entity\Collection\MembersCollection;
use Polidog\Chatwork\Entity\Factory\RoomFactory;
use Polidog\Chatwork\Entity\Member;
use Polidog\Chatwork\Entity\Room;
use Polidog\Chatwork\Entity\User;

/**
 * Class RoomsTest
 * @package Polidog\Chatwork\Api
 */
class RoomsTest extends \PHPUnit_Framework_TestCase
{
    
    private $httpClient;
    private $response;
    private $factory;
    
    public function setUp()
    {
        $this->httpClient = Phake::mock(ClientInterface::class);
        $this->response = Phake::mock(ResponseInterface::class);
        $this->factory = Phake::mock(RoomFactory::class);

        Phake::when($this->response)->json()->thenReturn([]);
    }

    /**
     * @test
     */
    public function チャットルームの一覧を取得する()
    {
        Phake::when($this->httpClient)->get('rooms')->thenReturn($this->response);
        Phake::when($this->factory)->collection($this->isType('array'))->thenReturn([]);
        $rooms = new Rooms($this->httpClient, $this->factory);
        $rooms->show();
        
        Phake::verify($this->httpClient,Phake::times(1))->get('rooms');
        Phake::verify($this->response,Phake::times(1))->json();
        Phake::verify($this->factory, Phake::times(1))->collection($this->isType('array'));
    }

    /**
     * @test
     */
    public function 指定したIDのチャットルームを取得することができる()
    {
        Phake::when($this->httpClient)->get(['rooms/{id}',['id' => 1]])
            ->thenReturn($this->response);
       
        Phake::when($this->factory)->entity($this->isType('array'));
        
        $rooms = new Rooms($this->httpClient, $this->factory);
        $rooms->detail(1);
        
        Phake::verify($this->httpClient,Phake::times(1))->get(['rooms/{id}',['id' => 1]]);
        Phake::verify($this->factory,Phake::times(1))->entity($this->isType('array'));
        
    }
    
    
    /**
     * @test
     */
    public function 新しくチャットルームを作成することができる()
    {
        $room = Phake::mock(Room::class);
        $members = Phake::mock(MembersCollection::class);
        
        Phake::when($this->httpClient)->post('rooms', $this->isType('array'))->thenReturn($this->response);
        Phake::when($this->response)->json()->thenReturn([
            "room_id" => 1
        ]);
        
        Phake::when($room)->toArray()->thenReturn([
            'members_admin_ids' => '1,2',
            'name' => 'test_room'
        ]);
        
        Phake::when($members)->getAdminIds()->thenReturn([
            1,2,3
        ]);
        Phake::when($members)->getMemberIds()->thenReturn([
            4,5,6
        ]);
        Phake::when($members)->getReadonlyIds()->thenReturn([
            7,8,9
        ]);

        $rooms = new Rooms($this->httpClient, new RoomFactory());
        $result = $rooms->create($room, $members);
        
        Phake::verify($this->httpClient, Phake::times(1))->post('rooms', $this->isType('array'));
        Phake::verify($this->response, Phake::times(1))->json();
        Phake::verify($members, Phake::times(1))->getAdminIds();
        Phake::verify($members, Phake::times(1))->getMemberIds();
        Phake::verify($members, Phake::times(1))->getReadonlyIds();
            
        $this->assertInstanceOf(Room::class, $result);
    }

    /**
     * @test
     */
    public function チャットルームを更新することができる()
    {
        $room = Phake::mock(Room::class);
        $room->roomId = 1;
        $room->name = "hoge";
        
        Phake::when($this->httpClient)
            ->put(['rooms/{id}',['id' => 1]],$this->isType('array'))
            ->thenReturn([]);
        
        Phake::when($room)
            ->toArray()
            ->thenReturn([]);
        
        $rooms = new Rooms($this->httpClient);
        $rooms->update($room);
        
        Phake::verify($this->httpClient,Phake::times(1))->put(['rooms/{id}',['id' => 1]],$this->isType('array'));
        Phake::verify($room,Phake::times(1))->toArray();
    }

    /**
     * @test
     */
    public function チャットルームを削除することができる()
    {
        $room = new Room();
        $room->roomId = 1;
        
        $rooms = new Rooms($this->httpClient);
        $rooms->remove($room, Rooms::ACTION_TYPE_LEAVE);
        
        Phake::verify($this->httpClient,Phake::times(1))
            ->delete(
                ['rooms/{id}',['id' => 1]],
                ['query' =>['action_type' => Rooms::ACTION_TYPE_LEAVE]]
            );
        
    }

    /**
     * @test
     */
    public function メンバーAPI用のオブジェクトを取得する()
    {
        $rooms = new Rooms($this->httpClient);
        $members = $rooms->members(1);
        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * @test
     */
    public function メッセージAPI用のオブジェクトを取得する()
    {
        $rooms = new Rooms($this->httpClient);
        $messages = $rooms->messages(1);
        $this->assertInstanceOf(Messages::class, $messages);
    }

    /**
     * @test
     */
    public function タスクAPI用のオブジェクトを取得する()
    {
        $rooms = new Rooms($this->httpClient);
        $tasks = $rooms->tasks(1);
        $this->assertInstanceOf(Tasks::class, $tasks);
    }    

    /**
     * @test
     */
    public function ファイルAPI用のオブジェクトを取得することができる()
    {
        $rooms = new Rooms($this->httpClient);
        $files = $rooms->files(1);
        $this->assertInstanceOf(Files::class, $files);
    }
    
}