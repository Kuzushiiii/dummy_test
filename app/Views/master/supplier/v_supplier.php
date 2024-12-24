<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card-header dflex align-center justify-end">
        <button class="btn btn-primary dflex align-center"
            onclick="return modalForm('Add Supplier', 'modal-lg', '<?= base_url('supplier/forms') ?>')">
            <i class="bx bx-plus-circle margin-r-2"></i>
            <span class="fw-normal fs-7">Add New</span>
        </button>
        <button class="btn btn-success dflex align-center margin-l-2" onclick="downloadexcel()">
            <i class="bx bx-bookmarks margin-r-2"></i>
            <span class="fw-normal fs-7">Export Excel</span>
        </button>
        <button class="btn btn-warning dflex align-center margin-l-2" onclick="downloadpdf()">
            <i class="bx bx-printer margin-r-2"></i>
            <span class="fw-normal fs-7">Export Pdf</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive margin-t-14p">
            <table class="table table-bordered table-responsive-lg table-master fs-7 w-100" id="dataTable">
                <thead>
                    <tr>
                        <td class="tableheader">No</td>
                        <td class="tableheader">Supplier</td>
                        <td class="tableheader">Address</td>
                        <td class="tableheader">Telephone</td>
                        <td class="tableheader">Email</td>
                        <td class="tableheader">File Path</td>
                        <td class="tableheader">Actions</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function downloadexcel() {
        window.location.href = '<?= base_url('supplier/export') ?>';
    }

    function downloadpdf() {
        window.location.href = '<?= base_url('supplier/pdf') ?>';
    }
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= base_url('supplier/table') ?>",
                    "type": "POST",
                    "dataSrc": function (json) {
                        // Ensure the data structure is correct
                        if (!json.data) {
                            console.error('Data structure is incorrect:', json);
                            return [];
                        }
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "suppliername" },
                    { "data": "address" },
                    { "data": "phone" },
                    { "data": "email" },
                    { "data": "filepath", "render": function(data, type, row) {
                        return '<a href="' + data + '" target="_blank">' + data + '</a>';
                    }},
                    { "data": "actions" }
                ]
            });
        }
    });

    function modalForm(title, size, url) {
        $('#modalForm .modal-title').text(title);
        $('#modalForm .modal-dialog').addClass(size);
        $('#modalForm .modal-body').load(url);
        $('#modalForm').modal('show');
    }

    function modalDelete(title, data) {
        if (confirm('Are you sure you want to delete ' + title + '?')) {
            $.ajax({
                type: 'POST',
                url: data.link + '/' + data.id,
                dataType: 'json',
                success: function(response) {
                    if (response.success == 1) {
                        alert(response.message);
                        $('#dataTable').DataTable().ajax.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        }
    }
</script>
<?= $this->include('template/v_footer') ?>