// scr/java/auth.js - Lógica compartida login/register/me/logout + protección de páginas
function phpUrl(file){
  // Resuelve /scr/php/file desde cualquier profundidad (ej: /scr/juegos/.../nivel1.html)
  const p = location.pathname;
  const idx = p.indexOf('/scr/');
  if(idx !== -1) return p.slice(0, idx+5) + 'php/' + file; // /scr/php/file
  // fallback si se sirve scr como DocumentRoot (XAMPP htdocs/Lamismisimapagina)
  if(p.includes('/php/')) return file;
  return 'php/' + file;
}
async function api(path, opts = {}) {
  // si path es php/archivo.php -> resolver bien
  const resolved = path.startsWith('php/') ? phpUrl(path.slice(4)) : path;
  const isPhp = resolved.includes('/php/');
  const headers = { ...opts.headers };
  if(isPhp && !headers['Content-Type'] && opts.body) headers['Content-Type']='application/json';
  const res = await fetch(resolved, {
    credentials: 'include',
    headers,
    ...opts
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.error || data.msg || `Error ${res.status}`);
  return data;
}

// --- LOGIN ---
const btnLogin = document.getElementById('btn-login');
if (btnLogin) {
  btnLogin.addEventListener('click', async () => {
    const errBox = document.getElementById('login-error');
    const username = document.getElementById('login-email')?.value.trim() // compat viejo id
                  || document.getElementById('login-username')?.value.trim();
    const password = document.getElementById('login-password')?.value;
    if (errBox) errBox.style.display = 'none';
    if (!username || !password) {
      if (errBox) { errBox.textContent = 'Completá usuario y contraseña'; errBox.style.display='block';}
      return;
    }
    btnLogin.disabled = true; btnLogin.textContent = 'Ingresando...';
    try {
      await api('php/login.php', { method:'POST', body: JSON.stringify({ username, password }) });
      window.location.href = 'inicio.html';
    } catch (e) {
      if (errBox) { errBox.textContent = e.message; errBox.style.display='block';}
      else alert(e.message);
    } finally { btnLogin.disabled=false; btnLogin.textContent='Iniciar sesión'; }
  });
}

// --- REGISTER ---
const btnReg = document.getElementById('btn-register');
if (btnReg) {
  btnReg.addEventListener('click', async () => {
    const errBox = document.getElementById('register-error');
    const username = document.getElementById('register-username')?.value.trim();
    const password = document.getElementById('register-password')?.value;
    const confirm  = document.getElementById('register-confirm')?.value;
    if (errBox) errBox.style.display='none';
    if (!username || !password) {
      if (errBox){errBox.textContent='Completá usuario y contraseña';errBox.style.display='block';}
      return;
    }
    if (confirm !== undefined && password !== confirm) {
      if (errBox){errBox.textContent='Las contraseñas no coinciden';errBox.style.display='block';}
      return;
    }
    btnReg.disabled=true; btnReg.textContent='Creando...';
    try {
      await api('php/register.php', { method:'POST', body: JSON.stringify({ username, password, confirm }) });
      window.location.href = 'inicio.html';
    } catch(e){
      if (errBox){errBox.textContent=e.message;errBox.style.display='block';}
      else alert(e.message);
    } finally {btnReg.disabled=false;btnReg.textContent='Crear cuenta';}
  });
}

// --- ME / proteger páginas ---
async function checkAuth({ redirectIfNoAuth = false, renderCuenta = false } = {}) {
  try {
    const data = await api('php/me.php');
    if (renderCuenta) renderCuentaInfo(data.user);
    return data.user;
  } catch (e) {
    if (redirectIfNoAuth) window.location.href = 'login.html';
    return null;
  }
}

function renderCuentaInfo(user){
  const pct = Math.round((user.xp_ganada / Math.max(1,user.xp_necesaria))*100);
  const set = (sel, val) => { const el=document.querySelector(sel); if(el) el.textContent=val; };
  set('#personal h2', user.username);
  set('#personal h6:nth-of-type(1)', '@'+user.username);
  set('.fpf', user.username.charAt(0).toUpperCase());
  // info bloques
  const bloques = document.querySelectorAll('.info_bloque');
  if(bloques[1]) bloques[1].textContent = 'Nivel ' + user.nivel;
  if(bloques[2]) bloques[2].textContent = pct + '% ('+user.xp_ganada+'/'+user.xp_necesaria+' XP)';
  // agregar barra si existe #info
  const info = document.getElementById('info');
  if(info && !document.getElementById('xp-bar')){
    const bar = document.createElement('div');
    bar.id='xp-bar';
    bar.style.cssText='height:12px;background:#eee;border-radius:999px;overflow:hidden;margin-top:10px';
    bar.innerHTML=`<div style="height:100%;width:${pct}%;background:#4caf50;transition:width .4s"></div>`;
    info.appendChild(bar);
  }
}

// Logout botones
document.querySelectorAll('#btn-logout, .btn-logout').forEach(b=>{
  b.addEventListener('click', async (e)=>{
    e.preventDefault();
    try{ await api('php/logout.php', {method:'POST'}); }catch{}
    window.location.href='login.html';
  });
});

// Helpers de XP para juegos
async function sumarXP(cantidad){
  try{
    const r = await api('php/xp.php', { method:'POST', body: JSON.stringify({ xp: cantidad }) });
    if(r.subio_nivel) alert('¡Subiste a nivel '+r.nivel+'!');
    return r;
  }catch(e){ console.warn('XP no guardada:', e.message); return null; }
}

// Auto-check si estamos en cuenta.html o inicio.html
if (document.getElementById('cuenta') || document.body.dataset.requireAuth === 'true') {
  // cuenta requiere login, inicio también puede mostrar user
  const needLogin = !!document.getElementById('cuenta');
  checkAuth({ redirectIfNoAuth: needLogin, renderCuenta: needLogin });
}
