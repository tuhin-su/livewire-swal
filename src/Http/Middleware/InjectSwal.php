<?php

namespace LaravelGenericSwal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectSwal
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Skip injection for Ajax, JSON, Livewire updates, or non-HTML responses
        if ($request->ajax() || $request->wantsJson() || $request->headers->has('X-Livewire')) {
            return $response;
        }

        // Only inject for successful 200 OK responses
        if (method_exists($response, 'status') && $response->status() !== 200) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type');
        if ($contentType && strpos($contentType, 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();
        
        // Find the last closing body tag
        $pos = strrpos($content, '</body>');
        if ($pos !== false) {
            $scripts = $this->getScripts();
            $content = substr($content, 0, $pos) . $scripts . substr($content, $pos);
            $response->setContent($content);
        }

        return $response;
    }

    protected function getScripts(): string
    {
        $swalCdn = '';
        $cdnUrl = config('laravel-generic-swal.swal_cdn', 'https://cdn.jsdelivr.net/npm/sweetalert2@11');
        
        if ($cdnUrl) {
            $swalCdn = '<script src="' . e($cdnUrl) . '"></script>' . "\n";
        }
        
        $jsPath = __DIR__ . '/../../resources/js/swal.js';
        $ourJs = file_exists($jsPath) ? file_get_contents($jsPath) : '';
        
        return "\n<!-- Laravel Generic Swal Helpers -->\n" . 
               $swalCdn . 
               "<script>\n" . $ourJs . "\n</script>\n";
    }
}
