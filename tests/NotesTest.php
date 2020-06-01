<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleHttp;

final class NotesTest extends TestCase
{
    public function testUnauthenticated()
    {
        global $argv;

        $client = new GuzzleHttp(['base_uri' => $argv[2], 'headers' => ['Accept' => 'application/json']]);

        try {
            $client->get('/api/notes');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    public function testEmptyNotes()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $response = $client->get('/api/notes');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('notes', $data);
        $this->assertEmpty($data->notes);
    }

    public function testWrongParameterForCreation()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);

        try {
            $client->post('/api/notes');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testCreateANoteWithUser1()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $request = [
            'content' => "Nouvelle note de l'utilisateur 1"
        ];
        $response = $client->post('/api/notes', ['form_params' => $request]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('note', $data);
        $this->assertObjectHasAttribute('created_at', $data->note);
        $this->assertObjectHasAttribute('updated_at', $data->note);
        $this->assertObjectHasAttribute('content', $data->note);
        $this->assertEquals($request['content'], $data->note->content);
        $this->assertObjectHasAttribute('user_id', $data->note);
        $this->assertEquals(1, $data->note->user_id);
    }

    public function testGetNotes()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $response = $client->get('/api/notes');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('notes', $data);
        $this->assertNotEmpty($data->notes);
        $this->assertObjectHasAttribute('created_at', $data->notes[0]);
        $this->assertObjectHasAttribute('updated_at', $data->notes[0]);
        $this->assertObjectHasAttribute('content', $data->notes[0]);
        $this->assertObjectHasAttribute('user_id', $data->notes[0]);
        $this->assertEquals(1, $data->notes[0]->user_id);
        $this->assertObjectHasAttribute('id', $data->notes[0]);
    }

    public function testCreateANoteWithUser2()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token2");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $request = [
            'content' => "Nouvelle note de l'utilisateur 2"
        ];
        $response = $client->post('/api/notes', ['form_params' => $request]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('note', $data);
        $this->assertObjectHasAttribute('created_at', $data->note);
        $this->assertObjectHasAttribute('updated_at', $data->note);
        $this->assertObjectHasAttribute('content', $data->note);
        $this->assertEquals($request['content'], $data->note->content);
        $this->assertObjectHasAttribute('user_id', $data->note);
        $this->assertEquals(2, $data->note->user_id);
    }

    public function testGetNoteThatDoesntExist()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);

        try {
            $client->get('/api/notes/3456789');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    public function testGetAuthorizedNote()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);

        $response = $client->get('/api/notes/1');

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('note', $data);
        $this->assertNotEmpty($data->note);
        $this->assertObjectHasAttribute('created_at', $data->note);
        $this->assertObjectHasAttribute('updated_at', $data->note);
        $this->assertObjectHasAttribute('content', $data->note);
        $this->assertObjectHasAttribute('user_id', $data->note);
        $this->assertEquals(1, $data->note->user_id);
        $this->assertObjectHasAttribute('id', $data->note);
    }

    public function testGetUnauthorizedNote()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);

        try {
            $client->get('/api/notes/2');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(403, $e->getCode());
        }
    }

    public function testWrongParameterForUpdate()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);

        try {
            $client->post('/api/notes');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testUpdateANoteThatDoesNotExist()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $request = [
            'content' => "Note mise à jour de l'utilisateur 1"
        ];
        try {
            $client->put('/api/notes/9999', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    public function testUpdateAUnauthorizedNote()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $request = [
            'content' => "Note mise à jour de l'utilisateur 1"
        ];
        try {
            $client->put('/api/notes/2', ['form_params' => $request]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(403, $e->getCode());
        }
    }

    public function testUpdateANoteWithUser1()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $request = [
            'content' => "Note mise à jour de l'utilisateur 1"
        ];
        $response =  $client->put('/api/notes/1', ['form_params' => $request]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertObjectHasAttribute('note', $data);
        $this->assertNotEmpty($data->note);
        $this->assertObjectHasAttribute('created_at', $data->note);
        $this->assertObjectHasAttribute('updated_at', $data->note);
        $this->assertObjectHasAttribute('content', $data->note);
        $this->assertEquals($request['content'], $data->note->content);
        $this->assertObjectHasAttribute('user_id', $data->note);
        $this->assertEquals(1, $data->note->user_id);
        $this->assertObjectHasAttribute('id', $data->note);
    }

    public function testDeleteANoteThatDoesNotExist()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        try {
            $client->delete('/api/notes/9999');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    public function testDeleteAnUnauthorizedNote()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        try {
            $client->delete('/api/notes/2');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(403, $e->getCode());
        }
    }

    public function testDeleteANoteWithUser1()
    {
        global $argv;

        $token = file_get_contents(__DIR__ . "/../data/token1");

        $client = new GuzzleHttp([
            'base_uri' => $argv[2],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ]
        ]);
        $response =  $client->delete('/api/notes/1');

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody());
        $this->assertEmpty($data);
    }
}
