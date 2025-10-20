function showForm(formId) {
    document.querySelectorAll(".card-box").forEach(form =>form.classList.remove("active"));
    document.getElementById(formId).classList.add("active")
}