<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UploadFileServices;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;

class UploadController extends Controller
{
     /**
     * Handles the file upload
     *
     * @param Request $request
     *
     */
    public function upload(Request $request) {

        $upload = new UploadFileServices();
        $save = $upload->saveupload($request);
        
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $upload->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }


}
