@extends('layouts/default')

{{-- Page title --}}
@section('title')
     {{ trans('general.import') }}
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
        <div class="box">
          <div class="box-body">

                <div class="col-md-3">
                <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-info fileinput-button">
                        <i class="fa fa-plus icon-white"></i>
                        <span>Select Import File...</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileupload" type="file" name="files[]" data-url="{{ config('app.url') }}/api/hardware/import" accept="text/csv">
                    </span>
                </div>
                <div class="col-md-9" id="progress-container" style="visibility: hidden; padding-bottom: 20px;">
                <!-- The global progress bar -->
                <div class="col-md-11">
                    <div id="progress" class="progress progress-striped active" style="margin-top: 8px;">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
                            <span id="progress-bar-text">0% Complete</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="pull-right progress-checkmark" style="display: none;">
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-md-12">



                        <table class="table table-striped" id="upload-table">
                            <thead>
                                <th>File</th>
                                <th>Created</th>
                                <th>Size</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                <tr>
                                    <td>{{ $file['filename'] }}</td>
                                    <td>{{ date("M d, Y g:i A", $file['modified']) }} </td>
                                    <td>{{ $file['filesize'] }}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="import/process/{{ $file['filename'] }}">
                                            <i class="fa fa-spinner process"></i> Process</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
        </div>
        @if (session()->has('import_errors'))
        <div class="errors-table">
        <div class="alert alert-warning">
            <strong>Warning</strong> {{trans('admin/hardware/message.import.errorDetail')}}
        </div>
        <table class="table table-striped table-bordered" id="errors-table">
            <thead>
                <th>Asset</th>
                <th>Errors</th>
            </thead>
            <tbody>
                @foreach (session('import_errors') as $asset => $itemErrors)
                <tr>
                    <td> {{ $asset }}</td>
                    <td>
                    @foreach ($itemErrors as $field => $values )
                            <b>{{ $field }}:</b>
                              @foreach( $values as $errorString)
                                      <span>{{$errorString[0]}} </span>
                              @endforeach
                              <br />
                    @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@section('moar_scripts')

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/jquery.fileupload.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/lib/jquery.fileupload-ui.css') }}">


        <script>
        $(function () {
            //binds to onchange event of your input field
            var uploadedFileSize = 0;
            $('#fileupload').bind('change', function() {
              uploadedFileSize = this.files[0].size;
              $('#progress-container').css('visibility', 'visible');
            });

            $('.process').bind('click', function() {
              $('.process').addClass('fa-spin');
            });

            $('#fileupload').fileupload({
                //maxChunkSize: 100000,
                dataType: 'json',
                formData: {_token: '{{ csrf_token() }}'},
                progress: function (e, data) {
                    //var overallProgress = $('#fileupload').fileupload('progress');
                    //var activeUploads = $('#fileupload').fileupload('active');
                    var progress = parseInt((data.loaded / uploadedFileSize) * 100, 10);
                    $('.progress-bar').addClass('progress-bar-warning').css('width',progress + '%');
                    $('#progress-bar-text').html(progress + '%');
                    //console.dir(overallProgress);
                },

                done: function (e, data) {
                    console.dir(data);

                    // We use this instead of the fail option, since our API
                    // returns a 200 OK status which always shows as "success"

                    if (data && data.jqXHR.responseJSON && data.jqXHR.responseJSON.error) {
                        $('#progress-bar-text').html(data.jqXHR.responseJSON.error);
                        $('.progress-bar').removeClass('progress-bar-warning').addClass('progress-bar-danger').css('width','100%');
                        $('.progress-checkmark').fadeIn('fast').html('<i class="fa fa-times fa-3x icon-white" style="color: #d9534f"></i>');
                        //console.log(data.jqXHR.responseJSON.error);
                    } else {
                        $('.progress-bar').removeClass('progress-bar-warning').removeClass('progress-bar-danger').addClass('progress-bar-success').css('width','100%');
                        $('.progress-checkmark').fadeIn('fast');
                        $('#progress-container').delay(950).css('visibility', 'visible');
                        $('.progress-bar-text').html('Finished!');
                        $('.progress-checkmark').fadeIn('fast').html('<i class="fa fa-check fa-3x icon-white" style="color: green"></i>');
                        $.each(data.result.files, function (index, file) {
                            $('<tr><td>' + file.name + '</td><td>Just now</td><td>' + file.filesize + '</td><td><a class="btn btn-info btn-sm" href="import/process/' + file.name + '"><i class="fa fa-spinner process"></i> Process</a></td></tr>').prependTo("#upload-table > tbody");
                            //$('<tr><td>').text(file.name).appendTo(document.body);
                        });
                    }
                    $('#progress').removeClass('active');


                }
            });
        });
        </script>
@stop

@stop
