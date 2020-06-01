<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleHttp;

final class AuthTest extends TestCase
{
    public function testRegisterWithoutParameters()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        try {
            $client->post('/api/register');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testRegisterWithInvalidEmail()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test',
            'email' => 'test',
            'password' => 'azertyuiop'
        ];

        try {
            $client->post('/api/register', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testRegisterWithInvalidPassword()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'azerty'
        ];

        try {
            $client->post('/api/register', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testRegisterUser1()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test 1',
            'email' => 'test1@test.com',
            'password' => 'azertyuiop'
        ];
        $response = $client->post('/api/register', ['form_params' => $request]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertIsObject($data);
        $this->assertObjectHasAttribute('token', $data);
        $this->assertNotEmpty($data->token);

        // Save token for next tests using authentication
        file_put_contents(__DIR__ . "/../data/token1", $data->token);
    }

    public function testRegisterUser2()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test 2',
            'email' => 'test2@test.com',
            'password' => 'azertyuiop'
        ];
        $response = $client->post('/api/register', ['form_params' => $request]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertIsObject($data);
        $this->assertObjectHasAttribute('token', $data);
        $this->assertNotEmpty($data->token);

        // Save token for next tests using authentication
        file_put_contents(__DIR__ . "/../data/token2", $data->token);
    }

    public function testRegisterWithExistingEmail()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test 1',
            'email' => 'test1@test.com',
            'password' => 'azertyuiop'
        ];

        try {
            $client->post('/api/register', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    static public function setUpBeforeClass(): void
    {
        global $argv;

        // Reset database
        $client = new GuzzleHttp(['base_uri' => $argv[2]]);
        $client->delete('/api/reset');
    }

    public function testLoginWithoutParameters()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        try {
            $client->post('/api/login');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testLoginWithInvalidEmail()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test',
            'email' => 'test',
            'password' => 'azertyuiop'
        ];

        try {
            $client->post('/api/login', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testLoginWithInvalidPassword()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'email' => 'test@test.com',
            'password' => 'azerty'
        ];

        try {
            $client->post('/api/login', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testLogin()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'email' => 'test1@test.com',
            'password' => 'azertyuiop'
        ];
        $response = $client->post('/api/login', ['form_params' => $request]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertIsObject($data);
        $this->assertObjectHasAttribute('token', $data);
        $this->assertNotEmpty($data->token);
    }

    public function testLoginWithUserWhoDoesNotExist()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        $request = [
            'name' => 'Test Error',
            'email' => 'test999@test.com',
            'password' => 'azertyuiop'
        ];

        try {
            $client->post('/api/login', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }
}
