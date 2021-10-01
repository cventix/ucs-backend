<?php

namespace App\Http\Controllers;

use App\Traits\ResponseGenerator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use ResponseGenerator;

    const DEFAULT_PAGE_SIZE = 10;
    const PER_PAGE = 'per_page';

    /**
     * @param Request $request BaseRequest.
     *
     * @return integer|mixed
     */
    protected function getPageSize(Request $request): int
    {
        $pageSize = self::DEFAULT_PAGE_SIZE;
        if ($request->has(self::PER_PAGE) && !empty($request->get(self::PER_PAGE))) {
            $pageSize = (int)$request->get(self::PER_PAGE);
        }

        return $pageSize;
    }

    /**
     * @param UploadedFile $file File.
     * @param string $directory Directory.
     *
     * @return string
     * @throws \Exception
     */
    protected static function uploadFile(UploadedFile $file, string $directory): string
    {
        $hashFile = sha1_file($file);
        $extension = $file->getClientOriginalExtension();
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $fileName = $hashFile . '.' . $extension;
        $path = $directory . '/' . $fileName;
        $move = Storage::disk('public')->put($path, file_get_contents($file));
        if (!$move) {
            throw new \Exception('System Can Not Save The File!');
        }

        return Storage::url($path);
    }
}
