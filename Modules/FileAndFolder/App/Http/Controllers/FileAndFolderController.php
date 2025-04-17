<?php

namespace Modules\FileAndFolder\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\FileAndFolder\App\Models\DeletedFilesAndFolders;
use Modules\Shared\FileUpload\FileUploadServiceInterface;
use ZipArchive;

class FileAndFolderController extends Controller
{
    public function __construct(protected FileUploadServiceInterface $fileUploadService) {}

    public function showFolderContents($folder = null)
    {
        $folder = urldecode($folder);

        if (is_null($folder) || $folder == "") {
            $folders = array_filter(Storage::disk('private')->directories(), function ($dir) {
                return basename($dir) !== 'temp' && basename($dir !== 'trash');
            });

            return view('folder.index', compact('folders'));
        }

        $folderPath = $folder;

        $filesInFolder = Storage::disk('private')->files($folderPath);
        $subfolders = Storage::disk('private')->directories($folderPath);

        return view('folder.index', compact('filesInFolder', 'subfolders', 'folder'));
    }

    public function download($type, $path)
    {
        if ($type === 'folder') {
            $zipFileName = basename($path) . '.zip';
            $zipPath = Storage::disk('private')->path("/temp/zip/{$zipFileName}");

            if (!Storage::disk('private')->exists("/temp/zip")) {
                Storage::disk('private')->makeDirectory("/temp/zip");
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                $files = Storage::disk('private')->allFiles($path);
                foreach ($files as $file) {
                    $relativeName = substr($file, strlen($path) + 1);
                    $zip->addFile(Storage::disk('private')->path($file), $relativeName);
                }
                $zip->close();
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        if ($type === 'file' && Storage::disk('private')->exists($path)) {
            return response()->download(Storage::disk('private')->path($path));
        }

        abort(404);
    }

    public function showFile(Request $request, $filename)
    {
        // $allowedDomains = ['https://yourdomain.com', 'https://other-accepteddomain.com'];
        // $referer = $request->headers->get('referer');

        // if (!$referer || !collect($allowedDomains)->contains(fn($domain) => str_starts_with($referer, $domain))) {
        //     abort(403, 'Unauthorized domain.');
        // }

        if (!Storage::disk('private')->exists($filename)) {
            abort(404);
        }

        $filePath = Storage::disk('private')->path($filename);

        return response()->file($filePath);
    }

    public function destroy($type, $path)
    {
        if (!Storage::disk('private')->exists($path)) {
            dd("file not exist");
        }

        $fileOrFolderPath = '/';
        if (basename($path) !== $path) {
            $fileOrFolderPath = dirname($path);
        }
        $fileOrFolderName = now()->timestamp . '_' . basename($path);

        DeletedFilesAndFolders::create([
            'type' => $type,
            'path' => $fileOrFolderPath,
            'name' => now()->timestamp . '_' . basename($path)
        ]);

        if ($type == 'folder') {
            $allFiles = Storage::disk('private')->allFiles($path);
            $allDirectories = Storage::disk('private')->allDirectories($path);
            $destination  = "/trash/" . $fileOrFolderName;

            foreach ($allDirectories as $dir) {
                $relativeDir = str_replace($path, $destination, $dir);
                Storage::disk('private')->makeDirectory($relativeDir);
            }

            foreach ($allFiles as $file) {
                $relativePath = str_replace($path, $destination, $file);
                Storage::disk('private')->move($file, $relativePath);
            }

            Storage::disk('private')->deleteDirectory($path);
        } elseif ($type == 'file') {
            $destination = "/trash/" . $fileOrFolderName;

            // Move the file
            Storage::disk('private')->move($path, $destination);
        }

        return redirect()->back();
    }
    public function restoreFile($type, $path)
    {
        $segments = explode('/', $path);
        $segmentCount = count($segments);

        // Get base trash folder name (e.g., 1744883063_Anime)
        $trashFolder = $segments[1];
        $deletedItem = DeletedFilesAndFolders::where('name', $trashFolder)->first();

        if (!$deletedItem) {
            return back()->withErrors('Original deleted folder not found.');
        }

        $originalBasePath = rtrim($deletedItem->path, '/');
        $cleanFolderName = preg_replace('/^\d+_/', '', $deletedItem->name); // e.g., "Anime"

        if ($type === 'file') {
            $innerPath = implode('/', array_slice($segments, 2));
            $cleanedInnerPath = preg_replace_callback('/(^|\/)(\d+_)/', fn($m) => $m[1], $innerPath);
            $restoredPath = $originalBasePath . '/' . $cleanFolderName . '/' . $cleanedInnerPath;

            $restoreDir = dirname($restoredPath);
            if (!Storage::disk('private')->exists($restoreDir)) {
                Storage::disk('private')->makeDirectory($restoreDir);
            }

            Storage::disk('private')->move($path, $restoredPath);
        }

        if ($type === 'folder') {
            $allDirs = Storage::disk('private')->allDirectories($path);
            $allFiles = Storage::disk('private')->allFiles($path);

            foreach ($allDirs as $dir) {
                // Remove base trash folder and clean timestamps
                $relative = substr($dir, strlen($path) + 1);
                $cleaned = preg_replace_callback('/(^|\/)(\d+_)/', fn($m) => $m[1], $relative);
                $finalPath = $originalBasePath . '/' . $cleanFolderName . '/' . $cleaned;

                Storage::disk('private')->makeDirectory($finalPath);
            }

            foreach ($allFiles as $file) {
                $fileSegments = explode('/', $file);
                array_shift($fileSegments);
                $fileSegments[0] = preg_replace('/^\d+_/', '', $fileSegments[0]);
                $finalPath = $originalBasePath . '/' . implode('/', $fileSegments);

                $finalDir = dirname($finalPath);
                if (!Storage::disk('private')->exists($finalDir)) {
                    Storage::disk('private')->makeDirectory($finalDir);
                }

                Storage::disk('private')->move($file, $finalPath);
            }

            Storage::disk('private')->deleteDirectory($path);
        }

        // Only delete DB record if root folder was restored
        if ($segmentCount === 2) {
            $deletedItem->delete();
        }

        return back()->with('success', 'Restored successfully!');
    }

    public function uploadFile(Request $request)
    {
        if (is_array($request->file)) {
            $url = $this->fileUploadService->multiUpload($request->uploadPath, $request->file, ['add_unix_time' => false]);
        } else {
            $url = $this->fileUploadService->singleUpload($request->uploadPath, $request->file, ['add_unix_time' => false]);
        }
        return response()->json(['url' => $url]);
    }
}
