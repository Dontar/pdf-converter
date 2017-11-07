<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use React\EventLoop\Factory;
use React\Stream;
use React\Socket\Server as SocketServer;
use React\Http\MiddlewareRunner;
use React\Http\Middleware\RequestBodyParserMiddleware;
use React\Http\Response;
use React\Http\Server;

require __DIR__ . '/vendor/autoload.php';

$loop = Factory::create();

$server = new Server(new MiddlewareRunner([
    new RequestBodyParserMiddleware(),
    function (ServerRequestInterface $request) use ($loop) {
        error_log(sprintf("%s %s\n", $request->getMethod(), $request->getUri()));
        $pdf = new PdfConverter();
        switch ($request->getUri()->getPath()) {
            case '/pdf':
                error_log(var_export($request->getUploadedFiles(), true));
                $files = $request->getUploadedFiles();
                if (count($files) > 0) {
                    $ff = function ($files) {
                        foreach ($files as $file) {
                            $file->moveTo($f = tempnam(sys_get_temp_dir(), "word_"));
                            yield $pdf->convertFile($f);
                        }
                    };
                    $body = new Stream($pdf->mergePdfs($ff()));
                } else {
                    $body = new Stream(
                        $pdf->convertStream(
                            StreamWrapper::getResource($request->getBody())
                        )
                    );
                }
                break;
            default:
                $body = "";
                break;
        }

        $hdr = array(
            "Content-Type" => "application/pdf",
            "Content-Disposition" => "inline; filename=pdf_document.pdf",
            'Cache-Control' => ' private, max-age=0, must-revalidate',
            'Pragma' => 'public'
        );
        return new Response( 200, $hdr, $body );
    }
]));

$socket = new SocketServer('0.0.0.0:3000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
