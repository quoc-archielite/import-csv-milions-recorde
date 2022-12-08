<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import CSV') }}
            <x-popup-delete route="{{ route('truncate') }}"/>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-form-import action="{{ route('import.store')}}" />
                    <button  data-modal-toggle="popup-modal" class="btn btn-danger inline-block float-end">Delete ALl</button>
                </div>
                <div class="pr-6 pl-6">

                    @if ($errors->has('csv_file'))
                        <x-alert-error message="{{ $errors->first('csv_file') }}"/>
                    @endif

                    @if (session('success'))
                       <x-alert-success message="{{session('success')}}"/>
                    @endif

                    @if ($errors)
                        @foreach($errors as $error)
                            <x-alert-error message="{{ $error }}"/>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="table table-striped">
                        <tr>
                            <th>STT</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                        @forelse ($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->id }}</strong></td>
                                <td>{{ $customer->first_name }}</td>
                                <td>{{ $customer->last_name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->email }}</td>
                            </tr>
                        @empty
                            <td colspan="2"></td>
                            <td>{{ __("No data") }}</td>
                            <td colspan="2"></td>
                        @endforelse
                    </table>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
