<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Request as RepairRequest;
use Illuminate\Http\Request;

class RequestController extends Controller{
    public function index(){
        $requests = RepairRequest::where('user_id', auth()->id())->get();
        return view('request.index', compact('requests'));
    }
    public function create(){
        $employees = Employee::all();
        return view('request.create', compact('employees'));
    }
    public function store(Request $request){
        $validatedData = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'employee' => 'required|exists:employees,id',
//            'file' => 'nullable|file|mimes:jpg, jpeg, png, pdf',
        ]);

//        dd($request->all());
        $repairRequest = new RepairRequest($validatedData);
        $repairRequest->user_id = auth()->id();
        $repairRequest->employee_id = $request->employee;
        $repairRequest->save();
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $repairRequest->file = $filePath;
        }

        return redirect()->route('student.personal')->with('successType', 'request_created')
            ->with('success', 'Запрос создан!');
    }

    public function show($id)
    {
        $request = RepairRequest::with('employee')->findOrFail($id);

        return view('request.show', compact('request'));
    }
        public function edit($id){
    //        dd($repairRequest);
            $employees = Employee::all();
            $repairRequest = RepairRequest::findOrFail($id);
            return view('request.edit', compact('repairRequest', 'employees'));
        }

    public function update(Request $request, RepairRequest $repairRequest)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $repairRequest->update($request->all());

        return redirect()->route('student.personal')
            ->with('successType', 'request_updated')
            ->with('success', 'Запрос обновлен!');
    }
    public function destroy(RepairRequest $repairRequest){
        $repairRequest->delete();
        return redirect()->route('request.index')->with('success', 'RepairRequest has been deleted');
    }
}
