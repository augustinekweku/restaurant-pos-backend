<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    public function getCompanies(Request $request)
    {

        try {
            //code...
            $res = Company::get();
            return $this->sendResponse($res, 'Companies retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function createCompany(Request $request)
    {
        //validate request
        $this->validate($request, [
            'company_name' => 'required',
            'about' => 'required',
        ]);
        try {
            //code...
            $res =  Company::create([
                'company_name' => $request->company_name,
                'about' => $request->about,
            ]);
            return $this->sendResponse($res, 'Company created successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function editCompany(Request $request)
    {
        //validate request
        $this->validate($request, [
            "id" => "required",
            'company_name' => 'required',
            'about' => 'required',
        ]);
        try {
            //code...
            $res =  Company::where('id', $request->id)->update([
                'company_name' => $request->company_name,
                'about' => $request->about,
            ]);
            return $this->sendResponse($res, 'Company updated successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function deleteCompany(Request $request, $id)
    {
        //validate request
        $this->validate($request, [
            'id' => 'required'
        ]);
        try {
            //code...
            if (!$id) return $this->sendError('Error', 'Company id is required', 500);
            $res = Company::where('id', $id)->delete();
            return $this->sendResponse($res, 'Company deleted successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
}