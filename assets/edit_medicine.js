document.querySelector("input[name='medicine_name']").focus();

const priceInput = document.querySelector("input[name='price']");
priceInput.addEventListener("input", () => {
    let val = priceInput.value.replace(/[^\d.]/g, "");
    priceInput.value = val ? parseFloat(val).toFixed(2) : "";
});

const stockInput = document.querySelector("input[name='stock']");
stockInput.addEventListener("input", () => {
    if (stockInput.value < 0) {
        alert("Stock cannot be negative!");
        stockInput.value = "";
    }
});

const expiryInput = document.querySelector("input[name='expiry_date']");
expiryInput.addEventListener("change", () => {
    let today = new Date().toISOString().split("T")[0];
    if (expiryInput.value < today) {
        alert("Expiry date must be in the future!");
        expiryInput.value = "";
    }
});

const form = document.getElementById("editMedicineForm");
form.addEventListener("submit", (e) => {
    if (!confirm("Are you sure you want to update this medicine?")) {
        e.preventDefault();
    } else {
        alert("✅ Medicine updated successfully!");
    }
});
