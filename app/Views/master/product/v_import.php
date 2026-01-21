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
        var url = '<?= base_url('public/downloadable/Template Product.xlsx') ?>';
        window.location.href = url;
    }

    async function getFiles(e) {
        //untuk ambil file dari input
        e = e || window.event;
        let file = e.target.files[0];
        //membaca file excell ke memory
        let data = await file.arrayBuffer();
        let wb = XLSX.read(data);
        // mengambil sheet pertama/A1
        let ws = wb.Sheets[wb.SheetNames[0]];
        //untuk mencari  baris terakhir excell
        let last_key = Object.keys(ws)
        last_key.shift();
        last_key.pop();
        last_key = last_key.filter(key => key !== '!margins');
        //Ambil cell terakhir "D12" dan diambil angkanya saja 12
        let getlen = last_key[last_key.length - 1];
        getlen = getlen.replace(/[^0-9\.]/g, '');
        // untuk setup variable yang digunakan untuk ngebatch
        let arr = [];
        let offset = 500;
        let keys = 0;
        //untuk set progress data di ui
        $("#alltotals").text(formatRupiah(getlen - 1));
        //loop semua baris excell
        for (let o = 1; o <= getlen * 1; o++) {
            //dengan skip header karena tidak digunakan di back end
            if (o === 1) continue;
            if (ws['A' + o] && ws['A' + o].v !== undefined) {
                keys++;
                //untuk diambil dari kolom a-d
                arr.push([
                    (ws['A' + o] && ws['A' + o].v !== undefined) ? ws['A' + o]['v'] : '',
                    (ws['B' + o] && ws['B' + o].v !== undefined) ? ws['B' + o]['v'] : '',
                    (ws['C' + o] && ws['C' + o].v !== undefined) ? ws['C' + o]['v'] : '',
                    (ws['D' + o] && ws['D' + o].v !== undefined) ? ws['D' + o]['v'] : '',
                ]);
            }
            //jika data sudah 500 data/ yang di setup di variable batch maka dikirim ke server ke function sendData
            if (keys == offset) {
                keys = 0;
                sendData(arr);
                arr = [];
            }
        }
        //kirim sisa datanya t disini digunakan untuk menentukan batch terakhir
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
        //untuk delay
        await sleep(2000);
        //update progress jumlah data yang dikiirm
        let textproses = $("#totalsent").text();
        $("#totalsent").text(formatRupiah(exp_number(textproses) + arr.length));
        // untuk mengirim data ke back end
        $.ajax({
            url: '<?= base_url('product/importExcel') ?>',
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
                //jika batch terakhir sudah selesai maka kirim notif
                if (isfinish == 't') {
                    showNotif("success", "Data updated successfully");
                    if (undfhproduct >= 1) {
                        showNotif("error", `${undfhproduct} product dilewatkan`);
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