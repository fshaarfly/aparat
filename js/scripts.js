document.addEventListener("DOMContentLoaded", function () {
  AOS.init({
    duration: 1000, // Durasi animasi dalam milidetik
    easing: "ease-in-out", // Efek animasi
    once: true, // Jalankan animasi hanya sekali
  });
});
const sidebar = document.getElementById("sidebar");
const sidebarToggle = document.getElementById("sidebarToggle");
const sidebarClose = document.getElementById("sidebarClose");

// Buka sidebar
sidebarToggle.addEventListener("click", () => {
  sidebar.classList.add("show");
});

// Tutup sidebar
sidebarClose.addEventListener("click", () => {
  sidebar.classList.remove("show");
});

// Tutup sidebar jika pengguna mengklik di luar sidebar
document.addEventListener("click", (e) => {
  if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
    sidebar.classList.remove("show");
  }
});
