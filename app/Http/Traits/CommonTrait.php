<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
// use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;

trait CommonTrait
{
    public function getUrlImage($url)
    {
        return $url ? env('AWS_URL') . '/' . $url : null;
    }

    public function uploadImageAws($file, $module = 'users')
    {
        // upload s3
        $extension = $file->getClientOriginalExtension();

        $img = $file;
        // create url path
        $extension = $img->getClientOriginalExtension();
        $uid = uniqid("16");
        $name = 'images/' . $module . '/' . $uid . '.' . $extension;

        //Create thumb
        $imgThumb = Image::make($img->getRealPath());
        $imgThumb->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
        });
        $name_thumb = 'images/' . $module . '/' . $uid . '_thumb.' . $extension;

        // upload s3
        Storage::disk('s3')->put($name, file_get_contents($img), 'public');
        Storage::disk('s3')->put($name_thumb, $imgThumb->stream(), 'public');

        return [
            'url' => $name,
            'url_thumb' => $name_thumb
        ];
    }

    public function uploadFileAws($file, $module = 'users')
    {
        // upload s3

        $extension = $file->getClientOriginalExtension();

        $img = $file;
        // create url path
        $extension = $img->getClientOriginalExtension();
        $uid = uniqid("16");
        $name = 'files/' . $module . '/' . $uid . '.' . $extension;

        // upload s3
        Storage::disk('s3')->put($name, file_get_contents($img), 'public');

        return [
            'url' => $name
        ];
    }

    public function dateNow()
    {
        return date('Y-m-d H:i:s');
    }

    public function diffMinutes($start, $end)
    {
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $diferencia = $start->diff($end);
        $totalMinutos = $diferencia->h * 60 + $diferencia->i;
        return $totalMinutos;
    }

    public function getTypeMultimedia($extension)
    {
        $extension = strtolower($extension);
        $type = 'other';
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
            $type = 'image';
        } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp'])) {
            $type = 'video';
        } elseif (in_array($extension, ['mp3', 'wav', 'wma', 'ogg', 'aac'])) {
            $type = 'audio';
        } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            $type = 'document';
        }
        return $type;
    }

    public function deleteFileAws($url)
    {
        Storage::disk('s3')->delete($url);
    }

    public function validateWeightFile($file, $maxSize = 5)
    {
        $maxSize = $maxSize * 1024 * 1024;
        if ($maxSize > $file->getSize() ) {
            return 1;
        }
        return 0;
    }

    public function getFileSize($file)
    {
        return $file->getSize() / 1000;
    }

    public function getBytesToMegaBytes($bytes)
    {
        if (!$bytes) {
            return "0 MB";
        }
        return round($bytes / 1000, 2) . " MB";
    }

    public function generateCode($school_id, $table, $prefix = 'A00')
    {
        $correlative = DB::table($table)->where('school_id', $school_id)->orderBy('id', 'desc')->first();
        $correlative = $correlative->correlative ?? 0;
        $number = $this->zeroFill($correlative + 1, 8);
        return [
            'correlative' => $correlative + 1,
            'code' => $prefix . $school_id . '-' . $number
        ];
    }

    public function zeroFill($valor, $long = 0)
    {
        return str_pad($valor, $long, '0', STR_PAD_LEFT);
    }
}
