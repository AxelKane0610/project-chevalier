



<div class="d-flex justify-content-end sticky common-pagination">
    {{ $certificates->withQueryString()->links('pagination::bootstrap-5') }}
</div>


@if($certificates->isEmpty())

    <div class="border rounded-4 p-5 bg-light text-center mt-4">

        <i class="bi bi-folder2-open display-5 text-primary"></i>

        <h6 class="mt-3">
            No Certificates
        </h6>

        <p class="text-muted mb-0">
            Uploaded files will appear here.
        </p>

    </div>

@else

    <table class="table table-hover align-middle mt-4">
        <i class="ti-link me-2" ></i> Certificates
        <tbody>

        @foreach($certificates as $certificate)

            <tr>

                <td style="width:80%">
                    <i class="bi bi-file-earmark me-2"></i>
                    {{ $certificate->name }}
                </td>

                <td class="text-end justify-content-center" style="width:20%">

                    <a
                        href="{{ asset('attachments/'.$certificate->file_path) }}"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary">

                        <i class="ti-eye"></i>

                    </a>

                    <a
                        href="{{ asset('attachments/'.$certificate->file_path) }}"
                        download="{{ $certificate->name }}"
                        class="btn btn-sm btn-outline-secondary">

                        <i class="ti-download"></i>

                    </a>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

@endif


