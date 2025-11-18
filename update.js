function confirmUpdate(form) {
    const qty = form.perubahan.value.trim();
    const aksi = form.aksi.value;

    if (!qty || qty <= 0) {
        alert("Please enter a valid quantity (must be greater than 0).");
        form.perubahan.focus();
        return false;
    }

    return true;
}


document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector("input[name='search']");
    const categorySelect = document.querySelector("select[name='filter']");
    const rows = document.querySelectorAll(".stock-table tbody tr");

    function liveFilter() {
        const search = searchInput.value.toLowerCase();
        const filter = categorySelect.value;

        rows.forEach(row => {
            const name = row.children[0].textContent.toLowerCase();
            const category = row.children[1].textContent;

            const matchName = name.includes(search);
            const matchCategory = filter === "" || filter === category;

            if (matchName && matchCategory) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    searchInput.addEventListener("input", liveFilter);

    categorySelect.addEventListener("change", liveFilter);
});