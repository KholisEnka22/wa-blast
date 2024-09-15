document.addEventListener("DOMContentLoaded", function () {
    const dataTable = document.getElementById("data-table");
    const searchInput = document.getElementById("search");
    const tbody = dataTable.getElementsByTagName("tbody")[0];
    const rows = Array.from(tbody.getElementsByTagName("tr")); // Simpan semua baris asli

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        let noDataFound = true;

        rows.forEach(function (row) {
            const columns = row.getElementsByTagName("td");
            let shouldHide = true;

            for (let j = 0; j < columns.length; j++) {
                const column = columns[j];
                if (column) {
                    const text = column.textContent || column.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        shouldHide = false;
                        noDataFound = false;
                        break;
                    }
                }
            }

            // Sembunyikan atau tampilkan baris berdasarkan hasil pencarian
            row.style.display = shouldHide ? "none" : "";
        });

        // Jika tidak ada data yang cocok
        if (noDataFound) {
            // Cek apakah sudah ada pesan "Tidak ada data"
            let noDataRow = document.getElementById("no-data-row");
            if (!noDataRow) {
                noDataRow = document.createElement("tr");
                noDataRow.setAttribute("id", "no-data-row");

                const noDataCell = document.createElement("td");
                noDataCell.setAttribute("colspan", "5"); // Sesuaikan jumlah kolom
                noDataCell.textContent = "Tidak ada data";
                noDataCell.style.textAlign = "center";
                noDataCell.style.backgroundColor = "#f5f5f9";

                noDataRow.appendChild(noDataCell);
                tbody.appendChild(noDataRow); // Tampilkan pesan
            }
        } else {
            // Hapus pesan "Tidak ada data" jika ada data yang cocok
            const noDataRow = document.getElementById("no-data-row");
            if (noDataRow) {
                noDataRow.remove();
            }
        }
    });
});
