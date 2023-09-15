<?php
/**
 * This file is part of the ProxmoxVE PHP API wrapper library (unofficial).
 *
 * @copyright 2014 César Muñoz <zzantares@gmail.com>
 * @license http://opensource.org/licenses/MIT The MIT License.
 */

namespace ProxmoxVE;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as TTestCase;

/**
 * @author César Muñoz <zzantares@gmail.com>
 */
class TestCase extends TTestCase {
    protected function getMockProxmox($method = null, $return = null) {
        if ($method) {
            $proxmox = $this->getMockBuilder('ProxmoxVE\Proxmox')
                ->setMethods(array($method))
                ->disableOriginalConstructor()
                ->getMock();

            $proxmox->expects($this->any())
                ->method($method)
                ->will($this->returnValue($return));

        } else {
            $proxmox = $this->getMockBuilder('ProxmoxVE\Proxmox')
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $proxmox;
    }


    protected function getProxmox($response) {
        $httpClient = $this->getMockHttpClient(true, $response);

        $credentials = [
            'hostname' => 'my.proxmox.tld',
            'username' => 'root',
            'password' => 'toor',
        ];

        return new Proxmox($credentials, null, $httpClient);
    }


    protected function getMockHttpClient($successfulLogin, $response = null) {
        if ($successfulLogin) {
            $stream = \GuzzleHttp\Psr7\Utils::streamFor('{"data":{"CSRFPreventionToken":"csrf","ticket":"ticket","username":"random"}}');
            $login  = new Response(202, ['Content-Length' => 0], $stream);
        } else {
            $login = new Response(400, ['Content-Length' => 0]);
        }

        $responseStream = \GuzzleHttp\Psr7\Utils::streamFor("{$response}");

        $mock = new MockHandler([
            $login,
            new Response(202, ['Content-Length' => 0], $responseStream),
        ]);


        $handler    = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        return $httpClient;
    }
}