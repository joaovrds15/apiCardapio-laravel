<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiFilesRequest;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Str;

class ApiFilesController extends Controller
{
    public function uploadServer(ApiFilesRequest $request)
    {
        $validated = $request->validated();
        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = (string) Str::uuid().$file->getClientOriginalName();

            return $file->move(public_path('images/'), $filename);
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
     *       @OA\Response(response="405", description="The only method available is Post"),
     *       @OA\Response(response="422", description="Wrong key or file format"),
     * )
     */
    public function uploadStorage(ApiFilesRequest $request)
    {
        $googleConfigFile = file_get_contents(config_path('googlecloud.json'));
        $storage = new StorageClient([
            'keyFile' => json_decode($googleConfigFile, true),
        ]);
        $storageBucketName = config('googlecloud.storage_bucket');
        $bucket = $storage->bucket($storageBucketName);
        $filePath = $this->uploadServer($request);
        $fileSource = fopen($filePath, 'r');
        $newFolderName = 'images';
        $googleCloudStoragePath = $newFolderName.'/'.basename($filePath);
        $bucket->upload($fileSource, [
            'name' => $googleCloudStoragePath,
        ]);

        return response()->json([
            'google_storage_url' => 'https://storage.cloud.google.com/'.$storageBucketName.'/'.$googleCloudStoragePath,
        ]);
    }

    public static function deleteStorage($ImageURL)
    {
        if (! empty($ImageURL)) {
            $googleConfigFile = file_get_contents(config_path('googlecloud.json'));
            $storage = new StorageClient([
                'keyFile' => json_decode($googleConfigFile, true),
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
