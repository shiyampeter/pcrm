<?php

namespace App\Http\Controllers\Api\masters;

use App\Http\Controllers\Controller;
use App\Models\SubWorkCategoryStatus;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class StatusController extends Controller
{
    public function createSubWorkCategoryStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->validatedData($request);
            $status = SubWorkCategoryStatus::create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['status' => $status], 'SubWorkCategoryStatus created successfully');

    }




    public function updateSubWorkCategoryStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->route('id');
            $data = $this->validatedData($request, $id);
            $status = SubWorkCategoryStatus::where(['id' => $id, 'status_deleted' => 0])->first();
            if (!$status) {
                return $this->returnError('Data not found');
            } else {
                $status->update($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['status' => $status], 'SubWorkCategoryStatus updated successfully');
    }


    public function deleteSubWorkCategoryStatus(Request $request)
    {
        try {
            $id = $request->route('id');
            $status = SubWorkCategoryStatus::where(['id' => $id, 'status_deleted' => 0])->first();
            if ($status) {
                $status->status_deleted = 1;
                $status->save();
            } else {
                return $this->returnError('Data not found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess([], 'Deleted successfully');
    }

    public function listSubWorkCategoryStatus(Request $request)
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


            $status = SubWorkCategoryStatus::where(['status_deleted' => 0])->where(function ($query) use ($search) {
                $columns = \Schema::getColumnListing('sub_work_category_status');
                for ($i = 1; $i < count($columns); $i++) {
                    $query->orWhere($columns[$i], 'like', '%' . $search . '%');
                }
            })
                ->orderby('id', $this->sort_order)
                ->paginate($this->perPage, ['*'], 'page', $this->pageNumber);
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($status, 'SubWorkCategoryStatus List');
    }

    public function viewSubWorkCategoryStatus(Request $request)
    {
        try {
            $id = $request->route('id');
            $status = SubWorkCategoryStatus::where(['id' => $id, 'status_deleted' => 0])->first();
            if (!$status) {
                return $this->returnError('SubWorkCategoryStatus Not Found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($status, 'SubWorkCategoryStatus');
    }


    private function validatedData(Request $request, $id = null): array
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255|unique:sub_work_category_status,status,' . $id . ',id,status_deleted,0',
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
            'status' => $request->status,
        ];
    }

}
