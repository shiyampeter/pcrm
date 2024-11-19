<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Models\SubWorkCategory;
use App\Models\SubWorkCategoryStatus;
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
        } else if ($type == 'workSubCategory') {
            $subWorkCategory = SubWorkCategory::where(['sub_work_del_status' => 0, 'sub_work_cate_id' => $request->get('work_id')])->select(
                'sub_work_id as value',
                'sub_work_cate_name as label',
                'sub_work_work_price',
                'sub_work_online_price',
                'sub_work_expense_price',
                'sub_work_discount_price',
                'sub_work_incentive_price',
                'sub_work_validity_status',
                'sub_work_validity',
                'sub_work_validity_date',
                'sub_work_alert_status',
                'sub_work_alert_days_type',
                'sub_work_alert_days',
                'status_id',
            )->get();
            return $this->returnSuccess($subWorkCategory, 'Sub Work Category Retrived Successfully');
        } else if ($type == 'workSubCategoryStatus') {
            $subWorkCategoryStatus = SubWorkCategoryStatus::where('status_deleted', 0)->select('id as value', 'status as label')->get();
            return $this->returnSuccess($subWorkCategoryStatus, 'Sub Work Category Status Retrived Successfully');
        } else {
            return $this->returnError('Type Not Found');
        }
    }
}
