<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManageIec;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class IecMiniController extends Controller
{
    public function createManageIec(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->validatedData($request);
            $data['iec_q_added_on'] = time();
            $data['iec_q_added_by'] = auth()->user()->id;
            $data['iec_q_added_ip'] = $request->getClientIp();
            $data['iec_q_work_type'] = 0; //"mini
            $iec_mini = ManageIec::create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['iec_mini' => $iec_mini], 'IEC created successfully');

    }


    public function createManageIecOnline(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->validatedDataOnline($request);
            $data['iec_q_added_on'] = time();
            $data['iec_q_added_by'] = auth()->user()->id;
            $data['iec_q_added_ip'] = $request->getClientIp();
            $data['iec_q_work_type'] = 1; //"mini
            $iec_mini = ManageIec::create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['iec_mini' => $iec_mini], 'IEC created successfully');

    }



    public function updateManageIec(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->route('id');
            $data = $this->validatedData($request, $id);
            $iec = ManageIec::where(['iec_q_id' => $id, 'iec_q_del_status' => 0])->first();
            if (!$iec) {
                return $this->returnError('Data not found');
            } else {
                $data['iec_q_modified_on'] = time();
                $data['iec_q_modified_by'] = auth()->user()->id;
                $data['iec_q_modified_ip'] = $request->getClientIp();
                $iec->update($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['iec' => $iec], 'IEC updated successfully');
    }


    public function updateManageIecOnline(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = $request->route('id');
            $data = $this->validatedDataOnline($request, $id);
            $iec = ManageIec::where(['iec_q_id' => $id, 'iec_q_del_status' => 0])->first();
            if (!$iec) {
                return $this->returnError('Data not found');
            } else {
                $data['iec_q_modified_on'] = time();
                $data['iec_q_modified_by'] = auth()->user()->id;
                $data['iec_q_modified_ip'] = $request->getClientIp();
                $iec->update($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        DB::commit();
        return $this->returnSuccess(['iec' => $iec], 'IEC updated successfully');
    }


    public function deleteManageIec(Request $request)
    {
        try {
            $id = $request->route('id');
            $iec = ManageIec::where(['iec_q_id' => $id, 'iec_q_del_status' => 0])->first();
            if ($iec) {
                $iec->iec_q_del_status = 1;
                $iec->iec_q_deleted_on = time();
                $iec->iec_q_deleted_by = auth()->user()->id;
                $iec->iec_q_deleted_ip = $request->getClientIp();
                $iec->save();
            } else {
                return $this->returnError('Data not found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess([], 'Deleted successfully');
    }

    public function listManageIec(Request $request)
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


            $iec = ManageIec::with('work:work_id,work_name', 'subWork:sub_work_id,sub_work_cate_name')->where(['iec_q_del_status' => 0])
                ->select(
                    'iec_q_id',
                    'iec_q_work',
                    'iec_q_sub_work',
                    'iec_complete',
                    'iec_q_amount',
                    'iec_q_expense',
                    'iec_q_income',
                    'iec_q_office_expense',
                    'iec_paid',
                    'iec_q_mobile',
                    'iec_q_name',
                    'iec_q_discount',
                    'iec_q_discount_amount',
                    'iec_online_payment_gothrough',
                    DB::raw('IF(iec_q_work_type = 0, "Mini", "Online") as iec_q_work_type'),
                )
                ->where(function ($query) use ($search) {
                    $columns = \Schema::getColumnListing('manage_iec');
                    for ($i = 1; $i < count($columns); $i++) {
                        $query->orWhere($columns[$i], 'like', '%' . $search . '%');
                    }
                })
                ->orderby('iec_q_id', $this->sort_order)
                ->paginate($this->perPage, ['*'], 'page', $this->pageNumber);
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($iec, 'ManageIec List');
    }

    public function viewManageIec(Request $request)
    {
        try {
            $id = $request->route('id');
            $iec = ManageIec::where(['iec_q_id' => $id, 'iec_q_del_status' => 0])
                ->select(
                    'iec_q_id',
                    'iec_q_work',
                    'iec_q_sub_work',
                    'iec_complete',
                    'iec_q_mobile',
                    'iec_q_amount',
                    'iec_q_expense',
                    'iec_q_income',
                    'iec_q_office_expense',
                    'iec_paid',
                    'iec_q_name',
                    'iec_q_discount',
                    'iec_q_discount_amount',
                    'iec_online_payment_gothrough',
                    DB::raw('"Mini" as iec_q_work_type'),

                )->first();
            if (!$iec) {
                return $this->returnError('Iec Not Found');
            }
        } catch (\Exception $e) {
            return $this->returnError($this->error ? $this->error : $e->getMessage());
        }
        return $this->returnSuccess($iec, 'ManageIec');
    }


    private function validatedData(Request $request, $id = null): array
    {
        $validator = Validator::make($request->all(), [
            'iec_q_work' => 'required|integer|exists:work_category,work_id,work_del_status,0',
            'iec_q_sub_work' => 'required|integer|exists:sub_work_category,sub_work_id,sub_work_del_status,0',
            'iec_complete' => 'required|integer|in:0,1',
            'iec_q_mobile ' => 'nullable|string',
            'iec_q_amount' => 'required',
            'iec_q_expense' => 'required',
            'iec_q_income' => 'required',
            'iec_paid' => 'required|integer|in:0,1',
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
            'iec_q_work' => $request->iec_q_work,
            'iec_q_sub_work' => $request->iec_q_sub_work,
            'iec_complete' => $request->iec_complete,
            'iec_q_mobile' => $request->iec_q_mobile,
            'iec_q_amount' => $request->iec_q_amount,
            'iec_q_expense' => $request->iec_q_expense,
            'iec_q_income' => $request->iec_q_income,
            'iec_paid' => $request->iec_paid
        ];
    }

    private function validatedDataOnline(Request $request, $id = null): array
    {
        $validator = Validator::make($request->all(), [
            'iec_q_work' => 'required|integer|exists:work_category,work_id,work_del_status,0',
            'iec_q_sub_work' => 'required|integer|exists:sub_work_category,sub_work_id,sub_work_del_status,0',
            'iec_complete' => 'required|integer|in:0,1',
            'iec_q_mobile ' => 'nullable|string',
            'iec_q_amount' => 'required',
            'iec_q_expense' => 'required',
            'iec_q_office_expense' => 'required',
            'iec_q_discount' => 'nullable',
            'iec_q_discount_amount' => 'required',
            'iec_q_name' => 'required',
            'iec_q_income' => 'required',
            'iec_paid' => 'required|integer|in:0,1',
            'iec_online_payment_gothrough' => 'nullable|in:office,customer',
        ]);
        if ($validator->fails()) {
            $this->error_code = "400";
            $this->error = $validator->errors();
            throw new \Exception($validator->errors());
        }
        return $this->getDataOnline($request, $id);
    }

    private function getDataOnline(Request $request, $id): array
    {

        return [
            'iec_q_work' => $request->iec_q_work,
            'iec_q_sub_work' => $request->iec_q_sub_work,
            'iec_complete' => $request->iec_complete,
            'iec_q_mobile' => $request->iec_q_mobile,
            'iec_q_amount' => $request->iec_q_amount,
            'iec_q_expense' => $request->iec_q_expense,
            'iec_q_office_expense' => $request->iec_q_office_expense,
            'iec_q_income' => $request->iec_q_income,
            'iec_paid' => $request->iec_paid,
            'iec_q_name' => $request->iec_q_name,
            'iec_q_discount' => $request->iec_q_discount,
            'iec_q_discount_amount' => $request->iec_q_discount_amount,
            'iec_online_payment_gothrough' => $request->iec_online_payment_gothrough
        ];
    }

}
