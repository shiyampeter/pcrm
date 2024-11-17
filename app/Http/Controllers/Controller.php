<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Models\WorkCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $error;


    protected $sort_column = 'created_at';
    protected $sort_order = 'DESC';
    protected $pageNumber = 1;
    protected $perPage = 15;

    public function storeImage($image, $folder, $quality = 90)
    {
        $imageName = time() . '.' . "webp";
        $img = Image::make($image)->orientate()->encode('webp', 100);
        $path = $folder . '/' . $imageName;
        Storage::disk('public')->put($path, (string) $img);
        return $path;
    }
    public function storeDocument($document, $folder)
    {
        $documentName = time() . '.' . $document->getClientOriginalExtension();
        $path = $folder . '/' . $documentName;
        Storage::disk('public')->put($path, (string) file_get_contents($document));
        return $path;
    }

    public function returnError($errors = false, $message = 'Error', $code = 400, $statuscode = '')
    {
        return response([
            'success' => false,
            'message' => $message,
            'statuscode' => $statuscode,
            'error' => $errors
        ], $code);
    }
    public function returnSuccess($data, $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public function createActivityLog($module, $moduleId, $doneBy, $oldValue, $newValue, $message)
    {
        ActivityLog::create([
            'module' => $module,
            'moduleId' => $moduleId,
            'doneBy' => $doneBy,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'message' => $message
        ]);
    }

    public function sendSms($phone, $message)
    {
        $url = 'https://1message.com/apis/api/sendmessage';
        $data = [
            'SecurityKey' => 'M7QDZXGKW6VTFXW2RH',
            'Messages' => [
                [
                    'MobileNos' => $phone,
                    'Message' => "Dear Alumni,Your One-Time Password (OTP) for verifying your alumni registration is $message. This code is valid for 10 minutes - Amalorpavam HSS",
                    'UniqueID' => 'UNIQUE_ID'
                ]
            ],
            'SenderID' => 'AHSSpy'
        ];
        $headers = [
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }


    public function essential(Request $request)
    {
        $type = $request->get('type');
        if ($type == 'workCategory') {
            $workCategory = WorkCategory::where('work_del_status', 0)->select('work_id as value', 'work_name as label')->get();
            return $this->returnSuccess($workCategory, 'Work Category Retrived Successfully');
        } else {
            return $this->returnError('Type Not Found');
        }
    }
}
