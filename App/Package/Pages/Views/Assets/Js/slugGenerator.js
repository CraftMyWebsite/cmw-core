<!-- Autofill slug input with title (auto format for slugs) -->
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('title').addEventListener('input', function () {
        const title = this.value;
        const slug = title.toLowerCase().normalize('NFD').replace(/'/g, '-').replace(/[\u0300-\u036f]/g, '').replace(/ /g, '-').replace(/[^a-z0-9-]/g, '')
        document.getElementById('slug').value = slug;
    });
});