<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Request as RepairRequest;

class RequestController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'employee' => 'required|exists:employees,id',
        ]);

        $repairRequest = new RepairRequest($validatedData);
        $repairRequest->user_id = auth()->id();
        $repairRequest->employee_id = $request->employee;
        $repairRequest->save();

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $repairRequest->file = $filePath;
        }

        return response()->json([
            'message' => 'Запрос создан!',
            'data' => $repairRequest,
        ], 201);
    }

    public function show($id)
    {
        $request = RepairRequest::with('employee')->findOrFail($id);

        return response()->json([
            'data' => $request,
        ], 200);
    }

    public function update(Request $request, RepairRequest $repairRequest)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $repairRequest->update($request->all());

        return response()->json([
            'message' => 'Запрос обновлен!',
            'data' => $repairRequest
        ], 200);
    }

    public function destroy(RepairRequest $repairRequest)
    {
        $repairRequest->delete();

        return response()->json([
            'message' => 'RepairRequest has been deleted'
        ], 200);
    }

    public function edit($id)
    {
        $repairRequest = RepairRequest::with('employee')->findOrFail($id);
        $employees = Employee::all();

        return response()->json([
            'repairRequest' => $repairRequest,
            'employees' => $employees
        ], 200);
    }

    public function index()
    {
        $requests = RepairRequest::where('user_id', auth()->id())->get();

        return response()->json([
            'data' => $requests
        ], 200);
    }

}
