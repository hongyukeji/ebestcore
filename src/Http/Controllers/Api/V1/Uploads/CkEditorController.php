<?php

namespace System\Http\Controllers\Api\V1\Uploads;

use System\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CkEditorController extends Controller
{
    public function index(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|image|max:10240',
        ], [
            'upload.required' => ':attribute 不能为空',
        ], [
            'upload' => '上传文件',
        ]);

        // 验证前
        /*$validator->after(function ($validator) {
            if ($this->somethingElseIsInvalid()) {
                $validator->errors()->add('field', 'Something is wrong with this field!');
            }
        });*/

        // 验证失败跳转
        if ($validator->fails()) {
            //return json(["uploaded" => 0, "error" => ["message" => "文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）"]]);
            return response()->json(["uploaded" => 0, "error" => ["message" => "文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）"]], 422);
        }

        $option = $request->input('option', 'product');
        $filePath = str_finish(config("uploads.paths.{$option}.image", config('uploads.paths.default')), '/') . date('Y/m/d');

        $file = $request->file('upload');
        if ($file->isValid()) {

            // 获取文件相关信息
            // $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            // $type = $file->getClientMimeType();     // image/jpeg

            $uuid = uuid();   // session_create_id() / uniqid()
            $fileName = str_finish($filePath, '/') . $uuid . '.' . $ext;
            $bool = Storage::disk()->put($fileName, file_get_contents($realPath));
            if ($bool) {
                //$callback = $_REQUEST["ckCsrfToken"];
                return response()->json(["uploaded" => 1, "fileName" => "{$uuid}", "url" => Storage::disk()->url($fileName)], 200);
            } else {
                return response()->json(["uploaded" => 0, "error" => ["message" => "上传失败"]], 422);
            }
        } else {
            return response()->json(["uploaded" => 0, "error" => ["message" => "文件不存在"]], 422);
        }
    }

    public function image_old()
    {
        $extensions = array("jpg", "bmp", "gif", "png");
        $uploadFilename = $_FILES['upload']['name'];
        $extension = pathInfo($uploadFilename, PATHINFO_EXTENSION);
        if (in_array($extension, $extensions)) {
            $uploadPath = str_replace("\\", '/', public_path()) . "/uploads/";
            // session_create_id
            $uuid = str_replace('.', '', uniqid("", TRUE)) . "." . $extension;
            $desname = $uploadPath . $uuid;
            $previewname = '/uploads/' . $uuid;
            $tag = move_uploaded_file($_FILES['upload']['tmp_name'], $desname);
            $callback = $_REQUEST["ckCsrfToken"];
            //echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'" . $previewname . "','');</script>";
            return json(["uploaded" => 1, "fileName" => "{$uuid}", "url" => assets($previewname)]);
        } else {
            //echo "<font color=\"red\"size=\"2\">*文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）</font>";
            return json(["uploaded" => 0, "error" => ["message" => "文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）"]]);
        }
    }
}
