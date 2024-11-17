<?php

namespace App\Http\Controllers\Api\masters;

use App\Http\Controllers\Controller;
use App\Models\WorkCategory;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class WorkCategoryController extends Controller
{
    public function createWorkCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->validatedData($request);
            $workCategory = WorkCategory::create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['workCategory' => $workCategory], 'WorkCategory created successfully');

    }




    public function updateWorkCategory(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->route('id');
            $data = $this->validatedData($request, $id);
            $workCategory = WorkCategory::where(['work_id' => $id, 'work_del_status' => 0])->first();
            if (!$workCategory) {
                return $this->returnError('Data not found');
            } else {
                $workCategory->update($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['workCategory' => $workCategory], 'WorkCategory updated successfully');
    }


    public function deleteWorkCategory(Request $request)
    {
        try {
            $id = $request->route('id');
            $workCategory = WorkCategory::where('work_id', $id)->first();
            if ($workCategory) {
                $workCategory->work_del_status = 1;
                $workCategory->save();
            } else {
                return $this->returnError('Data not found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess([], 'Deleted successfully');
    }

    public function listWorkCategory(Request $request)
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


            $workCategory = WorkCategory::where(['work_del_status' => 0])->where(function ($query) use ($search) {
                $columns = \Schema::getColumnListing('work_category');
                for ($i = 1; $i < count($columns); $i++) {
                    $query->orWhere($columns[$i], 'like', '%' . $search . '%');
                }
            })
                ->orderby('work_id', $this->sort_order)
                ->paginate($this->perPage, ['*'], 'page', $this->pageNumber);
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($workCategory, 'WorkCategory List');
    }

    public function viewWorkCategory(Request $request)
    {
        try {
            $id = $request->route('id');
            $workCategory = WorkCategory::where(['work_id' => $id, 'work_del_status' => 0])->first();
            if (!$workCategory) {
                return $this->returnError('WorkCategory Not Found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($workCategory, 'WorkCategory');
    }


    private function validatedData(Request $request, $id = null): array
    {
        $validator = Validator::make($request->all(), [
            'work_name' => 'required|string|max:255|unique:work_category,work_name,' . $id . ',work_id,work_del_status,0',
            'work_type' => 'required|integer|in:0,1',
            'work_tracking_no' => 'nullable|string',
            'work_tracking_website' => 'nullable|string',
            'work_completed' => 'nullable',
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
            'work_name' => $request->work_name,
            'work_type' => $request->work_type,
            'work_tracking_no' => $request->work_tracking_no,
            'work_tracking_website' => $request->work_tracking_website,
            'work_completed' => $request->work_completed
        ];
    }

}
