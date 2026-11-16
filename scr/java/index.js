function activar(btn) {
  document.querySelectorAll('.btnm').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function activar2(btn) {
  document.querySelectorAll('.btns').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}