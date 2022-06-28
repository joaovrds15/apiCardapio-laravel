<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiFilesController extends Controller
{
   public function uploadServer(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'file' => 'image',
        ]);

        if ($validator->fails()) {
            die(response()->json(['message' => 'Invalid file format'], 400));
        }

        if($request->file('file')){
            $file = $request->file('file');
            $filename = (string) Str::uuid(). $file->getClientOriginalName();
            return $file-> move(public_path('images/'), $filename);
            
        }
   }
/**
*   @OA\Post(
*       path="/api/file/upload",
*       summary="Endpoint for uploading an image",
*       tags={"File"},
*       @OA\RequestBody(
*           required=true,
*           @OA\MediaType(
*           mediaType="multipart/form-data",
*           @OA\Schema(
*               @OA\Property(
*                   description="Image of item in the menu",
*                   property="file",
*                   type="file",
*               ),
*               required={"file"}
*               )
*            )
*           ),
*       @OA\Response(response="200", description="Uploaded",),
*       @OA\Response(response="400", description="Image invalid"),
*       @OA\Response(response="405", description="The only method available is Post"),
* )
*/

    public function uploadStorage(Request $request)
    {
        $googleConfigFile = file_get_contents(config_path('googlecloud.json'));
        $storage = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true)
        ]);
        $storageBucketName = config('googlecloud.storage_bucket');
        $bucket = $storage->bucket($storageBucketName);
        $filePath = $this->uploadServer($request);
        $fileSource = fopen($filePath, 'r');
        $newFolderName = 'images';
        $googleCloudStoragePath = $newFolderName.'/'.basename($filePath);
        $bucket->upload($fileSource, [
            'name' => $googleCloudStoragePath
        ]);

        return response()->json([
            "google_storage_url" => 'https://storage.cloud.google.com/'.$storageBucketName.'/'.$googleCloudStoragePath,
        ]);
        
    }
    
    public static function deleteStorage($ImageURL){
        if (!empty($ImageURL)){
            $googleConfigFile = file_get_contents(config_path('googlecloud.json'));
            $storage = new StorageClient([
                'keyFile' => json_decode($googleConfigFile, true)
            ]);
            $imageName = basename($ImageURL);
            $storageBucketName = config('googlecloud.storage_bucket');
            $newFolderName = 'images';
            $bucket = $storage->bucket($storageBucketName);
            $object = $bucket->object($newFolderName.'/'.$imageName);
            $object->delete();
        }
        
    }

}
