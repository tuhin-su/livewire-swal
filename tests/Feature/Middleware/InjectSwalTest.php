<?php

namespace LaravelSwal\Tests\Feature\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LaravelSwal\Http\Middleware\InjectSwal;
use LaravelSwal\Tests\TestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InjectSwalTest extends TestCase
{
    protected function getMiddleware()
    {
        return new InjectSwal();
    }

    public function test_it_injects_scripts_into_html_response()
    {
        $middleware = $this->getMiddleware();
        $request = Request::create('/test', 'GET');
        
        $html = '<html><body><h1>Test</h1></body></html>';
        $response = new Response($html);
        $response->header('Content-Type', 'text/html');

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $content = $result->getContent();
        
        $this->assertStringContainsString('sweetalert2@11', $content);
        $this->assertStringContainsString('</body>', $content);
        // ensure script is before body tag
        $this->assertTrue(strpos($content, 'sweetalert2@11') < strpos($content, '</body>'));
    }

    public function test_it_does_not_inject_into_json_response()
    {
        $middleware = $this->getMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept', 'application/json');
        
        $response = new Response('{"message": "test"}');
        $response->header('Content-Type', 'application/json');

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertStringNotContainsString('sweetalert2@11', $result->getContent());
    }

    public function test_it_does_not_inject_for_livewire_requests()
    {
        $middleware = $this->getMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-Livewire', 'true');
        
        $html = '<html><body><h1>Test</h1></body></html>';
        $response = new Response($html);
        $response->header('Content-Type', 'text/html');

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertStringNotContainsString('sweetalert2@11', $result->getContent());
    }

    public function test_it_does_not_inject_on_non_200_responses()
    {
        $middleware = $this->getMiddleware();
        $request = Request::create('/test', 'GET');
        
        $html = '<html><body><h1>Not Found</h1></body></html>';
        $response = new Response($html, 404);
        $response->header('Content-Type', 'text/html');

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertStringNotContainsString('sweetalert2@11', $result->getContent());
    }
}
