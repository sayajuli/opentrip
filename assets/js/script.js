// 1. Fungsi Format Rupiah Global
function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

// 2. Event Listener Otomatis untuk input dengan class "input-rupiah"
document.addEventListener('DOMContentLoaded', function() {
    const inputRupiah = document.querySelectorAll('.input-rupiah');
    
    inputRupiah.forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            input.value = formatRupiah(this.value);
        });
    });
});