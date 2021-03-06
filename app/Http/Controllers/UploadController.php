<?php

namespace App\Http\Controllers;

use App\Helpers\ImageManager;
use App\Models\Image;
use App\Traits\ApiResponser;
use App\Traits\UploadValidate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UploadController extends Controller
{
    use UploadValidate, ApiResponser;

    public function __construct()
    {
    }

    public function uploadPhoto(Request $request)
    {
        if (is_null($request)) {
            $this->errorResponse("Request not null", Response::HTTP_BAD_REQUEST);
        }

        $featureName = $request->input('key');

        // validator, return if any error
        $this->validateUpload($request);

        $data = ImageManager::upload('value', "$featureName", $request);

        //save to DB
        $image = new Image();
        $image->img_name = $data->name;
        $image->img_url = $data->file;
        $image->img_mime = $data->mime;
        $image->img_size = $data->size;
        $image->feature = $featureName;
        $image->save();

        $imageFullPath = [
            'small' => env('APP_URL', 'http://localhost:8080') . "/storage/images/$featureName/small/$data->file",
            'medium' => env('APP_URL', 'http://localhost:8080') . "/storage/images/$featureName/medium/$data->file",
            'thumbnail' => env('APP_URL', 'http://localhost:8080') . "/storage/images/$featureName/thumbnail/$data->file"
        ];
        $images = [
            'img_id' => $image->id,
            'img_name' => $data->name,
            'img_url' => $data->file,
            'img_mime' => $data->mime,
            'img_size' => $data->size,
            'feature' => $featureName,
            'url' => $imageFullPath,
        ];
        return $this->successResponse($images, 'success');
    }

    public function getImage()
    {
        $img = ImageManager::getImage('1644223931_6200ddbbdcbbf.jpeg', 'image');
        echo env('APP_URL', 'http://localhost:8080') . $img;
    }
}
