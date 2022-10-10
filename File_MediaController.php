<?php

namespace App\Http\Controllers\Media;

use App\Enums\ContentTypeEnum;
use App\Helpers\StorageHelpers;
use App\Http\Controllers\Controller;
use File;
use Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{

    /**
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function index(string $path)
    {
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    /**
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function getDummyImage(string $filename)
    {
        $path = Storage::disk('local')->path("../../../resources/images/dummy/$filename");

        return $this->index($path);
    }


    /**
     * @param $image
     * @param $sparkId
     * @return mixed
     */
    public static function storeSparkImage($image, $sparkId)
    {
        $path = Auth::id().'/sparks/'.$sparkId.'/';
        Storage::disk('local')->put($path, $image);

        return $image->hashName();
    }

    /**
     * @param $image
     * @return mixed
     */
    public static function storeMomentImage($image)
    {
        $path = Auth::id() . '/moments/';
        Storage::disk('local')->put($path, $image);

        return $image->hashName();
    }

    /**
     * @param $image
     * @param $chatId
     * @return mixed
     */
    public static function storeChatIcon($image, $chatId)
    {
        $path = Auth::id() . '/chat_icon/'.$chatId.'/';
        Storage::disk('local')->put($path, $image);

        return $image->hashName();
    }

    /**
     * @param $image
     * @param $chatId
     * @return mixed
     */
    public static function storeChatImages($image, $chatId)
    {
        $path = Auth::id().'/chats/'.$chatId.'/';
        Storage::disk('local')->put($path, $image);

        return $image->hashName();
    }

    /**
     * @param UploadedFile $image
     * @param $type
     * @return string
     */
    public static function storeProfileImages($image, $type)
    {
        $name = StorageHelpers::generateFileName($image);
        $path = 'files/' . Auth::id() . '/profile/';
        $image->storeAs($path, $name);

        return $name;
    }

    /**
     * @param $filename
     * @param $type
     * @param string $ext
     * @return string
     */
    public static function storeProfileImageFromString($filename, $type, $ext = 'jpg')
    {
        $randNum = rand(100, 9999);
        $newName = md5(rand(100, 9999).time())."_$randNum.$ext";

        $path = Auth::id() . '/profile/';

        if ($type == 'profile_image') {
            $resized_image = Image::make($filename)->stream('jpg', 10);
            Storage::disk('local')->put($path.$newName, $resized_image);
        } else {
            Storage::disk('local')->put($path.$newName, $filename);
        }

        return $newName;
    }

    /**
     * @param string $imageName
     * @param string $type
     * @return bool
     */
    public static function deleteProfileImages($imageName, $type)
    {
        $pathToFile = 'files/' . Auth::id() . '/profile/' . $imageName;
        $exists = Storage::disk('local')->exists($pathToFile);

        if ($exists) {
            Storage::delete($pathToFile);

            return true;
        }

        return false;
    }

    public static function storeSparkThumbImage($image, $sparkId)
    {
        $image = StorageHelpers::createThumbnail($image, 60, 60);

        $path = Auth::id().'/sparks/'.$sparkId.'/thumb/';
        Storage::disk('local')->put($path, $image);

        return $image->hashName();
    }

    public static function storeMomentThumb($icon)
    {
        $img = StorageHelpers::createThumbnail($icon, 60, 60);
        $name = StorageHelpers::generateFileName($icon);

        $path = Auth::id() . '/moments/thumb/' . $name;
        Storage::disk('local')->put($path, $img);

        return $name;
    }
}
