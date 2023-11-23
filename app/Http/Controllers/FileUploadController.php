<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('file-upload');
    }


    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');
        $originalName = $request->header('X-Original-Name');
        $chunkIndex = $request->header('X-Chunk-Index');
        $totalChunks = $request->header('X-Total-Chunks');

        $tempPath = 'chunks/' . $originalName;

        Storage::disk('local')->put($tempPath . '_' . $chunkIndex, file_get_contents($file));

        if ($this->allChunksUploaded($tempPath, $totalChunks)) {
            $this->reassembleFile($tempPath, $originalName, $totalChunks);
        }

        return [
            'status' => true,
        ];
    }

    private function allChunksUploaded($tempPath, $totalChunks): bool
    {
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!Storage::disk('local')->exists($tempPath . '_' . $i)) {
                return false;
            }
        }
        return true;
    }

    private function reassembleFile($tempPath, $originalName, $totalChunks): void
    {
        $finalPath = 'uploads/' . $originalName;
        $fileHandle = fopen(Storage::disk('local')->path($finalPath), 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunk = Storage::disk('local')->get($tempPath . '_' . $i);
            fwrite($fileHandle, $chunk);
        }

        fclose($fileHandle);

        for ($i = 0; $i < $totalChunks; $i++) {
            Storage::disk('local')->delete($tempPath . '_' . $i);
        }
    }

}
