function activar(btn) {
  document.querySelectorAll('.btnm').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}