<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\Customer;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;


class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->paginate();
        return view('imports.index', compact('customers'));
    }

    public function store(FileRequest $request)
    {
        $file = $request->file('file_csv')->store('csv');
        $hash_file = hash_file('md5', Storage::disk('local')->path($file));

        $isUploaded = ImportLog::where('file_name', $hash_file)->count();

        if ($isUploaded) {
            return redirect()->route('import.index')->with('success', 'File uploadted !');
        }
        $importlog = ImportLog::create([
            'user_id' => auth()->id(),
            'file_name' => $hash_file,
        ]);
        $customers = (new FastExcel)->import(Storage::disk('local')->path($file), function  ($line) use ($importlog){
            return [
                'first_name' => $line['First Name'],
                'last_name' => $line['Last Name'],
                'email' => $line['Email'],
                'phone' => $line['Phone'],
                'import_log_id' => $importlog->id,
            ];
        });
        Storage::disk('local')->delete($file);
        $errors = [];
        foreach ($customers as $key => $customer) {
            $message = '';
            $validator = Validator::make($customer, [
                "first_name" => 'required|string',
                "last_name" => 'required|string',
                "email" => "required|string",
                "phone" => "required|string"
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->count() > 1) {
                    foreach ($validator->errors()->all() as $item) {
                        $message .= $item;
                    }
                }else {
                    $message = $validator->messages()->first();
                }
                $errors[$key+2] = 'Line '.($key+2).': '. $message;
            }
        }
        if (!$errors) {
            $customer_chunk = array_chunk($customers->toArray(), 10000);

            DB::beginTransaction();

            try {
                foreach ($customer_chunk as $customer)
                {
                    Customer::insert($customer);
                }

                DB::commit();
                return redirect()->route('import.index')->with('success', 'Import successfully !');

            } catch (\Exception $e) {
                DB::rollback();
            }
        } else {

            return redirect()->route('import.index')->with(compact('errors'));
        }

    }

    public function truncate()
    {
        Customer::truncate();

        return redirect()->route('import.index')->with('success', 'Delete all customers successfully !');
    }
}
