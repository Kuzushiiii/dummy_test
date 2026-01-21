<form id="importexcel" style="padding-inline: 0px;">
    <div class="row">
        <div>
            <div class="form-group">
                <label class="required">Excel File</label>
                <input type="file" name="excelfile" id="excelfile" accept=".xlsx, .xls" class="form-input" style="padding: 8px;pointer-events: unset !important;">
            </div>
        </div>
    </div>
    <div id="loading-alltrans" class="hiding">
        <h4>
            <i class='bx bx-loader-circle bx-spin text-info'></i> Processing <span class="text-primary" id="totalsent">0</span> / <span id="alltotals" class="text-primary">100</span>
        </h4>
    </div>
    <div class="modal-footer dflex" style="justify-content: space-between !important;">
        <button style="margin: 0 !important;" class="btn btn-info dflex align-center justify-center" type="button" onclick="downloadTemplate()">
            <i class="bx bx-download margin-r-2"></i>
            <span class="fw-normal fs-7">Template</span>
        </button>
        <div style="margin-left: 0 !important; margin-right: 0 !important;" class="dflex">
            <button class="btn btn-warning dflex button-import align-center margin-r-2" type="button" onclick="close_modal('modaldetail')">
                <i class="bx bx-x margin-r-2"></i>
                <span class="fw-normal fs-7">Cancel</span>
            </button>
            <button class="btn btn-primary dflex button-import align-center" type="submit">
                <i class="bx bx-check margin-r-2"></i>
                <span class="fw-normal fs-7">Process</span>
            </button>
        </div>
    </div>
</form>
<script>
    function downloadTemplate() {
        var url = '<?= base_url('public/downloadable/Template User.xlsx') ?>';
        window.location.href = url;
    }

    async function getFiles(e) {
        e = e || window.event;
        let file = e.target.files[0];
        let data = await file.arrayBuffer();
        let wb = XLSX.read(data);
        let ws = wb.Sheets[wb.SheetNames[0]];
        let last_key = Object.keys(ws)
        last_key.shift();
        last_key.pop();
        last_key = last_key.filter(key => key !== '!margins');
        let getlen = last_key[last_key.length - 1];
        getlen = getlen.replace(/[^0-9\.]/g, '');
        let arr = [];
        let offset = 500;
        let keys = 0;
        $("#alltotals").text(formatRupiah(getlen - 1));
        for (let o = 1; o <= getlen * 1; o++) {
            if (o === 1) continue;
            if (ws['A' + o] && ws['A' + o].v !== undefined) {
                keys++;
                arr.push([
                    (ws['A' + o] && ws['A' + o].v !== undefined) ? ws['A' + o]['v'] : '',
                    (ws['B' + o] && ws['B' + o].v !== undefined) ? ws['B' + o]['v'] : '',
                    (ws['C' + o] && ws['C' + o].v !== undefined) ? ws['C' + o]['v'] : '',
                    (ws['D' + o] && ws['D' + o].v !== undefined) ? ws['D' + o]['v'] : '',
                    (ws['E' + o] && ws['E' + o].v !== undefined) ? ws['E' + o]['v'] : '',
                ]);
            }
            if (keys == offset) {
                keys = 0;
                sendData(arr);
                arr = [];
            }
        }
        sendData(arr, 't');
    }

    $(document).ready(function() {
        $("#importexcel").on('submit', function(e) {
            e.preventDefault();
            $(".button-import").attr('disabled', 'disabled');
            $("#excelfile").attr('onchange', 'getFiles(event)');
            $("#btn-close-modaldetail").addClass('hiding')
            $("#excelfile").trigger('change');
            $("#loading-alltrans").removeClass('hiding');
            $('#excelfile').attr('disabled', 'disabled')
            return false;
        })
    })

    undfhproduct = 0

    async function sendData(arr, isfinish = 'f') {
        await sleep(2000);
        let textproses = $("#totalsent").text();
        $("#totalsent").text(formatRupiah(exp_number(textproses) + arr.length));
        $.ajax({
            url: '<?= base_url('user/importExcel') ?>',
            type: 'post',
            dataType: 'json',
            data: {
                datas: JSON.stringify(arr),
                <?= csrf_token() ?>: decrypter($("#csrf_token").val())
            },
            async: false,
            success: function(res) {
                $('#excelfile').removeAttr('disabled');
                $("#csrf_token").val(encrypter(res.csrfToken));
                undfhproduct += res.undfhproduct
                if (isfinish == 't') {
                    showNotif("success", "Data updated successfully");
                    if (undfhproduct >= 1) {
                        showNotif("error", `${undfhproduct} user dilewatkan`);
                    }
                    setTimeout(() => {
                        close_modal('modaldetail');
                        tbl.ajax.reload();
                    }, 250);
                }
                $(".button-import").removeAttr('disabled')
                $("#btn-close-modaldetail").removeClass('hiding')
            }
        })
    }
</script>