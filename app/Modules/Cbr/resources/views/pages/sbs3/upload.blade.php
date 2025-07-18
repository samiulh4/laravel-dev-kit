@extends('layouts.admin')

@section('style')
    <!-- Page CSS -->
@endsection

@section('content')
    <div class="row g-6">
        <div class="col-xl-12">
            <!-- File input -->
            <div class="card">
                <h5 class="card-header">Upload Bill/Card Data</h5>
                <div class="card-body">
                    <div id="progress"></div>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="formFileLg" class="form-label">File must be .xlsx or .csv</label>
                            <input class="form-control form-control-lg" id="formFileLg" type="file" name="file" />
                        </div>
                        <button type="submit" class="btn btn-primary float-end">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Page JS -->
    {{-- <script src="{{ asset('assets/admin/js/form-basic-inputs.js') }}"></script> --}}
    <script>
        $(document).ready(function() {


            // Handle form submit
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                let file = $('#formFileLg')[0].files[0];
                if (!file) {
                    alert("Please choose a file.");
                    return;
                }

                const chunkSize = 500 * 1024; // 500 KB
                const totalChunks = Math.ceil(file.size / chunkSize);

                let chunkIndex = 0;

                function uploadNextChunk() {
                    if (chunkIndex >= totalChunks) {
                        console.log("All chunks uploaded.");
                        $('#progress').text("Upload complete!");
                        finalizeUpload();
                        return;
                    }

                    let start = chunkIndex * chunkSize;
                    let end = Math.min(start + chunkSize, file.size);

                    let chunk = file.slice(start, end);

                    let formData = new FormData();
                    formData.append('chunk', chunk);
                    formData.append('index', chunkIndex);
                    formData.append('total', totalChunks);
                    formData.append('file_name', file.name);
                    formData.append('file_size', file.size);
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: "{{ url('admin/cbr/sbs-3/file/chunk/upload-chunk') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function() {
                            chunkIndex++;
                            let percent = Math.floor((chunkIndex / totalChunks) * 100);
                            $('#progress').text(`Uploading... ${percent}%`);
                            uploadNextChunk();
                        },
                        error: function() {
                            alert("Upload failed. Please try again.");
                        }
                    });
                }

                uploadNextChunk();

                function finalizeUpload() {
                    $.post("{{ url('admin/cbr/sbs-3/file/store/finalize-upload') }}", {
                        _token: '{{ csrf_token() }}',
                        file_name: file.name,
                        total_chunks: totalChunks
                    }, function(res) {
                        console.log("Server says: " + res.message);
                    });
                }

                // let formData = new FormData(this);
                // $.ajax({
                //     url: '',
                //     type: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     success: function(response) {
                //         console.log('Upload success:', response);
                //         alert('Upload successful!');
                //     },
                //     error: function(xhr) {
                //         console.error('Upload error:', xhr.responseText);
                //         alert('An error occurred.');
                //     }
                // });
            });

        });
    </script>
@endsection
