<?php

namespace System\Http\Controllers\Api\V1\Uploads;

use System\Handlers\FileUploadsHandler;
use System\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $option = $request->input('option', config('uploads.paths.default'));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileUploadsHandler = new FileUploadsHandler();
            $file_path = $fileUploadsHandler->upload($file, $option, ["png", "jpg", "gif"]);
            if ($file_path) {
                return Response()->json(
                    [
                        'success' => true,
                        'msg' => '图片上传成功',
                        //'path' => $file_path,
                        'file_path' => asset_url($file_path),
                    ]
                );
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => '只能上传 png | jpg | gif格式的图片'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'msg' => '图片文件不存在'
            ]);
        }
    }
}
