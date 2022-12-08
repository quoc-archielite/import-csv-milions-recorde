@props(['action'])

<form action="{{ $action }}" class="inline-block" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file_csv" class="form-control">
    <button class="btn btn-primary mt-3">Create </button>
</form>
