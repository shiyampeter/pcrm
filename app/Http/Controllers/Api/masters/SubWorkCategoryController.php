<?php

namespace App\Http\Controllers\Api\masters;

use App\Http\Controllers\Controller;
use App\Models\SubWorkCategory;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class SubWorkCategoryController extends Controller
{
    public function createSubWorkCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->validatedData($request);
            $subWorkCategory = SubWorkCategory::create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['subWorkCategory' => $subWorkCategory], 'SubWorkCategory created successfully');

    }




    public function updateSubWorkCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->route('id');
            $data = $this->validatedData($request, $id);
            $subWorkCategory = SubWorkCategory::where(['sub_work_id' => $id, 'sub_work_del_status' => 0])->first();
            if (!$subWorkCategory) {
                return $this->returnError('Data not found');
            } else {
                $subWorkCategory->update($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['subWorkCategory' => $subWorkCategory], 'SubWorkCategory updated successfully');
    }


    public function deleteSubWorkCategory(Request $request)
    {
        try {
            $id = $request->route('id');
            $subWorkCategory = SubWorkCategory::where('sub_work_id', $id)->first();
            if ($subWorkCategory) {
                $subWorkCategory->sub_work_del_status = 1;
                $subWorkCategory->save();
            } else {
                return $this->returnError('Data not found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess([], 'Deleted successfully');
    }

    public function listSubWorkCategory(Request $request)
    {
        try {
            $search = "";
            if ($request->filled('page')) {
                $this->pageNumber = $request->get('page');
            }
            if ($request->filled('per_page')) {
                $this->perPage = $request->get('per_page');
            }
            if ($request->filled('sort_column')) {
                $this->sort_column = $request->get('sort_column');
            }
            if ($request->filled('sort_order')) {
                $this->sort_order = $request->get('sort_order');
            }
            if ($request->filled('search')) {
                $search = $request->get('search');
            }


            $subWorkCategory = SubWorkCategory::where(['sub_work_del_status' => 0])->where(function ($query) use ($search) {
                $columns = \Schema::getColumnListing('sub_work_category');
                for ($i = 1; $i < count($columns); $i++) {
                    $query->orWhere($columns[$i], 'like', '%' . $search . '%');
                }
            })
                ->orderby('sub_work_id', $this->sort_order)
                ->paginate($this->perPage, ['*'], 'page', $this->pageNumber);
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($subWorkCategory, 'SubWorkCategory List');
    }

    public function viewSubWorkCategory(Request $request)
    {
        try {
            $id = $request->route('id');
            $subWorkCategory = SubWorkCategory::where(['sub_work_id' => $id, 'sub_work_del_status' => 0])->first();
            if (!$subWorkCategory) {
                return $this->returnError('SubWorkCategory Not Found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($subWorkCategory, 'SubWorkCategory');
    }


    private function validatedData(Request $request, $id = null): array
    {
        $validator = Validator::make($request->all(), [

            'sub_work_cate_id' => 'required|integer|exists:work_category,work_id,work_del_status,0',
            'sub_work_cate_name' => 'required|string|max:255|unique:sub_work_category,sub_work_cate_name,' . $id . ',sub_work_id,sub_work_del_status,0',
            'sub_work_work_price' => 'required|array',
            'sub_work_online_price' => 'required|array',
            'sub_work_expense_price' => 'required|array',
            'sub_work_discount_price' => 'required|array',
            'sub_work_incentive_price' => 'required|array',
            'sub_work_validity_status' => 'required',
            'sub_work_validity' => 'nullable|array',
            'sub_work_validity_date' => 'nullable|date',
            'sub_work_alert_status' => 'required',
            'sub_work_alert_days_type' => 'nullable',
            'sub_work_alert_days' => 'nullable',
            'status_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $this->error_code = "400";
            $this->error = $validator->errors();
            throw new \Exception($validator->errors());
        }
        return $this->getData($request, $id);
    }

    private function getData(Request $request, $id): array
    {

        return [
            'sub_work_cate_id' => $request->sub_work_cate_id,
            'sub_work_cate_name' => $request->sub_work_cate_name,
            'sub_work_work_price' => $request->sub_work_work_price,
            'sub_work_online_price' => $request->sub_work_online_price,
            'sub_work_expense_price' => $request->sub_work_expense_price,
            'sub_work_discount_price' => $request->sub_work_discount_price,
            'sub_work_incentive_price' => $request->sub_work_incentive_price,
            'sub_work_validity_status' => $request->sub_work_validity_status,
            'sub_work_validity' => $request->sub_work_validity,
            'sub_work_validity_date' => $request->sub_work_validity_date,
            'sub_work_alert_status' => $request->sub_work_alert_status,
            'sub_work_alert_days_type' => $request->sub_work_alert_days_type,
            'sub_work_alert_days' => $request->sub_work_alert_days,
            'status_id' => $request->status_id,
        ];
    }

}

